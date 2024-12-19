<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Business;
use App\Models\BusinessConversationMember;
use App\Models\BusinessConversation;
use App\Models\Chat;
use App\Models\ChatRequest;
use App\Models\Story;
use App\Models\BusinessImage;
use App\Models\Block;
use App\Models\BlockRange;
use App\Notifications\SendMessage;
use App\Notifications\SendMessageInGroup;
use App\Jobs\SendMessageNotificationJob;
use Illuminate\Support\Facades\Http;
use DB, Validator, Auth;

class ChatController extends Controller
{
    public function index(Request $request, $id)
    {
        $business = Business::select('id', 'name')->where('id', $id)->with(['conversation'])->first();
        $business->business_community = @$business->businessCommunity();
        $business->makeHidden(['category_name', 'rating_avg', 'rating_count', 'conversationMembers', 'collection_status']);
        return response()->json(['status' => true, 'message' => '', 'data' => $business]);
    }

    public function groupMembers(Request $request, $id)
    {
        $members = BusinessConversationMember::where('business_id', $id)->where('user_id', '!=', Auth::id())->pluck('user_id');
        $data = [];
        foreach ($members as $memberId) {
            $chatRequest = ChatRequest::where(function($query) use ($memberId) {
                    $query->where(['requested_by' => Auth::id(), 'requested_to' => $memberId]);
                })->orWhere(function($query) use ($memberId) {
                    $query->where(['requested_by' => $memberId, 'requested_to' => Auth::id()]);
                })->select('id', 'status', 'requested_by')->first();

            $data[] = [
                'id' => $memberId,
                'name' => getUserNameById($memberId),
                'image' => getUserImageById($memberId),
                'room_id' => @$chatRequest->id,
                'acceptation_status' => @$chatRequest->status,
                'requested_by' => @$chatRequest->requested_by,
                'created_at' => User::where('id', $memberId)->pluck('created_at')->first(),
                'block' => @Block::where(['user_id' => Auth::id(), 'blocked_user_id' => $memberId])->exists()
            ];
        }
        return response()->json(['status' => true, 'message' => '', 'data' => $data]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'business_id' => 'required|exists:businesses,id',
            'message' => 'required',
            'message_type' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()]);
        }

        DB::beginTransaction();

        try {

            $request['user_id'] = Auth::id();
            BusinessConversation::create($request->all());
            $businessMembers = BusinessConversationMember::where('business_id', $request->business_id);
            $businessMembers->where('online', '0')->increment('unread_messages');
            $requestedToIds = $businessMembers->where('user_id', '!=', Auth::id())->pluck('user_id')->toArray();
            Http::post('http://144.91.80.25:1017/message', [
                'roomId' => $request->business_id,
                'userId' => Auth::id(),
                'recieverIds' => $requestedToIds
            ]);
            $users = User::whereIn('id', $requestedToIds)->get();
            foreach ($users as $user) {
                $user->notify(new SendMessageInGroup(Auth::id(), $user->id, $request->business_id, $request->message));
            }
            DB::commit();
            return response()->json(['status' => true, 'message' => 'Message has been successfully sent!']);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function chatUsers(Request $request)
    {
        $sendBy = ChatRequest::where('requested_to', Auth::id())->where('status', '1')->whereNull('delete_requested_to')->select('id as room_id', 'requested_by as user_id', 'status')->addSelect(DB::raw("'one_to_one' as type"))->get();
        $sendTo = ChatRequest::where('requested_by', Auth::id())->whereNull('delete_requested_by')->select('id as room_id', 'requested_to as user_id', 'status')->addSelect(DB::raw("'one_to_one' as type"))->get();
        $businessIds = BusinessConversationMember::where('user_id', Auth::id())->select('id', 'business_id', 'unread_messages')->addSelect(DB::raw("'group' as type"))->get();
        $mergedData = $sendBy->concat($sendTo)->concat($businessIds);
        $resultData = [];
        foreach ($mergedData as $item) {
            if ($item->type == 'one_to_one') {
                $block = Block::where(['user_id' => Auth::id(), 'blocked_user_id' => $item->user_id]);
                $chat = Chat::where('room_id', $item->room_id)->orderBy('id', 'DESC')->first();
                $name = getUserNameById($item->user_id);
                $image = getUserImageById($item->user_id);
                $status = @$item->status;
                $stories = Story::where('user_id', $item->user_id)->whereNot('user_id', $block->pluck('blocked_user_id')->first())->where('status', '1')->pluck('file')->toArray();
                $unreadMessages = Chat::where(['room_id' => $item->room_id, 'unread_messages' => '0'])->where('user_id', '!=', Auth::id())->count();
            } else {
                $chat = BusinessConversation::where('business_id', $item->business_id)->orderBy('id', 'DESC')->first();
                $name = getBusinessNameById($item->business_id);
                $image = BusinessImage::where('business_id', $item->business_id)->pluck('image')->first();
                $stories = null;
                $unreadMessages = $item->unread_messages;
            }
            $resultData[] = [
                'id' =>  $item->type == 'one_to_one' ? $item->user_id : $item->business_id,
                'type' => $item->type,
                'room_id' => $item->type == 'one_to_one' ? $item->room_id : $item->business_id,
                'name' => $name,
                'image' => $image,
                'acceptation_status' => @$status,
                'message_type' => $chat ? $chat->message_type : null,
                'last_msg' => $chat ? $chat->message : null,
                'last_msg_time' => $chat ? $chat->created_at : null,
                'unread_messages' => @$unreadMessages,
                'stories' => $stories,
                'requested_by' => null,
                'activity' => ($chat && $chat->activity) ? $chat->activity : NULL,
                'block' => $item->type == 'one_to_one' ? @$block->exists() : false
            ];
        }
        usort($resultData, function ($a, $b) {
            return $b['last_msg_time'] <=> $a['last_msg_time'];
        });
        return response()->json([
            'status' => true,
            'message' => count($resultData) > 0 ? '' : 'No user found!',
            'data' => $resultData
        ]);
    }

    public function chatRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'room_id' => 'required|exists:chat_requests,id',
            'status' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()]);
        }
        DB::beginTransaction();
        try {

            $chatRequest = ChatRequest::where('id', $request->room_id);
            if ($request->status == '1') {
                $chatRequest->update(['status' => $request->status]);
                $msg = 'accepted';
            } else {
                $chatRequest->delete();
                $msg = 'rejected';
            }
            DB::commit();
            return response()->json(['status' => true, 'message' => 'Chat request has been '.$msg.'!']);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function chat(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'requested_to' => [
                'exists:users,id',
                function ($attribute, $value, $fail) {
                    if (Block::where(['user_id' => Auth::id(), 'blocked_user_id' => $value])->exists()) {
                        $fail('This user is blocked.');
                    }
                },
            ],
            'message' => 'required',
            'message_type' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()]);
        }

        DB::beginTransaction();
        try {
            $userId = $request->requested_to;
            if (!ChatRequest::where(function($query) use ($userId) {
                $query->where(['requested_by' => Auth::id(), 'requested_to' => $userId]);
            })->orWhere(function($query) use ($userId) {
                $query->where(['requested_by' => $userId, 'requested_to' => Auth::id()]);
            })->exists()) {
                $chatRequest = ChatRequest::create(['requested_to' => $request->requested_to, 'requested_by' => Auth::id(), 'status' => '0']);
                $request['room_id'] = $chatRequest->id;
            }
            $request['user_id'] = Auth::id();
            $requestedToIds[] = $request->requested_to;
            Chat::create($request->all());
            $chatRequest = ChatRequest::findOrFail($request->room_id);
            if (Auth::id() == $chatRequest->requested_by) {
                $chatRequest->update(['delete_requested_by' => NULL]);
            } else {
                $chatRequest->update(['delete_requested_to' => NULL]);
            }
            Http::post('http://144.91.80.25:1017/message', [
                'roomId' => $request->room_id,
                'userId' => Auth::id(),
                'recieverId' => $request->requested_to,
                'recieverIds' => $requestedToIds
            ]);
            Chat::where('room_id', $request->room_id)->where('user_id', '!=', Auth::id())->update(['unread_messages' => '1']);
            $user = User::findOrFail($request->requested_to);
            $user->notify(new SendMessage(Auth::id(), $request->requested_to, $request->message, $request->room_id));
            DB::commit();
            return response()->json(['status' => true, 'message' => 'Message has been successfully sent!', 'room_id' => (int)$request->room_id]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function chatMessages(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'room_id' => 'required|exists:chat_requests,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()]);
        }

        $chatRoom = ChatRequest::findOrFail($request->room_id);
        $deletedChatDate = Auth::id() == $chatRoom->requested_by ? $chatRoom->deleted_msg_date_by : $chatRoom->deleted_msg_date_to;

        Chat::where('room_id', $request->room_id)->where('user_id', '!=', Auth::id())->update(['unread_messages' => '1']);
        $messages = Chat::where('room_id', $request->room_id)->blockMsg($request->room_id);
        if (!empty($deletedChatDate)) {
            $messages->where('created_at', '>', $deletedChatDate);
        }
        $messages = $messages->get(['id', 'user_id', 'message', 'message_type', 'created_at']);
        return response()->json([
            'status' => true,
            'message' =>  count($messages) > 0 ? '' : 'No Message found!',
            'data' => $messages
        ]);
    }

    public function chatRequestUsers(Request $request)
    {
        $userIds = ChatRequest::where('requested_to', Auth::id())->where('status', '0')->pluck('requested_by', 'id')->toArray();
        $data = [];
        foreach ($userIds as $key => $userId) {
            $chat = Chat::where('room_id', $key)->orderBy('id', 'DESC')->first();
            $data[] = [
                'id' => $userId,
                'room_id' => $key,
                'name' => getUserNameById($userId),
                'image' => getUserImageById($userId),
                'message_type' => $chat ? $chat->message_type : null,
                'type' => 'one_to_one',
                'last_msg' => $chat->message,
                'last_msg_time' => $chat->created_at,
                'block' => Block::where(['user_id' => Auth::id(), 'blocked_user_id' => $userId])->exists()

            ];
        }
        return response()->json([
            'status' => true,
            'message' => count($data) > 0 ? '' : 'No request found!',
            'data' => array_reverse($data)
        ]);
    }

    public function activeInChat(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'room_id' => 'required',
            'status' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()]);
        }
        DB::beginTransaction();
        try {

            $businessMember = BusinessConversationMember::where(['business_id' => $request->room_id, 'user_id' => Auth::id()]);
            if ($businessMember->update(['online' => $request->status])) {
                if ($request->status == 1) {
                    $businessMember->update(['unread_messages' => '0']);
                }
            }
            DB::commit();
            return response()->json(['status' => true, 'message' => 'Status updated!']);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function deleteChat(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'type' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()]);
        }
        DB::beginTransaction();
        try {

            switch ($request->type) {
                case 'group':
                    BusinessConversationMember::where(['user_id' => Auth::id(), 'business_id' => $request->id])->delete();
                break;
                case 'one_to_one':
                    $chatRequest = ChatRequest::findOrFail($request->id);
                    if (Auth::id() == $chatRequest->requested_by) {
                        $chatRequest->update(['delete_requested_by' => now(), 'deleted_msg_date_by' => now()]);
                    } else {
                        $chatRequest->update(['delete_requested_to' => now(), 'deleted_msg_date_to' => now()]);
                    }
                break;
            }
            DB::commit();
            return response()->json(['status' => true, 'message' => 'Chat has been deleted!']);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }
}
