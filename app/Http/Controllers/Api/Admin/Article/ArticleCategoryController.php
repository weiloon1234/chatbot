<?php

namespace App\Http\Controllers\Api\Admin\Article;

use App\Http\Controllers\Controller;
use App\Models\ArticleCategory;
use Illuminate\Http\Request;

class ArticleCategoryController extends Controller
{
    public function buildForm(Request $request)
    {
        $data = [];

        if ($request->filled('id')) {
            $data['model'] = ArticleCategory::find($request->get('id'));
        }

        return makeResponse(true, null, $data);
    }

    public function submitForm(Request $request)
    {
        $rules = [];

        //        $rules['subject'] = ['required', 'string', 'min:1', 'max:48'];
        //        $rules['description'] = ['required', 'string', 'min:1', 'max:255'];

        foreach ((new ArticleCategory)->multiLanguageColumns() as $column) {
            foreach (loopLanguageForColumn($column) as $c) {
                $rules[$c['column']] = ['required', 'string', 'min:1', 'max:48'];
            }
        }

        if ($request->filled('id')) {
            $rules['id'] = ['required', 'exists:article_categories,id'];
        }

        $rules['sorting'] = ['nullable', 'isNumber'];
        $rules['main_display_style'] = ['required', 'in:'.implode(',', array_keys(ArticleCategory::getMainDisplayStyleLists()))];
        $rules['main_display_show_more'] = ['nullable', 'isYesNo'];
        $rules['main_display_show_title'] = ['nullable', 'isYesNo'];
        $rules['list_display_style'] = ['required', 'in:'.implode(',', array_keys(ArticleCategory::getListDisplayStyleLists()))];
        $rules['details_show_article_cover'] = ['nullable', 'isYesNo'];
        $rules['details_show_article_datetime'] = ['nullable', 'isYesNo'];

        $this->validate($request, $rules);

        try {
            \DB::beginTransaction();

            if ($request->filled('id')) {
                $model = ArticleCategory::find($request->get('id'));

                if (! $model) {
                    throw new \Exception(__('Record not found'));
                }
            } else {
                $model = new ArticleCategory;
            }

            foreach ((new ArticleCategory)->multiLanguageColumns() as $column) {
                foreach (loopLanguageForColumn($column) as $c) {
                    $model->{$c['column']} = $request->get($c['column']);
                }
            }

            $model->main_display_style = $request->get('main_display_style');
            $model->main_display_show_more = $request->get('main_display_show_more', 0);
            $model->main_display_show_title = $request->get('main_display_show_title', 0);
            $model->list_display_style = $request->get('list_display_style');
            $model->details_show_article_cover = $request->get('details_show_article_cover', 0);
            $model->details_show_article_datetime = $request->get('details_show_article_datetime', 0);
            $model->sorting = $request->get('sorting', 0);

            $model->save();

            \DB::commit();

            ArticleCategory::forgetCache();
            ArticleCategory::buildCache();

            return makeResponse(true, null, ['model' => $model]);
        } catch (\Exception $e) {
            \DB::rollBack();

            return makeResponse(false, $e);
        }
    }

    public function delete(Request $request)
    {
        $model = ArticleCategory::find($request->get('id'));

        if (! $model) {
            throw new \Exception(__('Record not found'));
        }

        $model->delete();

        ArticleCategory::forgetCache();
        ArticleCategory::buildCache();

        return makeResponse(true, null);
    }
}
