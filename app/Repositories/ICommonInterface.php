<?php


namespace App\Repositories;


use Illuminate\Http\Request;

interface ICommonInterface
{
    public function getall(Request $request);

    public function store(Request $request);

    public function update(Request $request);

    public function delete($id);

    public function detail($id);
}
