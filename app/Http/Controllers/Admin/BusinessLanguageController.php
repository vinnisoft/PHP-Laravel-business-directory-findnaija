<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DataTables\BusinessLanguageDataTable;
use App\Models\User;
use App\Models\Language;

class BusinessLanguageController extends Controller
{  
    public function index(BusinessLanguageDataTable $dataTable)
    {       
        return $dataTable->render('admin.business-language.index');
    }
    
    public function create()
    {
        return view('admin.business-language.create');
    }
    
    public function store(Request $request)
    {       
        if (Language::create($request->all())) {
            return response()->json(['status' => true, 'message' => 'Language has been successfully created!']);
        }
        return response()->json(['status' => false, 'message' => 'Sonething went wrong please try again!']);
    }
    
    public function edit($id)
    {
        $language = Language::where('id', $id)->first();
        return view('admin.business-language.edit', compact('language'));
    }
    
    public function update(Request $request, $id)
    {
        if (Language::where('id', $id)->update($request->except('_token'))) {
            return response()->json(['status' => true, 'message' => 'Language has been successfully updated!']);
        }
        return response()->json(['status' => false, 'message' => 'Sonething went wrong please try again!']);
    }
    
    public function destroy($id)
    {
        if (Language::where('id', $id)->delete()) {
            return response()->json(['status' => true, 'message' => 'Language has been successfully deleted!']);
        }
        return response()->json(['status' => false, 'message' => 'Sonething went wrong please try again!']);
    }
}
