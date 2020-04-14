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

class ImportResultSheet implements WithTitle, WithHeadings, FromArray, WithMapping, ShouldAutoSize, WithEvents
{
    use Exportable;
    private $title = '';
    private $content = array();
    protected $column_count = 4;

    public function __construct(string $title, $sheet_content)
    {
        $this->title = $title;
        $this->content = $sheet_content;
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
        return $row; // remove column type when return
    }

    public function headings(): array
    {
        return $this->_getHeading();
    }

    public function _getHeading()
    {
        return [
            __('stt'),
            __('ten'),
            __('trang_thai'),
            __('thong_tin'),
        ];
    }

    public function registerEvents(): array
    {
        return [

            AfterSheet::class => function (AfterSheet $event) {

                //Set Width
                for ($i = 1; $i <= $this->column_count; $i++) {
                    $event->sheet->getDelegate()->getColumnDimensionByColumn($i)->setWidth(20);
                }


                //Duyệt content và set style
                foreach ($this->content as $line) {

                    $color = 'FFFFFF';

                    $stt = $line[0] + 1;

                    if ($line[2] == 'success') {
                        $color = '5BBFDE';
                    } elseif ($line[2] == 'error') {
                        $color = 'E0E3E4';
                    }

                    //Set màu cho các row
                    $event->sheet->getDelegate()->getStyle("A$stt:D$stt")
                        ->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB($color);

                    //Enable xuống dòng cacs rows
                    $event->sheet->getDelegate()->getStyle("A$stt:D$stt")
                        ->getAlignment()
                        ->setVertical(Alignment::VERTICAL_TOP)
                        ->setWrapText(true);


                    //Căn lề left, top
                    $event->sheet->getDelegate()->getStyle("A$stt:D$stt")
                        ->getAlignment()
                        ->setVertical(Alignment::VERTICAL_TOP)
                        ->setHorizontal(Alignment::HORIZONTAL_LEFT);

                }

                //Set font bold row heading
                $styleArray = [
                    'font' => [
                        'bold' => true
                    ],
                ];
                $event->sheet->getDelegate()->getStyle('A1:D1')
                    ->applyFromArray($styleArray);

                //$event->sheet->getDelegate()
                //->getStyle('A1:D1')
                //->getAlignment()
                //->setVertical(Alignment::VERTICAL_TOP)
                //->setWrapText(true);

            },
        ];
    }
}
