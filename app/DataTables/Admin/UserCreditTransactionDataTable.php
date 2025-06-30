<?php

namespace App\DataTables\Admin;

use App\Models\UserCreditTransaction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class UserCreditTransactionDataTable extends DataTable
{
    public function getQuery(): Model|Builder|Collection|null
    {
        return (new UserCreditTransaction)->with(['user']);
    }

    public function getUnSortable(): array
    {
        return [];
    }

    public function mappings(): array
    {
        return [
            'user_identity' => function ($q) {
                return $q->user?->identity;
            },
            'transaction_type_explained' => function ($q) {
                return $q->explainTransactionType();
            },
        ];
    }

    public function filters(): void {}

    public function permissions(): array
    {
        return [
            'Manage user',
        ];
    }
}
