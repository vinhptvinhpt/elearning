<?php
require_once('config.php');
require_once('vendor/autoload.php');

use WindowsAzure\Common\Internal\MediaServicesSettings;
use WindowsAzure\Common\ServicesBuilder;
use WindowsAzure\MediaServices\Authentication\AzureAdClientSymmetricKey;
use WindowsAzure\MediaServices\Authentication\AzureAdTokenCredentials;
use WindowsAzure\MediaServices\Authentication\AzureAdTokenProvider;
use WindowsAzure\MediaServices\Authentication\AzureEnvironments;
use WindowsAzure\MediaServices\Models\AccessPolicy;
use WindowsAzure\MediaServices\Models\Asset;
use WindowsAzure\MediaServices\Models\Job;
use WindowsAzure\MediaServices\Models\Locator;
use WindowsAzure\MediaServices\Models\Task;
use WindowsAzure\MediaServices\Models\TaskOptions;

class VideoLibUtilities {
    public static function deleteStreamAsset($id) {
        $msg = '';
        try {
            $tenant = "quenguyeneasiatravel.onmicrosoft.com";
            $clientId = "c49b0518-fe9c-4da1-9369-d0e93d88601b";
            $clientKey = "ZtX_6~3xAAG9VF66DO2s9ha153-7_.e8oQ";
            $restApiEndpoint = "https://emedia.restv2.eastasia.media.azure.net/api/";

            // 1 - Instantiate the credentials
            $credentials = new AzureAdTokenCredentials(
                $tenant,
                new AzureAdClientSymmetricKey($clientId, $clientKey),
                AzureEnvironments::AZURE_CLOUD_ENVIRONMENT());

            // 2 - Instantiate a token provider
            $provider = new AzureAdTokenProvider($credentials);

            // 3 - Connect to Azure Media Services
            $restProxy = ServicesBuilder::getInstance()->createMediaServicesService(new MediaServicesSettings($restApiEndpoint, $provider));
            $asset = $restProxy->getAsset($id);

        } catch (\Exception $e) {
            $msg = $e->getMessage();
        }
        return $msg;
    }

    public static function getMediaLink($file)
    {
        $mezzanineFile = $file;
        try {
            $tenant = "quenguyeneasiatravel.onmicrosoft.com";
            $clientId = "c49b0518-fe9c-4da1-9369-d0e93d88601b";
            $clientKey = "ZtX_6~3xAAG9VF66DO2s9ha153-7_.e8oQ";
            $restApiEndpoint = "https://emedia.restv2.eastasia.media.azure.net/api/";

            // 1 - Instantiate the credentials
            $credentials = new AzureAdTokenCredentials(
                $tenant,
                new AzureAdClientSymmetricKey($clientId, $clientKey),
                AzureEnvironments::AZURE_CLOUD_ENVIRONMENT());

            // 2 - Instantiate a token provider
            $provider = new AzureAdTokenProvider($credentials);

            // 3 - Connect to Azure Media Services
            $restProxy = ServicesBuilder::getInstance()->createMediaServicesService(new MediaServicesSettings($restApiEndpoint, $provider));

            //$mezzanineFile = public_path('dtl.mp4');
            $sourceAsset = self::uploadFileAndCreateAsset($restProxy, $mezzanineFile);

            $encodedAsset = self::encodeToAdaptiveBitrateMP4Set($restProxy, $sourceAsset);

            $mediaLink = self::publishEncodedAsset($restProxy, $encodedAsset);
        } catch (\Exception $e) {
            $mediaLink = $e->getMessage();
        }
        return $mediaLink;
    }

    public static function uploadFileAndCreateAsset($restProxy, $mezzanineFileName)
    {
        $fileName = basename($mezzanineFileName);

        // create an empty "Asset" by specifying the name
        $asset = new Asset(Asset::OPTIONS_NONE);
        //$asset->setName('Mezzanine '.$mezzanineFileName);
        $asset->setName('Easia ' . $fileName);
        $asset = $restProxy->createAsset($asset);

        // create an Access Policy with Write permissions
        $accessPolicy = new AccessPolicy('UploadAccessPolicy');
        $accessPolicy->setDurationInMinutes(60.0);
        //$accessPolicy->setPermissions(AccessPolicy::PERMISSIONS_WRITE );
        $accessPolicy->setPermissions(AccessPolicy::PERMISSIONS_WRITE | AccessPolicy::PERMISSIONS_LIST);
        $accessPolicy = $restProxy->createAccessPolicy($accessPolicy);

        // create a SAS Locator for the Asset
        $sasLocator = new Locator($asset, $accessPolicy, Locator::TYPE_SAS);
        $sasLocator = $restProxy->createLocator($sasLocator);

        // get the mezzanine file content
        $fileContent = file_get_contents($mezzanineFileName);

        // use the 'uploadAssetFile' to perform a multi-part upload using the Block Blobs REST API storage operations
        $restProxy->uploadAssetFile($sasLocator, $fileName, $fileContent);

        // notify Media Services that the file upload operation is done to generate the asset file metadata
        $restProxy->createFileInfos($asset);

        // delete the SAS Locator (and Access Policy) for the Asset since we are done uploading files
        $restProxy->deleteLocator($sasLocator);
        $restProxy->deleteAccessPolicy($accessPolicy);

        return $asset;
    }

    public static function encodeToAdaptiveBitrateMP4Set($restProxy, $asset)
    {
        // Retrieve the latest 'Media Encoder Standard' processor version
        $mediaProcessor = $restProxy->getLatestMediaProcessor('Media Encoder Standard');

        // Create the Job; this automatically schedules and runs it
        $outputAssetName = 'Encoded ' . $asset->getName();
        $outputAssetCreationOption = Asset::OPTIONS_NONE;
        $taskBody = '<?xml version="1.0" encoding="utf-8"?><taskBody><inputAsset>JobInputAsset(0)</inputAsset><outputAsset assetCreationOptions="' . $outputAssetCreationOption . '" assetName="' . $outputAssetName . '">JobOutputAsset(0)</outputAsset></taskBody>';

        $task = new Task($taskBody, $mediaProcessor->getId(), TaskOptions::NONE);
        $task->setConfiguration('H264 Multiple Bitrate 720p');

        $job = new Job();
        $job->setName('Encoding Job');

        $job = $restProxy->createJob($job, array($asset), array($task));

        // Check to see if the Job has completed
        $result = $restProxy->getJobStatus($job);

        $jobStatusMap = array('Queued', 'Scheduled', 'Processing', 'Finished', 'Error', 'Canceled', 'Canceling');

        while ($result != Job::STATE_FINISHED && $result != Job::STATE_ERROR && $result != Job::STATE_CANCELED) {
//            echo "Job status: {$jobStatusMap[$result]}\r\n";
            sleep(10);
            $result = $restProxy->getJobStatus($job);
        }

        if ($result != Job::STATE_FINISHED) {
            return "The job has finished with a wrong status: {$jobStatusMap[$result]}\r\n";
        }


        // Get output asset
        $outputAssets = $restProxy->getJobOutputMediaAssets($job);
        $encodedAsset = $outputAssets[0];

        return $encodedAsset;
    }

    public static function publishEncodedAsset($restProxy, $encodedAsset)
    {
        // Get the .ISM AssetFile
        $files = $restProxy->getAssetAssetFileList($encodedAsset);
        $manifestFile = null;

        foreach ($files as $file) {
            if (self::endsWith(strtolower($file->getName()), '.ism')) {
                $manifestFile = $file;
            }
        }

        if ($manifestFile == null) {
            return "Unable to found the manifest file\r\n";
//            exit(-1);
        }

        // Create a 20 year read-only AccessPolicy
        $access = new AccessPolicy('Streaming Access Policy');
        $access->setDurationInMinutes(60 * 24 * 365 * 20);
        $access->setPermissions(AccessPolicy::PERMISSIONS_READ);
        $access = $restProxy->createAccessPolicy($access);

        // Create a Locator using the AccessPolicy and Asset
        $locator = new Locator($encodedAsset, $access, Locator::TYPE_ON_DEMAND_ORIGIN);
        $locator->setName('Streaming Locator');
        $locator = $restProxy->createLocator($locator);

        // Create a Smooth Streaming base URL
        $streamingUrl = $locator->getPath() . $manifestFile->getName() . '/manifest';


        return $streamingUrl;
    }

    public static function endsWith($haystack, $needle)
    {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }

        return substr($haystack, -$length) === $needle;
    }
}
