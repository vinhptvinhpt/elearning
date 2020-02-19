<?php

namespace App\Exports;


use App\Invoice;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Excel;


class UsersExport implements FromCollection, Responsable
{
    /**
    * @return \Illuminate\Support\Collection
    */
    use Exportable;
    private $data_list = [];
    private $fileName = 'invoices.xlsx';

    /**
     * Optional Writer Type
     */
    private $writerType = Excel::XLSX;

    /**
     * Optional headers
     */
    private $headers = [
        'Content-Type' => 'text/csv',
    ];

    public function __construct($list = []){
        $this->data_list = $list;
    }

    public function collection()
    {
        return $this->data_list;
    }
}
