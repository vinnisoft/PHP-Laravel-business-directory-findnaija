<?php

namespace App\DataTables;

use App\Models\CustomNotification;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class CustomNotificationDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()   
            ->addColumn('subject', function ($notification) {
                return $notification->subject;
            })
            ->addColumn('message', function ($notification) {
                return $notification->message;
            }) 
            ->addColumn('type', function ($notification) {
                return @implode(', ', json_decode($notification->type));
            })        
            ->addColumn('action', function ($notification) {
                return view('admin.custom-notification.action', ['id' => $notification->id])->render();
            })
            ->rawColumns(['action'])
            ->setRowId('id');
    }

    public function query(CustomNotification $model): QueryBuilder
    {
        return $model->newQuery();
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('usersDataTable')
            ->columns($this->getColumns())
            ->minifiedAjax()
            // ->orderBy(1)
            ->selectStyleSingle();
    }

    public function getColumns(): array
    {
        return [
            Column::make('DT_RowIndex')->title('Sr. No.')->orderable(false)->searchable(false),
            Column::make('subject'),
            Column::make('message')->width(800),
            Column::make('type'),
            Column::make('action')->orderable(false)->searchable(false),
        ];
    }

    protected function filename(): string
    {
        return 'Users_' . date('YmdHis');
    }
}
