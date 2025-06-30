<?php

namespace App\DataTables\Admin;

use App\Models\Bank;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class BankDataTable extends DataTable
{
    public function getQuery(): Model|Builder|Collection|null
    {
        return (new Bank)->with(['country']);
    }

    public function getUnSortable(): array
    {
        return [
            // 'unsortable_column1'
        ];
    }

    public function mappings(): array
    {
        return [
            'country_name' => function ($q) {
                return $q->country->name ?? '-';
            },
            // 'sample' => function ($q) {
            // return $q->model_column_name ?? '-';
            // }
        ];
    }

    public function filters(): void
    {
        // if (request()->filled('f_username')) {
        // $this->model = $this->model->where('username', 'LIKE', '%' . request()->get('f_username') . '%');
        // }
    }

    public function permissions(): array
    {
        return [
            'Manage bank',
        ];
    }
}
