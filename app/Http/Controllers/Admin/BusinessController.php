<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessOption;
use App\Models\BusinessRecommendation;
use App\Models\Category;
use App\Models\Language;
use App\Models\SubCategory;
use App\Models\Option;
use Illuminate\Http\Request;
use App\DataTables\BusinessDataTable;
use App\DataTables\NewBusinessDataTable;
use App\DataTables\RecomendedBusinessDataTable;
use App\Models\User;
use App\Models\Business;
use App\Models\BusinessHiring;
use App\Models\BusinessLanguage;
use App\Models\BusinessService;
use App\Models\BusinessImage;
use App\Models\BusinessTime;
use App\Models\BusinessSubCategory;
use App\Models\RejectBusinessReason;
use App\Notifications\BusinessNotification;
use App\Notifications\BusinessRecommendationNotification;
use App\Models\Continent;
use App\Notifications\BusinessStatusNotification;
use Illuminate\Support\Facades\Http;
use DB, Validator, Auth;

class BusinessController extends Controller
{
    public function index(BusinessDataTable $dataTable)
    {
        return $dataTable->render('admin.business.index');
    }

    public function newBusiness(NewBusinessDataTable $dataTable)
    {
        return $dataTable->render('admin.business.new-business');
    }

    public function recomendedBusiness(RecomendedBusinessDataTable $dataTable)
    {
        return $dataTable->render('admin.business.recomended-business');
    }

    public function create()
    {
        $categories = Category::pluck('name', 'id');
        $languages = Language::pluck('name', 'id');
        $users = User::where('email', '!=', 'admin@findnaija.com')
            ->select(DB::raw("CONCAT(first_name, ' ', last_name) as name"), 'id')
            ->pluck('name', 'id');
        $codes = $this->countryCodes();

        if (isset(request()->id)) {
            $recomendedBusiness = BusinessRecommendation::where('id', request()->id)->first();
            return view('admin.business.create', compact('categories', 'languages', 'users', 'codes', 'recomendedBusiness'));
        }
        return view('admin.business.create', compact('categories', 'languages', 'users', 'codes'));
    }

    public function store(Request $request)
    {
        // $validator = Validator::make($request->all(), [
        //     'email' => 'required',
        // ]);

        // if ($validator->fails()) {
        //     return response()->json(['status' => false, 'message' => $validator->messages()->first()]);
        // }
        DB::beginTransaction();

        try {

            $request['hiring_for_buss'] = $request->hiring_for_buss == 'yes' ? '1' : '0';
            if ($request->hasFile('nationald')) {
                $request['national_id'] = uploadFile($request->nationald, 'public/national-id');
            }
            if ($request->hasFile('business_reg')) {
                $request['business_registration'] = uploadFile($request->business_reg, 'public/business-registration');
            }
            if ($request->hasFile('business_logo')) {
                $request['logo'] = uploadFile($request->business_logo, 'public/business-logo');
            }
            $request['status'] = 'approved';
            $business = Business::create($request->except('options', 'price'));
            $business->createServices($request->service);
            $business->createOptions($request->options);
            $business->createTime($request->time);
            $business->createBusinessImages($request->images);
            $business->createHiring($request->hiring);
            $business->createLanguages($request->languages);
            $business->createKeyWords($request->key_words);
            if (isset($request->video)) {
                $video = $request->video;
                if (isset($video['video']) && is_object($video['video'])) {
                    $video['video'] = uploadFile($video['video'], 'public/business-video');
                }
                $business->createVideo($video);
            }
            $business->createSocialAccount($request->social);
            $business->createPaymentOption($request->payment);
            $business->createPriceMenu($request->price);
            $user = User::findOrFail($request->user_id);
            if (isset($request->recomendation)) {
                BusinessRecommendation::where('id', $request->recomendation)->update(['admin_id' => Auth::id(), 'status' => '1']);
                $notification = 'Your business recomendation request ('.$business->business_name.') has been successfully approved for listing!';
                $user->notify(new BusinessRecommendationNotification($user, $notification));
            }
            $user->notify(new BusinessNotification($user, $business->name));
            DB::commit();
            return response()->json(['status' => true, 'message' => 'Business has been successfully created!']);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function show($id)
    {
        $business = Business::findOrFail($id);
        return view('admin.business.show', compact('business'));
    }
    
    public function edit($id)
    {
        $business = Business::findOrFail($id);
        $categories = Category::pluck('name', 'id');
        $languages = Language::pluck('name', 'id');
        $bussLang = BusinessLanguage::where('business_id', $id)->pluck('language_id', 'id');
        $businessHirings = BusinessHiring::where('business_id', $id)->get();
        $businessImages = BusinessImage::where('business_id', $id)->get();
        // $businessService = BusinessService::where('business_id', $id)->pluck('name')->toArray();        
        $allService = SubCategory::select('name', 'id')->get();        
        $businessService = BusinessSubCategory::where('business_id', $id)->pluck('sub_category_id')->toArray();
        $allOptions = Option::where('category_id', $business->category)->get(['name', 'id']);
        $businessOptions = BusinessOption::where('business_id', $id)->pluck('option_id')->toArray();
        $businessTimes = BusinessTime::select('id', 'day', 'start_time', 'end_time', 'status')
            ->where('business_id', $id)->get()->pluck(null, 'day')->toArray();
        $users = User::where('email', '!=', 'admin@findnaija.com')
            ->select(DB::raw("CONCAT(first_name, ' ', last_name) as name"), 'id')
            ->pluck('name', 'id');
        $codes = $codes = $this->countryCodes();
        return view('admin.business.edit', compact('business', 'languages', 'categories', 'bussLang', 'businessTimes', 'businessHirings', 'businessImages', 'users', 'allService', 'businessService', 'allOptions', 'businessOptions', 'codes'));
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            
            $request['hiring_for_buss'] = $request->hiring_for_buss == 'yes' ? '1' : '0';
            if ($request->hasFile('nationald')) {
                $request['national_id'] = uploadFile($request->nationald, 'public/national-id');
            }
            if ($request->hasFile('business_reg')) {
                $request['business_registration'] = uploadFile($request->business_reg, 'public/business-registration');
            }
            if ($request->hasFile('business_logo')) {
                $request['logo'] = uploadFile($request->business_logo, 'public/business-logo');
            }

            $business = Business::findOrFail($id);
            $business->update($request->except(
                '_token', '_method', 'languages', 'images', 'hiring', 'time', 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'service', 'options', 'key_words', 'business_logo', 'price', 'status'
            ));            
            $business->createTime($request->time);
            // $business->businessImages($request->images);
            $business->createHiring($request->hiring);
            $business->createLanguages($request->languages);
            $business->createServices($request->service);
            $business->createOptions($request->options);
            $business->createKeyWords($request->key_words);
            if (isset($request->images) && count($request->images) > 0) {
                foreach ($request->images as $image) {
                    $image = uploadFile($image, 'public/business');
                    $business->images()->create(['image' => $image]);
                }
            }
            if (isset($request->video)) {
                $video = $request->video;
                if (isset($video['video']) && is_object($video['video'])) {
                    $video['video'] = uploadFile($video['video'], 'public/business-video');
                }
                $business->createVideo($video);
            }
            $business->createSocialAccount($request->social);
            $business->createPaymentOption($request->payment);
            $business->createPriceMenu($request->price);
            DB::commit();
            return response()->json(['status' => true, 'message' => 'Business has been successfully created!']);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function getSubCategory(Request $request)
    {
        $subCategories = SubCategory::where('category_id', $request->categoryId)->get(['id', 'name']);
        $catOptions = Option::select('id', 'name')->where('category_id', $request->categoryId)->get();
        $subCatOptions = '';
        $options = '';
        foreach ($subCategories as $subCategory) {           
            $subCatOptions .= '<option value="'.$subCategory->id.'">'.$subCategory->name.'</option>';
        }
        foreach ($catOptions as $catOption) {
            $options .= '<option value="'.$catOption->id.'">'.$catOption->name.'</option>';
        }
        return response()->json([
            'status' => count($subCategories) > 0 ? true : false, 
            'message' => '',
            'subCatOptions' => $subCatOptions,
            'catOptions' => $options
        ]);
    }
    
    public function approve(Request $request)
    {
        $business = Business::where('id', $request->id)->first();
        // if ($business->update(['status' => $request->status])) {
            if ($request->status == 'approved') {
                $business->update(['status' => $request->rejection_reason]);
                $msg = 'Business has been successfully approved!';
            } else {
                RejectBusinessReason::create([
                    'business_id' => $request->id,
                    'user_id' => Auth::id(),
                    'reason' => $request->rejection_reason
                ]);
                $business->update(['status' => $request->status]);
                $msg = 'Business has been rejected!';
            }
            $userId = Business::where('id', $request->id)->pluck('user_id')->first();
            User::findOrFail($userId)->notify(new BusinessStatusNotification($userId, $request->id, $request->status, $request->rejection_reason));
            return response()->json(['status' => true, 'message' => $msg]);
        // }
        // return response()->json(['status' => false, 'message' => 'Sonething went wrong please try again!']);
    }

    public function destroy($id)
    {
        if (Business::where('id', $id)->delete()) {
            return response()->json(['status' => true, 'message' => 'Business has been successfully deleted!']);
        }
        return response()->json(['status' => false, 'message' => 'Sonething went wrong please try again!']);
    }

    public function deleteImage(Request $request)
    {
        if (BusinessImage::where('id', $request->id)->delete()) {
            return response()->json(['status' => true, 'message' => 'Business image has been successfully deleted!']);
        }
        return response()->json(['status' => false, 'message' => 'Sonething went wrong please try again!']);
    }

    public function countryCodes()
    {
        $dialCodes = Http::get('https://countriesnow.space/api/v0.1/countries/codes');
        $codes = $dialCodes->json()['data'];

        $preferredCode = '+234';
        $preferredCodeIndex = array_search($preferredCode, array_column($codes, 'dial_code'));

        if ($preferredCodeIndex !== false) {
            $preferredCodeData = $codes[$preferredCodeIndex];
            unset($codes[$preferredCodeIndex]);
            array_unshift($codes, $preferredCodeData);
        }
        return $codes;
    }

    public function coverImage(Request $request)
    {
        BusinessImage::where('is_cover', 1)->update(['is_cover' => 0]);
        if (BusinessImage::where('id', $request->id)->update(['is_cover' => 1])) {
            return response()->json(['status' => true, 'message' => 'Image has been successfully set as a cover image!']);
        }
        return response()->json(['status' => false, 'message' => 'Sonething went wrong please try again!']);
    }
}
