<?php

namespace App\DataTables;

use App\Models\Business;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use App\Models\User;
use App\Models\Category;
use Carbon\Carbon;

class BusinessDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('user_id', function ($business) {
                return getUserNameById($business->user_id) ?? '-';
            })
            ->addColumn('action', function ($business) {
                return view('admin.business.action', ['id' => $business->id])->render();
            })
            ->addColumn('category', function ($business) {
                return $business->category_name;
            })
            ->rawColumns(['status', 'action'])
            ->setRowId('id');
    }


    public function query(Business $model): QueryBuilder
    {
        $businessStatus = ['pending', 'rejected'];
        $query = $model->newQuery();
        if (isset(request()->userId)) {
            $query->where('user_id', request()->userId);
        }
        $query->whereNotIn('status', $businessStatus);
        if (request()['search']['value']) {
            $search = request()['search']['value'];
            $userIds = User::where('first_name', 'like', "%$search%")->orWhere('last_name', 'like', "%$search%")->pluck('id')->toArray();
            $categoryIds = Category::where('name', 'like', "%$search%")->pluck('id')->toArray();
            $query->whereNotIn('status', $businessStatus)->whereIn('user_id', $userIds)
                ->orWhere('name', 'like', "%$search%")
                ->orWhere('address', 'like', "%$search%")
                ->orWhereIn('category', $categoryIds)
                ->orWhere('country', 'like', "%$search%");
        }
        return $query;
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('usersDataTable')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(7)
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
                Column::make('user_id')->title('User'),
                Column::make('country'),
                Column::make('name'),
                Column::make('address'),
                Column::make('category'),              
                Column::make('hiring_for_buss'),               
                Column::make('created_at')->title('Date'),
            ];
        } else {
            return [
                Column::make('DT_RowIndex')->title('Sr. No.')->orderable(false)->searchable(false),
                Column::make('user_id')->title('User'),
                Column::make('country'),
                Column::make('name'),
                Column::make('address'),
                Column::make('category'),              
                Column::make('hiring_for_buss'),
                Column::make('created_at')->title('Date'),
                Column::make('action')->orderable(false)->searchable(false),
            ];
        }        
    }

    protected function filename(): string
    {
        return 'Users_' . date('YmdHis');
    }
}
