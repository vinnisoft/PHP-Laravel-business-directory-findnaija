<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\DataTables\PlanDataTable;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Models\Plan;
use DB, Validator;

class PlanController extends Controller
{  
    public function index(PlanDataTable $subAdminDataTable)
    {
        return $subAdminDataTable->render('admin.plan.index');
    }
    
    public function create()
    {
        return view('admin.plan.create');
    }
    
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {

            $request['type'] = strtolower(str_replace(' ', '_', $request->type));
            Plan::create($request->all());
            DB::commit();
            return response()->json(['status' => true, 'message' => 'Plan has been successfully created!']);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function edit($id)
    {
        $plan = Plan::findOrFail($id);
        return view('admin.plan.edit', compact('plan'));
    }
    
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'confirm_password' => 'same:password',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()]);
        }

        DB::beginTransaction();
        try {

            $request['type'] = strtolower(str_replace(' ', '_', $request->type));
            Plan::where('id', $id)->update($request->except('_token', '_method'));
            DB::commit();
            return response()->json(['status' => true, 'message' => 'Plan has been successfully updated!']);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function destroy($id)
    {
        if (Plan::where('id', $id)->delete()) {
            return response()->json(['status' => true, 'message' => 'Plan has been deleted!']);
        } else {
            return response()->json(['status' => false, 'message' => 'Something went wrong please try again!']);
        }
    }
}
