<?php 

use App\Models\ActivityFeed;
use App\Models\Business;
use App\Models\Plan;
use App\Models\UserNotification;
use App\Models\AppNotification;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Continent;
use App\Models\Block;
use App\Models\TemporaryFile;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;
// use Validator;

function setResponse($data)
{
    return [
        'status' => true,
        'message' => count($data) > 0 ? '' : 'No data found!',
        'data' => count($data) > 0 ? $data->items() : [],
        'current_page' => count($data) > 0 ? $data->currentPage() : '',
        'first_page_url' => count($data) > 0 ? $data->url(1) : '',
        'from' => count($data) > 0 ? $data->firstItem() : '',
        'last_page' => count($data) > 0 ? $data->lastPage() : '',
        'last_page_url' => count($data) > 0 ? $data->url($data->lastPage()) : '',
        'links' => count($data) > 0 ? $data->links() : '',
        'next_page_url' => count($data) > 0 ? $data->nextPageUrl() : '',
        'path' => count($data) > 0 ? $data->url($data->currentPage()) : '',
        'per_page' => count($data) > 0 ? $data->perPage() : '',
        'prev_page_url' => count($data) > 0 ? $data->previousPageUrl() : '',
        'to' => count($data) > 0 ? $data->lastItem() : '',
        'total' => count($data) > 0 ? $data->total() : '',
    ];
}

function getUserNameById($id)
{
    return User::select(DB::raw("CONCAT(first_name, ' ', last_name) as name"))->where('id', $id)->pluck('name')->first();
}

function getUserImageById($id)
{
    $userImage = User::where('id', $id)->pluck('profile_image')->first();
    return !empty($userImage) ? $userImage : 'https://dummyimage.com/100x100/434141/fff&text=';
}

function getBusinessNameById($id)
{
    return Business::where('id', $id)->pluck('name')->first();
}

function uploadFile($file, $path)
{
    $fileName = $file->store($path);
    return explode('public/', $fileName)[1];
}

function upload($request)
{
    $validator = \Validator::make($request->all(), [
        'type' => 'required',
        'file' => 'required',
    ]);
    if ($validator->fails()) {
        return response()->json(['status' => false, 'message' => $validator->messages()->first()]);
    }
    $path = \Storage::disk('public')->put($request->type, $request->file('file'));
    // TemporaryFile::create(['file' => $path]);
    return response()->json(['status' => true, 'message' => 'File uploaded successfully', 'path' => $path]);
}

function deleteFile($file)
{
    if (TemporaryFile::where('file', $file)->exists()) {
        Storage::delete($file);
        TemporaryFile::where('file', $file)->delete();
    }
}

function getAllRouteNames()
{
    $routes = Route::getRoutes();
    $routeNames = [];

    foreach ($routes as $route) {
        $name = $route->action['as'] ?? null;
        if ($name && !in_array($name, [
            'sanctum.csrf-cookie', 'livewire.update', 'livewire.upload-file', 'livewire.preview-file', 'ignition.healthCheck', 'ignition.executeSolution', 'ignition.updateConfig', 'login', 'logout', 'register', 'password.request', 'password.email', 'password.reset', 'password.update', 'password.confirm'
        ])) {
            $routeNames[] = $name;
        }
    }
    return array_unique($routeNames);
}

function userNotification($userId, $notification)
{
    $appNotification = AppNotification::where('name', $notification)->get(['id', 'type']);
    $type = ['database'];
    foreach ($appNotification as $notification) {
        $userNotification = UserNotification::where('user_id', $userId)->where('notification_id', $notification->id)->exists();
        if ($userNotification) {
            $type[] = $notification->type;
        }
    }
    return $type;
}

function enableUserNotification($userId)
{
    foreach (AppNotification::where('type', 'fcm')->pluck('id') as $id) {
        UserNotification::create([
            'user_id' => $userId,
            'notification_id' => $id,
        ]);
    }
    return true;
}

function blockedUserIds()
{
    return Block::where('user_id', Auth::id())->pluck('blocked_user_id')->toArray();
}

function businessVideoType()
{
    return [
        '' => 'Select Type',
        'youtube' => 'YouTube',
        'upload' => 'Upload',
    ];
}

function businessType()
{
    return [
        '' => 'Select Type',
        'physical' => 'Physical',
        'individual' => 'Individual',
        'online' => 'Online',
    ];
}

function businessStatusOn()
{
    return [
        '' => 'Select Status',
        'verified' => 'Verified',
        'unverified' => 'Unverified',
        'verified_but_not_claimed' => 'Verified but not claimed',
    ];
}

function getPlanTypeById($id)
{
    return Plan::where('id', $id)->pluck('type')->first();
}

function planRestriction()
{
    $userPlan = UserSubscription::where(['user_id' => Auth::id(), 'status' => 'current'])->first();
    switch ($userPlan->plan->type) {
        case 'basic':
            $validation = [
                'buss_profile' => 'required|array|max:4',
                'video' => 'nullable|array|max:0',
                'social' => 'array|max:0',
                'payment' => 'array|max:0',
            ];
            $message = [
                'video.max' => 'Videos are not allowed with the basic plan',
                'social.max' => 'Social media are not allowed with the basic plan.',
                'payment.max' => 'Payment platform are not allowed with the basic plan.'
            ];
        break;
        case 'monthly':
            $validation = [
                'buss_profile' => 'required|array|max:16',
            ];
            $message = [];
        break;
        case 'yearly':
            $validation = [
                'buss_profile' => 'required|array|max:16',
            ];
            $message = [];
        break;
        default:
            $validation = [];
            $message = [];
        break;
    }
    return [
        'validation' => $validation,
        'message' => $message,
    ];
}

function getDistance($lat, $lon)
{
    return DB::raw("*, (6371 * acos(
        cos(radians($lat)) *
        cos(radians(latitude)) *
        cos(radians(longitude) - radians($lon)) +
        sin(radians($lat)) *
        sin(radians(latitude))
    )) AS distance");
}

function times()
{
    return [
        '' => '--:--',
        '01:00' => '01:00',
        '02:00' => '02:00',
        '03:00' => '03:00',
        '04:00' => '04:00',
        '05:00' => '05:00',
        '06:00' => '06:00',
        '07:00' => '07:00',
        '08:00' => '08:00',
        '09:00' => '09:00',
        '10:00' => '10:00',
        '11:00' => '11:00',
        '12:00' => '12:00'
    ];
}