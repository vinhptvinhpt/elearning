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

class ReportDetailSheet implements WithTitle, WithHeadings, FromArray, WithMapping, ShouldAutoSize, WithEvents
{
    use Exportable;
    private $title = '';
    private $content = array();
    private $type = '';
    protected $column_count = 4;

    public function __construct(string $title, $sheet_content, $type)
    {
        $this->title = $title;
        $this->content = $sheet_content;
        $this->type = $type;
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
        array_pop($row);
        return $row; // remove column type when return
    }

    public function headings(): array
    {
        return $this->_getHeading();
    }

    public function _getHeading()
    {
        if ($this->type == 'completed_training' || $this->type == 'completed_course') {
            $heading =  [
                __('ten'),
                __('da_hoan_thanh_dao_tao'),
                __('chua_hoan_thanh_dao_tao'),
                __('tong_so'),
            ];
        } elseif ($this->type == 'certificated') {
            $heading =  [
                __('ten'),
                __('da_hoan_thanh_dao_tao'),
                __('chua_hoan_thanh_dao_tao'),
                __('tong_so'),
            ];
        } elseif ($this->type == 'learning_time') {
            $heading =  [
                __('ten'),
                __('thoi_gian_hoc'),
                __('so_nguoi_tham_gia'),
                __('thoi_luong_hoc'),
            ];
        } else {
            $heading =  [
                __(''),
                __(''),
                __(''),
                __(''),
            ];
        }

        return $heading;
    }

    public function registerEvents(): array
    {
        return [

            AfterSheet::class => function (AfterSheet $event) {

                //Set Width
                for ($i = 1; $i <= $this->column_count; $i++) {
                    $event->sheet->getDelegate()->getColumnDimensionByColumn($i)->setWidth(20);
                }

                //Chú thích
                $event->sheet->getDelegate()->getColumnDimensionByColumn(6)->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimensionByColumn(7)->setWidth(20);

                $event->sheet->getDelegate()->setCellValue('G1', __('to_chuc'));
                $event->sheet->getDelegate()->getStyle("F1")
                    ->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('E0E3E4');

                $event->sheet->getDelegate()->setCellValue('G2', __('khung_nang_luc'));
                $event->sheet->getDelegate()->getStyle("F2")
                    ->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('5BBFDE');

                $event->sheet->getDelegate()->setCellValue('G3', __('khoa_hoc'));
                $event->sheet->getDelegate()->getStyle("F3")
                    ->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('D1FFE9');

                //Duyệt content và set style
                foreach ($this->content as $stt => $line) {

                    $color = 'FFFFFF';

                    $line_number = $stt + 2;

                    if ($line[4] == 'organization') {
                        $color = 'E0E3E4';
                    } elseif ($line[4] == 'training') {
                        $color = '5BBFDE';
                    } elseif ($line[4] == 'courses'){
                        $color = 'D1FFE9';
                    }

                    if ($line[4] != 'users') { //Set màu cho các row tổ chức, khung năng lực, khóa học
                        $event->sheet->getDelegate()->getStyle("A$line_number:D$line_number")
                            ->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setARGB($color);
                    } else { //Enable xuống dòng cho row users
                        $event->sheet->getDelegate()->getStyle("A$line_number:D$line_number")
                            ->getAlignment()
                            ->setVertical(Alignment::VERTICAL_TOP)
                            ->setWrapText(true);
                    }

                    $event->sheet->getDelegate()->getStyle("A$line_number:D$line_number")
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_LEFT);

                }
                $total_rows = count($this->content) + 1;

                //Set alignment left
                $event->sheet->getDelegate()->getStyle('D1:D' . $total_rows)
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_LEFT)
                    ->setVertical(Alignment::VERTICAL_TOP);
                $event->sheet->getDelegate()->getStyle('A1:A' . $total_rows)
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_LEFT)
                    ->setVertical(Alignment::VERTICAL_TOP);

                $event->sheet->getDelegate()->getStyle('G1:G' . $total_rows)
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_LEFT)
                    ->setVertical(Alignment::VERTICAL_TOP);

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
