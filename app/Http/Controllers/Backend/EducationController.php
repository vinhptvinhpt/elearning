<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\ModelHasRole;
use Illuminate\Http\Request;
use App\Repositories\BussinessRepository;

class EducationController extends Controller
{
    
    private $bussinessRepository;

    public function __construct(BussinessRepository $bussinessRepository)
    {
        $this->bussinessRepository = $bussinessRepository;
    }

    protected $keyword = '';

    public function viewIndexTeacher()
    {
        return view('education.index_teacher');
    }

    public function viewIndexStudent()
    {
        return view('education.index_student');
    }

    public function viewEditTeacher($user_id)
    {
        $redirect = redirect_accout_organize($user_id);
        if($redirect)
            return abort('401');
        $role = ModelHasRole::with('role')->where('model_id',$user_id)->first();
        return view('education.edit_teacher', [
            'user_id' => $user_id,
            'role_name' => $role['role']['name']
        ]);
    }

    public function viewEditDetailTeacher($user_id)
    {
        $redirect = redirect_accout_organize($user_id);
        if($redirect)
            return abort('401');
        $role = ModelHasRole::with('role')->where('model_id',$user_id)->first();
        return view('education.edit_detail_teacher', [
            'user_id' => $user_id,
            'role_name' => $role['role']['name']
        ]);
    }

    public function viewEditStudent($user_id)
    {
        $redirect = redirect_accout_organize($user_id);
        if($redirect)
            return abort('401');
        $role = ModelHasRole::with('role')->where('model_id',$user_id)->first();
        return view('education.edit_student', [
            'user_id' => $user_id,
            'role_name' => $role['role']['name']
        ]);
    }

    public function viewEditDetailStudent($user_id)
    {
        $redirect = redirect_accout_organize($user_id);
        if($redirect)
            return abort('401');
        $role = ModelHasRole::with('role')->where('model_id',$user_id)->first();
        return view('education.edit_detail_student', [
            'user_id' => $user_id,
            'role_name' => $role['role']['name']
        ]);
    }

    public function viewTrashUserTeacher(){
        return view('education.user_teacher_trash');
    }

    public function viewTrashUserStudent(){
        return view('education.user_student_trash');
    }

    public function apiListUserTeacher(Request $request)
    {
        return $this->bussinessRepository->apiListUserTeacher($request);
    }

    public function apiListUserStudent(Request $request)
    {
        return $this->bussinessRepository->apiListUserStudent($request);
    }

    public function apiListUserTeacherTrash(Request $request)
    {
        return $this->bussinessRepository->apiListUserTeacherTrash($request);
    }

    public function apiListUserStudentTrash(Request $request)
    {
        return $this->bussinessRepository->apiListUserStudentTrash($request);
    }
}
