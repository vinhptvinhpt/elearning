<?php

require_once('config.php');
require_once('vendor/autoload.php');

use Illuminate\Http\Request;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Common\ServicesBuilder;
use MicrosoftAzure\Storage\Blob\BlobRestProxy;

require_once('config.php');

$type = isset($_POST['type']) ? $_POST['type']:'get';
$page = isset($_POST['current']) ? $_POST['current']:1;
$recordPerPage = isset($_POST['recordPerPage']) ? $_POST['recordPerPage']:1;
$file = $_FILES['file'];
$containerNameGet = 'asset-f8418a8e-bf70-44d8-bba0-b4c3144d7dd6';

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
        uploadVideoToAzure($file);
        $nameFile = $_FILES["file"]["name"];
        $urlVideo = $containerNameGet."/".$nameFile;
        $sql = "INSERT INTO tms_videolib(name, url, user_id, deleted) VALUES('".$nameFile."', '".$urlVideo."', ".$USER->id.", 0)";
        $DB->execute($sql);
        $res['a'] = $sql;
    }
    catch (Exception $e) {
    }
    echo json_encode($res);
    die;
}

//upload
function uploadVideoToAzure($filetoUpload)
{
    try {
        $accesskey = "GRC03bagorlSpRO94e40uAuM/4o+xpw5pC/g3FMYy1u9fPDtmyybjPd4m74x0Pabc8wPmCte90f/rwYV+7nJqw==";
        $storageAccount = 'elearningdata';
        $containerName = 'asset-f8418a8e-bf70-44d8-bba0-b4c3144d7dd6';
        $blobName = $filetoUpload['name'];
        $destinationURL = "https://$storageAccount.blob.core.windows.net/$containerName/$blobName";
        $currentDate = gmdate("D, d M Y H:i:s T", time());
        $handle = fopen($filetoUpload['tmp_name'], "r");
        $fileLen = $filetoUpload['size'];
        $headerResource = "x-ms-blob-cache-control:max-age=3600\nx-ms-blob-type:BlockBlob\nx-ms-date:$currentDate\nx-ms-version:2015-12-11";
        $urlResource = "/$storageAccount/$containerName/$blobName";
        $arraysign = array();
        $arraysign[] = 'PUT';               /*HTTP Verb*/
        $arraysign[] = '';                  /*Content-Encoding*/
        $arraysign[] = '';                  /*Content-Language*/
        $arraysign[] = $fileLen;            /*Content-Length (include value when zero)*/
        $arraysign[] = '';                  /*Content-MD5*/
        //        $arraysign[] = 'image/png';         /*Content-Type*/
        $arraysign[] = 'video/mp4';         /*Content-Type*/
        $arraysign[] = '';                  /*Date*/
        $arraysign[] = '';                  /*If-Modified-Since */
        $arraysign[] = '';                  /*If-Match*/
        $arraysign[] = '';                  /*If-None-Match*/
        $arraysign[] = '';                  /*If-Unmodified-Since*/
        $arraysign[] = '';                  /*Range*/
        $arraysign[] = $headerResource;     /*CanonicalizedHeaders*/
        $arraysign[] = $urlResource;
        /*CanonicalizedResource*/
        $str2sign = implode("\n", $arraysign);

        $sig = base64_encode(hash_hmac('sha256', urldecode(utf8_encode($str2sign)), base64_decode($accesskey), true));
        $authHeader = "SharedKey $storageAccount:$sig";

        $headers = [
            'Authorization: ' . $authHeader,
            'x-ms-blob-cache-control: max-age=3600',
            'x-ms-blob-type: BlockBlob',
            'x-ms-date: ' . $currentDate,
            'x-ms-version: 2015-12-11',
            'Content-Type: video/mp4',
            'Content-Length: ' . $fileLen
        ];

        $ch = curl_init($destinationURL);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, "PUT");
        curl_setopt($ch, CURLOPT_INFILE, $handle);
        curl_setopt($ch, CURLOPT_INFILESIZE, $fileLen);
        curl_setopt($ch, CURLOPT_UPLOAD, true);
        curl_exec($ch);
        curl_close($ch);
    }catch (Exception $e) {
        echo $e->getMessage();
        die;
    }
}
