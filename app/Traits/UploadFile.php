<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

trait UploadFile
{
    protected function getFileStorage()
    {
        return getUploadStorage();
    }

    protected $default_allowed_image = ['png', 'jpg', 'jpeg', 'png'];

    protected $default_allowed_file = ['pdf', 'xlsx', 'xls', 'doc', 'docx'];

    /**
     * Dynamically set attributes on the model.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return string
     */
    public function uploadFileSetAttribute($key, $value)
    {
        $m = 'fileColumns';
        if (method_exists(get_class($this), $m) && array_key_exists($key, $this->$m())) {
            if ($value instanceof UploadedFile) {
                $setting = $this->$m()[$key];
                $ext = $value->guessClientExtension();
                $path = getUploadFileBasePath(null);
                if (isset($setting['path'])) {
                    if (! \Str::startsWith('/', $setting['path'])) {
                        $path .= '/';
                    }
                    $path .= $setting['path'];
                    if (! \Str::endsWith('/', $path)) {
                        $path .= '/';
                    }
                } else {
                    $path .= '/etc/';
                    if (isset($setting['rename']) && $setting['rename'] == 'id') {
                        throw new \Exception(__('Unknown error').':UPLOAD_FILE:'.$key.':ID_RENAME_MUST_SET_PATH');
                    }
                }

                $date_folder = false;

                if (isset($setting['rename'])) {
                    switch ($setting['rename']) {
                        case 'id':// FOR filename that follow ID, must model->save() to generate ID first
                            $file_name = $this->id;
                            if (! $file_name) {
                                throw new \Exception(__('Unknown error').':UPLOAD_FILE:'.$key.':NO_ID');
                            }
                            break;
                        default:
                            $file_name = generateRandomUniqueName(16);
                            $date_folder = true;
                            break;
                    }
                } else {
                    $file_name = generateRandomUniqueName(16);
                    $date_folder = true;
                }

                if ($date_folder === true) {
                    $path .= now()->format('Y/m/');
                    $file_name = now()->format('d_').$file_name;
                }

                if (! isset($file_name) || ! $file_name || mb_strlen($file_name) <= 0) {
                    throw new \Exception(__('Unknown error').':UPLOAD_FILE:'.$key.':NO_FILENAME');
                }

                $file_name = config('app.env').'_'.$file_name;

                if (isset($setting['rename_append'])) {
                    $file_name .= $setting['rename_append'];
                }

                $path = preg_replace('#/+#', '/', $path);

                // UPLOADED image, start check if need to resize
                if (substr($value->getMimeType(), 0, 5) == 'image') {
                    if (isset($setting['allow_ext'])) {
                        if ($setting['allow_ext'] != '*' && ! in_array($ext, $setting['allow_ext'])) {
                            throw new \Exception(trans('validation.mimes', ['attribute' => $key, 'values' => implode(',', $setting['allow_ext'])]));
                        }
                    } elseif (! in_array($ext, $this->default_allowed_image)) {
                        throw new \Exception($ext.'/'.trans('validation.mimes', ['attribute' => $key, 'values' => implode(',', $this->default_allowed_image)]));
                    }

                    $manager = new ImageManager(new Driver);

                    $img = $manager->read($value);

                    if (isset($setting['width']) && isset($setting['height'])) {
                        $img->resize(width: $setting['width'], height: $setting['height']);
                    } elseif (isset($setting['width']) && ! isset($setting['height'])) {
                        $img->scale(width: $setting['width']);
                    } elseif (! isset($setting['width']) && isset($setting['height'])) {
                        $img->scale(height: $setting['height']);
                    }

                    if ($ext == 'png') {
                        $this->getFileStorage()->put($path.$file_name.'.'.$ext, $img->toPng()->toFilePointer());
                    } else {
                        $this->getFileStorage()->put($path.$file_name.'.'.$ext, $img->toJpeg()->toFilePointer());
                    }
                } else {
                    if (isset($setting['allow_ext'])) {
                        if ($setting['allow_ext'] != '*' && ! in_array($ext, $setting['allow_ext'])) {
                            throw new \Exception(trans('validation.mimes', ['attribute' => $key, 'values' => implode(',', $setting['allow_ext'])]));
                        }
                    } elseif (! in_array($ext, $this->default_allowed_file)) {
                        throw new \Exception(trans('validation.mimes', ['attribute' => $key, 'values' => implode(',', $this->default_allowed_file)]));
                    }

                    $this->getFileStorage()->putFileAs($path, $value, $file_name.'.'.$ext);
                }

                return $path.$file_name.'.'.$ext.'?t='.now()->timestamp;
            } elseif (is_string($value)) {
                return $value;
            }
        } else {
            return $value;
        }
    }

    /**
     * Dynamically retrieve attributes on the model.
     *
     * @param  string  $key
     * @return mixed
     */
    public function uploadFileGetAttribute($key)
    {
        $m = 'fileColumns';
        if (method_exists(get_class($this), 'fileColumns') && array_key_exists($key, $this->$m()) && \Str::startsWith($this->getAttribute($key), getUploadFileBasePath(null))) {
            // NEED TO REMOVE LEADING SLASH
            $url = $this->getFileStorage()->url(ltrim($this->getAttribute($key), '/'));

            return rawurldecode($url);
        } else {
            return $this->getAttribute($key);
        }
    }

    public function getUploadFileToArray()
    {
        // TRIGGER GETTER FOR JSON
        foreach ($this->fileColumns() as $column => $setting) {
            $arr[$column] = $this->__get($column);
        }

        return $arr;
    }

    public function getUploadFileToJson($options = 0)
    {
        // TRIGGER GETTER FOR JSON
        foreach ($this->fileColumns() as $column => $setting) {
            $this->{$column} = $this->__get($column);
        }
    }
}
