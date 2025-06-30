<?php

namespace App\DataTables\Admin;

use App\Models\ArticleCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class ArticleCategoryDataTable extends DataTable
{
    public function getQuery(): Model|Builder|Collection|null
    {
        return new ArticleCategory;
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
            // 'sample' => function ($q) {
            // return $q->model_column_name ?? '-';
            // }
            'main_display_style_explained' => function ($q) {
                return $q->explainMainDisplayStyle();
            },
            'list_display_style_explained' => function ($q) {
                return $q->explainListDisplayStyle();
            },
        ];
    }

    public function filters(): void
    {
        if (request()->filled('filter-name')) {
            $this->model = $this->model->where(function ($q) {
                foreach (loopLanguageForColumn('name') as $c) {
                    $q->orWhere($c['column'], 'LIKE', '%'.request()->get('filter-name').'%');
                }
            });
        }
    }

    public function permissions(): array
    {
        return [
            'Manage article category',
        ];
    }
}
