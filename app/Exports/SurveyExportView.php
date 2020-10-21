<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;


class SurveyExportView implements FromView
{
    public $dataResult;
    public $header;
    public $survey;

    public function __construct($dataResult, $header, $survey)
    {
        $this->dataResult = $dataResult;
        $this->header = $header;
        $this->survey = $survey;
    }

    public function view(): View
    {
        return View('survey.export_excel', ['dataModel' => $this->dataResult, 'header' => $this->header, 'survey' => $this->survey]);
    }
}
