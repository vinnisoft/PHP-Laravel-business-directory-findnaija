<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityFeed;
use App\Models\Category;
use App\Models\Language;
use App\Models\ReviewFile;
use App\Models\ReviewReply;
use App\Models\ReviewReport;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Business;
use App\Models\BusinessHiring;
use App\Models\BusinessLanguage;
use App\Models\Review;
use App\Models\ReviewLike;
use App\Notifications\ReviewNotification;
use DB, Validator, Auth;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()]);
        }

        if ($request->type == 'business') {
            $businessIds = Business::where('user_id', Auth::id())->pluck('id');
            $reviews = Review::whereIn('business_id', $businessIds)->orderBy('id', 'DESC')->with(['files', 'replies'])->paginate(20);
        } else {
            $reviews = Review::where('user_id', Auth::id())->orderBy('id', 'DESC')->with(['files', 'replies'])->paginate(20);
        }
        $oneMonthAgo = now()->subMonth();
        foreach ($reviews as $review) {
            $business = Business::where('id', $review->business_id)->first();
            $review->business = [
                'id' => $business->id,
                'name' => $business->name,
                'image' => $business->image,
                'address' => $business->address,
            ];
            $review->likes_all_time = $review->likes()->count();
            $review->likes_last_month = $review->likes()->whereDate('created_at', '>=', $oneMonthAgo)->count();
        }
        return response()->json(setResponse($reviews));
    }

    public function show(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:reviews,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()]);
        }
        $review = Review::where('id', $request->id)->with(['files', 'replies'])->first();
        return response()->json(['status' => true, 'message' => '', 'data' => $review]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'business_id' => 'required|exists:businesses,id',
            'comment' => 'required',
            'rating' => 'required',            
            'files' => 'array|min:1',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()]);
        }

        DB::beginTransaction();
        try {
            
            $request['user_id'] = Auth::id();
            $review = Review::create($request->all());
            $review->createFiles($request['files']);
            $userId = Business::where('id', $request->business_id)->pluck('user_id')->first();
            User::findOrFail($userId)->notify(new ReviewNotification(Auth::id(), $userId, $request->business_id, $review));
            ActivityFeed::create([
                'user_id' => Auth::id(),
                'type' => 'left_review',
                'data' => json_encode([
                    'message' => '<p>You left a review on <span>'.getBusinessNameById($request->business_id).'</span></p>',
                    'icon' => 'ic_chat_send.svg',
                    'id' => (int)$review->id
                ])
            ]);

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Review has been successfully submitted!']);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:reviews,id',
            'business_id' => 'required|exists:businesses,id',
            'comment' => 'required',
            'rating' => 'required',
            'files' => 'array|min:1',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()]);
        }

        DB::beginTransaction();
        try {

            $request['user_id'] = Auth::id();
            Review::where('id', $request->id)->update($request->except('files'));
            if (isset($request['files']) && count($request['files']) > 0) {
                $review = Review::findOrFail($request->id);
                $review->files()->delete();
                $review->createFiles($request['files']);
            }
            DB::commit();
            return response()->json(['status' => true, 'message' => 'Review has been successfully Updated!']);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function report(Request $request)
    {
        $request['user_id'] = Auth::id();
        $validator = Validator::make($request->all(), [
            'review_id' => 'required|exists:reviews,id',
            'user_id' => 'required|exists:users,id',
            'reason' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()]);
        }
        if (ReviewReport::create($request->all())) {
            return response()->json(['status' => true, 'message' => 'Reported!']);
        }
        return response()->json(['status' => false, 'message' => 'Something went wrong please try again']);
    }

    public function reply(Request $request)
    {
        $request['user_id'] = Auth::id();
        $validator = Validator::make($request->all(), [
            'review_id' => 'required|exists:reviews,id',
            'user_id' => 'required|exists:users,id',
            'comment' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()]);
        }
        if (ReviewReply::create($request->all())) {
            $userId = Review::where('id', $request->review_id)->pluck('user_id')->first();
            ActivityFeed::create([
                'user_id' => $userId,
                'type' => 'comment_on_review',
                'data' => json_encode([
                    'message' => '<p>Someone left a comment on your <span>review</span></p>',
                    'icon' => 'ic_chat_send.svg',
                    'id' => (int)$request->review_id
                ])
            ]);
            return response()->json(['status' => true, 'message' => 'Review reply has been successfully posted']);
        }
        return response()->json(['status' => false, 'message' => 'Something went wrong please try again']);
    }
    public function reviewFiles(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'business_id' => 'required|exists:businesses,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()]);
        }
        $reviewIds = Review::where('business_id', $request->business_id)->pluck('id');
        $files = ReviewFile::whereIn('review_id', $reviewIds)->select('id', 'file', 'review_id')->get();
        foreach ($files as $file) {
            $user = Review::where('id', $file->review_id)->first(['id', 'user_id', 'rating', 'comment']);
            $file->user_name = getUserNameById($user->user_id);
            $file->user_profile = getUserImageById($user->user_id);
            $file->rating = $user->rating;
            $file->comment = $user->comment;
            $file->like = $user->like;
            $file->like_count = $user->like_count;
        }
        return response()->json([
            'status' => !empty($files) > 0 ? true : false,
            'message' => !empty($files) > 0 ? '' : 'No file found!',
            'data' => $files,
        ]);
    }

    public function singleReviewFiles(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:review_files,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()]);
        }
        $reviewsFile = ReviewFile::where('id', $request->id)->first();
        $reviews = Review::where('id', $reviewsFile->review_id)->first(['id', 'business_id', 'user_id', 'comment', 'rating']);
        $reviews->file = $reviewsFile->file;
        return response()->json([
            'status' => !empty($reviews) > 0 ? true : false,
            'message' => !empty($reviews) > 0 ? '' : 'No file found!',
            'data' => $reviews,
        ]);
    }

    public function likeUnLike(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'review_id' => 'required|exists:reviews,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()]);
        }
        $request['user_id'] = Auth::id();
        if (ReviewLike::where($request->all())->exists()) {
            ReviewLike::where($request->all())->delete();
            return response()->json(['status' => true, 'message' => 'Unliked!']);
        } else {
            ReviewLike::create($request->all());
            $userId = Review::where('id', $request->review_id)->pluck('user_id')->first();
            ActivityFeed::create([
                'user_id' => $userId,
                'type' => 'like_review',
                'data' => json_encode([
                    'message' => '<p>Someone liked your <span>review</span></p>',
                    'icon' => 'ic_unlike.svg',
                    'id' => (int)$request->review_id
                ])
            ]);
            return response()->json(['status' => true, 'message' => 'Liked!']);
        }
    }

    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:reviews,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()]);
        }
        $request['user_id'] = Auth::id();
        if (Review::where($request->all())->delete()) {
            return response()->json(['status' => true, 'message' => 'Review has been deleted!']);
        }
        return response()->json(['status' => false, 'message' => 'Something went wrong please try again!']);
    }

    public function featuredReviews(Request $request)
    {
        $reviews = Review::leftJoin('review_likes', 'reviews.id', '=', 'review_likes.review_id')
            ->select('reviews.id', 'reviews.comment', 'reviews.user_id', 'reviews.rating', 'reviews.business_id', DB::raw('COUNT(review_likes.id) as likes_count'))
            ->groupBy('reviews.id', 'reviews.comment', 'reviews.user_id', 'reviews.rating', 'reviews.business_id')
            ->orderBy('likes_count', 'DESC')
            ->with(['files'])
            ->get();
        foreach ($reviews as $review) {
            $business = Business::where('id', $review->business_id)->first();
            $review->business = [
                'id' => $business->id,
                'name' => $business->name,
                'image' => $business->image,
                'address' => $business->address,
            ];
        }
        return response()->json([
            'status' => count($reviews) > 0 ? true : false,
            'message' => count($reviews) > 0 ? '' : 'No review found!',
            'data' => $reviews
        ]);
    }
}
