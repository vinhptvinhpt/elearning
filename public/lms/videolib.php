<?php

require_once('config.php');
require_once('vendor/autoload.php');

use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Common\ServicesBuilder;

require_login();

$params = array();
$PAGE->set_context($context);
$PAGE->set_url('/videolib.php');
$PAGE->set_pagelayout('mydashboard');
$PAGE->set_pagetype('my-index');
$PAGE->set_title(get_string('videolibrary'));
$PAGE->set_heading($header);

echo $OUTPUT->header();

$list_video = getListVideoAzure();
$additional_param = "?sv=2017-04-17&sr=c&si=746d77ad-7266-4fc9-a957-9bfbac043930&sig=nFSTSk7bpjBou7LVrrqETZdxWOfYXXq4%2Bkyp6mJ53U8%3D&st=2020-03-06T12%3A07%3A20Z&se=2120-03-06T12%3A07%3A20Z";

?>

<div class="container">
    <div class="view-account">
        <section class="module">
            <div class="module-inner">
                <div class="content-panel">
                    <div class="content-header-wrapper">
                        <h2 class="title"><i class="fa fa-video-camera"></i> VIDEOS LIBRARY</h2>
                    </div>
                    <div class="content-utilities">
                        <div class="page-nav">
                            <span class="indicator">View:</span>
                            <div class="btn-group" role="group">
                                <button class="active btn btn-default" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Grid View" id="drive-grid-toggle"><i class="fa fa-th-large"></i></button>
                                <button class="btn btn-default" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="List View" id="drive-list-toggle"><i class="fa fa-list-ul"></i></button>
                            </div>
                        </div>
                        <div class="actions">
                            <div class="btn-group">
                                <button class="btn btn-default dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false"><i class="fa fa-filter"></i> Sorting <span class="caret"></span></button>
                                <ul class="dropdown-menu">
                                    <li><a href="#">Newest first</a></li>
                                    <li><a href="#">Oldest first</a></li>
                                </ul>
                            </div>
                            <!-- <div class="btn-group" role="group">
                                <button type="button" class="btn btn-default" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Refresh"><i class="fa fa-refresh"></i></button>
                                <button type="button" class="btn btn-default" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Delete"><i class="fa fa-trash-o"></i></button>
                            </div> -->
                        </div>
                    </div>
                    <div class="drive-wrapper drive-grid-view">
                        <?php foreach ($list_video as $video) {
                        ?>
                            <div class="grid-items-wrapper">
                                <div class="drive-item module text-center">
                                    <div class="drive-item-inner module-inner">
                                        <div class="drive-item-title"><a href="#"><?= $video->getName() ?></a></div>
                                        <div class="drive-item-thumb">
                                            <a href=""><i class="fa fa-file-video-o text-warning"></i></a>
                                        </div>
                                    </div>
                                    <div class="drive-item-footer module-footer">
                                        <ul class="utilities list-inline">
                                            <li><a href="#" data-toggle="tooltip" data-placement="top" title="" data-original-title="Copy link" onclick="copyToClipboard('<?= $video->getUrl() . $additional_param ?>')"><i class="fa fa-external-link"></i></a></li>
                                            <!-- <li><a href="#" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"><i class="fa fa-trash"></i></a></li> -->
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>

                    <!-- <div class="drive-wrapper drive-list-view"> 
                         <div class="table-responsive drive-items-table-wrapper">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th class="type"></th>
                                        <th class="name truncate">Name</th>
                                        <th class="date">Uploaded</th>
                                        <th class="size">Size</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="type"><i class="fa fa-file-text-o text-primary"></i></td>
                                        <td class="name truncate"><a href="#">Meeting Notes.txt</a></td>
                                        <td class="date">Sep 23, 2015</td>
                                        <td class="size">18 KB</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div> -->
                    <form id="form_upload_file" action="videolib.php" method="post" enctype="multipart/form-data">
                        Select File to upload:
                        <input accept="video/*" type="file" name="fileazure" class="form-control" id="fileazure" multiple style="display: inline-block;width: 300px; margin: 10px;height: auto;">
                        <!-- <input type="submit" value="Upload file" name="submit"> -->
                        <button class="btn btn-success" type="submit" name="submit"><i class="fa fa-plus"></i> Upload file</button>
                    </form>
                </div>
            </div>
        </section>
    </div>
</div>

<!-- <script src="http://netdna.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script> -->
<script type="text/javascript">
    function copyToClipboard(text) {
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val(text).select();
        document.execCommand("copy");
        $temp.remove();

        return false;
    }
</script>
</body>

<?php

// testSDKAzure();

echo $OUTPUT->footer();

if (isset($_FILES['fileazure'])) {
    $errors = array();
    $file_name = $_FILES['fileazure']['name'];
    $file_size = $_FILES['fileazure']['size'];
    $file_tmp = $_FILES['fileazure']['tmp_name'];
    $file_type = $_FILES['fileazure']['type'];
    $file_ext = strtolower(end(explode('.', $_FILES['fileazure']['name'])));

    $extensions = array("webm", "mp4", "ogv");

    if (in_array($file_ext, $extensions) === false) {
        $errors[] = "extension not allowed, please choose a video file.";
    }

    if ($file_size > 509715200) {
        $errors[] = 'File size must be excately 2 MB';
    }

    if (empty($errors) == true) {
        uploadVideoToAzure($_FILES['fileazure']);
    } else {
        echo "error";
    }
}

function uploadVideoToAzure($filetoUpload)
{
    $accesskey = "GRC03bagorlSpRO94e40uAuM/4o+xpw5pC/g3FMYy1u9fPDtmyybjPd4m74x0Pabc8wPmCte90f/rwYV+7nJqw==";
    $storageAccount = 'elearningdata';
    //        $filetoUpload = realpath('./bg_login.png');
    //        $filetoUpload = public_path() . '/images/bg_login.png';
    // $filetoUpload = public_path() . '/eldata.mp4';
    // echo ('path<br/>');
    // echo ($filetoUpload . '<br/>');
           $containerName = 'asset-f8418a8e-bf70-44d8-bba0-b4c3144d7dd6';
    // $containerName = 'elearning';
    $blobName = $filetoUpload['name'];
    // $blobName = "sample.mp4";

    $destinationURL = "https://$storageAccount.blob.core.windows.net/$containerName/$blobName";


    $currentDate = gmdate("D, d M Y H:i:s T", time());
    $handle = fopen($filetoUpload['tmp_name'], "r");
    // $handle = fopen("C:\xampp_new\htdocs\lms\sample.mp4", "r");
    // $fileLen = filesize($blobName);
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
    $arraysign[] = $urlResource;        /*CanonicalizedResource*/

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
    // 
    // curl_setopt(
    //     $ch,
    //     CURLOPT_POSTFIELDS,
    //     array(
    //       'file' =>
    //           '@'            . $filetoUpload['tmp_name']
    //           . ';filename=' . $filetoUpload['file']['name']
    //           . ';type='     . $filetoUpload['file']['type']
    //     ));
    curl_setopt($ch, CURLOPT_INFILE, $handle);
    curl_setopt($ch, CURLOPT_INFILESIZE, $fileLen);
    curl_setopt($ch, CURLOPT_UPLOAD, true);
    $result = curl_exec($ch);

    // echo('Result<br/>');
    // echo($result . '<br/>');

    // echo('Error<br/>');
    echo(curl_error($ch));

    curl_close($ch);
}

function getListVideoAzure()
{
    $conn = 'BlobEndpoint=https://elearningdata.blob.core.windows.net/;QueueEndpoint=https://elearningdata.queue.core.windows.net/;FileEndpoint=https://elearningdata.file.core.windows.net/;TableEndpoint=https://elearningdata.table.core.windows.net/;SharedAccessSignature=sv=2019-02-02&ss=bfqt&srt=sco&sp=rwdlacup&se=2040-04-06T14:56:12Z&st=2020-04-06T06:56:12Z&spr=https&sig=q87j3KR6ZAThNolTZSAOCuVkWoUbwtn%2B47sXkp2OXx8%3D';

    $blobRes = ServicesBuilder::getInstance()->createBlobService($conn);
    $containerName = 'asset-f8418a8e-bf70-44d8-bba0-b4c3144d7dd6';
    //        $containerName = 'elearning';

    try {
        // List blobs.
        $blob_list = $blobRes->listBlobs($containerName);
        $blobs = $blob_list->getBlobs();
        return $blobs;
        // echo "~~~~~~~~~~~~~~~~Blob list~~~~~~~~~~~~~~~~~~ <br />";
        // foreach ($blobs as $blob) {
        //     echo $blob->getName() . ": " . $blob->getUrl() . ": " . json_encode($blob->getProperties()) . ": " . $blob->getSnapshot() . "<br />";
        //     //                echo $blob->getProperties() . ": " . $blob->getSnapshot();
        // }
        // echo "~~~~~~~~~~~~~~~~~~~END~~~~~~~~~~~~~~~~~~~~~ <br />";
    } catch (ServiceException $e) {
        $code = $e->getCode();
        $error_message = $e->getMessage();
        echo $code . ": " . $error_message . "<br />";
    }
}
