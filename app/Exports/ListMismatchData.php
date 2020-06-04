<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ListMismatchData implements WithMultipleSheets
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
            if($key == 'NhanVien')
                $sheets[] = new UserSheet($key, $sheet_content);
            else if($key == 'DaiLy')
                $sheets[] = new BranchSheet($key, $sheet_content);
            else if($key == 'DiemBanHang co giay chung nhan')
                $sheets[] = new SaleroomHaveCertificateSheet($key, $sheet_content);
            else if($key == 'DiemBanHang k giay chung nhan')
                $sheets[] = new SaleroomSheet($key, $sheet_content);
        }

        return $sheets;
    }
}
