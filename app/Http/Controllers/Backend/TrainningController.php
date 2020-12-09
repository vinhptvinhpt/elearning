<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Repositories\TrainningRepository;
use App\TmsOrganizationEmployee;
use App\TmsTrainningGroup;
use Config;
use Illuminate\Http\Request;
use App\Repositories\BussinessRepository;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use mod_lti\local\ltiservice\response;
use WindowsAzure\Common\Internal\MediaServicesSettings;
use WindowsAzure\MediaServices\Authentication\AzureAdClientSymmetricKey;
use WindowsAzure\MediaServices\Authentication\AzureAdTokenCredentials;
use WindowsAzure\MediaServices\Authentication\AzureAdTokenProvider;
use WindowsAzure\MediaServices\Authentication\AzureEnvironments;
use WindowsAzure\MediaServices\MediaServicesRestProxy;
use WindowsAzure\MediaServices\Models\AccessPolicy;
use WindowsAzure\MediaServices\Models\Asset;
use WindowsAzure\Common\ServicesBuilder;
use WindowsAzure\Common\ServiceException;
use WindowsAzure\MediaServices\Models\Job;
use WindowsAzure\MediaServices\Models\Locator;
use WindowsAzure\MediaServices\Models\Task;
use WindowsAzure\MediaServices\Models\TaskOptions;

class TrainningController extends Controller
{
    private $bussinessRepository;
    private $trainningRepository;

    public function __construct(BussinessRepository $bussinessRepository, TrainningRepository $trainningRepository)
    {
        $this->bussinessRepository = $bussinessRepository;
        $this->trainningRepository = $trainningRepository;
    }

    private $keyword;
    private $trainning_id;

    //view hiển thị danh sách khung nang luc
    //ThoLD (12/11/2019)
    public function viewIndex()
    {
        return view('trainning.index');
    }

    public function viewCreate()
    {
        return view('trainning.create');
    }

    public function viewDetail($id)
    {
        return view('trainning.detail', ['trainning_id' => $id]);
    }

    public function viewTrainningListUser()
    {
        return view('trainning.list_user');
    }

    //lay danh sach khoa hoc mau chua co trong khung nang luc
    public function apiGetListSampleCourse(Request $request)
    {
        return $this->trainningRepository->apiGetListSampleCourse($request);
    }

    //lay danh sach khoa hoc mau da co trong khung nang luc
    public function apiGetCourseSampleTrainning(Request $request)
    {
        return $this->trainningRepository->apiGetCourseSampleTrainning($request);
    }

    //api lấy danh sách khóa học khung năng lực type = 2
    public function apiGetListTrainingCourse(Request $request)
    {
        return $this->trainningRepository->apiGetListTrainingCourse($request);
    }

    public function apiSaveOrder(Request $request)
    {
        return $this->trainningRepository->apiSaveOrder($request);
    }

    public function apiGetListTrainning(Request $request)
    {
        return $this->trainningRepository->apiGetListTrainning($request);
    }

    public function apiGetListTrainingForFilter()
    {
        return $this->trainningRepository->apiGetListTrainingForFilter();
    }

    public function apiCreateTrainning(Request $request)
    {
        return $this->trainningRepository->store($request);
    }

    public function apiGetDetailTrainning($id)
    {
        return $this->trainningRepository->apiGetDetailTrainning($id);
    }

    public function apiEditTrainning(Request $request)
    {
        return $this->trainningRepository->update($request);
    }

    public function apiDeteleTrainning($id)
    {
        return $this->trainningRepository->delete($id);
    }

    //them khoa hoc vao khung nang luc
    public function apiAddCourseTrainning(Request $request)
    {
        return $this->trainningRepository->apiAddCourseTrainning($request);
    }

    //them khoa hoc vao khung nang luc
    public function apiAddCourseToTraining(Request $request)
    {
        return $this->trainningRepository->apiAddCourseToTraining($request);
    }


    //xoa khoa hoc khoi khung nang luc
    public function apiRemoveCourseTrainning(Request $request)
    {
        return $this->trainningRepository->apiRemoveCourseTrainning($request);
    }

    public function apiRemoveCourseFromTraining(Request $request)
    {
        return $this->trainningRepository->apiRemoveCourseFromTraining($request);
    }

    public function apiTrainningListUser(Request $request)
    {
        return $this->trainningRepository->apiTrainningListUser($request);
    }

    public function apiTrainningList(Request $request)
    {
        return $this->bussinessRepository->apiTrainningList($request);
    }

    public function apiTrainningChange(Request $request)
    {
        return $this->bussinessRepository->apiTrainningChange($request);
    }

    public function apiTrainningRemove(Request $request)
    {
        return $this->trainningRepository->apiTrainningRemove($request);
    }

    public function apiUpdateUserTrainning($trainning_id)
    {
        return $this->bussinessRepository->apiUpdateUserTrainning($trainning_id);
    }

    public function apiUpdateStudentTrainning($trainning_id)
    {
        return $this->bussinessRepository->apiUpdateStudentTrainning($trainning_id);
    }

    public function apiUpdateUserMarket($trainning_id)
    {
        return $this->bussinessRepository->apiUpdateUserMarket($trainning_id);
    }

    public function apiUpdateUserMarketCourse($course_id)
    {
        return $this->bussinessRepository->apiUpdateUserMarketCourse($course_id);
    }

    public function apiUpdateUserBGT()
    {
        return $this->bussinessRepository->apiUpdateUserBGT();
    }

    public function apiGetUsersOutTrainning(Request $request)
    {
        return $this->trainningRepository->apiGetUsersOutTranning($request);
    }

    public function apiAddUserToTrainning(Request $request)
    {
        return $this->trainningRepository->apiAddUserToTrainning($request);
    }

    public function apiAddUserOrganiToTrainning(Request $request)
    {
        return $this->trainningRepository->apiAddUserOrganiToTrainning($request);
    }

    public function apiRemoveMultiUser(Request $request)
    {
        return $this->trainningRepository->removeMultiUser($request);
    }


    public function testAPI()
    {
//        $tbl1 = '(select toe.organization_id, toe.user_id,tor.parent_id from tms_organization_employee toe
//                 join tms_organization tor on tor.id = toe.organization_id
//                 order by tor.parent_id, toe.id) ttoe';
//
//        $tbl2 = '(select @pv := 2) initialisation';
//
//        $tbl = $tbl1 . ',' . $tbl2;
//        $tbl = DB::raw($tbl);

//        $unionTbl = '(select toe.organization_id,toe.user_id from tms_organization_employee toe where toe.organization_id = 2)';
//        $unionTbl = TmsOrganizationEmployee::where('organization_id', 2)->select('organization_id', 'user_id');
//        $unionTbl = DB::table('tms_organization_employee as toe')->where('toe.organization_id','=',2)
//            ->select('toe.organization_id','toe.user_id');

//        $users = DB::table($tbl)->whereRaw('find_in_set(ttoe.parent_id, @pv)')
//            ->whereRaw('length(@pv := concat(@pv, \',\', ttoe.organization_id))')
//            ->union($unionTbl)
//            ->select('ttoe.organization_id', 'ttoe.user_id')->get();

//        $users = TmsTrainningGroup::select('trainning_id', 'group_id', 'type', DB::raw('count(trainning_id) as total_tr'))->groupBy('trainning_id')->get();

        $tblQuery = '(select  ttoe.organization_id,
       ttoe.user_id
        from    (select toe.organization_id, toe.user_id,tor.parent_id from tms_organization_employee toe
         join tms_organization tor on tor.id = toe.organization_id
         order by tor.parent_id, toe.id) ttoe,
        (select @pv := 2) initialisation
        where   find_in_set(ttoe.parent_id, @pv)
        and     length(@pv := concat(@pv, \',\', ttoe.organization_id))
        UNION
        select   toe.organization_id,toe.user_id from tms_organization_employee toe where toe.organization_id = 2
        ) as org_us';

        $tblQuery = DB::raw($tblQuery);

        $leftJoin = '(select user_id, trainning_id from tms_traninning_users  where trainning_id = 9) ttu';
        $leftJoin = DB::raw($leftJoin);

        $users = DB::table($tblQuery)->leftJoin($leftJoin, 'ttu.user_id', '=', 'org_us.user_id')
            ->whereNull('ttu.trainning_id')
            ->pluck('org_us.user_id')->toArray();

        return response()->json($users);
    }


    public function getMediaLink()
    {
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

            $mezzanineFile = public_path('dtl.mp4');
            $sourceAsset = $this->uploadFileAndCreateAsset($restProxy, $mezzanineFile);

            $encodedAsset = $this->encodeToAdaptiveBitrateMP4Set($restProxy, $sourceAsset);
            $mediaLink = $this->publishEncodedAsset($restProxy, $encodedAsset);
        } catch (\Exception $e) {
            $mediaLink = $e->getMessage();
        }
        return $mediaLink;
    }

    public function uploadFileAndCreateAsset($restProxy, $mezzanineFileName)
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

    public function encodeToAdaptiveBitrateMP4Set($restProxy, $asset)
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

    public function publishEncodedAsset($restProxy, $encodedAsset)
    {
        // Get the .ISM AssetFile
        $files = $restProxy->getAssetAssetFileList($encodedAsset);
        $manifestFile = null;

        foreach ($files as $file) {
            if ($this->endsWith(strtolower($file->getName()), '.ism')) {
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
        $stremingUrl = $locator->getPath() . $manifestFile->getName() . '/manifest';


        return $stremingUrl;
    }

    public function endsWith($haystack, $needle)
    {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }

        return substr($haystack, -$length) === $needle;
    }
}
