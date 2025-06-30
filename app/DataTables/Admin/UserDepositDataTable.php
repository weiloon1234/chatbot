<?php

namespace App\DataTables\Admin;

use App\Models\Deposit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class UserDepositDataTable extends DataTable
{
    public function getQuery(): Model|Builder|Collection|null
    {
        return (new Deposit)->where('role', '=', 'user')->with(['user', 'bank', 'country']);
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
                return $q->user ? $q->user->identity : '';
            },
            'admin_username' => function ($q) {
                return $q->admin ? $q->admin->username : '';
            },
            'country_name' => function ($q) {
                return $q->country ? $q->country->name : '';
            },
            'credit_type_explained' => function ($q) {
                return $q->explainUserCreditType();
            },
            'bank_name' => function ($q) {
                return $q->bank ? $q->bank->{'name_'.app()->getLocale()} : '';
            },
            'deposit_method_explained' => function ($q) {
                return $q->explainUserDepositMethod();
            },
            'currency_amount_explained' => function ($q) {
                return $q->country->explainCurrency($q->currency_amount, true, 2);
            },
            'status_explained' => function ($q) {
                return $q->explainStatus();
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
            'Manage user deposit',
        ];
    }
}
