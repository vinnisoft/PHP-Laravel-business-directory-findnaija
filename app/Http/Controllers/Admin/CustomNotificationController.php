<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use Illuminate\Http\Request;
use App\DataTables\CustomNotificationDataTable;
use App\Notifications\CustomNotification;
use App\Models\User;
use App\Models\CustomNotification as CreateCustomNotification;
use Validator, DB;

class CustomNotificationController extends Controller
{  
    public function index(CustomNotificationDataTable $dataTable)
    {       
        return $dataTable->render('admin.custom-notification.index');
    }
    
    public function create()
    {
        return view('admin.custom-notification.create');
    }
    
    public function send(Request $request)
    {     
        DB::beginTransaction();
        try {
            
            foreach (User::whereNot('email', 'like', '%admin%')->get() as $user) {
                $user->notify(new CustomNotification($user->id, $request->subject, $request->message, $request->type));
            }           
            $request['type'] = json_encode($request->type);            
            CreateCustomNotification::create($request->all());

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Notification has been successfully sent!']);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function recend(Request $request, $id)
    {
        DB::beginTransaction();
        try {

            $notification = CreateCustomNotification::where('id', $id)->first();
            foreach (User::whereNot('email', 'like', '%admin%')->get() as $user) {
                $user->notify(new CustomNotification($user->id, $notification->subject, $notification->message, json_decode($notification->type)));
            }            

            DB::commit();
            return redirect()->back()->with(['status' => true, 'message' => 'Notifications has been successfully sent!']);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function edit($id)
    {
        $notification = CreateCustomNotification::where('id', $id)->first();
        return view('admin.custom-notification.edit', compact('notification'));
    }
    
    public function update(Request $request, $id)
    {
        $request['type'] = json_encode($request->type);
        if (CreateCustomNotification::where('id', $id)->update($request->except('_token', '_method'))) {
            return response()->json(['status' => true, 'message' => 'Notification has been successfully updated!']);
        }
        return response()->json(['status' => false, 'message' => 'Sonething went wrong please try again!']);
    }

    public function destroy($id)
    {
        if (CreateCustomNotification::where('id', $id)->delete()) {
            return response()->json(['status' => true, 'message' => 'Notification has been successfully deleted!']);
        }
        return response()->json(['status' => false, 'message' => 'Sonething went wrong please try again!']);
    }
}
