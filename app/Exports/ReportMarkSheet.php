<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ReportMarkSheet implements WithTitle, WithHeadings, FromArray, WithMapping, ShouldAutoSize, WithEvents
{
    use Exportable;
    private $title = '';
    private $headings = [];
    private $content = array();
    protected $column_count = 0;

    public function __construct(string $title, $sheet_content, $headings)
    {
        $this->title = $title;
        $this->content = $sheet_content;
        $this->headings = $headings;
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
        return $this->content;
    }

    public function map($row): array
    {
        $row = $this->_getRowBy($row);
        return $row;
    }

    public function _getRowBy($row) //overwrite row data if necessary
    {
        return $row;
    }

    public function headings(): array
    {
        return $this->_getHeading();
    }

    public function _getHeading()
    {
        return $this->headings;
    }

    public function registerEvents(): array
    {
        return [

            AfterSheet::class => function (AfterSheet $event) {

                $this->column_count = count($this->headings);

                $endingCol = $event->sheet->getDelegate()->getHighestColumn();
                $endingRow = $event->sheet->getDelegate()->getHighestRow();

                //Set Width
                for ($i = 1; $i <= $this->column_count; $i++) {
                    $event->sheet->getDelegate()->getColumnDimensionByColumn($i)->setWidth(20);
                }

                //Set color heading row
                $event->sheet->getDelegate()
                    ->getStyle("A1:" . $endingCol . "1")
                    ->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('E0E3E4');

                //Set font bold row heading
                $styleArray = [
                    'font' => [
                        'bold' => true
                    ],
                ];

                $event->sheet->getDelegate()->getStyle('A1:' . $endingCol . '1')->applyFromArray($styleArray);

                $event->sheet->getDelegate()->getStyle('A1:' . $endingCol . $endingRow)->getAlignment()->setVertical(Alignment::VERTICAL_TOP)->setWrapText(true);
            },
        ];
    }
}
