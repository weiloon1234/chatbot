<?php

namespace App\Traits;

trait EditorJs
{
    public function editorJsSetAttribute($key, $value)
    {
        $m = 'editorJsColumns';
        if (method_exists(get_class($this), $m) && in_array($key, $this->$m())) {
            if (is_string($value)) {
                try {
                    return json_decode($value, false, 512, JSON_THROW_ON_ERROR);
                } catch (\JsonException $exception) {
                    return [];
                }
            }
        }

        return $value;
    }

    public function getEditorJsToArray()
    {
        if (method_exists($this, 'editorJsColumns') && count($this->editorJsColumns())) {
            $arr = [];
            foreach ($this->editorJsColumns() as $column) {
                if (is_array($this->{$column})) {
                    $arr[$column] = json_encode($this->{$column}, JSON_UNESCAPED_SLASHES);
                }
            }

            return $arr;
        } else {
            return [];
        }
    }

    public function toEditorJsJson()
    {
        if (method_exists($this, 'editorJsColumns') && count($this->editorJsColumns())) {
            foreach ($this->editorJsColumns() as $column) {
                if (is_array($this->{$column})) {
                    $this->{$column} = json_encode($this->{$column}, JSON_UNESCAPED_SLASHES);
                }
            }
        }
    }
}
