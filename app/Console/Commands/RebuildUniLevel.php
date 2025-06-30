<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\UserUnilevel;
use Illuminate\Console\Command;
use Log;

class RebuildUniLevel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rebuild_unilevel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rebuild unilevel';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            ini_set('max_execution_time', 0); // 0=NOLIMIT

            \DB::statement('TRUNCATE `user_unilevels`');

            $this->call('fix_unilevel');

            User::with(['introducer'])
                ->whereNotNull('introducer_user_id')
                ->orderBy('unilevel', 'ASC')
                ->orderBy('id', 'ASC')->chunk(1000, function ($users, $page) {
                    $this->info('running for '.$page);

                    foreach ($users as $user) {
                        $introducer = $user->introducer;

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
                });

            $msg = sprintf('Successfully '.$this->description.' at %s', \Carbon\Carbon::now()->format('Y-m-d H:i:s'));
            Log::info($msg);

            if (app()->runningInConsole()) {
                $this->comment(PHP_EOL.$msg.PHP_EOL);
            } else {
                //                return makeResponse(true, $msg);
            }
        } catch (\Exception $e) {
            //            \DB::rollback();
            $msg = sprintf('Error while '.$this->signature.', file: %s, line: %s, message: %s', $e->getFile(), $e->getLine(), $e->getMessage());
            $this->info($msg);
            Log::info($msg);

            if (app()->runningInConsole()) {
                //                \Mail::raw($msg, function ($message) {
                //                    $message->to('', 'Developer')->subject(config('env.APP_URL') . ' ' . $this->signature . ' Error');
                //                });
                $this->comment(PHP_EOL.$msg.PHP_EOL);
            } else {
                addError($msg);

                return makeResponse(false, $msg);
            }
        }
    }
}
