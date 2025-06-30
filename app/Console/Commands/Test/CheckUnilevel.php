<?php

namespace App\Console\Commands\Test;

use App\Models\UserUnilevel;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Log;

class CheckUnilevel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:check_unilevel';

    /**
     * The console command description.0
     *
     * @var string
     */
    protected $description = 'Test/Check unilevel';

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

            foreach (\App\Models\User::orderBy('unilevel', 'DESC')->orderBy('id', 'DESC')->get() as $user) {
                $this->info($user->username.' (#'.$user->id.') checking');
                $upline = $user->introducer;
                while ($upline) {
                    $row = UserUnilevel::where('user_id', '=', $user->id)
                        ->where('introducer_user_id', '=', $upline->id)
                        ->count();
                    if ($row != 1) {
                        $this->info($user->username.'(#'.$user->id).') got problem loading unilevel with upline '.$upline->username.' (#'.$upline->id.') at gen '.($user->unilevel - $upline->unilevel);
                    }

                    $upline = $upline->introducer;
                }
            }

            $msg = sprintf('Successfully '.$this->signature.' at %s', \Carbon\Carbon::now()->format('Y-m-d H:i:s'));
            Log::info($msg);

            if (app()->runningInConsole()) {
                $this->comment(PHP_EOL.$msg.PHP_EOL);
            } else {
                return makeResponse(true, $msg);
            }
        } catch (\Exception $e) {
            \DB::rollBack();
            $msg = sprintf('Error while '.$this->signature.', file: %s, line: %s, message: %s', $e->getFile(), $e->getLine(), $e->getMessage());
            Log::info($msg);

            if (app()->runningInConsole()) {
                $this->comment(PHP_EOL.$msg.PHP_EOL);
            } else {
                addError($msg);

                return makeResponse(false, $msg);
            }
        }
    }

    public function fetchUser()
    {
        $response = Http::get('https://random-data-api.com/api/users/random_user');

        return $response->json();
    }
}
