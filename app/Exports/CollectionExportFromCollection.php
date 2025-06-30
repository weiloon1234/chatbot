<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeSheet;

class CollectionExportFromCollection implements FromCollection, ShouldAutoSize, WithCustomCsvSettings, WithEvents, WithHeadings, WithStrictNullComparison
{
    use Exportable;

    protected $model = null;

    protected $headings = [];

    protected $columns = [];

    public function __construct($model, $headings, $columns)
    {
        $this->model = $model;

        $this->columns = $columns;
        $this->headings = $headings;
    }

    public function headings(): array
    {
        return $this->headings;
    }

    public function collection()
    {
        return $this->model;
    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                $event->sheet
                    ->getPageSetup()
                    ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
            },
            AfterSheet::class => function (AfterSheet $event) {
                //                $sheet = $event->getSheet();
                //                $max_col = $sheet->getColumnDimensions();
                //                foreach ($max_col as $col => $col_details) {
                //                    $sheet->
                //                }
                //                \Log::info($max_col);
                $event->sheet->autoSize();
            },
        ];
    }

    public function getCsvSettings(): array
    {
        // Define your custom import settings for only this class
        return [
            'input_encoding' => 'UTF-8',
        ];
    }
}
