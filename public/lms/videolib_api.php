<?php

require_once('config.php');
require_once('vendor/autoload.php');

use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Common\ServicesBuilder;

require_login();


require_once('config.php');

$type = isset($_POST['type']) ? $_POST['type']:'get';
$page = isset($_POST['current']) ? $_POST['current']:1;
$recordPerPage = isset($_POST['recordPerPage']) ? $_POST['recordPerPage']:1;
$nameFile = isset($_POST['nameFile']) ? $_POST['nameFile']: "";
if($type == 'get'){
    $sqlGetVideos = "select name, url from tms_videolib where deleted = 0 and user_id=".$USER->id;
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
else if($type == 'delete'){
    try {
        $resultVid = deleteVideo($nameFile);
        if($resultVid == 1){
            $sql = "delete from tms_videolib where name = '".$nameFile."'";
            $DB->execute($sql);
        }else{
            $resultVid = 0;
        }
    }catch (Exception $e) {
        $resultVid = 0;
    }
    echo json_encode(['result'=> $resultVid]);
}
else{
    try {
        if(!empty($nameFile)){
            $urlVideo = "asset-f8418a8e-bf70-44d8-bba0-b4c3144d7dd6/".$nameFile;
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


function deleteVideo($name){
    $conn = 'BlobEndpoint=https://elearningdata.blob.core.windows.net/;QueueEndpoint=https://elearningdata.queue.core.windows.net/;FileEndpoint=https://elearningdata.file.core.windows.net/;TableEndpoint=https://elearningdata.table.core.windows.net/;SharedAccessSignature=sv=2019-02-02&ss=bfqt&srt=sco&sp=rwdlacup&se=2040-04-06T14:56:12Z&st=2020-04-06T06:56:12Z&spr=https&sig=q87j3KR6ZAThNolTZSAOCuVkWoUbwtn%2B47sXkp2OXx8%3D';

    $blobRes = ServicesBuilder::getInstance()->createBlobService($conn);
    $containerName = 'asset-f8418a8e-bf70-44d8-bba0-b4c3144d7dd6';
    try {
        $blobRes->deleteBlob($containerName, $name);
        return 1;
    } catch (ServiceException $e) {
        $code = $e->getCode();
        $error_message = $e->getMessage();
        echo $code . ": " . $error_message . "<br />";
        return 0;
    }

}
