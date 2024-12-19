<?php

namespace App\DataTables;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class UsersDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('action', function ($user) {
                return view('admin.user.action', ['id' => $user->id])->render();
            })
            ->setRowId('id')
            ->rawColumns(['action']);
    }


    public function query(User $model): QueryBuilder
    {
        return $model->newQuery()->whereNot('email', 'like', '%admin%');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('usersDataTable')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(6)
            ->selectStyleSingle()
            ->parameters([
                'dom' => 'Bfrtip',
                'buttons' => ['excel', 'csv', 'pdf', 'reset'],
            ]);
    }

    public function getColumns(): array
    {        
        if (isset(request()['action']) && in_array(request()['action'], ['excel', 'csv', 'pdf'])) {
            return [
                Column::make('DT_RowIndex')->title('Sr. No.')->orderable(false)->searchable(false),
                Column::make('first_name'),
                Column::make('last_name'),
                Column::make('email'),
                Column::make('country'),
                Column::make('phone_number'),
                Column::make('created_at'),
            ];
        } else {
            return [
                Column::make('DT_RowIndex')->title('Sr. No.')->orderable(false)->searchable(false),
                Column::make('first_name'),
                Column::make('last_name'),
                Column::make('email'),
                Column::make('country'),
                Column::make('phone_number'),
                Column::make('created_at'),
                Column::make('action')->orderable(false)->searchable(false),
            ];   
        }
    }

    protected function filename(): string
    {
        return 'Users_' . date('YmdHis');
    }
}
