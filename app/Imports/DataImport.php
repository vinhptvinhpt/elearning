<?php

namespace App\Imports;

use App\User;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsUnknownSheets;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithConditionalSheets;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class DataImport implements ToModel, WithCalculatedFormulas, WithColumnFormatting, WithMultipleSheets
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    use Importable;
    use WithConditionalSheets;

    public function model(array $row)
    {
        return new User([
            //
        ]);
    }
    public function columnFormats(): array
    {
        return [
            'I' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }

    //
    public function sheets(): array
    {
        return [
            'NhanVien' => new FirstSheetImport(),
            'DaiLy' => new FirstSheetImport(),
            'DiemBanHang co giay chung nhan' => new FirstSheetImport(),
            'DiemBanHang k giay chung nhan' => new FirstSheetImport(),
            'DS tạo tk' => new FirstSheetImport(),
            'Sheet1' => new FirstSheetImport(),
        ];
    }

    public function conditionalSheets(): array
    {
        return [
            'NhanVien' => new FirstSheetImport(),
            'DaiLy' => new FirstSheetImport(),
            'DS tạo tk' => new FirstSheetImport(),
        ];
    }
}
class FirstSheetImport implements SkipsUnknownSheets
{
    public function onUnknownSheet($sheetName)
    {
        // E.g. you can log that a sheet was not found.
        info("Sheet {$sheetName} was skipped");
    }
}

