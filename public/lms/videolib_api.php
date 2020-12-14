<?php

require_once('config.php');
require_once('vendor/autoload.php');
require_once ('videolib_utilities.php');

use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Common\ServicesBuilder;

use Carbon\Carbon;

require_login();

require_once('config.php');
//variable
$containerName = 'asset-f8418a8e-bf70-44d8-bba0-b4c3144d7dd6';
$accountName = "elearningdata";
$accountKey = "GRC03bagorlSpRO94e40uAuM/4o+xpw5pC/g3FMYy1u9fPDtmyybjPd4m74x0Pabc8wPmCte90f/rwYV+7nJqw==";


//get
$type = isset($_POST['type']) ? $_POST['type']:'get';
$page = isset($_POST['current']) ? $_POST['current']:1;
$recordPerPage = isset($_POST['recordPerPage']) ? $_POST['recordPerPage']:1;
$nameFile = isset($_POST['nameFile']) ? $_POST['nameFile']: "";
$stream_link = isset($_POST['stream_link']) ? $_POST['stream_link']: "";
$file = isset($_FILES['file']) ? $_FILES['file'] : "";

//type = get: get list
if($type == 'get'){
    //get list videos with name in db
    $sqlGetVideos = "select name, url, stream_link from tms_videolib where deleted = 0 and user_id=".$USER->id;
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
//type = delete : delete video
else if($type == 'delete'){
    try {
        //call function delete
        $resultVid = deleteVideo($nameFile, $containerName, $accountName);
        //get stream_link
        if (strlen($stream_link) > 0) {
            $org_link = preg_replace("(^https?://)", "", $stream_link);
            $org_link_extract = explode('/', $org_link);
            $asset_id = $org_link_extract[1];
            if (strlen($asset_id) > 0) {
                //$delete_stream = VideoLibUtilities::deleteStreamAsset($asset_id);
            }
        }
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
//type = generate: generate url to video
else if($type == 'generate'){
    $getUrl = generateSASUrl($nameFile, $accountName, $containerName, $accountKey);
    echo json_encode(['url'=> $getUrl]);
}
//else is put: put name file into db after push video into azure
else{
    try {
        if(!empty($nameFile)){
            //Create live stream link, use uploaded temp file to upload, not need to save file
            $live_stream_link = VideoLibUtilities::getMediaLink($file["tmp_name"]);
            $urlVideo = $containerName."/".$nameFile;
            $sql = "INSERT INTO tms_videolib(name, url, stream_link, user_id, deleted) VALUES('".$nameFile."', '".$urlVideo."', '".$live_stream_link."', ".$USER->id.", 0)";
            $DB->execute($sql);
            $res['result'] = 1;
        }
    }catch (Exception $e) {
        $res['result'] = 0;
    }
    echo json_encode($res);
    die;
}

//delete video with name and container name
function deleteVideo($name, $containerName, $accountName){
    $conn = 'BlobEndpoint=https://'.$accountName.'.blob.core.windows.net/;QueueEndpoint=https://elearningdata.queue.core.windows.net/;FileEndpoint=https://elearningdata.file.core.windows.net/;TableEndpoint=https://elearningdata.table.core.windows.net/;SharedAccessSignature=sv=2019-02-02&ss=bfqt&srt=sco&sp=rwdlacup&se=2040-04-06T14:56:12Z&st=2020-04-06T06:56:12Z&spr=https&sig=q87j3KR6ZAThNolTZSAOCuVkWoUbwtn%2B47sXkp2OXx8%3D';
    $blobRes = ServicesBuilder::getInstance()->createBlobService($conn);
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

function generateSASUrl($blob_name, $accountName, $container, $key){
    $end_date = Carbon::now()->addHour(23)->addMinute(58);

    $end_date = gmdate('Y-m-d\TH:i:s\Z', strtotime($end_date));

//    $key = 'GRC03bagorlSpRO94e40uAuM/4o+xpw5pC/g3FMYy1u9fPDtmyybjPd4m74x0Pabc8wPmCte90f/rwYV+7nJqw==';

    $_signature = getSASForBlob($accountName, $container, $blob_name, 'b', 'r', $end_date, $key);
    $_blobUrl = getBlobUrl($accountName, $container, $blob_name, 'b', 'r', $end_date, $_signature);
    return $_blobUrl;
}





function getSASForBlob($accountName, $container, $blob, $resourceType, $permissions, $expiry, $key)
{

    /* Create the signature */
    $_arraysign = array();
    $_arraysign[] = $permissions;
    $_arraysign[] = '';
    $_arraysign[] = $expiry;
    $_arraysign[] = '/' . $accountName . '/' . $container . '/' . $blob;
    $_arraysign[] = '';
    $_arraysign[] = "2014-02-14"; //the API version is now required
    $_arraysign[] = '';
    $_arraysign[] = '';
    $_arraysign[] = '';
    $_arraysign[] = '';
    $_arraysign[] = '';

    $_str2sign = implode("\n", $_arraysign);

    return base64_encode(
        hash_hmac('sha256', urldecode(utf8_encode($_str2sign)), base64_decode($key), true)
    );
}

function getBlobUrl($accountName, $container, $blob, $resourceType, $permissions, $expiry, $_signature)
{
    /* Create the signed query part */
    $_parts = array();
    $_parts[] = (!empty($expiry)) ? 'se=' . urlencode($expiry) : '';
    $_parts[] = 'sr=' . $resourceType;
    $_parts[] = (!empty($permissions)) ? 'sp=' . $permissions : '';
    $_parts[] = 'sig=' . urlencode($_signature);
    $_parts[] = 'sv=2014-02-14';

    /* Create the signed blob URL */
    $_url = 'https://'
        . $accountName . '.blob.core.windows.net/'
        . $container . '/'
        . $blob . '?'
        . implode('&', $_parts);

    return $_url;
}


