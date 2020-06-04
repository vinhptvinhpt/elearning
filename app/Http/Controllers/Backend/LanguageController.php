<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\BussinessRepository;


class LanguageController extends Controller
{
    private $bussinessRepository;

    public function __construct(BussinessRepository $bussinessRepository)
    {
        $this->bussinessRepository = $bussinessRepository;
    }

    public function applicationSetLocale(Request $request, $lang)
    {
        $request->session()->put('lang', $lang);
    }

    public function getInfoSidebar()
    {
        return $this->bussinessRepository->getInfoSidebar();
    }
}
