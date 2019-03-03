<?php
//引用此页面前需先引用conf.php
error_reporting(E_ALL ^ E_DEPRECATED);

LIB('db');
LIB('dp');
LIB('ds');
LIB('us');
LIB('ub');
LIB('rp');
define("DEFAULT_PAGE_SIZE",5);
define("DEFAULT_START_SEEK",0);
class BackgroundController extends DBManager {
	
	//页面内容配置
    public $pages = [
        'navi'=>['id'=>'nav','title'=>'导航栏','path'=>'admin/view/navagator.php'],
		'personalinfo'=>['id'=>'pinf','title'=>'个人信息块','path'=>'admin/view/personalinfo.php'],
        'postDream'=>['id'=>'post','title'=>'发布梦想池','path'=>'admin/view/postdream.php'],
        'verify'=>['id'=>'verf','title'=>'审核','path'=>'admin/view/verify.php'],
        'datas'=>['id'=>'data','title'=>'数据','path'=>'admin/view/data.php'],
        'orders'=>['id'=>'ord','title'=>'订单','path'=>'admin/view/order.php'],
        'activity'=>['id'=>'act','title'=>'活动','path'=>'admin/view/activity.php'],
        'redRefund'=>['id'=>'refund','title'=>'红包退款','path'=>'admin/view/refund.php'],
        'redPackage'=>['id'=>'redpack','title'=>'红包','path'=>'admin/view/redpackage.php']
    ];
	
	//导航栏配置,索引为id,js用
    public $navigateList = [
        'a_post'=>['title'=>'发布梦想池','class'=>'lnr lnr-home'],
        'a_verify'=>['title'=>'中标用户审核','class'=>'lnr lnr-pencil'],
        'a_data'=>['title'=>'访问数据统计','class'=>'lnr lnr-dice'],
		'a_order'=>['title'=>'订单查看','class'=>'lnr lnr-store'],
        'a_activity'=>['title'=>'活动照片','class'=>'lnr lnr-enter'],
        'a_refund'=>['title'=>'红包退款','class'=>'lnr lnr lnr-arrow-left'],
        'a_redpack'=>['title'=>'红包','class'=>'lnr lnr-map']
    ];
	
	//创建目录导航
	public static function BuildPageIndex($seek,$count,$size,$HalfPageMax = 3){
		$pageIndex = [];
		$currentPage = Ceil($seek/$size);
		
		
		$totalPage = Ceil($count/$size);
		
		$startIndex = ($currentPage - $HalfPageMax)<0?0: ($currentPage - $HalfPageMax);
		$endIndex = ($currentPage + $HalfPageMax)>$totalPage?$totalPage: ($currentPage + $HalfPageMax);
		
		$pageIndex['allowLast'] = $startIndex>0;
		$pageIndex['allowNext'] = $endIndex<$totalPage;
		
		for($i=$startIndex;$i<$endIndex;$i++){
			$pageIndex['list'][$i] = $i*$size;
		}
		$pageIndex['current'] = $currentPage;
		return $pageIndex;
	}
    
	public function BuildNavigatorList(){
        return $this->navigateList;
    }

	//引用导航栏
    public function BuildNavigator(){
        $pageData = $this->pages['navi'];
        $pageData['navList'] = $this->BuildNavigatorList();
        require ($pageData['path']);
    }

	//引用订单
	public function BuildOrders(){
        $pageData = $this->pages['orders'];
		
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
		
		$pageData['index'] = self::BuildPageIndex($pageData['seek'],$pageData['ordCount'],$pageData['size']);
		
        require ($pageData['path']);
	}
	
    //引用发布梦想池
    public function BuildPostDream(){
        $pageData = $this->pages['postDream'];
        $DPM = new DreamPoolManager();
        $pageData['count'] = $DPM->CountPools()['count'];
        $pageData['psize'] = (isset($_REQUEST['psize']))?$_REQUEST['psize']:DEFAULT_PAGE_SIZE;
        $pageData['seek'] = (isset($_REQUEST['seek']))?$_REQUEST['seek']:DEFAULT_START_SEEK;

        $pageData['pages'] = ceil($pageData['count']/$pageData['psize']);
        $pageData['pools'] = $DPM->ListPoolsByRange($pageData['seek'],$pageData['psize'])['Pools'];

        require ($pageData['path']);
    }

    //引用审核结果
    public function BuildVerify(){
        $pageData = $this->pages['verify'];
		
		
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
        $pageData = $this->pages['personalinfo'];
        $USM = new UserManager();
		$selfInfo = UserManager::GetUserInfo($uid);
		$pageData['selfInfo'] = $selfInfo;
        require ($pageData['path']);
	}
	
	//引用数据
	public function BuildDatas(){
        $pageData = $this->pages['datas'];

		$uBehaviour = new UserBehaviourManager();
        $pageData['recs'] = $uBehaviour->GetRecordsRecordsByRange(0,20)['recs'];
		
        require ($pageData['path']);
	}

	//引用活动照片
	public function BuildActivity(){
        $pageData = $this->pages['activity'];
        $awardController = new AwardManager();
        $pageData['act'] = $awardController->ActivityLive();
        require ($pageData['path']);
    }
	
	public function BuildRefund(){
		$pid = isset($_REQUEST['pid'])?$_REQUEST['pid']:'20190208';
		$pageData = $this->pages['redRefund'];
		$redOrderController = new RedPackManage();
		$pageData['packs'] = $redOrderController->CollectRefundInfo($pid);
		require ($pageData['path']);
	}
	
	public function BuildRedPackage(){
		$pid = isset($_REQUEST['pid'])?$_REQUEST['pid']:'';
		$pageData = $this->pages['redPackage'];
		//$pid = "20190209";
		if($pid!=''){
			$redOrderController = new RedPackManage();
			$seek= isset($_REQUEST['seek'])?$_REQUEST['seek']:0;
			$count =  isset($_REQUEST['count'])?$_REQUEST['count']:10;
			$_REQUEST['type'] = "listview";
			$pageData['packs'] = $redOrderController->GetRedPacksInfo($pid,$seek,$count);
			$pageData['index'] = self::BuildPageIndex($seek,$pageData['packs']['total'],$count);
		}
		require ($pageData['path']);
	}

	
    public function info()
    {
        return "BackgroundController"; // TODO: Change the autogenerated stub
    }
	
	public function BackgroundController(){
		
	}
}
?>