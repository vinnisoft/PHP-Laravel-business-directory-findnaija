<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Collection;
use App\Models\UserCollection;
use DB, Validator, Auth;

class CollectionController extends Controller
{
    public function index(Request $request)
    {
        $collection = Collection::query();
        if (isset($request->latitude) && isset($request->longitude)) {
            $userIds = User::locatedWithinRadius($request->latitude, $request->longitude, 50)->pluck('id');
            $collection->whereIn('user_id', $userIds)->whereNotIn('user_id', blockedUserIds())->where('featured', '1');
        }
        if (isset($request->search) && $request->search == 'my') {
            $collection->where('user_id', Auth::id());
        }
        $collection = $collection->select('id', 'user_id', 'name', 'picture', 'description', 'scope', 'featured')->paginate(20);
        return response()->json(setResponse($collection));
    }

    public function show(Request $request)
    {
        $collection = Collection::where('id', $request->id)->with(['userCollection'])->get(['id', 'user_id', 'name', 'picture', 'description']);
        return response()->json(['status' => true, 'message' => '', 'data' => $collection]);
    }
    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
            'business_id' => 'exists:businesses,id',
            'featured' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()]);
        }
        DB::beginTransaction();
        try {

            $request['user_id'] = Auth::id();
            $collection = Collection::updateOrCreate(['id' => $request->id], $request->all());
            if (isset($request->business_id)) {
                UserCollection::create(['collection_id' => $collection->id, 'business_id' => $request->business_id]);
            }
            DB::commit();
            $msg = isset($request->id) ? 'updated!' : 'created!';
            return response()->json(['status' => true, 'message' => 'Collection has been successfully '.$msg]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function userCollection(Request $request)
    {
        $validator = Validator::make($request->all(), [            
            'collection_id' => 'required|exists:collections,id',            
            'business_id' => 'required|exists:businesses,id',            
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()]);
        }
        DB::beginTransaction();
        try {

            if (UserCollection::where($request->all())->exists()) {
                UserCollection::where($request->all())->delete();
                $msg = 'Business has been successfully removed from Collection!';
            } else {
                UserCollection::create($request->all());
                $msg = 'Business has been successfully added to Collection!';
            }            
            DB::commit();
            return response()->json(['status' => true, 'message' => $msg]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function featured(Request $request, $id)
    {
        $request['id'] = $id;
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:collections,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()]);
        }
        $collection = Collection::where('id', $id)->first();
        if ($collection->featured == '0') {
            $collection->update(['featured' => '1']);
            return response()->json(['status' => true, 'message' => 'Collection has been successfully set as a featured!']);
        }
        $collection->update(['featured' => '0']);
        return response()->json(['status' => true, 'message' => 'Collection has been successfully removed from featured!']);
    }

    public function delete($id)
    {
        if (Collection::where('id', $id)->delete()) {
            return response()->json(['status' => true, 'message' => 'Collection has been successfully deleted!']);
        }
        return response()->json(['status' => false, 'message' => 'Something went wrong please try again!']);
    }
}
