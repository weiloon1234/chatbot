<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\HtmlString;

class AppController extends Controller
{
    public function getApp(Request $request)
    {
        $role = $this->getRole($request);

        $meta = [
            'title' => config('env.APP_NAME'),
            'description' => config('env.APP_NAME'),
            'image' => asset('/img/logo-only.png'),
            'image_alt' => null,
        ];

        if (\Crawler::isCrawler()) {
            $paths = explode('/', $request->path());

            //            if ($request->is('article/*')) {
            //                $id = end($paths);
            //                $article = Article::find($id);
            //
            //                if ($article) {
            //                    $meta['title'] = $article->subject;
            //                    $meta['description'] = $article->description;
            //                    $meta['image'] = $article->cover;
            //                    $meta['image_alt'] = asset('/img/logo-only.png');
            //                }
            //            }
        }

        return view('app', ['assets' => $this->getAssets($role), 'role' => $role, 'meta' => $meta]);
    }

    protected function getRole(Request $request): string
    {
        return \Str::startsWith($request->path(), ['admin']) ? 'admin' : 'user';
    }

    protected function getPort(): array
    {
        return [
            'user' => 3030,
            'admin' => 3031,
        ];
    }

    protected function getAssetBasePath($role): string
    {
        $url = request()->server->get('SERVER_NAME').':'.$this->getPort()[$role];

        return request()->server->get('HTTPS') === 'on' ? 'https://'.$url : 'http://'.$url;
    }

    protected function getAssets($role): HtmlString
    {
        $dev = false;
        $path = $this->getAssetBasePath($role);
        if (app()->environment('local')) {
            try {
                Http::withoutVerifying()
                    ->get($path);
                $dev = true;
            } catch (\Exception $e) {
                //                dd($e->getMessage());
            }
        }

        if ($dev) {
            return new HtmlString(<<<HTML
                <script type="module" src="{$path}/@vite/client"></script>
                <script type="module" src="{$path}/resources/scripts/{$role}/main.js"></script>
            HTML);
        }

        $manifest = getAssetManifest($role);

        if (isset($manifest['version'])) {
            view()->share('assetVersion', $manifest['version']);
        }

        if (isset($manifest['revision'])) {
            view()->share('assetRevision', $manifest['revision']);
        }

        return new HtmlString(<<<HTML
            <script type="module" src="/build/{$role}/{$manifest['resources/scripts/'.$role.'/main.js']['file']}"></script>
            <link rel="stylesheet" href="/build/{$role}/{$manifest['resources/scripts/'.$role.'/main.js']['css'][0]}">
        HTML);
    }
}
