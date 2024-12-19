<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityFeed;
use App\Models\Business;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Story;
use App\Models\Support;
use App\Models\ReportUser;
use App\Models\ChatRequest;
use App\Models\ViewStory;
use App\Models\Block;
use App\Models\BlockRange;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Pagination\LengthAwarePaginator;
use DB, Validator, Log, Newsletter;
use Illuminate\Support\Facades\Http;
// use Spatie\Newsletter\Facades\Newsletter;

class ProfileController extends Controller
{
    public function profile(Request $request)
    {
        $user = User::query();
        if (isset ($request->user_id)) {
            $user->where('id', $request->user_id);
        } else {
            $user->where('id', Auth::id());
        }
        $user = $user->with(['subscription'])->first();
        if (isset($user->subscription->id)) {
            $user->previous_subscripton = UserSubscription::where('user_id', Auth::id())->where('id', '!=', $user->subscription->id)->get();
        } else {
            $user->previous_subscripton = null;
        }
        return response()->json([
            'status' => true,
            'message' => 'User profile',
            'data' => $user
        ]);
    }


    public function searchUsers(Request $request)
    {
        if (!empty($request->search)) {
            $users = User::select('id', 'first_name', 'last_name', 'profile_image')
                ->where('id', '!=', Auth::id())
                ->where('first_name', 'like', "%$request->search%")
                ->orWhere('last_name', 'like', "%$request->search%")
                ->orWhere('email', 'like', "%$request->search%")
                ->orWhere('username', 'like', "%$request->search%")
                ->get();
            foreach ($users as $user) {
                $userId = $user->id;
                $chatRequest = ChatRequest::where(function ($query) use ($userId) {
                    $query->where(['requested_by' => Auth::id(), 'requested_to' => $userId]);
                })->orWhere(function ($query) use ($userId) {
                    $query->where(['requested_by' => $userId, 'requested_to' => Auth::id()]);
                })->select('id', 'status')->first();
                $user->room_id = @$chatRequest->id;
                $user->acceptation_status = @$chatRequest->status;
                $user->block = Block::where(['user_id' => Auth::id(), 'blocked_user_id' => $user->id])->exists();
                $user->type = 'user';
            }
            $businessResults = Business::select('id', 'name', 'email', 'logo')
                ->addSelect(DB::raw("'business' as type"))
                ->where('name', 'like', "%$request->search%")
                ->orWhere('email', 'like', "%$request->search%")
                ->get();
            $businessResults->makeHidden(['category_name', 'rating_avg', 'rating_count', 'collection_status', 'authentication', 'calculate_distance']);
            $mergedResults = $users->merge($businessResults);
            $perPage = 20;
            $currentPage = LengthAwarePaginator::resolveCurrentPage();
            $currentPageResults = $mergedResults->slice(($currentPage - 1) * $perPage, $perPage)->all();
            $paginator = new LengthAwarePaginator($currentPageResults, $mergedResults->count(), $perPage, $currentPage);

            return response()->json(setResponse($paginator));
        }

        return response()->json(setResponse([]));
    }

    public function updateProfile(Request $request)
    {
        if (Auth::user()->update($request->all())) {
            return response()->json(['status' => true, 'message' => 'User profile has been successfully updated!', 'data' => Auth::user()]);
        }
        return response()->json(['status' => false, 'message' => 'Something went wrong please try again!']);
    }

    public function story(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required',
            'type' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
        }
        $request['user_id'] = Auth::id();
        if (Story::create($request->all())) {
            return response()->json(['status' => true, 'message' => 'Story has been successfully added!']);
        }
        return response()->json(['status' => false, 'message' => 'Something went wrong please try again!']);
    }

    public function stories(Request $request)
    {
        $requestedTo = ChatRequest::where('requested_by', Auth::id())->pluck('requested_to')->toArray();
        $requestedBy = ChatRequest::where('requested_to', Auth::id())->pluck('requested_by')->toArray();
        $userIds = array_unique(array_merge([Auth::id()], $requestedTo, $requestedBy));
        $data = [];
        foreach ($userIds as $userId) {
            $stories = Story::where('user_id', $userId)->where('status', '1')->select('id', 'file', 'type')->get()->toArray();
            if (count($stories) > 0) {
                $data[] = [
                    'user_id' => $userId,
                    'user_name' => getUserNameById($userId),
                    'user_image' => getUserImageById($userId),
                    'stories' => $stories,
                ];
            }
        }
        return response()->json([
            'status' => true,
            'message' => count($data) > 0 ? '' : 'No story found',
            'data' => $data
        ]);
    }

    public function readStory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'story_id' => 'required|exists:stories,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
        }
        if (ViewStory::create(['story_id' => $request->story_id, 'user_id' => Auth::id()])) {
            return response()->json(['status' => true, 'message' => 'This story has been successfully readed!']);
        }
        return response()->json(['status' => false, 'message' => 'Something went wrong please try again!']);
    }

    public function deleteStory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:stories,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
        }
        if (Story::where('id', $request->id)->delete()) {
            return response()->json(['status' => true, 'message' => 'This story has been successfully deleted!']);
        }
        return response()->json(['status' => false, 'message' => 'Something went wrong please try again!']);
    }

    public function support(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
        }
        $request['user_id'] = Auth::id();
        if (Support::create($request->all())) {
            return response()->json(['status' => true, 'message' => 'Support request submitted successfully!']);
        }
        return response()->json(['status' => false, 'message' => 'Something went wrong please try again!']);
    }

    public function closeAccount(Request $request)
    {
        if (User::where('id', Auth::id())->forceDelete()) {
            return response()->json(['status' => true, 'message' => 'Account has been successfully deleted!']);
        }
        return response()->json(['status' => false, 'message' => 'Something went wrong please try again!']);
    }

    public function reportUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reported_to' => 'required|exists:users,id',
            'message' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
        }
        $request['reported_by'] = Auth::id();
        if (ReportUser::create($request->all())) {
            return response()->json(['status' => true, 'message' => 'Reported!']);
        }
        return response()->json(['status' => false, 'message' => 'Something went wrong please try again!']);
    }

    public function updateFcmToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fcm_token' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
        }
        if (User::where('id', Auth::id())->update(['fcm_token' => $request->fcm_token])) {
            return response()->json(['status' => true, 'message' => 'Token has been updated!']);
        }
        return response()->json(['status' => false, 'message' => 'Something went wrong please try again!']);
    }

    public function updateBusinessDistance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'business_distance' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
        }
        if (User::where('id', Auth::id())->update(['business_distance' => $request->business_distance])) {
            return response()->json(['status' => true, 'message' => 'Business distance has been updated!']);
        }
        return response()->json(['status' => false, 'message' => 'Something went wrong please try again!']);
    }

    public function blockUnBlock(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'blocked_user_id' => 'required|exists:users,id',
            'room_id' => 'required|exists:chat_requests,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
        }
        $roomId = $request->room_id;
        unset($request['room_id']);
        $request['user_id'] = Auth::id();
        if (Block::where($request->all())->exists()) {
            BlockRange::where([
                'block_by' => Auth::id(),
                'room_id' => $roomId,
                'end_date' => null,
            ])->update(['end_date' => now()]);

            Block::where($request->all())->delete();
            $msg = 'User has been successfully unblocked!';
        } else {
            Block::create($request->all());

            BlockRange::create([
                'block_by' => Auth::id(),
                'room_id' => $roomId,
                'start_date' => now(),
            ]);

            // $chatRequest = ChatRequest::findOrFail($roomId);
            // if (Auth::id() == $chatRequest->requested_by) {
            //     $chatRequest->update(['deleted_msg_date_by' => now()]);
            // } else {
            //     $chatRequest->update(['deleted_msg_date_to' => now()]);
            // }
            $msg = 'User has been successfully blocked!';
        }
        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function blockedUsers(Request $request)
    {
        $users = User::whereIn('id', blockedUserIds())->select('id', 'first_name', 'last_name', 'profile_image')->get();
        foreach ($users as $user) {
            $memberId = $user->id;
            $user->room_id = ChatRequest::where(function ($query) use ($memberId) {
                $query->where(['requested_by' => Auth::id(), 'requested_to' => $memberId]);
            })->orWhere(function ($query) use ($memberId) {
                $query->where(['requested_by' => $memberId, 'requested_to' => Auth::id()]);
            })->pluck('id')->first();
        }
        return response()->json([
            'status' => true,
            'message' => count($users) > 0 ? '' : 'No user found!',
            'data' => $users
        ]);
    }

    public function userSubscription(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'plan_id' => 'required|exists:plans,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
        }
        DB::beginTransaction();
        try {

            $coindition = ['user_id' => Auth::id(), 'status' => 'current'];
            if (UserSubscription::where($coindition)->exists()) {
                UserSubscription::where($coindition)->update(['status' => 'previous']);
            }
            $request['user_id'] = Auth::id();
            $request['start_date'] = date('Y-m-d');
            UserSubscription::create($request->all());

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Subscription has been successfully created!']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function cancelSubscription(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:user_subscriptions,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
        }
        if (UserSubscription::where('id', $request->id)->update(['status' => 'cancelled'])) {
            return response()->json(['status' => true, 'message' => 'Subscription has been successfully cancelled!']);
        }
        return response()->json(['status' => false, 'message' => 'Something went wrong please try again']);
    }

    public function subscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
        }

        $email = $request->input('email');
        $apiKey = env('MAILCHIMP_API_KEY');
        $serverPrefix = env('MAILCHIMP_SERVER_PREFIX');
        $listId = env('MAILCHIMP_LIST_ID');

        // Mailchimp API URL to check if the email exists
        $subscriberHash = md5(strtolower($email));
        $url = "https://{$serverPrefix}.api.mailchimp.com/3.0/lists/{$listId}/members/{$subscriberHash}";
        $response = Http::withBasicAuth('anystring', $apiKey)->get($url);
        if ($response->successful()) {
            return response()->json(['status' => false, 'message' => 'Email already subscribed']);
        }

        $url = "https://{$serverPrefix}.api.mailchimp.com/3.0/lists/{$listId}/members";
        $response = Http::withBasicAuth('anystring', $apiKey)->post($url, [
            'email_address' => $email,
            'status' => 'subscribed',
        ]);

        if ($response->successful()) {
            return response()->json(['status' => true, 'message' => 'Subscribed']);
        }

        return response()->json(['status' => false, 'message' => $response->json('detail')]);
    }

    public function activityFeed(Request $request)
    {
        $lastWeekStartDate = Carbon::now()->startOfWeek()->subWeek()->format('Y-m-d');
        $lastWeekEndDate = Carbon::now()->endOfWeek()->subWeek()->format('Y-m-d');
        $baseQuery = ActivityFeed::select('id', 'user_id', 'data', 'created_at')->where('user_id', Auth::id())->orderBy('id', 'DESC');
        $groupedRecords = [
            'today' => (clone $baseQuery)->whereDate('created_at', Carbon::today())->get(),
            'yesterday' => (clone $baseQuery)->whereDate('created_at', Carbon::yesterday())->get(),
            'last_week' => (clone $baseQuery)->whereBetween('created_at', [$lastWeekStartDate, $lastWeekEndDate])->get()
        ];

        return response()->json([
            'status' => count($groupedRecords) > 0,
            'message' => count($groupedRecords) > 0 ? '' : 'Something went wrong, please try again',
            'data' => $groupedRecords,
        ]);
    }

}