<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function fetch(Request $request)
    {
        $admin = $request->user();

        $data = [
            'notifications' => [],
        ];

        if ($admin) {
            $permissions = $admin->getEnablePermissions();
            if (isset($permissions['Manage user deposit'])) {
                $data['notifications']['pending_user_deposit'] = Deposit::where('role', '=', 'user')->where('status', '=', 0)->count();
            }
            //            if (isset($permissions['manage_user_order'])) {
            //                $data['notifications']['pending_user_order'] = UserOrder::where('status', '=', 0)->count();
            //            }
        }

        return makeResponse(true, null, $data);
    }
}
