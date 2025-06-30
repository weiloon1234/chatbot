<?php

namespace App\Console\Commands\Generate;

use Illuminate\Console\Command;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Log;

class ProgressiveWebApp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:pwa';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate PWA manifest and image';

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

            $default = $this->getDefault();

            $name = $this->ask('Name of the app?', $default['name']);
            $short_name = $this->ask('Short name of the app?', $default['short_name']);
            $description = $this->ask('Description?', $default['description']);

            $display = $this->ask('Display mode? fullscreen, standalone (recommended), minimal-ui or browser only', $default['display']);
            if (! in_array($display, ['fullscreen', 'standalone', 'minimal-ui', 'browser'])) {
                throw new \Exception('Invalid display mode');
            }

            $scope = $this->ask('Scope?', $default['scope']);
            $start_url = $this->ask('Start url?', $default['start_url']);

            $theme_color = $this->ask('Theme color?', $default['theme_color']);
            $background_color = $this->ask('Background color?', $default['background_color']);

            $need_generate_icon = $this->ask('Do you want to generate icons? yes or no', 'yes');
            $generate_icon = false;
            if (in_array(mb_strtoupper($need_generate_icon), ['YES', 'Y'])) {
                $generate_icon = true;
            }

            $all_sizes = [
                16,
                32,
                36,
                48,
                72,
                96,
                128,
                144,
                152,
                167,
                180,
                192,
                196,
                228,
                270,
                320,
                480,
                512,
            ];

            if ($generate_icon) {
                $app_icon_path = $this->ask('Icon path? icon must be 512x512px and transparent background if want to add background color, put it in public folder', 'img/logo.png');
                $manager = new ImageManager(new Driver);
                $base_icon = $manager->read(public_path($app_icon_path));

                if ($base_icon->width() != 512 || $base_icon->height() != 512) {
                    throw new \Exception('Image provided not 512x512px');
                }

                $icon_background_color = null;
                if (in_array(mb_strtoupper($this->ask('Transparent background? yes or no', 'no')), ['NO', 'N'])) {
                    $icon_background_color = $this->ask('Icon background color?', $theme_color);
                    $transparent_background = false;
                } else {
                    $transparent_background = true;
                }

                touchFolder('icons');

                foreach ($all_sizes as $size) {
                    $manager = new ImageManager(new Driver);
                    $icon = $manager->read(public_path($app_icon_path));

                    $this->generateIcon($icon, $size, $size, $transparent_background, $icon_background_color)
                        ->save(base_path('public/icons/logo-'.$size.'x'.$size.'.png'));
                }
            }

            $final_arr = [];
            $final_arr['name'] = $name;
            $final_arr['short_name'] = $short_name;
            if ($description) {
                $final_arr['description'] = $description;
            }
            $final_arr['display'] = $display;
            $final_arr['scope'] = $scope;
            $final_arr['start_url'] = $start_url;
            $final_arr['theme_color'] = $theme_color;
            $final_arr['background_color'] = $background_color;
            $final_arr['icons'] = [];

            foreach ($all_sizes as $size) {
                $final_arr['icons'][] = [
                    'src' => '/icons/logo-'.$size.'x'.$size.'.png',
                    'sizes' => $size.'x'.$size,
                    'type' => 'image/png',
                ];

                if ($size == 512) {
                    $final_arr['icons'][] = [
                        'src' => '/icons/logo-'.$size.'x'.$size.'.png',
                        'sizes' => $size.'x'.$size,
                        'type' => 'image/png',
                        'purpose' => 'any',
                    ];

                    $final_arr['icons'][] = [
                        'src' => '/icons/logo-'.$size.'x'.$size.'.png',
                        'sizes' => $size.'x'.$size,
                        'type' => 'image/png',
                        'purpose' => 'maskable',
                    ];
                }
            }

            file_put_contents(public_path('/manifest.json'), json_encode($final_arr, JSON_PRETTY_PRINT));
            file_put_contents(public_path('/site.webmanifest'), json_encode($final_arr, JSON_PRETTY_PRINT));

            $msg = sprintf('Successfully '.$this->signature.' at %s', \Carbon\Carbon::now()->format('Y-m-d H:i:s'));
            Log::info($msg);

            if (app()->runningInConsole()) {
                $this->comment(PHP_EOL.$msg.PHP_EOL);
            } else {
                return makeResponse(true, $msg);
            }
        } catch (\Exception $e) {
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

    public function generateIcon(\Intervention\Image\Image $img, int $width, int $height, bool $transparent_background, string $background_color): \Intervention\Image\Image
    {
        $img = $img->resize($width, $height);

        if (! $transparent_background) {
            $manager = new ImageManager(new Driver);
            $bg = $manager->create($width, $height)->fill($background_color);
            $img = $bg->place($img->resize(bcmul($width, 0.9), bcmul($height, 0.9)), 'center');
        }

        return $img;
    }

    public function getDefault()
    {
        if (file_exists(base_path('/public/manifest.json'))) {
            $current_data = json_decode(file_get_contents(base_path('/public/manifest.json')), true);
        } else {
            $current_data = [];
        }

        return [
            'name' => $current_data['name'] ?? config('env.APP_NAME'),
            'short_name' => $current_data['short_name'] ?? mb_strtoupper(preg_replace('/[^A-Za-z0-9-_]/', '', $current_data['name'] ?? config('env.APP_NAME'))),
            'description' => $current_data['description'] ?? null,
            'display' => $current_data['display'] ?? 'standalone',
            'scope' => $current_data['scope'] ?? '/',
            'start_url' => $current_data['start_url'] ?? '/',
            'theme_color' => $current_data['theme_color'] ?? '#ffffff',
            'background_color' => $current_data['background_color'] ?? '#000000',
        ];
    }
}
