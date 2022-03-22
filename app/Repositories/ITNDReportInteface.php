<?php


namespace App\Repositories;

use Illuminate\Http\Request;

interface ITNDReportInteface
{
    public function getCompetencyCourse(Request $request);

    public function assignCourseCompetency(Request $request);

    public function removeAssignCourseCompetency(Request $request);
}
