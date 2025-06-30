<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function fetch(Request $request)
    {
        if ($request->filled('page_tag')) {
            $model = Page::where('tag', '=', $request->get('page_tag'))->first();

            return makeResponse(true, null, ['model' => $model]);
        } else {
            $models = Page::orderBy('id', 'ASC')->get();

            return makeResponse(true, null, ['models' => $models]);
        }
    }
}
