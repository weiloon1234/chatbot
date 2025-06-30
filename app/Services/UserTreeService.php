<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserUnilevel;

class UserTreeService
{
    public $user = null;

    public $up = null;

    public $introducer = null;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function triggerRegister(User $introducer)
    {
        $this->introducer = $introducer;
        $this->updateUpline();
    }

    public function updateUpline()
    {
        $this->updateIntroducer();
    }

    public function updateIntroducer()
    {
        $user = $this->user;

        // UPDATE SPONSOR
        if ($user) {
            $introducer = $user->introducer;
            if ($introducer) {
                $uu = new UserUnilevel;
                $uu->user_id = $user->id;
                $uu->user_unilevel = $user->unilevel;
                $uu->introducer_user_id = $introducer->id;
                $uu->introducer_unilevel = $introducer->unilevel;
                $uu->save();

                \DB::statement('
                    INSERT INTO user_unilevels (`user_id`, `user_unilevel`, `introducer_user_id`, `introducer_unilevel`, `created_at`, `updated_at`)
                    SELECT "'.$user->id.'" as `user_id`, "'.$user->unilevel.'" as `user_unilevel`,
                    `introducer_user_id`, `introducer_unilevel`,
                    "'.$uu->created_at.'" as `created_at`, "'.$uu->created_at.'" as `updated_at`
                     FROM `user_unilevels` WHERE `user_id` = "'.$introducer->id.'" ORDER BY `updated_at` ASC, `id` ASC;
                ');
            }
        }
    }
}
