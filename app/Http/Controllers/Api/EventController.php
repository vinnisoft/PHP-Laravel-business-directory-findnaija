<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityFeed;
use App\Models\Event;
use App\Models\EventDiscussion;
use App\Models\EventMember;
use App\Models\EventCategory;
use App\Models\Business;
use App\Models\User;
use App\Models\GoingToEvent;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB, Validator, Auth;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $businessIdsOfBlockedUsers = Business::whereIn('user_id', blockedUserIds())->pluck('id')->toArray();
        $events = Event::filter($request->all())->orderBy('id', 'DESC')->whereNotIn('business_id', $businessIdsOfBlockedUsers)->paginate(20);
        return response()->json(setResponse($events));
    }
    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'business_id' => 'required|exists:businesses,id',
            'title' => 'required',
            'image' => 'required',
            'address' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'scope' => 'required',
            'description' => 'required',
            'category_id' => 'required|exists:event_categories,id',
            'start_date' => 'required',
            'end_date' => 'required',
            'start_time' => 'required',
            'end_time' => 'required'            
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()]);
        }
        // $request['start_time'] = Carbon::parse($request->start_time);
        // $request['end_time'] = Carbon::parse($request->end_time);
        if (Event::updateOrCreate(['id' => $request->id], $request->all())) {
            return response()->json(['status' => true, 'message' => 'Event has been successfully created!']);
        }
        return response()->json(['status' => false, 'message' => 'Something went wrong please try again!']);
    }

    public function show(Request $request, $id)
    {
        $request['id'] = $id;
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:events,id',            
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()]);
        }
        $event = Event::where('id', $id)->with(['eventMember'])->first();
        $event->host = [
            'id' => $event->user_id,
            'name' => getUserNameById($event->user_id),
            'image' => getUserImageById($event->user_id),
        ];
        $event->discussion = EventDiscussion::where('event_id', $id)->get();
        return response()->json(['status' => true, 'message' => '', 'data' => $event]);
    }

    public function delete(Request $request, $id)
    {
        $request['id'] = $id;
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:events,id',            
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()]);
        }
        if (Event::where('id', $id)->delete()) {
            return response()->json(['status' => true, 'message' => 'Event has been successfully deleted!']);
        }
        return response()->json(['status' => false, 'message' => 'Something went wrong please try again!']);
    }
    
    public function interested(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'event_id' => 'required|exists:events,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()]);
        }
        $request['user_id'] = Auth::id();
        if (EventMember::where($request->all())->exists()) {
            EventMember::where($request->all())->delete();
            return response()->json(['status' => true, 'message' => 'You have removed from event members list!']);
        } else {
            EventMember::create($request->all());
            return response()->json(['status' => true, 'message' => 'You have added in event members list!']);
        }
    }

    public function goingToEvent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'event_id' => 'required|exists:events,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()]);
        }
        $request['user_id'] = Auth::id();
        if (GoingToEvent::where($request->all())->exists()) {
            GoingToEvent::where($request->all())->delete();
            return response()->json(['status' => true, 'message' => 'Removed!']);
        } else {
            GoingToEvent::create($request->all());
            $event = Event::where('id', $request->event_id)->select('title', 'address')->first();
            ActivityFeed::create([
                'user_id' => Auth::id(),
                'type' => 'attended_event',
                'data' => json_encode([
                    'message' => '<p>You attended the <span>'.$event->title.'</span> event in '.$event->address.'</p>',
                    'icon' => 'ic_event.svg',
                    'id' => (int)$request->event_id
                ])
            ]);
            return response()->json(['status' => true, 'message' => 'Done!']);
        }
    }

    public function eventCategories()
    {
        $eventCategories = EventCategory::orderBy('id', 'DESC')->get(['id', 'name']);
        return response()->json([
            'status' => count($eventCategories) > 0 ? true : false,
            'message' => count($eventCategories) > 0 ? '' : 'NO category found',
            'data' => $eventCategories
        ]);
    }

    public function discussion(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'event_id' => 'required|exists:events,id',
            'message' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()]);
        }
        $request['user_id'] = Auth::id();
        if (EventDiscussion::create($request->all())) {
            return response()->json(['status' => true, 'message' => 'message has been sent!']);
        }
        return response()->json(['status' => false, 'message' => 'Something went wrong please try again!']);
    }
}