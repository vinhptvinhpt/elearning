<?php


namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\BussinessRepository;

class StudentController extends Controller
{

    private $bussinessRepository;

    public function __construct(BussinessRepository $bussinessRepository)
    {
        $this->bussinessRepository = $bussinessRepository;
    }

    //view list students get certificate
    public function viewStudentUncertificate()
    {
        return view('education.student_uncertificate');
    }

    public function viewEditCertificate($id)
    {
        return view('education.edit_certificate', ['id' => $id]);
    }

    public function viewStudentsCertificate()
    {
        return view('education.student_certificate');
    }

    public function apiListStudentsUncertificate(Request $request)
    {
        return $this->bussinessRepository->apiListStudentsUncertificate($request);
    }
    public function apiGenerateSelectedUser(Request $request)
    {
        return $this->bussinessRepository->apiGenerateSelectedUser($request);
    }
    public function generateCodeCertificate(Request $request)
    {
        return $this->bussinessRepository->generateCodeCertificate($request);
    }
    public function randomNumber($length)
    {
        return $this->bussinessRepository->randomNumber($length);
    }
    public function apiListStudentsCertificate(Request $request)
    {
        return $this->bussinessRepository->apiListStudentsCertificate($request);
    }
    public function sendMail($user, $certificatecode)
    {
        return $this->bussinessRepository->sendMail($user, $certificatecode);
    }
    public function settingCertificate()
    {
        return $this->bussinessRepository->settingCertificate();
    }
    public function apiGetListImagesCertificate()
    {
        return $this->bussinessRepository->apiGetListImagesCertificate();
    }
    public function apiCreateCertificate(Request $request)
    {
        return $this->bussinessRepository->apiCreateCertificate($request);
    }
    public function apiDelete($id)
    {
        return $this->bussinessRepository->apiDeleteStudent($id);
    }
    public function apiDetailCertificate(Request $request)
    {
        return $this->bussinessRepository->apiDetailCertificate($request);
    }
    public function apiUpdateCertificate(Request $request)
    {
        return $this->bussinessRepository->apiUpdateCertificate($request);
    }
    public function apiAutoGenCertificate()
    {
        return $this->bussinessRepository->apiAutoGenCertificate();
    }
    public function autoGenCertificate($user_id)
    {
        return $this->bussinessRepository->autoGenCertificate($user_id);
    }
}
