<?php

namespace App\Http\Controllers\Api\Admin\Article;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function buildForm(Request $request)
    {
        $data = [];

        if ($request->filled('id')) {
            $data['model'] = Article::find($request->get('id'));
        }

        $data['categories'] = ArticleCategory::Sorted()->get();

        return makeResponse(true, null, $data);
    }

    public function submitForm(Request $request)
    {
        $rules = [];

        //        $rules['subject'] = ['required', 'string', 'min:1', 'max:48'];
        //        $rules['description'] = ['required', 'string', 'min:1', 'max:255'];

        foreach ((new Article)->multiLanguageColumns() as $column) {
            foreach (loopLanguageForColumn($column) as $c) {
                if ($column == 'subject') {
                    $rules[$c['column']] = ['required', 'string', 'min:1', 'max:48'];
                } elseif ($column == 'description') {
                    $rules[$c['column']] = ['required', 'string', 'min:1', 'max:255'];
                } elseif ($column == 'cover') {
                    if ($request->filled('id')) {
                        foreach (loopLanguageForColumn('cover') as $column) {
                            if ($request->hasFile($c['column'])) {
                                $rules[$column['column']] = ['nullable', 'image'];
                            } else {
                                $rules[$column['column']] = ['nullable'];
                            }
                        }
                    } else {
                        foreach (loopLanguageForColumn('cover') as $column) {
                            $rules[$column['column']] = ['required', 'image'];
                        }
                    }
                }
            }
        }

        $rules['article_category_id'] = ['required', 'exists:article_categories,id'];
        $rules['sorting'] = ['nullable', 'isNumber'];

        $this->validate($request, $rules);

        try {
            \DB::beginTransaction();

            if ($request->filled('id')) {
                $model = Article::find($request->get('id'));

                if (! $model) {
                    throw new \Exception(__('Record not found'));
                }
            } else {
                $model = new Article;
            }

            foreach ((new Article)->multiLanguageColumns() as $column) {
                if ($column != 'cover') {
                    foreach (loopLanguageForColumn($column) as $c) {
                        $model->{$c['column']} = $request->get($c['column']);
                    }
                }
            }

            $model->sorting = $request->get('sorting', 0);
            $model->article_category_id = $request->get('article_category_id');
            if (! $model->exists) {
                $model->save();
            }

            foreach (loopLanguageForColumn('cover') as $c) {
                if ($request->hasFile($c['column'])) {
                    $model->{$c['column']} = $request->file($c['column']);
                }
            }

            $model->save();

            \DB::commit();

            ArticleCategory::forgetCache();
            ArticleCategory::buildCache();

            return makeResponse(true, null, ['article' => $model]);
        } catch (\Exception $e) {
            \DB::rollBack();

            return makeResponse(false, $e);
        }
    }

    public function delete(Request $request, $id)
    {
        $model = Article::find($id);

        if (! $model) {
            throw new \Exception(__('Record not found'));
        }

        $model->delete();

        ArticleCategory::forgetCache();
        ArticleCategory::buildCache();

        return makeResponse(true, null);
    }
}
