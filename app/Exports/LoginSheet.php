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

class LoginSheet implements WithTitle, WithHeadings, FromArray, WithMapping, ShouldAutoSize, WithEvents
{

    use Exportable;
    private $title;
    private $datas = array();
    protected $length;
    protected $focus_lines = array();

    public function __construct($title, $sheet_content)
    {
        $this->title = $title;
        $this->datas = $sheet_content;
    }

    /**
     * @return array
     */
    public function array(): array
    {
        // TODO: Implement array() method.
        return $this->datas;
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        // TODO: Implement registerEvents() method.

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

                $total_rows = count($this->datas) + 1;
                $event->sheet->getDelegate()->getStyle('F1:F' . $total_rows)
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $event->sheet->getDelegate()->getStyle('A1:A' . $total_rows)
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_LEFT);

                $event->sheet->getDelegate()->getStyle('A1:F1')->applyFromArray($styleArray);
            },
        ];
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        // TODO: Implement headings() method.
        $headings = $this->_getHeading();
        $this->length = count($headings);

        return $headings;
    }

    public function _getHeading()
    {
        return [
            __('stt'),
            __('username'),
            __('ho_ten'),
            __('thoi_gian')
        ];
    }

    /**
     * @param mixed $row
     *
     * @return array
     */
    public function map($row): array
    {
        // TODO: Implement map() method.
        return $row;
    }

    /**
     * @return string
     */
    public function title(): string
    {
        // TODO: Implement title() method.
        return $this->title;
    }
}
