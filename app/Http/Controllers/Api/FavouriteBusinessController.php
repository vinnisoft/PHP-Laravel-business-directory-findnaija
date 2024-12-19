<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\FavouriteBusinessPhotos;
use Illuminate\Http\Request;
use App\DataTables\FavoriteBusinessDataTable;
use App\Models\User;
use App\Models\Business;
use App\Models\FavouriteBusiness;
use DB, Validator;

class FavouriteBusinessController extends Controller
{  
    public function index(Request $request)
    {
        $favoriteBusiness = FavouriteBusiness::orderBy('id', 'DESC')->paginate(20);
        return response()->json(setResponse($favoriteBusiness));
    } 

    public function show(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:favorite_businesses,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()]);
        }

        $favoriteBusines = FavouriteBusiness::where('id', $request->id)->with(['photos'])->first();
        return response()->json(['status' => true, 'message' => '', 'data' => $favoriteBusines]);
    }    
}
