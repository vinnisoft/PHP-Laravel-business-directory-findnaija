<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\AdGoal;
use App\Models\AdMedia;
use App\Models\AdPayment;
use Auth;
use Illuminate\Http\Request;
use DB, Validator;

class AdController extends Controller
{
    public function index()
    {
        $ads = Ad::where('is_active', 1)->with(['goal', 'media', 'business', 'audiance', 'location'])->orderBy('id', 'DESC')->get();
        return response()->json([
            'status' => true,
            'message' => count($ads) > 0 ? '' : 'No ad found',
            'data' => $ads,
        ]);
    }

    public function show(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:ads,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()]);
        }
        $ad = Ad::where('id', $request->id)->with(['goal', 'media', 'business', 'audiance', 'location'])->orderBy('id', 'DESC')->first();
        return response()->json([
            'status' => true,
            'message' => '',
            'data' => $ad,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required',            
            'business_id'  => 'required_if:type,business',         
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()]);
        }
        DB::beginTransaction();

        try {
            
            $request['user_id'] = Auth::id();
            $request['is_active'] = 0;
            $ad = Ad::updateOrCreate(['id' => $request->id], $request->except('ad_media'));
            $adId = isset($request->id) ? $request->id : $ad->id;
            $msg = isset($request->id) ? 'updated!' : 'created!';
            if (isset($request->ad_media) && count($request->ad_media) > 0) {
                AdMedia::where('ad_id', $adId)->delete();
                foreach ($request->ad_media as $media) {                   
                    AdMedia::create(['ad_id' => $adId, 'media' => $media]); 
                }                  
            }
            $createdAd = Ad::where('id', $ad->id)->with(['goal', 'media', 'business', 'audiance', 'location'])->first();
            DB::commit();
            return response()->json(['status' => true, 'message' => 'Ad has been successfully '.$msg, 'data' => $createdAd]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function goals()
    {
        $goals = AdGoal::orderBy('id', 'DESC')->get();
        return response()->json([
            'status' => true,
            'message' => count($goals) > 0 ? '' : 'No goal found',
            'data' => $goals,
        ]);
    }

    public function setGoals(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:ads,id',
            'goal_id'  => 'required|exists:ad_goals,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()]);
        }
        $request['is_active'] = 0;
        if (Ad::where('id', $request->id)->update(['ad_goal_id' => $request->goal_id])) {
            $ad = Ad::where('id', $request->id)->with(['goal', 'media', 'business', 'audiance', 'location'])->first();
            return response()->json(['status' => true, 'message' => '', 'data' => $ad]);
        }
        return response()->json(['status' => false, 'message' => 'Something went wrong please try again']);
    }

    public function setAudienceLocation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ad_id'  => 'required|exists:ads,id',
            'audiance' => 'required|array|min:1',
            'address' => 'required|array|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()]);
        }
        DB::beginTransaction();

        try {

            $ad = Ad::where('id', $request->ad_id)->with(['goal', 'media', 'business', 'audiance', 'location'])->first();
            if (isset($request->audiance) && count($request->audiance) > 0) {
                $ad->audiance()->delete();
                foreach ($request->audiance as $audiance) { 
                    $ad->audiance()->create(['category_id' => $audiance]);
                }
            }
            if (isset($request->address) && count($request->address) > 0) {
                $ad->location()->delete();
                foreach ($request->address as $address) {
                    $ad->location()->create($address);
                }
            }

            $updatedAd = Ad::where('id', $request->ad_id)->with(['goal', 'media', 'business', 'audiance', 'location'])->first();
            $updatedAd->total_price = count($request->audiance) + count($request->address);
            DB::commit();
            return response()->json(['status' => true, 'message' => 'Ad audiance and address has been successfully saved!', 'data' => $updatedAd]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function setBudgetDuration(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ad_id'  => 'required|exists:ads,id',
            'status' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'budget' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()]);
        }
        $request['is_active'] = 0;
        if (Ad::where('id', $request->ad_id)->update($request->except('ad_id'))) {
            $ad = Ad::where('id', $request->ad_id)->with(['goal', 'media', 'business', 'audiance', 'location'])->orderBy('id', 'DESC')->first();
            return response()->json(['status' => true, 'message' => 'Add budget and duration has been updated!', 'data' => $ad]);
        }
        return response()->json(['status' => false, 'message' => 'Something went wrong please try again']);
    }

    public function stopAp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'  => 'required|exists:ads,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()]);
        }
        if (Ad::where('id', $request->id)->update(['end_date' => now(), 'is_active' => 0])) {
            $ad = Ad::where('id', $request->id)->with(['goal', 'media', 'business', 'audiance', 'location'])->orderBy('id', 'DESC')->first();
            return response()->json(['status' => true, 'message' => 'Ad has been successfully stopped!', 'data' => $ad]);
        }
        return response()->json(['status' => false, 'message' => 'Something went wrong please try again']);
    }

    public function adPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ad_id'  => 'required|exists:ads,id',
            'price' => 'required',
            'payment_method' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()]);
        }
        $request['user_id'] = Auth::id();
        if (AdPayment::create($request->all())) {
            Ad::where('id', $request->ad_id)->update(['is_active' => 1]);
            return response()->json(['status' => true, 'message' => 'Ad payment has been successfully created!']);
        }
        return response()->json(['status' => false, 'message' => 'Something went wrong please try again']);
    }
}