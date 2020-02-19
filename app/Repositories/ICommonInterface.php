<?php


namespace App\Repositories;


interface ICommonInterface
{
    public function getall($keyword, $page, $pageSize);

    public function insert(object $table);

    public function update($id);

    public function delete($id);
}
