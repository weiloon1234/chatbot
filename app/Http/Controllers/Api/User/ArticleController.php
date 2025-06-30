<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function home(Request $request)
    {
        return makeResponse(true, null, [
            'categories' => ArticleCategory::getFromCache(),
        ]);
    }

    public function fetch(Request $request)
    {
        $models = Article::with(['category'])
            ->Sorted();

        if ($request->filled('category_id')) {
            $models = $models->where('category_id', '=', $request->get('category_id'));
        }

        if ($request->filled('id')) {
            return makeResponse(true, null, ['model' => $models->find($request->get('id'))]);
        } else {
            return $models->paginate();
        }
    }
}
