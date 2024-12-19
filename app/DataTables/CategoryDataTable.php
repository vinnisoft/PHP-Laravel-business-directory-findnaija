<?php

namespace App\DataTables;

use App\Models\Category;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class CategoryDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('category_group', function ($category) {
                return $category->categoryGroup->name;
            })
            ->addColumn('category_on_home', function ($category) {
                return view('admin.category.toggle-checkbox', ['id' => $category->id, 'status' => $category->category_on_home, 'name' => 'category_on_home'])->render();
            })
            ->addColumn('show_on_home', function ($category) {
                return view('admin.category.toggle-checkbox', ['id' => $category->id, 'status' => $category->show_on_home, 'name' => 'category'])->render();
            })
            ->addColumn('graphic_on_home', function ($category) {
                return view('admin.category.toggle-checkbox', ['id' => $category->id, 'status' => $category->graphic_on_home, 'name' => 'graphic'])->render();
            })
            ->addColumn('action', function ($category) {
                return view('admin.category.action', ['id' => $category->id])->render();
            })
            ->rawColumns(['category_on_home', 'show_on_home', 'graphic_on_home', 'action'])
            ->setRowId('id');
    }

    public function query(Category $model): QueryBuilder
    {
        return $model->newQuery();
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('usersDataTable')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1)
            ->selectStyleSingle();
    }

    public function getColumns(): array
    {
        return [
            Column::make('DT_RowIndex')->title('Sr. No.')->orderable(false)->searchable(false),
            Column::make('name'),
            Column::make('category_group'),
            Column::make('category_on_home'),
            Column::make('show_on_home')->title('New Business On Home'),
            Column::make('graphic_on_home'),
            Column::make('action')->orderable(false)->searchable(false),
        ];
    }

    protected function filename(): string
    {
        return 'Users_' . date('YmdHis');
    }
}
