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

class ReportDetailRawSheet implements WithTitle, WithHeadings, FromArray, WithMapping, ShouldAutoSize, WithEvents
{
    use Exportable;
    private $title = '';
    private $content = array();
    private $mode = '';
    private $type = '';
    protected $column_count = 4;

    public function __construct(string $title, $sheet_content, $mode, $type)
    {
        $this->title = $title;
        $this->content = $sheet_content;
        $this->mode = $mode;
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
        if ($this->mode == 'learning_time') { //Convert seconds to hours
            $row[8] = round($row[8]/3600, 4);
        }
        //array_pop($row); // remove column type when return
        return $row;
    }

    public function headings(): array
    {
        return $this->_getHeading();
    }

    public function _getHeading()
    {
        if ($this->mode == 'completed_training') {
            $heading =  [
                'No',
                'Name',
                'Email',
                'Department',
                'Country',
                'Office',
                'Competency framework',
                'Completed',
            ];
        } elseif ( $this->mode == 'completed_course') {
            $heading =  [
                'No',
                'Name',
                'Email',
                'Department',
                'Country',
                'Office',
                'Competency framework',
                'Course',
                'Completed',
            ];
        } elseif ($this->mode == 'certificated') {
            $heading =  [
                'No',
                'Name',
                'Email',
                'Department',
                'Country',
                'Office',
                'Competency framework',
                'Certificated',
            ];
        } elseif ($this->mode == 'learning_time') {
            $heading =  [
                'No',
                'Name',
                'Email',
                'Department',
                'Country',
                'Office',
                'Competency framework',
                'Course',
                'Actual learning hours',
                'Training duration'
            ];
        } else {
            $heading =  [];
        }

//        if ($this->type == 'single_course') {
//            $competency_heading_key = array_search('Competency framework', $heading);
//            unset($heading[$competency_heading_key]);
//        }

        return $heading;
    }

    public function registerEvents(): array
    {
        return [

            AfterSheet::class => function (AfterSheet $event) {
                $endingCol = 'A';
                if ($this->mode == 'certificated' || $this->mode == 'completed_training') {
                    $this->column_count = 8;
                    $endingCol = 'H';
                } elseif ($this->mode == 'completed_course') {
                    $this->column_count = 9;
                    $endingCol = 'I';
//                    if ($this->type == 'single_course') {
//                        $this->column_count = 8;
//                        $endingCol = 'H';
//                    }
                } elseif ($this->mode == 'learning_time') {
                    $this->column_count = 10;
                    $endingCol = 'J';
//                    if ($this->type == 'single_course') {
//                        $this->column_count = 9;
//                        $endingCol = 'I';
//                    }
                }

                //Set Width
                for ($i = 1; $i <= $this->column_count; $i++) {
                    $event->sheet->getDelegate()->getColumnDimensionByColumn($i)->setWidth(20);
                }

                //Set color heading row
                $event->sheet->getDelegate()->getStyle("A1:" . $endingCol . "1")
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
                $event->sheet->getDelegate()->getStyle('A1:' . $endingCol . '1')
                    ->applyFromArray($styleArray);

            },
        ];
    }
}
