<?php

namespace App\DataTables\Admin;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class UserDataTable extends DataTable
{
    public function getQuery(): Model|Builder|Collection|null
    {
        return (new User)->with(['introducer', 'country']);
    }

    public function getUnSortable(): array
    {
        return [];
    }

    public function mappings(): array
    {
        return [
            'gender' => function ($q) {
                if ($q->gender == 1) {
                    return __('Male');
                } elseif ($q->gender == 2) {
                    return __('Female');
                } else {
                    return __('Unknown');
                }
            },
            'is_origin' => function ($q) {
                return $q->isOrigin();
            },
            'introducer_identity' => function ($q) {
                return $q->introducer?->identity;
            },
            'country_name' => function ($q) {
                return $q->country?->name;
            },
        ];
    }

    public function filters(): void
    {
        if (request()->filled('f_username')) {
            $this->model = $this->model->where('username', 'LIKE', '%'.request()->get('f_username').'%');
        }
    }

    public function permissions(): array
    {
        return [
            'Manage user',
        ];
    }
}
