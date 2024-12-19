<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\AdMedia;
use App\Models\Business;
use App\Models\DealOffer;
use App\Models\DealOfferLocation;
use App\Models\UserSubscription;
use Auth;
use Illuminate\Http\Request;
use DB, Validator;

class DealOfferController extends Controller
{
    public function index()
    {
        $userLatitude = Auth::user()->latitude;
        $userLongitude = Auth::user()->longitude;
        $dealOfferLocations = DealOfferLocation::select(getDistance($userLatitude, $userLongitude))
            ->having('distance', '<=', 50)
            ->orderBy('distance', 'asc')
            ->get();
        $dealOfferIds = $dealOfferLocations->pluck('deal_offer_id')->toArray();
        $offers = DealOffer::whereIn('id', $dealOfferIds)->with(['locations'])->get();
        foreach ($offers as $offer) {
            $location = $dealOfferLocations->firstWhere('deal_offer_id', $offer->id);
            $offer->distance = $location ? $location->distance : null;
        }
        return response()->json([
            'status' => true,
            'message' => count($offers) > 0 ? '' : 'No offer found',
            'data' => $offers,
        ]);
    }

    public function show($id)
    {
        $offer = DealOffer::where('id', $id)->with(['locations'])->first();
        $business = Business::where('id', $offer->business_id)->first();
        $offer->business_detail = [
            'title' => $business->name,
            'address' => $business->address,
            'rating' => $business->rating_avg,
            'email' => $business->email,
            'phone' => $business->buss_phone_code.' '.$business->buss_phone_number,
            'website' => $business->website,
        ];
        $offer->other_offers = DealOffer::where('id', '!=', $id)->where('business_id', $offer->business_id)->get();
        return response()->json([
            'status' => true,
            'message' => $offer ? '' : 'No offer found',
            'data' => $offer,
        ]);
    }

    public function store(Request $request)
    {
        $userPlanId = UserSubscription::where(['user_id' => Auth::id(), 'status' => 'current'])->pluck('plan_id')->first();
        if (getPlanTypeById($userPlanId) == 'basic') {
            return response()->json(['status' => false, 'message' => 'You are not able to create deals & offers with basic plan!']);
        }
        $validator = Validator::make($request->all(), [
            'business_id' => 'required|exists:businesses,id',
            'title' => 'required',            
            'picture' => 'required',            
            'description' => 'required',            
            'start_date' => 'required',            
            'end_date' => 'required',
            'address' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()]);
        }
        DB::beginTransaction();

        try {
            
            $request['user_id'] = Auth::id();
            $offer = DealOffer::updateOrCreate(['id' => $request->id], $request->all());
            $dealOfferId = isset($request->id) ? $request->id : $offer->id;
            if (isset($request->address) && count($request->address) > 0) {
                DealOfferLocation::where('deal_offer_id', $dealOfferId)->delete();
                foreach ($request->address as $address) {
                    DealOfferLocation::create([
                        'deal_offer_id' => $dealOfferId,
                        'location' => $address['location'],
                        'latitude' => $address['latitude'],
                        'longitude' => $address['longitude'],
                    ]);
                }
            }      
            DB::commit();
            return response()->json(['status' => true, 'message' => 'Deal & Offer has been successfully created!']);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        if (DealOffer::where('id', $id)->delete()) {
            return response()->json(['status' => true, 'message' => 'Deal & Offer has been successfully deleted!']);
        }
        return response()->json(['status' => false, 'message' => 'Something went wrong please try again!']);
    }
}