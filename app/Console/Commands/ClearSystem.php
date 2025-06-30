<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Log;

class ClearSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear_system';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear system';

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

            $settings = [
                ['path' => storage_path('debugbar'), 'glob' => '/*.json'],
                ['path' => storage_path('logs'), 'glob' => '/*.log'],
                ['path' => storage_path('logs'), 'glob' => '/*.gz'],
            ];

            foreach ($settings as $setting) {
                //                $this->info($setting['path'] . $setting['glob']);
                $files = glob($setting['path'].$setting['glob']);
                foreach ($files as $file) {
                    if (is_file($file)) {
                        exec('rm -f '.$file);
                        $this->info($file.' deleted');
                    }
                }
            }

            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('view:clear');
            Artisan::call('route:clear');
            Artisan::call('sanctum:prune-expired');

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
        $response = Http::post('https://api.namefake.com');

        return $response->json();
    }
}
