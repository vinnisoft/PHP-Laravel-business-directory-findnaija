<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\DataTables\BusinessReportDataTable;
use App\Models\BusinessOption;
use DB, Validator, Auth;
use App\Models\BusinessReport;

class BusinessReportController extends Controller
{
    public function index(BusinessReportDataTable $dataTable)
    {
        return $dataTable->render('admin.business-report.index');
    }
    
    public function show($id)
    {
        $report = BusinessReport::findOrFail($id);
        return view('admin.business-report.show', compact('report'));
    }

    public function destroy($id)
    {
        if (BusinessReport::where('id', $id)->delete()) {
            return response()->json(['status' => true, 'message' => 'Business report has been successfully deleted!']);
        }
        return response()->json(['status' => false, 'message' => 'Sonething went wrong please try again!']);
    }
}