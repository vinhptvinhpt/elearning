<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;

class InvitationSheet implements WithTitle, FromArray, ShouldAutoSize, WithEvents
{
    use Exportable;
    private $title;
    private $data_list = array();

    public function __construct(string $title, $sheet_content)
    {
        $this->title = $title;
        $this->data_list = $sheet_content;
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return $this->title;
    }

    public function array(): array
    {
        return $this->data_list;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $styleArray = [
                    'font' => [
                        'bold' => true
                    ],
                ];
                $event->sheet->getDelegate()->getStyle('A1:E1')->applyFromArray($styleArray);
            },
        ];
    }
}
