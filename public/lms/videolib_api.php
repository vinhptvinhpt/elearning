<?php

require_once('config.php');
require_once('vendor/autoload.php');


require_once('config.php');

$type = isset($_POST['type']) ? $_POST['type']:'get';
$page = isset($_POST['current']) ? $_POST['current']:1;
$recordPerPage = isset($_POST['recordPerPage']) ? $_POST['recordPerPage']:1;
$nameFile = isset($_POST['nameFile']) ? $_POST['nameFile']: "";
if($type == 'get'){
    $sqlGetVideos = "select name, url from tms_videolib where deleted = 0";
    $videos = array_values($DB->get_records_sql($sqlGetVideos));
    //paging
    $total = count($videos); //total items in array
    $limit = $recordPerPage; //per page
    $totalPages = ceil( $total/ $limit ); //calculate total pages
    $page = max($page, 1); //get 1 page when $_GET['page'] <= 0
    $page = min($page, $totalPages); //get last page when $_GET['page'] > $totalPages
    $offset = ($page - 1) * $limit;
    if( $offset < 0 ) $offset = 0;
    $yourDataArray = array_slice($videos, $offset, $limit );
    echo json_encode(['videos'=> $yourDataArray, 'totalPage' => $totalPages]);
}
else{
    try {
        if(!empty($nameFile)){
            $urlVideo = $containerName."/".$nameFile;
            $sql = "INSERT INTO tms_videolib(name, url, user_id, deleted) VALUES('".$nameFile."', '".$urlVideo."', ".$USER->id.", 0)";
            $DB->execute($sql);
            $res['result'] = 1;
        }
    }catch (Exception $e) {
        $res['result'] = 0;
    }
    echo json_encode($res);
    die;
}
