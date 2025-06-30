<?php

namespace App\DataTables\Admin;

use App\Models\Article;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class ArticleDataTable extends DataTable
{
    public function getQuery(): Model|Builder|Collection|null
    {
        return (new Article)->with(['category']);
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
            'category_name' => function ($q) {
                return $q->category?->translateName();
            },
            // 'sample' => function ($q) {
            // return $q->model_column_name ?? '-';
            // }
        ];
    }

    public function filters(): void
    {
        if (request()->filled('filter-subject')) {
            $this->model = $this->model->where(function ($q) {
                foreach (loopLanguageForColumn('subject') as $c) {
                    $q->orWhere($c['column'], 'LIKE', '%'.request()->get('filter-subject').'%');
                }
            });
        }
    }

    public function permissions(): array
    {
        return [
            'Manage article',
            // 'manage_user',
            // 'manage_user_advanced',
        ];
    }
}
