<?php

namespace App\Console\Commands\Spam;

use App\Managers\UserManager;
use App\Models\InvestmentPlan;
use App\Models\User;
use App\Models\UserInvestment;
use App\Models\UserInvestmentTransaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Log;

class SpamUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spam:user';

    /**
     * The console command description.0
     *
     * @var string
     */
    protected $description = 'Spam user';

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

            $number = $this->ask('How many user?', 1000);
            for ($i = 0; $i < $number;) {
                if ($i + 100 < $number) {
                    $load = 100;
                } else {
                    $load = $number - $i;
                }

                $i += $load;

                $records = $this->fetchUser($load);
                foreach ($records as $json) {
                    $username = str_replace('.', '_', $json['username']);
                    $name = $json['last_name'].' '.$json['first_name'];

                    while (User::where('username', '=', $username)->count()) {
                        $username = str_replace('.', '_', $json['username']).rand(10000, 10000000);
                        $json['email'] = $username.'@ask.com';
                        $name = $json['last_name'].' '.$json['first_name'];
                    }

                    $introducer = User::inRandomOrder()->first();
                    $country = $introducer->country;

                    $user = new User;
                    $user->introducer_user_id = $introducer->id;
                    $user->unilevel = $introducer->unilevel + 1;
                    $user->username = $username;
                    $user->email = $json['email'];
                    $user->name = $name;
                    $user->country_id = $country->id;
                    $user->contact_country_id = $country->id;
                    $user->contact_number = str_replace('-', '', $json['social_insurance_number']);
                    $user->country_id = $country->id;
                    $user->save();

                    $um = new UserManager($user);
                    $um->triggerRegister($introducer);

                    if (rand(1, 2) == 2) {
                        $plan = InvestmentPlan::inRandomOrder()->first();
                        $min_invest = 20000;

                        $rk = \Str::uuid();
                        $params = [
                            'code' => $plan->code,
                            'plan_name' => $plan->plan_name,
                        ];

                        $ui = new UserInvestment;
                        $ui->user_id = $user->id;
                        $ui->fillInvestmentPlan($plan);
                        $ui->total_invest = $min_invest;
                        $ui->current_balance = $min_invest;
                        $ui->status = UserInvestment::STATUS_PROCESSING;
                        $ui->related_key = $rk;
                        $ui->params = $params;
                        $ui->save();

                        $uit = new UserInvestmentTransaction;
                        $uit->user_id = $user->id;
                        $uit->user_investment_id = $ui->id;
                        $uit->investment_plan_id = $plan->id;
                        $uit->transaction_type = 1;
                        $uit->amount = $min_invest;
                        $uit->active = 0;
                        $uit->related_key = $rk;
                        $uit->params = $params;
                        $uit->save();
                    }

                    $this->info('New username '.$user->username.' created, '.$i.' of '.$number);
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

    public function fetchUser($size)
    {
        $response = Http::get('https://random-data-api.com/api/users/random_user?size='.$size);

        return $response->json();
    }
}
