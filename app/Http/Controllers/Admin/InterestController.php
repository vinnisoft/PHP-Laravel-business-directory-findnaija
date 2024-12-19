<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DataTables\InterestDataTable;
use App\Models\User;
use App\Models\Interest;

class InterestController extends Controller
{  
    public function index(InterestDataTable $dataTable)
    {       
        return $dataTable->render('admin.interest.index');
    }
    
    public function create()
    {
        return view('admin.interest.create');
    }
    
    public function store(Request $request)
    {       
        if (Interest::create($request->all())) {
            return response()->json(['status' => true, 'message' => 'Interest has been successfully created!']);
        }
        return response()->json(['status' => false, 'message' => 'Sonething went wrong please try again!']);
    }
    
    public function edit($id)
    {
        $interest = Interest::where('id', $id)->first();
        return view('admin.interest.edit', compact('interest'));
    }
    
    public function update(Request $request, $id)
    {
        if (Interest::where('id', $id)->update($request->except('_token'))) {
            return response()->json(['status' => true, 'message' => 'Interest has been successfully updated!']);
        }
        return response()->json(['status' => false, 'message' => 'Sonething went wrong please try again!']);
    }
    
    public function destroy($id)
    {
        if (Interest::where('id', $id)->delete()) {
            return response()->json(['status' => true, 'message' => 'Interest has been successfully deleted!']);
        }
        return response()->json(['status' => false, 'message' => 'Sonething went wrong please try again!']);
    }
}
