<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\DefaultValueBinder as MaatWebsiteDefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class DefaultValueBinder extends MaatWebsiteDefaultValueBinder
{
    /**
     * @param  Cell  $cell  Cell to bind value to
     * @param  mixed  $value  Value to bind in cell
     * @return bool
     */
    public function bindValue(Cell $cell, $value)
    {
        if (is_array($value)) {
            $value = \json_encode($value);
        } elseif (is_numeric($value)) {
            $value = (string) $value;
            //
            //            \Log::info($value);
            $cell->setValueExplicit($value, DataType::TYPE_STRING);

            //
            return true;
        } elseif ($value instanceof Carbon) {
            $cell->setValueExplicit($value->format('Y-m-d H:i:s'), DataType::TYPE_STRING);

            //
            return true;
        }

        return parent::bindValue($cell, $value);
    }
}
