<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class EditorController extends Controller
{
    const FOLDERS = [
        'article' => ['Manage article'],
        'page' => ['Manage page'],
        'setting' => ['Manage setting'],
    ];

    public function uploadImage(Request $request)
    {
        $admin = $request->user();

        try {
            \DB::beginTransaction();

            if (! isset(static::FOLDERS[$request->get('folder')])) {
                throw new \Exception(__('Permission denied'));
            }

            $permission = static::FOLDERS[$request->get('folder')];

            if (is_array($permission) && count($permission) > 0) {
                $have_permission = false;
                foreach ($permission as $p) {
                    if ($admin->hasPermission($p)) {
                        $have_permission = true;
                    }
                }

                if (! $have_permission) {
                    throw new \Exception(__('Permission denied'));
                }
            } elseif ($permission != null) {
                if (! $admin->hasPermission($permission)) {
                    throw new \Exception(__('Permission denied'));
                }
            }

            $file = $request->file('image');
            $folder = $request->get('folder');

            $manager = new ImageManager(new Driver);

            $img = $manager->read($file);

            $img->scale(700);

            $ext = $file->guessClientExtension();
            $path = getUploadFileBasePath($folder.'/'.now()->format('Y/m/'));
            $file_name = now()->format('d').'-'.generateRandomUniqueName(16);

            if ($ext == 'png') {
                getUploadStorage()->put($path.$file_name.'.'.$ext, $img->toPng()->toFilePointer());
            } else {
                getUploadStorage()->put($path.$file_name.'.'.$ext, $img->toJpeg()->toFilePointer());
            }

            \DB::commit();

            return makeResponse(true, null, [
                'url' => getUploadStorage()->url($path.$file_name.'.'.$ext),
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();

            return makeResponse(false, $e);
        }
    }
}
