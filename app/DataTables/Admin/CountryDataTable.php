<?php

namespace App\DataTables\Admin;

use App\Config;
use App\Models\Country;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class CountryDataTable extends DataTable
{
    public function getQuery(): Model|Builder|Collection|null
    {
        return new Country;
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
            'status_explained' => function ($q) {
                return $q->explainStatus();
            },
            'conversion_rate' => function ($q) {
                return fundFormat($q->conversion_rate, Config::DECIMAL_POINT_READABLE);
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
            'Manage country',
        ];
    }
}
