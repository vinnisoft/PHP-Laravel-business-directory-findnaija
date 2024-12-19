<?php

namespace App\DataTables;

use App\Models\User;
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class UserDataTable extends DataTable
{
    public function dataTable($query)
    {
        return datatables($query)
            ->addIndexColumn()
            ->addColumn('name', function ($user) {
                return $user->first_name . ' ' . $user->last_name;
            })
            ->addColumn('email', function ($user) {
                return $user->email;
            })
            ->addColumn('country', function ($user) {
                return $user->country;
            })
            ->addColumn('phone_number', function ($user) {
                return $user->phone_number;
            })
            ->addColumn('date_of_birth', function ($user) {
                return $user->dob;
            })
            ->addColumn('action', function ($user) {
                return view('admin.user.action', ['id' => $user->id]);
            });
    }

    public function query(User $users)
    {
        // echo '<pre>';
        // print_r(request()->columns[0]);
        // die;
        $query = $users->newQuery();
        $query->where('email', '!=', 'admin@findnaija.com');

        if ($this->request()->has('search.value')) {
            $search = $this->request()->input('search.value');
            $query->where(function ($query) use ($search) {
                $query->where('id', 'like', "%$search%")
                    ->orWhere('first_name', 'like', "%$search%")
                    ->orWhere('last_name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhere('country', 'like', "%$search%");
            });
        }
        // if (request()->columns[0]['data'] == 'DT_RowIndex' && request()->columns[0]['orderable'] == 'DT_RowIndex') {

        // }

        $query->orderBy('id', 'DESC');
        return $query->select('id', 'first_name', 'last_name', 'email', 'country', 'phone_number', 'dob');
    }


    public function html()
    {
        return $this->builder()
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->parameters([
                'order' => [[0, 'asc']],
                'serverSide' => true,
                'dom' => 'Blfrtip',
                'buttons' => ['export', 'print', 'reset', 'reload'],
            ]);
    }

    protected function getColumns()
    {
        return [
            'DT_RowIndex' => ['orderable' => false, 'searchable' => false, 'title' => 'Sr. No.'],
            'name' => ['orderable' => false, 'searchable' => true],
            'email' => ['orderable' => true, 'searchable' => true],
            'country' => ['orderable' => false, 'searchable' => true],
            'phone_number' => ['orderable' => false, 'searchable' => true],
            'date_of_birth' => ['orderable' => false, 'searchable' => true],
            'action' => ['orderable' => false, 'searchable' => false],
        ];
    }
}
