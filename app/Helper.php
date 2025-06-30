<?php

function fundFormat($value, $decimal = \App\Config::DECIMAL_POINT)
{
    return number_format(round($value, $decimal, PHP_ROUND_HALF_DOWN), $decimal, '.', ',');
}

function isEmpty($value)
{
    return $value === null || $value === '' || $value === 'null';
}

function notEmpty($value)
{
    return ! isEmpty($value);
}

function sanitizeUsername($value)
{
    return mb_strtolower(trim($value));
}

function sanitizePassword($password)
{
    if (\Hash::needsRehash($password)) {
        $password = bcrypt($password);
    }

    return $password;
}

function pushFlash($key, $value)
{
    $values = \Session::get($key, []);
    $values[] = $value;
    \Session::flash($key, $values);
}

function addError($msg)
{
    if (! is_array($msg)) {
        pushFlash('flash_error', $msg);
    } else {
        foreach ($msg as $key => $var) {
            if (is_array($var)) {
                foreach ($var as $k => $v) {
                    pushFlash('flash_error', $v);
                }
            } else {
                pushFlash('flash_error', $var);
            }
        }
    }
}

function addInfo($msg)
{
    if (! is_array($msg)) {
        pushFlash('flash_info', $msg);
    } else {
        foreach ($msg as $key => $var) {
            if (is_array($var)) {
                foreach ($var as $k => $v) {
                    pushFlash('flash_info', $v);
                }
            } else {
                pushFlash('flash_info', $var);
            }
        }
    }
}

function addSuccess($msg)
{
    if (! is_array($msg)) {
        pushFlash('flash_success', $msg);
    } else {
        foreach ($msg as $key => $var) {
            if (is_array($var)) {
                foreach ($var as $k => $v) {
                    pushFlash('flash_success', $v);
                }
            } else {
                pushFlash('flash_success', $var);
            }
        }
    }
}

function makeResponseErrorForField($field, $message)
{
    return makeResponse(false, __('Something wrong please check red field'), [
        'errors' => [
            $field => [$message],
        ],
    ]);
}

function makeResponse($code, $message = null, $data = [])
{
    if ($message instanceof \Exception) {
        $debug = config('env.APP_DEBUG');
        if ($debug === true) {
            $message = $message->getFile().' / '.$message->getLine().' . '.$message->getMessage();
        } else {
            $message = $message->getMessage();
        }
    }

    if ($code === true) {
        $code = 200;
    } elseif ($code === false) {
        $code = 422;
    }

    if (request()->ajax() || request()->wantsJson()) {
        if ($message != null) {
            $data['message'] = $message;
        } else {
            switch ($code) {
                case 422:
                    $data['message'] = __('Something went wrong');
                    break;
                case 200:
                    $data['message'] = __('Operation success');
                    break;
                default:
                    $data['message'] = __('Unknown error');
                    break;
            }
        }

        return response()->json($data)->setStatusCode($code);
    } else {
        switch ($code) {
            case 200:
                if ($message != null) {
                    addSuccess($message);
                } else {
                    addSuccess(__('Operation success'));
                }
                break;
            default:
                if ($message != null) {
                    addError($message);
                } else {
                    addError(__('Something went wrong'));
                }

                if (isset($data['errors'])) {
                    foreach ($data['errors'] as $field => $data1) {
                        if (is_array($data1)) {
                            foreach ($data1 as $data2) {
                                addError($data2);
                            }
                        } else {
                            addError($data1);
                        }
                    }
                }
                break;
        }

        return redirect()->back()->withInput();
    }
}

function getContactNumberExtension()
{
    return collect(\App\Models\Country::getCountriesFromCache())->pluck('ext', 'id')->toArray();
}

function getCountry()
{
    return collect(\App\Models\Country::getCountriesFromCache())->pluck('name', 'id')->toArray();
}

function routeName()
{
    return request()->route()->getName();
}

function splitListKeyToString($arr)
{
    return implode(',', array_keys($arr));
}

function getYesNoForSelect()
{
    return [
        0 => __('No'),
        1 => __('Yes'),
    ];
}

/**
 * @return array[
 *  'default_column' => string,
 *  'column' => string,
 *  'locale' => string,
 *  'text' => string,
 *  'is_default' => boolean
 *  'is_current' => boolean,
 * ]
 */
function loopLanguageForColumn(string $column)
{
    $arr = [];
    foreach (config('app.locales') as $locale => $text) {
        $arr[] = [
            'default_column' => $column.'_'.config('app.locale'),
            'column' => $column.'_'.$locale,
            'locale' => $locale,
            'text' => $text,
            'is_default' => config('app.locale') == $locale,
            'is_current' => app()->getLocale() == $locale,
        ];
    }

    return $arr;
}

function touchFolder($path)
{
    $path = public_path($path);

    if (! file_exists($path)) {
        mkdir($path, 0777, true);
    }
}

function generateRandomUniqueName($length = 40)
{
    return \Str::random($length);
}

function generateRandomHtmlId($length = 20)
{
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }

    return $randomString;
}

function getUploadFileBasePath($path)
{
    if ($path != null && \Str::startsWith($path, '/')) {
        $path = ltrim($path, '/');
    }

    return 'cuploads/'.$path;
}

/**
 * @return \Illuminate\Contracts\Filesystem\Filesystem
 */
function getUploadStorage()
{
    return \Illuminate\Support\Facades\Storage::disk('r2');
}

function isValidDate(string $date, string $format = 'Y-m-d'): bool
{
    $dateObj = DateTime::createFromFormat($format, $date);

    return $dateObj && $dateObj->format($format) == $date;
}

function getClass($class, $double_back_slash = false)
{
    $s = get_class($class);
    if ($double_back_slash) {
        $s = str_replace('\\', '\\\\', $s);
    }

    return $s;
}

function getAssetManifest($role)
{
    return cache()->sear($role.'_asset_manifest', function () use ($role) {
        return json_decode(file_get_contents(
            public_path('build/'.$role.'/.vite/manifest.json')
        ), true);
    });
}
