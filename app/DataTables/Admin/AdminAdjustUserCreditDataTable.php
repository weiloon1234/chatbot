<?php

namespace App\DataTables\Admin;

use App\Models\AdminAdjustUserCredit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class AdminAdjustUserCreditDataTable extends DataTable
{
    public function getQuery(): Model|Builder|Collection|null
    {
        return (new AdminAdjustUserCredit)->with(['user', 'admin']);
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
            'user_identity' => function ($q) {
                return $q->user ? $q->user->identity : '-';
            },
            'admin_username' => function ($q) {
                return $q->admin ? $q->admin->username : '-';
            },
            'credit_type_explained' => function ($q) {
                return __('Credit '.$q->credit_type);
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
            'Manage user credit',
        ];
    }
}
