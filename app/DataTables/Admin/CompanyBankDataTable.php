<?php

namespace App\DataTables\Admin;

use App\Models\CompanyBank;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class CompanyBankDataTable extends DataTable
{
    public function getQuery(): Model|Builder|Collection|null
    {
        return (new CompanyBank)->with(['bank', 'country']);
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
            'bank_name' => function ($q) {
                return $q->bank?->translateName() ?? '-';
            },
            'country_name' => function ($q) {
                return $q->country->name ?? '-';
            },
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
            'Manage company bank',
        ];
    }
}
