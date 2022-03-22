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

class ReportSheet implements WithTitle, WithHeadings, FromArray, WithMapping, ShouldAutoSize, WithEvents
{
    use Exportable;
    private $title;
    private $selected_level;
    private $members = array();
    protected $length;
    protected $focus_lines = array();

    public function __construct(string $title, $selected_level, $sheet_content)
    {
        $this->title = $title;
        $this->selected_level = $selected_level;
        $this->members = $sheet_content;

        foreach ($sheet_content as $stt => $item) {
            if ($selected_level == "district" || $selected_level == "city") {
                if (strlen($item[0]) != 0 && !is_numeric($item[0])) {
                    $this->focus_lines[] = $stt+2;
                }
            } else {
                if (strlen($item[0]) != 0) {
                    $this->focus_lines[] = $stt+2;
                }
            }
        }
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
        return $this->members;
    }

    public function map($row): array
    {
        $row = $this->_getRowBy($row);
        return $row;
    }

    public function _getRowBy($row) //overwrite row data if necessary
    {

//        $row = [
//            !empty($row->attribute) ? $row->attribute : 'N/A',
//        ...
//        ];

        return $row;
    }

    public function headings(): array
    {
        $headings = $this->_getHeading();
        $this->length = count($headings);

        return $headings;
    }

    public function _getHeading()
    {
        switch ($this->selected_level) {
            case 'city':
                $level = __('dai_ly');
                break;
            case 'district':
                $level = __('tinh_thanh');
                break;
            default:
                $level = __('diem_ban');
        }

        return [
            __('stt'),
            $level,
            __('chua_hoan_thanh_dao_tao'),
            __('da_hoan_thanh_dao_tao'),
            __('da_co_giay_chung_nhan'),
            __('tong_so'),
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                for ($i = 1; $i <= $this->length; $i++) {
                    $event->sheet->getDelegate()->getColumnDimensionByColumn($i)->setWidth(20);
                }
                $styleArray = [
                    'font' => [
                        'bold' => true
                    ],
                ];

                foreach ($this->focus_lines as $line) {
                    $event->sheet->getDelegate()->getStyle("A$line:F$line")
                        ->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setARGB('FFE8E5E5');
                    $event->sheet->getDelegate()->getStyle("A$line:F$line")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                }

                $total_rows = count($this->members) + 1;
                $event->sheet->getDelegate()->getStyle('F1:F' . $total_rows)
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $event->sheet->getDelegate()->getStyle('A1:A' . $total_rows)
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_LEFT);

                $event->sheet->getDelegate()->getStyle('A1:F1')->applyFromArray($styleArray);
                //$event->sheet->getDelegate()->getStyle('A1:F1')->getAlignment()->setVertical(Alignment::VERTICAL_TOP)->setWrapText(true);
            },
        ];
    }
}
