<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Log;

class FixUnilevel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix_unilevel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix Sponsor Pax and IDS after manual changing sponsor tree';

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

            User::with(['introducer'])->orderBy('id', 'ASC')->chunk(500, function ($users) {
                foreach ($users as $user) {
                    if ($user->introducer) {
                        $user->unilevel = $user->introducer->unilevel + 1;
                    } else {
                        $user->unilevel = 1;
                    }
                    $user->save();
                }
            });

            \DB::commit();
            // Calculation -- END

            $msg = sprintf('Successfully fix sponsors at %s', \Carbon\Carbon::now()->format('Y-m-d H:i:s'));
            Log::info($msg);

            if (app()->runningInConsole()) {
                $this->comment(PHP_EOL.$msg.PHP_EOL);
            } else {
                return makeResponse(true, $msg);
            }
        } catch (\Exception $e) {
            \DB::rollback();
            $msg = sprintf('Error while fixing sponsors, file: %s, line: %s, message: %s', $e->getFile(), $e->getLine(), $e->getMessage());
            Log::info($msg);

            if (app()->runningInConsole()) {
                \Mail::raw($msg, function ($message) {
                    $message->to('', 'Developer')->subject(config('env.APP_URL').' Fix sponsors Error');
                });
                $this->comment(PHP_EOL.$msg.PHP_EOL);
            } else {
                addError($msg);

                return makeResponse(false, $msg);
            }
        }
    }
}
