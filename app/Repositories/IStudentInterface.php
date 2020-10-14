<?php


namespace App\Repositories;

use Illuminate\Http\Request;

interface IStudentInterface
{
    public function checkImageExist(Request $request);
}
