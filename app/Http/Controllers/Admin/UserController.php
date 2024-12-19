<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Notifications\AccountDetailNotification;
use App\DataTables\UsersDataTable;
use App\DataTables\SupportDataTable;
use App\Models\User;
use DB, Validator;

class UserController extends Controller
{  
    public function index(UsersDataTable $userDataTable)
    {
        return $userDataTable->render('admin.user.index');
    }
    
    public function create()
    {
        return view('admin.user.create');
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
            $request['username'] = explode("@", $request->email)[0];
            $request['email_verified_at'] = now();
            $user = User::create($request->all());
            $user->notify(new AccountDetailNotification($user, $request->confirm_password));
            DB::commit();
            return response()->json(['status' => true, 'message' => 'User has been successfully created!']);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.user.edit', compact('user'));
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
            User::where('id', $id)->update($request->except('_token', 'confirm_password'));

            DB::commit();
            return response()->json(['status' => true, 'message' => 'User has been successfully updated!']);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function destroy($id)
    {
        if (User::where('id', $id)->delete()) {
            return response()->json(['status' => true, 'message' => 'User has been deleted!']);
        } else {
            return response()->json(['status' => false, 'message' => 'Something went wrong please try again!']);
        }
    }

    public function support(SupportDataTable $supportDataTable)
    {
        return $supportDataTable->render('admin.support.index');
    }
}
