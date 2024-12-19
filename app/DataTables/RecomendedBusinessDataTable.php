<?php

namespace App\DataTables;

use App\Models\BusinessRecommendation;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use App\Models\User;
use App\Models\Category;
use Carbon\Carbon;

class RecomendedBusinessDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()           
            ->addColumn('action', function ($business) {
                return view('admin.business.action', ['id' => $business->id])->render();
            })
            ->rawColumns(['action'])
            ->setRowId('id');
    }


    public function query(BusinessRecommendation $model): QueryBuilder
    {
        $query = $model->newQuery();     
        return $query->where('status', '0');
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
                Column::make('business_name'),
                Column::make('business_address'),
                Column::make('customer_name'),
                Column::make('customer_address'),
                Column::make('business_phone'),              
                Column::make('website'),               
                Column::make('created_at')->title('Date'),
            ];
        } else {
            return [
                Column::make('DT_RowIndex')->title('Sr. No.')->orderable(false)->searchable(false),
                Column::make('business_name'),
                Column::make('business_address'),
                Column::make('customer_name'),
                Column::make('customer_address'),
                Column::make('business_phone'),              
                Column::make('website'),
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
