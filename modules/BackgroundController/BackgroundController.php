<?php
//引用此页面前需先引用conf.php
error_reporting(E_ALL ^ E_DEPRECATED);
LIB('utils');
LIB('db');
LIB('dp');
LIB('ds');
LIB('us');
LIB('ub');
LIB('rp');
LIB('view');
LIB('op');
define("DEFAULT_PAGE_SIZE",5);
define("DEFAULT_START_SEEK",0);
class BackgroundController extends DBManager {

    //通用数据查询接口
  /*  public function GeneralSearchAccess($type,$seek,$count,$condition){
        $condition = 1;
        $condition = json_decode($condition);
        foreach ($condition as $key => $value) {
            $condition = self::C_And($condition,self::FieldIsValue())
        }
        $searchList = [

        ];
        $datas = DBResultToArray(
            $this->SelectDataByQuery($this->TName($type),
                $searchCondition
            ),true
        );
        $count = $this->CountTableRowByQuery($this->TName($type),$searchCondition);*/
        /*foreach ($datas as $key => $value) {
            $value['starttime'] = date('Y-m-d',$value['starttime']);
            $value['lasttime'] = date('Y-m-d H:i:s',$value['lasttime']);
            $value['state'] =$stateText[$value['state']];
            unset($value['firstday']);
            $datas[$key] = $value;
        }*/

  /*      $backMsg = RESPONDINSTANCE('0');
        $backMsg['count'] = $count;
        $backMsg['data'] = $datas;
        return $backMsg;
    }*/

	//页面内容配置
    public $pages = [
        'bnav'=>['id'=>'bnav','title'=>'导航栏','path'=>'admin/view/navagator.php','notnav'=>true],
		'pinfo'=>['id'=>'pinf','title'=>'个人信息块','path'=>'admin/view/personalinfo.php','class'=>'lnr lnr-home','notnav'=>true],
        'a_post'=>['title'=>'发布梦想池','path'=>'admin/view/postdream.php','class'=>'lnr lnr-home'],
        'a_verify'=>['title'=>'审核','path'=>'admin/view/verify.php','class'=>'lnr lnr-pencil'],
        'a_data'=>['title'=>'数据','path'=>'admin/view/data.php','class'=>'lnr lnr-dice'],
        'a_order'=>['title'=>'订单','path'=>'admin/view/order.php','class'=>'lnr lnr-store'],
        'a_activity'=>['title'=>'活动','path'=>'admin/view/activity.php','class'=>'lnr lnr-enter'],
        'a_refund'=>['title'=>'红包退款','path'=>'admin/view/refund.php','class'=>'lnr lnr lnr-arrow-left'],
        'a_redpack'=>['title'=>'红包','path'=>'admin/view/redpackage.php','class'=>'lnr lnr-map'],
        'a_operation'=>['title'=>'行动','path'=>'admin/view/operation.php','class'=>'lnr lnr-rocket'],
        'a_tools'=>['title'=>'行动工具','path'=>'admin/view/tools.php','class'=>'lnr lnr-calendar-full'],
        'a_file'=>['title'=>'文件上传','path'=>'admin/view/filemanager.php','class'=>'lnr lnr lnr-file-add']
    ];
	
	public function CurrentPageData(){
		$id = $_REQUEST[$_GET['act']];
		$tData = isset($this->pages[$id])?$this->pages[$id]:$this->pages['a_post'];
		$tData['id'] = $id;
		return $tData;
	}
    
	public function BuildNavigatorList(){
		$navlist = [];
		foreach($this->pages as $key=>$value){
			if(isset($value['notnav']) && $value['notnav']){
				
			}else{
				$navlist[$key] = $value;
			}
		}
        return $navlist;
    }

	//引用导航栏
    public function BuildNavigator(){
        $pageData = $this->CurrentPageData();
        $pageData['navList'] = $this->BuildNavigatorList();
		//echo json_encode($pageData);
        require ($pageData['path']);
    }

	//引用订单
	public function BuildOrders(){
        $pageData = $this->CurrentPageData();
		
		$pageData['size'] = 10;
		
		$pageData['tele'] = isset($_REQUEST['tele'])?$_REQUEST['tele']:"";
		
		$pageData['startTime'] = isset($_REQUEST['startTime'])?$_REQUEST['startTime']:date('Y-m-01', strtotime(date("Y-m-d")));
		
		$pageData['lastTime'] = isset($_REQUEST['endTime'])?$_REQUEST['endTime']:date('Y-m-d', strtotime($pageData['startTime']." +1 month -1 day"));
		
		$DSM = new DreamServersManager();
		
		
		$_REQUEST['date'] = $pageData['startTime'];
		
		$_REQUEST['datemax'] = $pageData['lastTime'];
		
		$_REQUEST['tele'] = $pageData['tele'];
		
		$countObject = $DSM->GetOrderCountByTeleORDate();
		
		$orderCount = $countObject['ordCount'];
		
		$totalBill = $countObject['totalBill'];
		
		$pageData['seek'] = isset($_REQUEST['seek'])?$_REQUEST['seek']:0;
		
		$pageData['count'] = isset($_REQUEST['count'])?$_REQUEST['count']:$pageData['size'];
		
		
		$orders = $DSM->GetOrdersByTeleORDate($pageData['seek'],$pageData['count'])['orders'];
		
		$pageData['ordCount'] = $orderCount;
		
		$pageData['totalBill'] = $totalBill;
		
		$pageData['orders'] = $orders;
		
		$pageData['index'] = UtilsManager::BuildPageIndex($pageData['seek'],$pageData['ordCount'],$pageData['size']);
		
        require ($pageData['path']);
	}
	
    //引用发布梦想池
    public function BuildPostDream(){
        $pageData = $this->CurrentPageData();
        $DPM = new DreamPoolManager();
        $pageData['count'] = $DPM->CountPools()['count'];
        $pageData['psize'] = (isset($_REQUEST['psize']))?$_REQUEST['psize']:DEFAULT_PAGE_SIZE;
        $pageData['seek'] = (isset($_REQUEST['seek']))?$_REQUEST['seek']:DEFAULT_START_SEEK;

        $pageData['pages'] = ceil($pageData['count']/$pageData['psize']);
        $pageData['pools'] = $DPM->ListPoolsByRange($pageData['seek'],$pageData['psize'])['Pools'];
		$pageData['tlist'] = SnippetManager::GetTemplateList();
        require ($pageData['path']);
    }

    //引用审核结果
    public function BuildVerify(){
        $pageData = $this->CurrentPageData();
		
		
		$btn_submit_style = 'btn disable';
		$btn_unsubmit_style = 'btn disable';
		$btn_lose_style = 'btn disable';
		$tab = 'submit';
		if(isset($_REQUEST['tab'])){
			$tab = $_REQUEST['tab'];
			switch($_REQUEST['tab']){
				case 'submit':
					$btn_submit_style = 'btn btn-primary';
					break;
				case 'unsubmit':
					$btn_unsubmit_style = 'btn btn-primary';
					break;
				case 'lose':
					$btn_lose_style = 'btn btn-primary';
					break;
				default:
					$btn_submit_style = 'btn btn-primary';
					break;
			}
		}else{
			$btn_submit_style = 'btn btn-primary';
		}
		$pageData['btnStyle'] = [
			'submit'=>$btn_submit_style,
			'unsubmit'=>$btn_unsubmit_style,
			'lose'=>$btn_lose_style
		];
		
		
        $USM = new UserManager();
        $result = $USM->ViewAllVerifyInfox($tab);

        if($result['result'] == 'true'){
            $pageData['verify'] = $result['verify'];
        }
		
        require ($pageData['path']);
    }
	
	//引用个人信息
	public function BuildPersonalInfo($uid){
        $pageData = $this->CurrentPageData();
        $USM = new UserManager();
		$selfInfo = UserManager::GetUserInfo($uid);
		$pageData['selfInfo'] = $selfInfo;
        require ($pageData['path']);
	}
	
	//引用数据
	public function BuildDatas(){
        $pageData = $this->CurrentPageData();

		$uBehaviour = new UserBehaviourManager();
        $pageData['recs'] = $uBehaviour->GetRecordsRecordsByRange(0,20)['recs'];
		
        require ($pageData['path']);
	}

	//引用活动照片
	public function BuildActivity(){
        $pageData = $this->CurrentPageData();
        $awardController = new AwardManager();
        $pageData['act'] = $awardController->ActivityLive();
        require ($pageData['path']);
    }

    //引用退款
	public function BuildRefund(){
		$pid = isset($_REQUEST['pid'])?$_REQUEST['pid']:'20190208';
        $pageData = $this->CurrentPageData();
		$redOrderController = new RedPackManage();
		$pageData['packs'] = $redOrderController->CollectRefundInfo($pid);
		$DPM = new DreamPoolManager();
		$pageData['pids'] = $DPM->PoolIdList()['pids'];
		require ($pageData['path']);
	}

    //引用红包
	public function BuildRedPackage(){
		$pid = isset($_REQUEST['pid'])?$_REQUEST['pid']:'';
        $pageData = $this->CurrentPageData();
		//$pid = "20190209";
		if($pid!=''){
			$redOrderController = new RedPackManage();
			$seek= isset($_REQUEST['seek'])?$_REQUEST['seek']:0;
			$count =  isset($_REQUEST['count'])?$_REQUEST['count']:10;
			$_REQUEST['type'] = "listview";
			$pageData['packs'] = $redOrderController->GetRedPacksInfo($pid,$seek,$count);
			$pageData['index'] = UtilsManager::BuildPageIndex($seek,$pageData['packs']['total'],$count);
		}
		$DPM = new DreamPoolManager();
		$pageData['pids'] = $DPM->PoolIdList()['pids'];
		require ($pageData['path']);
	}

	//引用行动
    public function BuildOperation(){
        $pageData = $this->CurrentPageData();
        $OPM = new OperationManager();
        $state = FREE_PARS('state','ALL');
        $seek = FREE_PARS('seek','0');
        $count = FREE_PARS('count','5');

        $datas = $OPM->GetOperationData($state,$seek,$count);
        $pageData['data'] = $datas['data'];
        $pageData['index'] = $datas['index'];
        $pageData['fields'] = $datas['fields'];
        $pageData['count'] = $datas['count'];

        require ($pageData['path']);
    }

    public function BuildTools(){
        $pageData = $this->CurrentPageData();
        $COM = new ContractManager();
        $seek = FREE_PARS('seek','0');
        $count = FREE_PARS('count','5');
        $datas = $COM->GetPublicAccounts($seek,$count);
        $pageData['data'] = $datas['data'];
        $pageData['index'] = $datas['index'];
        $pageData['fields'] = $datas['fields'];
        $pageData['count'] = $datas['count'];
		$input = [
			['title'=>'公众号名称','id'=>'title'],
			['title'=>'图标链接','id'=>'icon'],
			['title'=>'二维码链接','id'=>'qrcode'],
			['title'=>'公众号简介','id'=>'description','flag'=>'textarea','cols'=>'30','rows'=>'10'],
			['title'=>'公众号类别','id'=>'type'],   
		];
        require ($pageData['path']);
    }

    public function BuildFiles(){
        $pageData = $this->CurrentPageData();
        $seek = FREE_PARS('seek','0');
        $count = FREE_PARS('count','5');
        $input = [
            ['title'=>'文件选择','id'=>'uploader','type'=>"file",'multiple'=>'true']
        ];
        $SNM = new SnippetManager();
        $datas = $SNM->UploadFileInfo($seek,$count);
        $pageData['data'] = $datas['data'];
        $pageData['index'] = $datas['index'];
        $pageData['fields'] = $datas['fields'];
        $pageData['count'] = $datas['count'];
        require ($pageData['path']);
    }

    //引用打卡记录
    public function BuildAttendence(){
        $pageData = $this->CurrentPageData();
    }

    //引用邀请
    public function BuildInvite(){

    }

	
    public function info()
    {
        return "BackgroundController"; // TODO: Change the autogenerated stub
    }
	
	public function BackgroundController(){
		
	}
}
?>