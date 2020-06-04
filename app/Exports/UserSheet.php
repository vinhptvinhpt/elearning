<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;

class UserSheet implements WithTitle, WithHeadings, FromArray, WithMapping, ShouldAutoSize, WithEvents
{
    private $title;
    private $members = array();
    protected $length;

    public function __construct(string $title, $sheet_content)
    {
        $this->title = $title;
        $this->members = $sheet_content;
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
        $row = [
//            !empty($row['stt']) ? $row['stt'] : 'N/A',
            !empty($row['fullname']) ? $row['fullname'] : '',
            !empty($row['cmtnd']) ? $row['cmtnd'] : '',
            !empty($row['username']) ? $row['username'] : '',
            !empty($row['password']) ? $row['password'] : '',
            !empty($row['message']) ? $row['message'] : '',
        ];
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
        $heading = [
//            'STT',
            'Họ và tên',
            'Số chứng minh thư nhân dân',
            'Tên đăng nhập',
            'Mật khẩu',
            'Chi tiết'
        ];

        return $heading;
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
                $event->sheet->getDelegate()->getStyle('A1:E1')->applyFromArray($styleArray);
            },
        ];
    }
}
