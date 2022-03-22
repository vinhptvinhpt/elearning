<?php


namespace App\Repositories;


use Illuminate\Http\Request;

interface IMdlCourseInterface
{
    public function apiDeleteCourse(Request $request);

    public function apiGetListCourseSample();

    public function apiCloneCourse(Request $request);

    public function apiGetListCourseConcen(Request $request);

    public function apiGetListCourseRestore(Request $request);

    public function apiRestoreCourse(Request $request);

    public function apiUserCurrentEnrol(Request $request);

    public function apiUserNeedEnrol(Request $request);

    public function apiEnrolUser(Request $request);

    public function apiRemoveEnrolUser(Request $request);

    public function apiGetTotalActivityCourse(Request $request);

    public function apiStatisticUserInCourse(Request $request);

    public function apiListAttendanceUsers(Request $request);

    public function apiDeleteEnrolNotUse();

    public function importFile();

    public function apiImportQuestion(Request $request);



    public function changestatuscourse(Request $request);

    public function updateCourse($id, Request $request);

    public function importExcelEnrol(Request $request);

    public function getOptionalCourses(Request $request);

    public function assignOptionalCourse(Request $request);

    public function removeAssignOptionalCourse(Request $request);

    public function apiGetExistedCodeLibraries();

    public function cloneCourseLibrary(Request $request);

}
