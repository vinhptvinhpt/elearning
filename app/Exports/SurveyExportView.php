<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;


class SurveyExportView implements FromView
{
    public $dataResult;

    public function __construct($dataResult)
    {
        $this->dataResult = $dataResult;
    }

    public function view(): View
    {
        return View('survey.export_excel', ['dataModel' => $this->dataResult]);
    }
}
