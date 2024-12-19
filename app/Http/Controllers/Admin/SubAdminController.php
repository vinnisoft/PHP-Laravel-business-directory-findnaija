<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\DataTables\SubAdminDataTable;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use DB, Validator;

class SubAdminController extends Controller
{  
    public function index(SubAdminDataTable $subAdminDataTable)
    {
        return $subAdminDataTable->render('admin.admins.index');
    }
    
    public function create()
    {
        $permissions = Permission::get();
        return view('admin.admins.create', compact('permissions'));
    }
    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()]);
        }

        DB::beginTransaction();
        try {

            $request['password'] = Hash::make($request->confirm_password);
            $user = User::create($request->all());
            $user->assignRole('subAdmin');
            if (count($request->permission) > 0) {
                $user->givePermissionTo($request->permission);
            }
            DB::commit();
            return response()->json(['status' => true, 'message' => 'Sub Admin has been successfully created!']);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $permissions = Permission::get();
        return view('admin.admins.edit', compact('user', 'permissions'));
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

            if (isset($request->confirm_password)) {
                $request['password'] = Hash::make($request->confirm_password);
            } else {
                unset($request['password']);
            }
            User::where('id', $id)->update($request->except('_token', 'confirm_password', 'permission'));
            $user = User::findOrFail($id);
            if (isset($request->permission) && count($request->permission) > 0) {
                $user->syncPermissions($request->permission);
            } else {
                $user->revokePermissionTo(getAllRouteNames());
            }
            DB::commit();
            return response()->json(['status' => true, 'message' => 'Sub Admin has been successfully updated!']);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function destroy($id)
    {
        if (User::where('id', $id)->delete()) {
            DB::table('model_has_permissions')->where('model_id', $id)->delete();
            return response()->json(['status' => true, 'message' => 'User has been deleted!']);
        } else {
            return response()->json(['status' => false, 'message' => 'Something went wrong please try again!']);
        }
    }
}
