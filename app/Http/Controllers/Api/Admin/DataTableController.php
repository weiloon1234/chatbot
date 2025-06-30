<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DataTableController extends Controller
{
    public function load(Request $request)
    {
        $class = '\\App\\DataTables\\Admin\\'.$request->get('model').'DataTable';
        $file = app_path('DataTables/Admin/'.$request->get('model').'DataTable.php');
        $records = [];
        if (file_exists($file)) {
            $me = $request->user();

            $model = new $class;

            //            // SUPER ADMIN OR DEV ONLY
            if (in_array($request->get('model'), ['Admin', 'AdminGroup'])) {
            } else {
                if (! $me->hasPermission($model->permissions())) {
                    throw new \Exception(__('Permission denied'));
                }
            }

            if ($request->get('export') == 1) {
                return $model->getExport();
            } else {
                $records = $model->getRecords();
            }
        }

        return makeResponse(true, null, [
            'records' => $records,
            'per_page' => $model->ipp ?? $request->get('ipp'),
            'total_records' => $model->total_records ?? 0,
            'total_pages' => $model->total_pages ?? 0,
            'page' => request()->get('p', 1),
        ]);
    }
}
