<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DataTables\EventCategoryDataTable;
use App\Models\User;
use App\Models\EventCategory;

class EventCategoryController extends Controller
{  
    public function index(EventCategoryDataTable $dataTable)
    {       
        return $dataTable->render('admin.event-category.index');
    }
    
    public function create()
    {
        return view('admin.event-category.create');
    }
    
    public function store(Request $request)
    {       
        if (EventCategory::create($request->all())) {
            return response()->json(['status' => true, 'message' => 'Event category has been successfully created!']);
        }
        return response()->json(['status' => false, 'message' => 'Sonething went wrong please try again!']);
    }
    
    public function edit($id)
    {
        $eventcategory = EventCategory::where('id', $id)->first();
        return view('admin.event-category.edit', compact('eventcategory'));
    }
    
    public function update(Request $request, $id)
    {
        if (EventCategory::where('id', $id)->update($request->except('_token', '_method'))) {
            return response()->json(['status' => true, 'message' => 'Event category has been successfully updated!']);
        }
        return response()->json(['status' => false, 'message' => 'Sonething went wrong please try again!']);
    }
    
    public function destroy($id)
    {
        if (EventCategory::where('id', $id)->delete()) {
            return response()->json(['status' => true, 'message' => 'Event category has been successfully deleted!']);
        }
        return response()->json(['status' => false, 'message' => 'Sonething went wrong please try again!']);
    }
}
