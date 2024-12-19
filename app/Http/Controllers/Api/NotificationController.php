<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserNotification;
use DB, Validator, Auth;

class NotificationController extends Controller
{
    public function index($type)
    {
        $notifications = AppNotification::where('type', $type)->get(['id', 'type', 'name']);
        return response()->json(['status' => true, 'message' => '', 'data' => $notifications]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:app_notifications,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()]);
        }
        $condition = ['user_id' => Auth::id(), 'notification_id' => $request->id];
        $notification = UserNotification::where($condition);
        if ($notification->exists()) {
            $notification->delete();
            $msg = 'Notification has been disbaled!';
        } else {
            UserNotification::create($condition);
            $msg = 'Notification has been enabled!';
        }
        return response()->json(['status' => true, 'message' => $msg]);
    }
}
