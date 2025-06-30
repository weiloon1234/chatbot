<?php

namespace App\Console\Commands\Generate;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Log;
use ReflectionClass;

class GenerateDomainEnum extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:domain_enum';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate domain enum for VueJS loaded from model';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    // Models/methods to exclude
    protected $blacklist = [
        'models' => [
            // \App\Models\User::class,
        ],
        'methods' => [
            // \App\Models\Deposit::class => ['getSecretLists'],
        ],
    ];

    protected $translations = [];

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            ini_set('max_execution_time', 0); // 0=NOLIMIT

            $models = $this->discoverModels();
            $data = [];

            $this->translations = collect(json_decode(file_get_contents(base_path('lang/en.json')), true));

            foreach ($models as $modelClass) {
                $modelName = class_basename($modelClass);
                $methods = $this->getListMethods($modelClass);

                if (empty($methods)) {
                    $this->line("Skipping {$modelName} - no list methods found");

                    continue;
                }

                $data[$modelName] = [];

                foreach ($this->getListMethods($modelClass) as $method) {
                    try {
                        $key = $this->methodToKey($method);
                        $rawItems = $modelClass::$method();

                        $processed = [];
                        foreach ($rawItems as $k => $v) {
                            $processed[$k] = $this->extractTranslationKey($v);
                        }

                        $data[$modelName][$key] = $processed;
                    } catch (\Throwable $e) {
                        $this->error("Failed processing {$modelClass}::{$method}: ".$e->getMessage());
                    }
                }
            }

            $this->generateJsFile($data);
            $this->info('Domain Enums generated!');

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

    protected function discoverModels(): array
    {
        $modelsPath = app_path('Models');

        return collect(File::allFiles($modelsPath))
            ->map(fn ($file) => 'App\\Models\\'.$file->getFilenameWithoutExtension())
            ->filter(fn ($class) => is_subclass_of($class, Model::class))
            ->reject(fn ($class) => in_array($class, $this->blacklist['models']))
            ->values()
            ->toArray();
    }

    protected function getListMethods(string $modelClass): array
    {
        return collect((new ReflectionClass($modelClass))->getMethods())
            ->filter(fn ($method) => $method->isStatic() &&
                $method->isPublic() &&
                preg_match('/^get[A-Za-z]+Lists$/', $method->name)
                //                && $method->getNumberOfParameters() === 0 // Skip methods with parameters
            )
            ->map(fn ($method) => $method->name)
            ->reject(fn ($method) => in_array($method, $this->blacklist['methods'][$modelClass] ?? []))
            ->toArray();
    }

    protected function processList($items)
    {
        return collect($items)->mapWithKeys(function ($value, $key) {
            // Extract translation key from __() helper
            if (is_string($value) && preg_match("/__\(['\"](.+?)['\"]\)/", $value, $matches)) {
                return [(string) $key => $matches[1]];
            }

            return [(string) $key => $value];
        })->toArray();
    }

    protected function methodToKey(string $method): string
    {
        return Str::snake(
            preg_replace(['/^get/', '/Lists$/'], '', $method)
        );
    }

    protected function extractTranslationKey($value)
    {
        if (! is_string($value)) {
            return $value;
        }

        $key = $this->translations->search($value);

        if ($key !== false) {
            return $key;
        } else {
            return $value;
        }
    }

    protected function generateJsFile(array $data): void
    {
        $jsContent = "// AUTO-GENERATED DOMAIN ENUMERATIONS - DO NOT EDIT\n";
        $jsContent .= 'export default '.json_encode($data,
            JSON_PRETTY_PRINT |
            JSON_UNESCAPED_UNICODE |
            JSON_UNESCAPED_SLASHES
        ).";\n";

        file_put_contents(resource_path('scripts/domain-enums.js'), $jsContent);
    }
}
