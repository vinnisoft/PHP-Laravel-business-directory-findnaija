<?php

namespace App\DataTables;

use App\Models\FavouriteBusiness;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class FavouriteBusinessDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('business', function ($business) {
                return getBusinessNameById($business->business_id) ?? '-';
            })
            ->addColumn('action', function ($business) {
                return view('admin.favorite-business.action', ['id' => $business->id])->render();
            })
            ->setRowId('id');
    }

    public function query(FavouriteBusiness $model): QueryBuilder
    {
        return $model->newQuery();
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('favouriteBusinessDataTable')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1)
            ->selectStyleSingle();
    }

    public function getColumns(): array
    {
        return [
            Column::make('DT_RowIndex')->title('Sr. No.')->orderable(false)->searchable(false),
            Column::make('business'),
            Column::make('action')->orderable(false)->searchable(false),
        ];
    }

    protected function filename(): string
    {
        return 'Users_' . date('YmdHis');
    }
}
