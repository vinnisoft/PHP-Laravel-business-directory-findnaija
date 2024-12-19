<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityFeed;
use App\Models\Plan;
use App\Models\UserSubscription;
use App\Notifications\VerifyEmail;
use App\Notifications\ForgotPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Interest;
use App\Models\UserInterest;
use App\Models\Business;
use App\Models\BusinessConversationMember;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use DB, Validator, Log;
use Illuminate\Support\Facades\Cache;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()]);
        }

        DB::beginTransaction();
        try {

            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                $user = Auth::user();
                $user->tokens()->delete();

                if ($user->email_verified_at !== null) {
                    $user->token = $user->createToken('MyApp')->plainTextToken;
                    $response = ['status' => true, 'message' => 'User has been successfully logged in!', 'data' => $user];
                } else {
                    Auth::logout();
                    $response = ['status' => false, 'message' => 'Email not verified!'];
                }
            } else {
                $response = ['status' => false, 'message' => 'Invalid credentials'];
            }
            DB::commit();
            return response()->json($response);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function signUp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'country' => 'required',
            'phone_number' => 'required',
            'dob' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()]);
        }

        DB::beginTransaction();
        try {

            $request['password'] = Hash::make($request->password);
            $request['login_type'] = 'normal';
            $request['otp'] = rand(1000, 9999);
            $request['username'] = explode("@", $request->email)[0];
            $user = User::create($request->all());
            $user->assignRole('user');
            UserSubscription::create([
                'user_id' => $user->id,
                'plan_id' => Plan::where('type', 'basic')->pluck('id')->first(),
                'start_date' => date('Y-m-d'),
                'status' => 'current',
            ]);
            $user->notify(new VerifyEmail($user));
            enableUserNotification($user->id);
            DB::commit();
            return response()->json(['status' => true, 'message' => 'The user has been successfully created. An OTP has been sent to your email. Please verify your email.', 'data' => $request['otp']]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function resendOtp(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'email' => 'required',
        ]);
        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validation->errors()->first()
            ]);
        }

        DB::beginTransaction();
        try {

            $user = User::where('email', $request->email)->first();
            $request['otp'] = rand(1000, 9999);
            $user->update($request->all());
            $user->notify(new VerifyEmail($user));
            DB::commit();
            return response()->json(['status' => true, 'message' => 'OTP sent successfully!']);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function socialLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => [
                'required',
                Rule::unique('users')->where(function ($query) {
                    $query->where('login_type', 'normal');
                }),
            ],
            'social_id' => 'required',
            'country' => 'required',
            'phone_number' => 'required',
            // 'dob' => 'required',
            'login_type' => 'required',
            'latitude' => Rule::requiredIf(function () use ($request) {
                return !User::where('email', $request->email)->exists();
            }),
            'longitude' => Rule::requiredIf(function () use ($request) {
                return !User::where('email', $request->email)->exists();
            }),
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()]);
        }

        // if (User::withTrashed()->where('email', $request->email)->whereNotNull('deleted_at')->exists()) {
        //     return response()->json(['status' => false, 'message' => 'The account with this email has been deleted!']);
        // }

        DB::beginTransaction();
        try {

            $request['password'] = Hash::make($request->social_id);
            $request['login_type'] = $request->login_type;
            $request['email_verified_at'] = now();
            $request['username'] = explode("@", $request->email)[0];
            if (User::where('email', $request->email)->whereNull('deleted_at')->exists()) {
                User::where('email', $request->email)->update($request->all());
                $user = User::where('email', $request->email)->first();
            } else {
                $user = User::create($request->all());
                UserSubscription::create([
                    'user_id' => $user->id,
                    'plan_id' => Plan::where('type', 'basic')->pluck('id')->first(),
                    'start_date' => date('Y-m-d'),
                    'status' => 'current',
                ]);
                enableUserNotification($user->id);
            }
            $user->tokens()->delete();
            $user->token = $user->createToken('MyApp')->plainTextToken;
            $user->interest = UserInterest::where('user_id', $user->id)->count();

            DB::commit();
            return response()->json(['status' => true, 'message' => 'User has been successfully login by ' . $request->login_type . '!', 'data' => $user]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function verifyEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'otp' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()]);
        }

        DB::beginTransaction();
        try {

            $condition = ['email' => $request->email, 'otp' => $request->otp];
            $user = User::where($condition)->first();
            if ($user) {
                User::where($condition)->update(['email_verified_at' => now()]);
                $msg = 'Email has been verified!';
                $user->token = $user->createToken('MyApp')->plainTextToken;
                $status = true;
            } else {
                $status = false;
                $msg = 'Invalid OTP';
            }
            DB::commit();
            return response()->json(['status' => $status, 'message' => $msg, 'data' => $user]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()]);
        }
        if (User::withTrashed()->where('email', $request->email)->whereNotNull('deleted_at')->exists()) {
            return response()->json(['status' => false, 'message' => 'This account has been deleted!']);
        }

        DB::beginTransaction();
        try {

            $user = User::where('email', $request->email)->first();
            $name = $user->first_name . ' ' . $user->last_name;
            $otp = rand(1000, 9999);
            $user->otp = $otp;
            $user->save();
            $user->notify(new ForgotPassword($name, $otp));

            DB::commit();
            return response()->json(['status' => true, 'message' => 'An OTP has been sent to the email please check', 'otp' => $otp]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function ChangePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'new_password' => 'required',
            'confirm_password' => 'required|same:new_password',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()]);
        }
        if (User::withTrashed()->where('email', $request->email)->whereNotNull('deleted_at')->exists()) {
            return response()->json(['status' => false, 'message' => 'This account has been deleted!']);
        }

        DB::beginTransaction();
        try {

            User::where('email', $request->email)->update(['password' => Hash::make($request->confirm_password)]);
            DB::commit();
            return response()->json(['status' => true, 'message' => 'Password has been updated!']);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'new_password' => 'required',
            'confirm_password' => 'required|same:new_password',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()]);
        }

        $user = Auth::user();
        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json(['status' => false, 'message' => 'Old password is incorrect!']);
        }

        DB::beginTransaction();
        try {

            $user->update(['password' => Hash::make($request->confirm_password)]);
            DB::commit();
            return response()->json(['status' => true, 'message' => 'Password has been updated!']);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function logOut()
    {
        if (Auth::user()->tokens()->delete()) {
            return response()->json(['status' => true, 'message' => 'User has been logged out!']);
        }
        return response()->json(['status' => false, 'message' => 'Something went wrong, please try again!']);
    }

    public function interests(Request $request)
    {
        $interests = Interest::select('id', 'name')->orderBy('id', 'DESC')->get();
        return response()->json([
            'status' => count($interests) > 0 ? true : false,
            'message' => count($interests) > 0 ? '' : 'No interest found!',
            'data' => $interests
        ]);
    }

    public function userInterests(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'interest_id' => 'required|array|min:1|exists:categories,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()]);
        }
        foreach ($request->interest_id as $interest_id) {
            $condition = ['user_id' => Auth::id(), 'interest_id' => $interest_id];
            if (!UserInterest::where($condition)->exists()) {
                UserInterest::create($condition);
            }
        }
        return response()->json(['status' => true, 'message' => 'User Interest has been successfully created!']);
    }

    public function myBusinesses()
    {
        $businesses = Business::where(['user_id' => Auth::id()])->orderBy('id', 'DESC')->with(['images', 'hirings', 'languages', 'reviews', 'times', 'services', 'options', 'keyWords', 'socialAccount', 'video', 'paymentOption'])->paginate(20);
        return response()->json(setResponse($businesses));
    }

    public function selectBusinesses()
    {
        $businesses = Business::where('user_id', Auth::id())->where('status', '!=', 'rejected')->orderBy('id', 'DESC')->get(['id', 'name'])->makeHidden(['category_name', 'image', 'rating_avg', 'rating_count', 'collection_status']);
        return response()->json([
            'status' => count($businesses) > 0 ? true : false,
            'message' => count($businesses) > 0 ? '' : 'No business found!',
            'data' => $businesses
        ]);
    }

    public function searchBusinesses(Request $request)
    {
        $name = $request->search;
        if (!empty($name)) {
            $businesses = Business::where('status', '1')->where('name', 'LIKE', "%$name%")->orderBy('id', 'DESC')->get(['id', 'name'])->makeHidden(['category_name', 'image', 'rating_avg', 'rating_count', 'collection_status']);
        } else {
            $businesses = [];
        }
        return response()->json([
            'status' => count($businesses) > 0 ? true : false,
            'message' => count($businesses) > 0 ? '' : 'No business found!',
            'data' => $businesses
        ]);
    }

    public function checkedInBusiness(Request $request)
    {
        $businessIds = BusinessConversationMember::where('user_id', Auth::id())->pluck('business_id');
        $businesses = Business::whereIn('id', $businessIds)->orderBy('id', 'DESC')->get(['id', 'country', 'name', 'address']);
        foreach ($businesses as $business) {
            $business->check_in_status = BusinessConversationMember::where(['business_id' => $business->id, 'user_id' => Auth::id()])->exists();
        }
        return response()->json([
            'status' => count($businesses) > 0 ? true : false,
            'message' => count($businesses) > 0 ? '' : 'No business found!',
            'data' => $businesses
        ]);
    }
    
    public function myNotifications(Request $request)
    {
        $notifications = Auth::user()->unreadNotifications;
        return response()->json([
            'status' => true,
            'message' => !empty($notifications) ? '' : 'No notifications found!',
            'data' => $notifications
        ]);
    }

    public function plans(Request $request)
    {
        $plans = Plan::get();
        $subscripton = UserSubscription::where(['user_id' => Auth::id(), 'status' => 'current'])->first();
        $previousSubscripton = UserSubscription::where(['user_id' => Auth::id(), 'status' => 'previous'])->orderBy('id', 'DESC')->get();
        return response()->json([
            'status' => true,
            'message' => !empty($plans) ? '' : 'No plans found!',
            'data' => $plans,
            'subscripton' => $subscripton,
            'previous_subscripton' => $previousSubscripton
        ]);
    }

    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()]);
        }
        $user = Auth::user();
        if (!Hash::check($request->password, $user->password)) {
            return response()->json(['status' => false, 'message' => 'Password dose not matched!']);
        }
        if (User::where('id', Auth::id())->delete()) {
            return response()->json(['status' => true, 'message' => 'User account has been deleted!']);
        }
        return response()->json(['status' => false, 'message' => 'Something went wrong please try again!']);
    }

    public function clearHistory(Request $request)
    {
        ActivityFeed::where('user_id', Auth::id())->delete();
        return response()->json(['status' => true, 'message' => 'History has been cleared!']);
    }
}
