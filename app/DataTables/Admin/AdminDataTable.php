<?php

namespace App\DataTables\Admin;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class AdminDataTable extends DataTable
{
    public function getQuery(): Model|Builder|Collection|null
    {
        $models = (new Admin)->with(['group']);

        $admin = request()->user();
        if (! $admin instanceof Admin) {
            return $models->where('id', '=', 0); // make it no data only
        }

        if ($admin->isDeveloper()) {
            $models = $models->ListForDeveloper();
        } elseif ($admin->isSuperAdmin()) {
            $models = $models->ListForSuperAdmin();
        } else {
            $models = $models->ListForAdmin();
        }

        return $models;
    }

    public function getUnSortable(): array
    {
        return [
            // 'sortable_column1'
        ];
    }

    public function mappings(): array
    {
        return [
            'is_dev' => function ($q) {
                return $q->isDeveloper();
            },
            'admin_group' => function ($q) {
                if ($q->isSuperAdmin()) {
                    return $q->explainType();
                } else {
                    return $q->group->group_name ?? '-';
                }
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
            'Manage admin',
        ];
    }
}
