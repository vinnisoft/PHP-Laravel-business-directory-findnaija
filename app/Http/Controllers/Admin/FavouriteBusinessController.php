<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\FavouriteBusinessPhotos;
use Illuminate\Http\Request;
use App\DataTables\FavouriteBusinessDataTable;
use App\Models\User;
use App\Models\Business;
use App\Models\FavouriteBusiness;
use DB;

class FavouriteBusinessController extends Controller
{  
    public function index(FavouriteBusinessDataTable $dataTable)
    {       
        return $dataTable->render('admin.favorite-business.index');
    }
    
    public function create()
    {
        $favoriteBusinessIds = FavouriteBusiness::pluck('business_id')->toArray();
        $businesses = Business::where('status', '!=', 'rejected')->whereNotIn('id', $favoriteBusinessIds)->orderBy('id', 'DESC')->pluck('name', 'id')->prepend('Select Business', '')->toArray();
        return view('admin.favorite-business.create', compact('businesses'));
    }
    
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {

            $favouriteBusiness = FavouriteBusiness::create($request->all());
            foreach ($request->images as $image) {
                $favouriteBusinessPhoto['favorite_business_id'] = $favouriteBusiness->id;
                $favouriteBusinessPhoto['photo'] = uploadFile($image, 'public/favorite-business');
                FavouriteBusinessPhotos::create($favouriteBusinessPhoto);
            }

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Business has been successfully created!']);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function edit($id)
    {
        $businesses = Business::where('status', '1')->orderBy('id', 'DESC')->pluck('name', 'id')->prepend('Select Business', '')->toArray();
        $favouriteBusiness = FavouriteBusiness::findOrFail($id);      
        return view('admin.favorite-business.edit', compact('businesses', 'favouriteBusiness'));
    }
    
    public function update(Request $request, $id)
    {;
        DB::beginTransaction();

        try {

            FavouriteBusiness::where('id', $id)->update($request->except('_token', '_method', 'images'));
            if (isset($request->images) && count($request->images) > 0) {
                FavouriteBusinessPhotos::where('favorite_business_id', $id)->delete();
                foreach ($request->images as $image) {
                    $favouriteBusinessPhoto['favorite_business_id'] = $id;
                    $favouriteBusinessPhoto['photo'] = uploadFile($image, 'public/favorite-business');
                    FavouriteBusinessPhotos::create($favouriteBusinessPhoto);
                }
            }            

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Business has been successfully created!']);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function destroy($id)
    {
        if (FavouriteBusiness::where('id', $id)->delete()) {
            return response()->json(['status' => true, 'message' => 'Interest has been successfully deleted!']);
        }
        return response()->json(['status' => false, 'message' => 'Sonething went wrong please try again!']);
    }
}
