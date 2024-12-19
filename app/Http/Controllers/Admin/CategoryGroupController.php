<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DataTables\CategoryGroupDataTable;
use App\Models\User;
use App\Models\CategoryGroup;
use Validator, DB;

class CategoryGroupController extends Controller
{  
    public function index(CategoryGroupDataTable $dataTable)
    {       
        return $dataTable->render('admin.category-group.index');
    }
    
    public function create()
    {
        return view('admin.category-group.create');
    }
    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',           
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()]);
        }

        DB::beginTransaction();
        try {

        
            CategoryGroup::create($request->all());
            DB::commit();
            return response()->json(['status' => true, 'message' => 'Category Group has been successfully created!']);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }    
    
    public function edit($id)
    {
        $categoryGroup = CategoryGroup::where('id', $id)->first();
        return view('admin.category-group.edit', compact('categoryGroup'));
    }
    
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',           
        ]);
        
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()]);
        }
        DB::beginTransaction();
        try {

            CategoryGroup::where('id', $id)->update($request->except('_token', '_method'));
            DB::commit();
            return response()->json(['status' => true, 'message' => 'Category Group has been successfully updated!']);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function destroy($id)
    {
        if (CategoryGroup::where('id', $id)->delete()) {
            return response()->json(['status' => true, 'message' => 'Category Group has been successfully deleted!']);
        }
        return response()->json(['status' => false, 'message' => 'Sonething went wrong please try again!']);
    }
}
