<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ListMismatchSaleroom implements WithMultipleSheets
{
    use Exportable;

    private $members = array();

    public function __construct($members)
    {
        $this->members = $members;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];
        foreach ($this->members as $key=>$sheet_content) {
            $sheets[] = new SaleroomSheet($key, $sheet_content);
        }

        return $sheets;
    }
}
