<?php

namespace App\DataTables;

use App\Models\Business;
use App\Models\BusinessReport;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use App\Models\User;
use App\Models\Category;
use Carbon\Carbon;

class BusinessReportDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('user_id', function ($report) {
                return getUserNameById($report->user_id) ?? '-';
            })
            ->addColumn('business_id', function ($report) {
                return getBusinessNameById($report->business_id);
            })
            ->addColumn('action', function ($report) {
                return view('admin.business-report.action', ['id' => $report->id])->render();
            })
            ->rawColumns(['action'])
            ->setRowId('id');
    }


    public function query(BusinessReport $model): QueryBuilder
    {
        $query = $model->newQuery();
        if (request()['search']['value']) {
            $search = request()['search']['value'];
            $userIds = User::where('first_name', 'like', "%$search%")->orWhere('last_name', 'like', "%$search%")->pluck('id')->toArray();
            $businessIds = Business::where('name', 'like', "%$search%")->pluck('id')->toArray();
            $query->whereIn('user_id', $userIds)->orWhereIn('business_id', $businessIds);
        }
        return $query;
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('businessReportDataTable')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(4)
            ->selectStyleSingle();
            // ->parameters([
            //     'dom' => 'Bfrtip',
            //     'buttons' => ['excel', 'csv', 'pdf', 'reset'],
            // ]);
    }

    public function getColumns(): array
    {
        return [
            Column::make('DT_RowIndex')->title('Sr. No.')->orderable(false)->searchable(false),
            Column::make('user_id')->title('User'),
            Column::make('business_id')->title('Business')->orderable(false)->searchable(true),
            Column::make('category'),
            Column::make('created_at')->title('Date'),
            Column::make('action')->orderable(false)->searchable(false),
        ];       
    }

    protected function filename(): string
    {
        return 'Users_' . date('YmdHis');
    }
}
