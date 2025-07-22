<?php

namespace App\Models;

use App\Traits\EditorJs;
use App\Traits\HasAuditTrail;
use App\Traits\UploadFile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @mixin IdeHelperBaseModel
 */
class BaseModel extends Model
{
    use HasAuditTrail;

    public bool $disabled_audit_trail = false;

    public function getAuditTrailDescription(): string
    {
        $c = explode('\\', get_class($this));

        return end($c).'(#'.$this->id.')';
    }

    public function __call($method, $args)
    {
        if (Str::startsWith($method, 'explain')) {
            $name = substr(Str::camel($method), 7);
            $field = Str::snake($name);

            $m = 'get'.ucfirst(substr(Str::camel($method), 7)).'Lists';
            if (method_exists(get_class($this), $m)) {
                $arr = $this->$m(...$args);

                return isset($arr[$this->$field]) ? $arr[$this->$field] : __('Unknown');
            } else {
                return parent::__call($method, $args);
            }
        } elseif (Str::startsWith($method, 'translate')) {
            $attr = Str::snake(substr($method, 9));

            $locale = app()->getLocale();
            if (isset($this->attributes[mb_strtolower($attr.'_'.$locale)])) {
                return $this->attributes[mb_strtolower($attr.'_'.$locale)];
            }

            $default_locale = config('app.locale');
            if (isset($this->attributes[mb_strtolower($attr.'_'.$default_locale)])) {
                return $this->attributes[mb_strtolower($attr.'_'.$default_locale)];
            }

            return parent::__call($method, $args);
        } else {
            return parent::__call($method, $args);
        }
    }

    public function setParamsAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['params'] = json_encode($value, JSON_UNESCAPED_SLASHES);
        } elseif (is_array(json_decode($value, true))) {
            $this->attributes['params'] = $value;
        } elseif (is_null($value)) {
            $this->attributes['params'] = null;
        } else {
            $this->attributes['params'] = json_encode($value, JSON_UNESCAPED_SLASHES);
        }
    }

    public function getParamsAttribute($value)
    {
        if (! $value) {
            return [];
        }
        $params = json_decode($value, true);
        foreach ($params as $key => $var) {
            if (is_array($var)) {
                unset($params[$key]);
            }
        }
        //        if (!is_array($value)) {
        //            $params = [];
        //        }

        return $params;
    }

    public function setAttribute($key, $value)
    {
        $cc = Str::studly($key);
        $method = 'get'.$cc.'Lists';
        if (method_exists(get_called_class(), $method)) {
            $arr = $this->$method();
            if (! array_key_exists($value, $arr)) {
                if (method_exists(get_called_class(), 'ignoreInvalidAttributes')) {
                    $ignore = $this->ignoreInvalidAttributes();
                } else {
                    $ignore = [];
                }

                if (! in_array($cc, $ignore)) {
                    throw new \Exception(__('Invalid attribute', ['attribute' => $cc]));
                }
            }
        }

        parent::setAttribute($key, $value);
    }

    public function __set($key, $value)
    {
        if (in_array(UploadFile::class, array_keys((new \ReflectionClass($this))->getTraits()))) {
            $value = $this->uploadFileSetAttribute($key, $value);
        }

        if (in_array(EditorJs::class, array_keys((new \ReflectionClass($this))->getTraits()))) {
            $value = $this->editorJsSetAttribute($key, $value);
        }

        parent::__set($key, $value);
    }

    public function __get($key)
    {
        $get = false;
        $data = null;
        if (in_array(UploadFile::class, class_uses_recursive($this))) {
            $get = true;
            $data = $this->uploadFileGetAttribute($key);
        }

        if ($get) {
            return $data;
        } else {
            return $this->getAttribute($key);
        }
    }

    public function toArray()
    {
        $arr = parent::toArray();
        if (in_array(UploadFile::class, class_uses_recursive($this))) {
            $arr = array_merge($arr, $this->getUploadFileToArray());
        }

        if (in_array(EditorJs::class, class_uses_recursive($this))) {
            $arr = array_merge($arr, $this->getEditorJsToArray());
        }

        return $arr;
    }

    public function toJson($options = 0)
    {
        if (in_array(UploadFile::class, class_uses_recursive($this))) {
            $this->getUploadFileToJson($options);
        }

        if (in_array(EditorJs::class, class_uses_recursive($this))) {
            $this->toEditorJsJson();
        }

        return parent::toJson($options);
    }
}
