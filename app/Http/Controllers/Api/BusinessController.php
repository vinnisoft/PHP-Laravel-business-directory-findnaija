<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityFeed;
use App\Models\BusinessImage;
use App\Models\BusinessRecommendation;
use App\Models\Category;
use App\Models\Language;
use App\Models\RejectBusinessReason;
use App\Models\UserInterest;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Business;
use App\Models\BusinessHiring;
use App\Models\BusinessLanguage;
use App\Models\BusinessService;
use App\Models\BusinessTime;
use App\Models\BusinessConversationMember;
use App\Models\BusinessConversation;
use App\Models\BusinessReport;
use App\Notifications\BusinessNotification;
use App\Notifications\BusinessRecommendationNotification;
use App\Notifications\JobContactNotification;
use App\Notifications\CheckInNotification;
use Illuminate\Support\Facades\Mail;
use DB, Validator, Auth;

class BusinessController extends Controller
{
    public function index(Request $request)
    {
        $categoryIds = UserInterest::where('user_id', Auth::id())->pluck('interest_id')->toArray();
        $businesses = Business::filter($request->all());
        if (!empty($request->latitude) && !empty($request->longitude)) {
            $businesses->distance($request->latitude, $request->longitude, $request->distance ?? 50);
        }
        $businesses = $businesses->whereNotIn('user_id', blockedUserIds())->whereNotIn('status', ['rejected', 'pending', 'expired'])->paginate(20);
        return response()->json(setResponse($businesses));
    }

    public function show(Request $request, $id)
    {
        $request['id'] = $id;
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:businesses,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()]);
        }
        $business = Business::where('id', $id)->with(['images', 'hirings', 'languages', 'reviews', 'times', 'services', 'options', 'events', 'keyWords', 'socialAccount', 'video', 'paymentOption', 'priceMenu', 'dealOffers'])->first();
        $business->business_community = $business->businessCommunity();
        $business->check_in_status = Auth::id() == $business->user_id ? true : BusinessConversationMember::where(['business_id' => $business->id, 'user_id' => Auth::id()])->exists();
        if ($business->status == 'rejected') {
            $business->reject_reason = RejectBusinessReason::where('business_id', $business->id)->first();
        }
        $business->makeHidden('conversationMembers');
        return response()->json([
            'status' => !empty($business) > 0 ? true : false,
            'message' => !empty($business) > 0 ? '' : 'No business found!',
            'data' => $business,
        ]);
    }

    public function store(Request $request)
    {
        if (!UserSubscription::where(['user_id' => Auth::id(), 'status' => 'current'])->exists()) {
            return response()->json(['status' => false, 'message' => "You don't have any current plan right now"]);
        }
        $validation = [
            'country' => 'required',
            'name' => 'required',
            'email' => 'required',
            'address' => 'required',
            'category' => 'required|exists:categories,id',
            'buss_phone_number' => 'required',
            'buss_phone_code' => 'required',
            'detail' => 'required',
            'owner_first_name' => 'required',
            'owner_last_name' => 'required',
            'owner_phone_number' => 'required',
            'owner_phone_code' => 'required',
            'hiring_for_buss' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'nationald' => 'required',
            'business_reg' => 'required',
            'continent' => 'required',
            'state' => 'required',
            'city' => 'required',
            // 'options' => 'required|array|min:1',
            'key_words' => 'required|array|min:1',
            'registration_expire_date' => 'required',
            'logo' => 'required',
            'type' => 'required',
        ];
        $message = [
            'key_words.required' => 'The keywords field is required!'
        ];
        $mergedValidation = array_merge($validation, planRestriction()['validation']);
        $mergedMessage = array_merge($message, planRestriction()['message']);
        $validator = Validator::make($request->all(), $mergedValidation,$mergedMessage);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()]);
        }

        DB::beginTransaction();

        try {

            $request['hiring_for_buss'] = $request->hiring_for_buss == 'yes' ? '1' : '0';
            if (!empty($request->nationald)) {
                $request['national_id'] = $request->nationald;
            }
            if (!empty($request->business_reg)) {
                $request['business_registration'] = $request->business_reg;
            }
            $user = Auth::user();
            $request['status'] = 'pending';
            $request['user_id'] = $user->id;
            $business = Business::create($request->except('price'));
            $business->createServices($request->service);
            $business->createOptions($request->options);
            $business->createTime($request->time);
            $business->businessImages($request->buss_profile);
            $business->createHiring($request->hiring);
            $business->createLanguages($request->language_id);
            $business->createKeyWords($request->key_words);
            $business->createSocialAccount($request->social);
            $business->createVideo($request->video);
            $business->createPaymentOption($request->payment);
            $business->createPriceMenu($request->price);

            BusinessConversationMember::create(['business_id' => $business->id, 'user_id' => Auth::id(), 'unread_messages' => 0]);
            $user->notify(new BusinessNotification($user, $business->name));
            ActivityFeed::create([
                'user_id' => Auth::id(),
                'type' => 'add_business',
                'data' => json_encode([
                    'message' => '<p>You successfully added a <span>business</span></p>',
                    'icon' => 'ic_business_img.svg',
                    'id' => (int)$business->id
                ])
            ]);
            DB::commit();
            return response()->json(['status' => true, 'message' => 'Business has been successfully created! Awaiting approval from admin side!']);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function recommendationBusiness(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'business_name' => 'required',
            'business_address' => 'required',
            'customer_name' => 'required',
            'customer_address' => 'required',
            // 'business_phone' => 'required',
            'website' => 'required',
            'detail' => 'required',
            'country' => 'required',
            'continent' => 'required',
            'state' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()]);
        }

        DB::beginTransaction();

        try {

            $user = Auth::user();
            $request['status'] = '0';
            $request['user_id'] = $user->id;
            $business = BusinessRecommendation::create($request->all());
            $notification = 'Your business recomendation request ('.$business->business_name.') has been successfully sent, Awating for admin approval!';
            $user->notify(new BusinessRecommendationNotification($user, $notification));
            DB::commit();
            return response()->json(['status' => true, 'message' => 'Business Recommendation has been successfully created! Awaiting approval from admin side!']);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function update(Request $request, $id)
    {
        $request['business_id'] = $id;
        $validation = [
            'country' => 'required',
            'name' => 'required',
            'address' => 'required',
            'category' => 'required|exists:categories,id',
            'buss_phone_number' => 'required',
            'buss_phone_code' => 'required',
            'detail' => 'required',
            'owner_first_name' => 'required',
            'owner_last_name' => 'required',
            'owner_phone_number' => 'required',
            'owner_phone_code' => 'required',
            'hiring_for_buss' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'continent' => 'required',
            'state' => 'required',
            'city' => 'required',
            // 'options' => 'required|array|min:1',
            'key_words' => 'required|array|min:1',
            'registration_expire_date' => 'required',
            'type' => 'required',
        ];
        $message = [
            'key_words.required' => 'The keywords field is required!'
        ];
        $mergedValidation = array_merge($validation, planRestriction()['validation']);
        $mergedMessage = array_merge($message, planRestriction()['message']);
        $validator = Validator::make($request->all(), $mergedValidation,$mergedMessage);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()]);
        }

        DB::beginTransaction();

        try {

            $request['hiring_for_buss'] = $request->hiring_for_buss == 'yes' ? '1' : '0';
            if (!empty($request->nationald)) {
                $request['national_id'] = $request->nationald;
            }
            if (!empty($request->business_reg)) {
                $request['business_registration'] = $request->business_reg;
            }
            if (!empty($request->address) || count($request->buss_profile) > 0) {
                $request['status'] = 'pending';
            }
            $business = Business::findOrFail($id);
            $business->update($request->except('business_id', 'language_id', 'hiring', 'buss_profile', 'nationald', 'business_reg', 'time', 'service', 'options', 'key_words', 'price'));
            $business->createServices($request->service);
            $business->createOptions($request->options);
            $business->createTime($request->time);
            $business->businessImages($request->buss_profile);
            $business->createHiring($request->hiring);
            $business->createLanguages($request->language_id);
            $business->createKeywords($request->key_words);
            $business->createSocialAccount($request->social);
            $business->createVideo($request->video);
            $business->createPaymentOption($request->payment);
            $business->createPriceMenu($request->price);
            if (!empty($request->buss_profile) && count($request->buss_profile) > 0) {
                ActivityFeed::create([
                    'user_id' => Auth::id(),
                    'type' => 'uploaded_new_photo',
                    'data' => json_encode([
                        'message' => '<p>You uploaded a new <span>photo</span></p>',
                        'icon' => 'ic_camera.svg',
                        'id' => (int)$id
                    ])
                ]);
            }
            DB::commit();
            return response()->json(['status' => true, 'message' => 'Business has been successfully updated!']);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        if (Business::where('id', $id)->delete()) {
            return response()->json(['status' => true, 'message' => 'Business has been successfully deleted!']);
        }
        return response()->json(['status' => false, 'message' => 'Sonething went wrong please try again!']);
    }    
    
    public function language()
    {
        $language = Language::select('id', 'name')->get();
        return response()->json([
            'status' => count($language) > 0 ? true : false,
            'message' => count($language) > 0 ? '' : 'No language found!',
            'data' => $language
        ]);
    }

    public function newBusinesses()
    {
        // $category = Category::where('show_on_home', '1')->select('id', 'name')->first();
        $businesses = Business::where('status', 'approved')->whereNotNull('logo')->orderBy('id', 'DESC')->paginate(20);
        $businesses->makeHidden(['rating_avg', 'continent_name', 'rating_count']);
        return response()->json(setResponse($businesses));
    }

    public function categoryBusinesses($id)
    {
        $category = Category::where('id', $id)->select('id', 'name')->first();
        $businesses = Business::where(['category' => $id, 'status' => 'approved'])->orderBy('id', 'DESC')->paginate(9);
        $businesses->makeHidden(['category_name', 'rating_avg', 'continent_name', 'rating_count']);
        return response()->json(setResponse($businesses));
    }

    public function similarBusinesses()
    {
        $categoryIds = UserInterest::where('user_id', Auth::id())->pluck('interest_id')->toArray();
        $businesses = Business::select('id', 'name')
            ->where('status', 'approved')
            ->whereIn('category', $categoryIds)
            ->orderBy('id', 'DESC')
            ->paginate(9);
        $businesses->makeHidden(['category_name', 'rating_avg', 'continent_name']);
        return response()->json(setResponse($businesses));
    }
    
    public function businessesNearByMe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required',
            'longitude' => 'required',           
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()]);
        }

        $businesses = Business::distance($request->latitude, $request->longitude, 50)
            ->whereIn('status', ['approved', 'verified'])
            ->paginate(20);
        return response()->json(setResponse($businesses));
    }

    public function checkIn(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'business_id' => 'required|exists:businesses,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()]);
        }
        DB::beginTransaction();
        try {

            $request['user_id'] = Auth::id();
            if (BusinessConversationMember::where($request->all())->exists()) {
                BusinessConversationMember::where($request->all())->delete();
                $activity = getUserNameById($request->user_id).' has left this conversation';
                $msg = 'You have successfully checked out!';
            } else {
                $request['unread_messages'] = '0';
                BusinessConversationMember::create($request->all());
                $userId = Business::where('id', $request->business_id)->pluck('user_id')->first();
                User::findOrFail($userId)->notify(new CheckInNotification(Auth::id(), $userId, $request->business_id));
                $activity = getUserNameById($request->user_id).' has joined this conversation';
                $msg = 'You have successfully checked in!';
                ActivityFeed::create([
                    'user_id' => Auth::id(),
                    'type' => 'check_in_business',
                    'data' => json_encode([
                        'message' => '<p>You checked in to <span>'.getBusinessNameById($request->business_id).'</span></p>',
                        'icon' => 'ic_location_checkin.svg',
                        'id' => (int)$request->business_id
                    ])
                ]);
            }
            BusinessConversation::create(['business_id' => $request->business_id, 'user_id' => $request->user_id, 'message_type' => 'activity', 'activity' => $activity]);
            DB::commit();
            return response()->json(['status' => true, 'message' => $msg]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function report(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'business_id' => 'required|exists:businesses,id',
            'category' => 'required',
            'reason' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()]);
        }
        DB::beginTransaction();
        try {

            $request['user_id'] = Auth::id();
            BusinessReport::create($request->all());
            DB::commit();
            return response()->json(['status' => true, 'message' => 'Report has been successfully submitted!']);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function availableJobs(Request $request)
    {
        $user = Auth::user();
        if (!empty($user->latitude) && !empty($user->longitude)) {
            $businessesIds = Business::distance($user->latitude, $user->longitude, 50)->where('status', 'approved')->pluck('id');
            $jobs = BusinessHiring::whereIn('business_id', $businessesIds)->with(['business'])->paginate(20);
            return response()->json(setResponse($jobs));
        }
        return response()->json(['status' => false, 'message' => 'User location is not available. Please enable location services to find available jobs near you.']);
    }

    public function businessHiring($id)
    {
        $jobs = BusinessHiring::where('business_id', $id)->with(['business'])->get();
        return response()->json([
            'status' => true,
            'message' => count($jobs) > 0 ? '' : 'No job found!',
            'data' => $jobs
        ]);
    }

    public function jobContact(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'business_id' => 'required|exists:businesses,id',
            'subject' => 'required',
            'message' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()]);
        }

        DB::beginTransaction();
        try {

            $business = Business::where('id', $request->business_id)->select('id', 'name', 'user_id')->first();
            $user = User::where('id', $business->user_id)->first();
            $user->notify(new JobContactNotification($user->first_name, Auth::id(), $business->name, $request->subject, $request->message));
            ActivityFeed::create([
                'user_id' => Auth::id(),
                'type' => 'applied_to_job',
                'data' => json_encode([
                    'message' => '<p>You successfully applied to a <span>jop opening</span></p>',
                    'icon' => 'ic_event.svg',
                    'id' => (int)$request->business_id
                ])
            ]);
            DB::commit();
            return response()->json(['status' => true, 'message' => 'Message has been sent!']);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function featuredTestimonials(Request $request)
    {
        $businesses = Business::select('id', 'owner_first_name', 'owner_last_name', 'address', 'detail', 'user_id')->where('status', 'approved')->withCount('reviews')->orderByDesc('reviews_count')->paginate(10);
        foreach ($businesses as $business) {
            $business->owner_profile = getUserImageById($business->user_id);
        }
        return response()->json(setResponse($businesses));
    }
}
