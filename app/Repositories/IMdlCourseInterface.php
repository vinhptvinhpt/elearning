<?php


namespace App\Repositories;


use App\MdlCourse;
use Illuminate\Http\Request;

interface IMdlCourseInterface
{
    public function changestatuscourse(Request $request);
}
