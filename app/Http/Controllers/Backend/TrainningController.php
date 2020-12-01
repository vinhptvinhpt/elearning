<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Repositories\TrainningRepository;
use App\TmsOrganizationEmployee;
use App\TmsTrainningGroup;
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

    public function getAllRoute()
    {
//        $arrData = [];
//        $routeCollection = Route::getRoutes();

        //dd($routeCollection->getRoutes()[250]);

//        foreach ($routeCollection as $value) {
//            $data_item['url'] = $value->uri();
//            $data_item['method'] = $value->getName();
//            array_push($arrData,$data_item);
//        }

//        $arrData = [];
//        $routeCollection = Route::getRoutes()->getRoutes();
//
////        dd($routeCollection[250]);
//
//        foreach ($routeCollection as $value) {
//            if (array_key_exists('middleware', $value->action)) {
//                if (in_array('clearance', $value->action['middleware']) && strpos($value->uri, 'locale') == false) {
//
//                    $data_item['url'] = $value->uri;
//                    $data_item['method'] = $value->methods;
//                    $data_item['action'] = $value->action['middleware'];
//                    array_push($arrData, $data_item);
//
//                }
//            }
//        }
//        return response()->json($arrData);
        $data = updateFlagCron('enroll_trainning.json', 'write', 'stop');
        $data = json_decode($data, true);
        if ($data['flag'] == 'stop') {
            return 'stop';
        }
        return 'start';
        return response()->json($data);
    }

    public function testMediaAzure()
    {
        try {
            $tenant = "quenguyeneasiatravel.onmicrosoft.com";
            $username = 'quenguyen@easia-travel.net';
            $password = 'EasiaTVC-2020';
            $clientId = "c49b0518-fe9c-4da1-9369-d0e93d88601b";
            $clientKey = "ZtX_6~3xAAG9VF66DO2s9ha153-7_.e8oQ";
            $restApiEndpoint = "https://emedia.restv2.eastasia.media.azure.net/api/";
//        $pfxFileName = "C:\\Path\\To\\keystore.pfx";
//        $pfxPassword = "KeyStorePassword";

            // 1 - Instantiate the credentials
            $credentials = new AzureAdTokenCredentials(
                $tenant,
                new AzureAdClientSymmetricKey($clientId, $clientKey),
                AzureEnvironments::AZURE_CLOUD_ENVIRONMENT());

            // 2 - Instantiate a token provider
            $provider = new AzureAdTokenProvider($credentials);

            // 3 - Connect to Azure Media Services
            $restProxy = ServicesBuilder::getInstance()->createMediaServicesService(new MediaServicesSettings($restApiEndpoint, $provider));


            $asset = new Asset(Asset::OPTIONS_NONE);
            $asset = $restProxy->createAsset($asset);


            $access = new AccessPolicy('EasiaEMService');
            $access->setDurationInMinutes(100);
            $access->setPermissions(AccessPolicy::PERMISSIONS_WRITE);
            $access = $restProxy->createAccessPolicy($access);

            $sasLocator = new Locator($asset, $access, Locator::TYPE_SAS);
            $sasLocator->setStartTime(new \DateTime('now -5 minutes'));
            $sasLocator = $restProxy->createLocator($sasLocator);

            $restProxy->uploadAssetFile($sasLocator, 'dtl1.mp4', public_path('dtl.mp4'));
            $restProxy->createFileInfos($asset);
            return 'success';
        } catch (\Exception $e) {
            return $e->getMessage();
        }

    }

    public function getMediaAzure()
    {
        try {
            $tenant = "quenguyeneasiatravel.onmicrosoft.com";
            $username = 'quenguyen@easia-travel.net';
            $password = 'EasiaTVC-2020';
            $clientId = "c49b0518-fe9c-4da1-9369-d0e93d88601b";
            $clientKey = "ZtX_6~3xAAG9VF66DO2s9ha153-7_.e8oQ";
            $restApiEndpoint = "https://emedia.restv2.eastasia.media.azure.net/api/";
//        $pfxFileName = "C:\\Path\\To\\keystore.pfx";
//        $pfxPassword = "KeyStorePassword";

            // 1 - Instantiate the credentials
            $credentials = new AzureAdTokenCredentials(
                $tenant,
                new AzureAdClientSymmetricKey($clientId, $clientKey),
                AzureEnvironments::AZURE_CLOUD_ENVIRONMENT());

            // 2 - Instantiate a token provider
            $provider = new AzureAdTokenProvider($credentials);

            // 3 - Connect to Azure Media Services
            $restProxy = ServicesBuilder::getInstance()->createMediaServicesService(new MediaServicesSettings($restApiEndpoint, $provider));

            $asset = new Asset(Asset::OPTIONS_NONE);
            $asset = $restProxy->createAsset($asset);


            $access = new AccessPolicy('EasiaEMService');
            $access->setDurationInMinutes(100);
            $access->setPermissions(AccessPolicy::PERMISSIONS_WRITE);
            $access = $restProxy->createAccessPolicy($access);

            $sasLocator = new Locator($asset, $access, Locator::TYPE_SAS);
            $sasLocator->setStartTime(new \DateTime('now -5 minutes'));
            $sasLocator = $restProxy->createLocator($sasLocator);

            $restProxy->uploadAssetFile($sasLocator, 'dtl3.mp4', public_path('dtl.mp4'));
            $restProxy->createFileInfos($asset);

            //encode file
//            $mediaProcessor = $restProxy->getLatestMediaProcessor('emedia');
//            \Log::info(json_encode($mediaProcessor));
//            $task = new Task('[Task XML body]', $mediaProcessor->getId(), TaskOptions::NONE);
//            $task->setConfiguration('Task_dtl2');
//
//            $restProxy->createJob(new Job(), array($asset), array($task));


            $accessPolicy = new AccessPolicy('EasiaEMService');
            $accessPolicy->setDurationInMinutes(100);
            $accessPolicy->setPermissions(AccessPolicy::PERMISSIONS_READ);
            $accessPolicy = $restProxy->createAccessPolicy($accessPolicy);

//            // Download URL
//            $sasLocator = new Locator($asset, $accessPolicy, Locator::TYPE_SAS);
//            $sasLocator->setStartTime(new \DateTime('now -5 minutes'));
//            $sasLocator = $restProxy->createLocator($sasLocator);
//
//            // Azure needs time to publish media
//            sleep(30);
//
//            $downloadUrl = $sasLocator->getBaseUri() . '/' . '[File name]' . $sasLocator->getContentAccessComponent();

            // Streaming URL
            $originLocator = new Locator($asset, $accessPolicy, Locator::TYPE_ON_DEMAND_ORIGIN);
            $originLocator = $restProxy->createLocator($originLocator);

            // Azure needs time to publish media
            sleep(30);

            $streamingUrl = $originLocator->getPath() . 'dtl3.mp4' . "/manifest";

            return $streamingUrl;
        } catch (\Exception $e) {
            return $e->getMessage();
        }

    }
}
