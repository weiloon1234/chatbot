<?php

namespace App\DataTables\Admin;

use App\Models\AuditTrail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class AuditTrailDataTable extends DataTable
{
    public function getQuery(): Model|Builder|Collection|null
    {
        return (new AuditTrail)->with(['admin']);
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
            'admin_username' => function ($q) {
                return $q->admin->username ?? '-';
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
            'Audit trail',
        ];
    }
}
