<?php

namespace App\DataTables;

use App\Models\Support;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class SupportDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()   
            ->addColumn('user', function ($support) {
                return getUserNameById($support->user_id) ?? '-';
            })
            ->filterColumn('user', function ($query, $keyword) {
                $userIds = User::where('first_name', 'like', "%$keyword%")->orWhere('last_name', 'like', "%$keyword%")->pluck('id')->toArray();
                $query->whereIn('user_id', $userIds);
            })
            ->addColumn('business_id', function ($support) {
                return getBusinessNameById($support->business_id) ?? '-';
            })
            ->addColumn('national_id', function ($support) {
                if (!empty($support->national_id)) {
                    return '<h5 class="mt-2"><a href="'.$support->national_id.'" target="_blank"><i class=" fa fa-file-alt"></i>National Id</a></h5>';
                }
            })
            ->addColumn('business_registration', function ($support) {
                if (!empty($support->business_registration)) {
                    return '<h5 class="mt-2"><a href="'.$support->business_registration.'" target="_blank"><i class=" fa fa-file-alt"></i> Business Registration</a></h5>';
                }
            })       
            ->rawColumns(['national_id', 'business_registration'])
            ->setRowId('id');
    }

    public function query(Support $model): QueryBuilder
    {
        return $model->newQuery();
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('supportDataTable')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1)
            ->selectStyleSingle();
    }

    public function getColumns(): array
    {
        return [
            Column::make('DT_RowIndex')->title('Sr. No.')->orderable(false)->searchable(false),
            Column::make('user'),
            Column::make('type'),
            Column::make('subject'),
            Column::make('notes'),
            Column::make('business_id'),
            Column::make('national_id'),
            Column::make('business_registration'),
        ];
    }

    protected function filename(): string
    {
        return 'Users_' . date('YmdHis');
    }
}
