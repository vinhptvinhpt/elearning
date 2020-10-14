<?php


namespace App\Repositories;


use App\MdlCourse;
use Illuminate\Http\Request;

interface IMdlCourseInterface
{
    public function changestatuscourse(Request $request);

    public function updateCourse($id, Request $request);

    public function importExcelEnrol(Request $request);

    public function getOptionalCourses(Request $request);

    public function assignOptionalCourse(Request $request);

    public function removeAssignOptionalCourse(Request $request);

}
