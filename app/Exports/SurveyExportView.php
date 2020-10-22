<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;


class SurveyExportView implements FromView
{
    public $dataResult;
    public $header;
    public $survey;
    public $course_info;

    public function __construct($dataResult, $header, $survey, $course_info)
    {
        $this->dataResult = $dataResult;
        $this->header = $header;
        $this->survey = $survey;
        $this->course_info = $course_info;
    }

    public function view(): View
    {
        return View('survey.export_excel', ['dataModel' => $this->dataResult, 'header' => $this->header, 'survey' => $this->survey, 'course_info' => $this->course_info]);
    }
}
