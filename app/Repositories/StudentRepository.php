<?php


namespace App\Repositories;


use App\ViewModel\ResponseModel;
use Illuminate\Http\Request;

class StudentRepository implements IStudentInterface
{
    /**
     * Check certificate image exists
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkImageExist(Request $request)
    {
        // TODO: Implement checkImageExist() method.
        $reponse = new ResponseModel();
        try {
            $code = $request->input('code');
            $cer_path = storage_path() . '/app/public/upload/certificate/' . $code . '_certificate.jpeg';
            $badge_path = storage_path() . '/app/public/upload/certificate/' . $code . '_badge.jpeg';
            if (file_exists($cer_path)) {
                $reponse->existCertificate = 1;
            }

            if (file_exists($badge_path)) {
                $reponse->existBadge = 1;
            }

            $reponse->status = true;


        } catch (\Exception $e) {
            $reponse->status = false;
            $reponse->existBadge = 0;
            $reponse->existCertificate = 0;
        }
        return response()->json($reponse);
    }
}
