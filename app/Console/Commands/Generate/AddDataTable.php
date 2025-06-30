<?php

namespace App\Console\Commands\Generate;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Log;

class AddDataTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:dt';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add data table';

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

            $role = $this->ask('Role? capital matters! EG: Admin', 'Admin');

            $model_name = $this->ask('Model name? capital matters! EG: UserOrder');

            $model_path = 'Models/'.$model_name.'.php';
            $dt_path = 'DataTables/'.$role.'/'.$model_name.'DataTable.php';

            $model_file = app_path($model_path);
            $dt_file = app_path($dt_path);

            if (file_exists($dt_file)) {
                throw new \Exception($dt_path.' exists');
            }

            if (! file_exists($model_file)) {
                if (! in_array($this->ask($model_path.' doesn\'t exists, continue?', 'N'), ['Y', 'y'])) {
                    $this->error('Abort');

                    return;
                }
            }

            $permissions = '';
            if (mb_strtoupper($role) === 'ADMIN') {
                $permissions = $this->ask('Permission? separate by comma(,) EG: Manage user,Manage user advance');

                if ($permissions) {
                    $arr = explode(',', $permissions);
                    $temp = '';
                    foreach ($arr as $row) {
                        if (trim($row)) {
                            $temp .= "'".trim($row)."', ";
                        }
                    }
                    $permissions = $temp;
                } elseif (! $permissions) {
                    $permissions = "//'Manage user', 'Manage user advance'";
                }
            }

            if (! $permissions) {
                $permissions = "//'Manage user', 'Manage user advance'";
            }

            $file = file_get_contents(app_path('Console/Commands/Generate/_add_data_table.txt'));
            $file = str_replace('{{MODEL}}', $model_name, $file);
            $file = str_replace('{{ROLE}}', $role, $file);
            $file = str_replace('{{PERMISSIONS}}', $permissions, $file);

            file_put_contents($dt_file, $file);

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
