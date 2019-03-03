<?php
//引用此页面前需先引用conf.php
error_reporting(E_ALL ^ E_DEPRECATED);
LIB("aw");//
LIB("ds");//
LIB("dr");//
LIB("dp");//
LIB("no");//通知
LIB("us");//用户
class RedPackManage extends DBManager {

    public function info()
    {
        return "RedPackManage"; // TODO: Change the autogenerated stub
    }

    public static function GenerateRedPackageID(){
        $RPM = new RedPackManage();
        //生成订单号
        do{
            $newOrderID = 300000000000+((PRC_TIME()%999999).(rand(10000,99999)));
        }while($RPM->SelectDataFromTable('tROrder',['rid'=>$newOrderID,'_logic'=>' ']));
        return $newOrderID;
        return "1111123447465765757653";
    }
    //生成领取红包时的订单号
    public static function GenerateRedPackageOrderID(){
        $RPM = new RedPackManage();
        //生成订单号
        do{
            $newOrderID = 900000000000+((PRC_TIME()%999999).(rand(10000,99999)));
        }while($RPM->SelectDataFromTable('tROrder',['rid'=>$newOrderID,'_logic'=>' ']));
        return $newOrderID;
    }

    public static function GenerateRedPackageOrder($uid,$pid,$bill,$did){
        $RPM = new RedPackManage();
        $orderArray = [
            "oid"=>self::GenerateRedPackageOrderID(),
            "uid"=>$uid,
            "pid"=>$pid,
            "bill"=>$bill,
            "ctime"=>PRC_TIME(),
            "ptime"=>PRC_TIME(),
            "state"=>"SUCCESS",
            "dcount"=>1,
            "did"=>$did,
        ];
        $RPM->InsertDataToTable($RPM->TName('tOrder'),$orderArray);
        return $orderArray;
    }

    //获取红包的领取信息
    public static function HasUserRedPackageRec($uid,$rid){
        $RPM = new RedPackManage();
        $count = DBResultToArray($RPM->SelectDataByQuery( $RPM->TName('tRReco'),
            self::C_And(
                self::FieldIsValue('uid',$uid),
                self::FieldIsValue('rid',$rid)
            ),false,'COUNT(*)'
        ),true);

        if(empty($count)){
            return false;
        }

        $count = $count[0]['COUNT(*)'];

        return $count>0;
    }

    //获取红包的领取信息
    public static function GenerateRedPackageRecInfo($rid){
        $RPM = new RedPackManage();
        $count = DBResultToArray($RPM->SelectDataByQuery($RPM->TName('tRReco'),self::FieldIsValue('rid',$rid),false,'COUNT(*)'),true);
        if(empty($count)){
            return [
                "rpid"=>$rid.'_0',
                "index"=>0,
            ];
        }
        $count = $count[0]['COUNT(*)'];

        return [
            "rpid"=>$rid.'_'.$count,
            "index"=>$count,
        ];
    }

    //删除所有用户的Payment订单
    public static function RemoveAllPaymentRedOrder($uid){
        $RPM = new RedPackManage();
        $RPM->DeletDataByQuery($RPM->TName('tROrder'),
            self::C_And(
                self::FieldIsValue('uid',$uid),
                self::FieldIsValue('state',"PAYMENT")
            )
        );
    }

    //获取红包剩余数量
    public static function GetRedPackLessCount($rid){
        $RPM = new RedPackManage();
        $less = DBResultToArray($RPM->SelectDataByQuery($RPM->TName('tROrder'),
                self::FieldIsValue('rid',$rid)
        ),true);
        if(empty($less)){
            return 0;
        }
        $less = $less[0];
        return $less['rcount'] - $less['gcount'];
    }

    //发红包统一下单准备支付
    public function CreateRedPackae($pid,$rcount,$content,$bill,$uid){
        /*
         *
         * 前提条件的判断
         *
         * */
        //用户绑定手机号
            //未绑定，返回需绑定
        if(!UserManager::IdentifyTeleUser($uid)){
            return RESPONDINSTANCE('11');//若未绑定手机即会提示先绑定手机
        }

        //梦想互助期号是否存在，未完成互助
            //梦想互助失效，返回失败

        $RunningResult = DreamPoolManager::IsPoolRunning($pid);

        if($RunningResult['result']=="false"){
            return RESPONDINSTANCE('5');//梦想池失效（完成互助或到时）
        }

        //rcount小于200，
            //rcount大于200，返回错误
        if($rcount>200){
            return RESPONDINSTANCE('67');
        }

        //rcount小于剩余，
            //rcount大于剩余，返回错误
        $lessCount = DreamPoolManager::GetLessLotteryCount($pid);

        //当rcount大于梦想互助剩余梦想数量
        if($rcount>$lessCount){
            return RESPONDINSTANCE('69',$lessCount);
        }

        //删除用户有PAYMENT的订单
        self::RemoveAllPaymentRedOrder($uid);

        $rid = self::GenerateRedPackageID();
        $gcount = 0;
        $acount = 1;
        $rtype = "STANDARD";
        $ctime = PRC_TIME();
        $ptime = 0;
        $state = "PAYMENT";


        //统一下单 crp
        $DSM = new DreamServersManager();
        $orderInfo = $DSM->WxPayWeb($rid,$bill,$uid);
        if($orderInfo['code'] != "0"){
            //统一下单错误
//            return RESPONDINSTANCE('');
            return RESPONDINSTANCE('68',$orderInfo['code']);
        }else{
            //插入红包信息进入数据库，redpackorder数据表
            $this->InsertDataToTable(
                $this->TName('tROrder'),
                [
                    "rid"=>$rid,
                    "uid"=>$uid,
                    "pid"=>$pid,
                    "bill"=>$bill,
                    "rcount"=>$rcount,
                    "gcount"=>$gcount,
                    "acount"=>$acount,
                    "content"=>$content,
                    "rtype"=>$rtype,
                    "ctime"=>$ctime,
                    "ptime"=>$ptime,
                    "state"=>$state,
                ]
            );
        }
        $backMsg = RESPONDINSTANCE('0');
        $backMsg['order'] = $orderInfo;
        $backMsg['rid'] = $rid;
        return $backMsg;
    }

    //红包支付创建成功 cprs
    public function CreateRedPackSuccess($uid,$rid){
        //判断用户是否创建了该红包
            //用户不拥有该红包，为错误
        $existPackage = DBResultToArray($this->SelectDataByQuery($this->TName('tROrder'),
            self::C_And(
                self::FieldIsValue('uid',$uid),
                self::C_And(
                    self::FieldIsValue('rid',$rid),
                    self::FieldIsValue('state','PAYMENT')
                )
            )
        ),false,self::SqlField('rid'));
        if(empty($existPackage)){
            return RESPONDINSTANCE('70');
        }

        $this->UpdateDataToTableByQuery($this->TName('tROrder'),
            [
                'state'=>"RUNNING",
                'ptime'=>PRC_TIME()
            ],
			self::C_And(
                self::FieldIsValue('uid',$uid),
                self::C_And(
                    self::FieldIsValue('rid',$rid),
                    self::FieldIsValue('state','PAYMENT')
                )
            )
        );

        //订单状态修改为RUNNINNG
        //ptime修改为当前时间
        $backMsg = RESPONDINSTANCE('0');
        return $backMsg;
    }
    //获取红包信息,打开领取红包页面 grp
    public function GetRedPack($rid){

        //从rid获取红包信息，
        $redpack = DBResultToArray($this->SelectDataByQuery($this->TName('tROrder'),
            self::FieldIsValue('rid',$rid)
        ),true);
        if(empty($redpack)){
            return RESPONDINSTANCE('70');
        }

        $backMsg = RESPONDINSTANCE('0');
        $backMsg['redpack'] = $redpack[0];

        $poolTotalBill = DreamPoolManager::Pool($backMsg['redpack']['pid'])['tbill'];
        $backMsg['redpack']['ptbill'] = $poolTotalBill;
        $userInfo = UserManager::GetUsersInfoByString($backMsg['redpack']['uid'])[$backMsg['redpack']['uid']];
        $backMsg['sender']['headicon'] = $userInfo['headicon'];
        $backMsg['sender']['nickname'] = $userInfo['nickname'];
        return $backMsg;
    }
    //获取用户红包列表,红包记录页面（发出）gurps
    public function GetUserRedPacksSend($uid,$seek,$count){
        //通过uid获取用户发出的红包信息
        $redpacks = DBResultToArray(
            $this->SelectDataByQuery(
                $this->TName('tROrder'),
                self::Limit(
				self::OrderBy(
                    self::C_And(
                        self::FieldIsValue('state',"PAYMENT","!="),
                        self::FieldIsValue('uid',$uid)
                    ),"ctime","DESC"),
                    $seek,$count
                )
            ),true
        );
		$pidList= [];
		foreach($redpacks as $pack){
			$query = self::FieldIsValue('pid',$pack['pid']);
			if(in_array($query,$pidList)){
				continue;
			}
			array_push($pidList,$query);
		}
		
		$pools = DBResultToArray($this->SelectDataByQuery($this->TName('tPool'),self::LogicString($pidList,' OR ')),false);
		
		
		foreach($redpacks as $key=>$pack){
			if(isset($pools[$pack['pid']])){
				$redpacks[$key]['pstate'] = $pools[$pack['pid']]['state'];
//				echo $key.'=>'.$pools[$pack['pid']]['state'];
			}
		}

        $stats = DBResultToArray(
            $this->SelectDataByQuery(
                $this->TName('tROrder'),
                    self::C_And(
                        self::FieldIsValue('state',"PAYMENT","!="),
                        self::FieldIsValue('uid',$uid)
                    ),false,"SUM(`rcount`),SUM(`bill`)"
            ),true
        );

        if(!empty($stats)) {
            $stats = $stats[0];
            $countPack = $stats['SUM(`rcount`)'];
            $totalBills = $stats['SUM(`bill`)'];
            $stats = ['countPack' => $countPack, 'totalBill'=>$totalBills];
        }

        $backMsg = RESPONDINSTANCE('0');
		$backMsg['packs'] = $redpacks;
		$backMsg['stats'] = $stats;
        return $backMsg;
    }

    //获取红包的领取记录(领取),grpr
    public function GetRedPackrecive($rid,$seek,$count){
        $redpack = DBResultToArray($this->SelectDataByQuery(
            $this->TName('tROrder'),
            self::FieldIsValue('rid',$rid),
            false,
            self::LogicString(
                [
                    self::SqlField('rcount'),
                    self::SqlField('gcount')
                ],","
            )
        ),true);
        if(empty($redpack)){
            return RESPONDINSTANCE('70');
        }

        $pack = DBResultToArray(
            $this->SelectDataByQuery(
                $this->TName('tRReco'),
                self::Limit(
                    self::OrderBy(
                        self::FieldIsValue('rid',$rid),
                        "gtime",
                        "DESC"
                    ),
                    $seek,$count
                )
            ),true
        );

        $uidList = [];
        $oidList = [];
        foreach ($pack as $item) {
            $uidSql = self::FieldIsValue('uid',$item['uid']);
            if(!in_array($uidSql,$uidList)){
                array_push($uidList,$uidSql);
            }

            $oidSql = self::FieldIsValue('oid',$item['oid']);
            if(!in_array($oidSql,$oidList)){
                array_push($oidList,$oidSql);
            }
        }
        $userInfo = DBResultToArray(
            $this->SelectDataByQuery(
                $this->TName('tUser'),
                self::LogicString($uidList,' OR '),
                false,
                self::LogicString(
                    [
                        self::SqlField('uid'),
                        self::SqlField('nickname'),
                        self::SqlField('headicon')
                    ],","
                )
            ),false
        );


        $lotteryInfo = DBResultToArray(
            $this->SelectDataByQuery(
                $this->TName('tLottery'),
                self::LogicString($oidList,' OR '),
                false,
                self::LogicString(
                    [self::SqlField('oid'),
                        self::SqlField('lid')],","
                )
            ),false
        );


        foreach ($pack as $key=>$item) {
            if(isset($userInfo[$item['uid']])){
                $pack[$key]['nickname'] = $userInfo[$item['uid']]['nickname'];
                $pack[$key]['headicon'] = $userInfo[$item['uid']]['headicon'];
            }
            if(isset($lotteryInfo[$item['oid']])){
                $pack[$key]['lid'] = $lotteryInfo[$item['oid']]['lid'];
            }
        }
        $backMsg = RESPONDINSTANCE('0');
        $backMsg['reco'] = $pack;
        $backMsg['redpack'] = $redpack[0];
        return $backMsg;
    }


    //获取用户红包列表,红包记录页面（收到）gurpr
    public function GetUserRedPacksRecive($uid,$seek,$count){
        //通过uid获取用户收到的红包信息
        $redpacks = DBResultToArray(
			$this->SelectDataByQuery(
				$this->TName('tRReco'),
				self::Limit(
					self::OrderBy(
						self::FieldIsValue('uid',$uid),
						"gtime",
						"DESC"
					),
					$seek,$count
				)
			),true
		);

        $ridList = [];

        foreach ($redpacks as $index=>$pack){
            array_push($ridList,self::FieldIsValue('rid',$pack['rid']));
        }



        $tRedPackages = DBResultToArray(
            $this->SelectDataByQuery($this->TName('tROrder'),
                self::C_And(
                    self::FieldIsValue('state',"PAYMENT","!="),
                    self::Brackets(self::LogicString($ridList,' OR '))
                ),
                false,self::LogicString(
                    [
                        self::SqlField('rid'),
                        self::SqlField('uid')
                    ],',')),false);

        //echo '999:'.json_encode($tRedPackages);

        $userCondition = self::Brackets(
            self::LogicString(
                EachFunction(ListAttributeToArray($tRedPackages,'uid'),
                    function ($item){
                        return self::FieldIsValue('uid',$item);
                    }
                ),
                ' OR '
            )
        );

        $userInfo = DBResultToArray(
            $this->SelectDataByQuery(
                $this->TName('tUser'),
                $userCondition,
                false,
                self::LogicString(
                    [
                        self::SqlField('uid'),
                        self::SqlField('nickname'),
                        self::SqlField('headicon')
                    ],
                    ','
                )
            ),
            false
        );


        $nicknameArray = [];
        foreach ($tRedPackages as $rid=>$item) {
            //$uid = $item['uid'];
            $nicknameArray[$rid]['nickname'] = $userInfo[$item['uid']]['nickname'];
            //$nicknameArray[$rid]['headicon'] = $userInfo[$item['uid']]['headicon'];
        }
       // echo json_encode($nicknameArray);


        /*$redpacks = EachFunction($redpacks,function ($packs){
            if(isset($nicknameArray[$packs['rid']])){
                //$packs['nickname'] = $nicknameArray[$packs['rid']]['nickname'];
            }
            return $packs;
        });*/

        $res = [];
        foreach ($redpacks as $item) {
            if(isset($nicknameArray[$item['rid']])){

                $item['nickname'] = $nicknameArray[$item['rid']]['nickname'];
                //$item['headicon'] = $nicknameArray[$item['rid']]['headicon'];
                array_push($res,$item);
            }
        }
        // json_encode($res);

        $stats = DBResultToArray(
            $this->SelectDataByQuery(
                $this->TName('tRReco'),
                self::FieldIsValue('uid',$uid),
                false,"COUNT(*),SUM(`pbill`)"
            ),true
        );

        if(!empty($stats)) {
            $stats = $stats[0];
            $countPack = $stats['COUNT(*)'];
            $totalBills = $stats['SUM(`pbill`)'];
            $stats = ['countPack' => $countPack, 'totalBill'=>$totalBills];
        }

        $backMsg = RESPONDINSTANCE('0');
		$backMsg['packs'] = $res;//$redpacks;
        $backMsg['stats'] = $stats;
        return $backMsg;
    }
    //领取红包 orp
	public function OpenRedPack($uid,$rid){

        //获取红包信息
        $redInfo = DBResultToArray($this->SelectDataByQuery($this->TName('tROrder'),self::FieldIsValue('rid',$rid)),true);
        if(empty($redInfo)){
            return RESPONDINSTANCE('70');//不存在该红包信息
        }
        $redInfo = $redInfo[0];
        $poolTotalBill = DreamPoolManager::Pool($redInfo['pid'])['tbill'];
        if(self::HasUserRedPackageRec($uid,$rid)){
            $result = DBResultToArray($this->SelectDataByQuery($this->TName('tRReco'),
                self::C_And(
                    self::FieldIsValue('rid',$rid),
                    self::FieldIsValue('uid',$uid)
                )),true);
            $result = $result[0];
            $lid = DBResultToArray($this->SelectDataByQuery($this->TName('tLottery'),
                self::C_And(
                    self::FieldIsValue('oid',$result['oid']),
                    self::FieldIsValue('uid',$uid)
                )),true);
            $backMsg = RESPONDINSTANCE('72');//用户已经领取该红包
            $backMsg['reco'] = $result;
            if(isset($lid[0]['lid'])){
                $backMsg['reco']['lid'] = $lid[0]['lid'];
            }
            $backMsg['pid'] = $redInfo['pid'];
            $backMsg['totalBill'] = $poolTotalBill;
            return $backMsg;
        }

        //判断用户当日购买份数是否到达5次
        $dayLimit = UserManager::CheckDayBoughtLimit($uid);
        if($dayLimit<=0){
            return RESPONDINSTANCE('18');//用户当日购买量超过上限
        }

        //判断红包是否有效，红包存在未领取份数，对应的梦想互助未结束
        $packLess = self::GetRedPackLessCount($rid);
        if($packLess<=0){
            return RESPONDINSTANCE('74');
        }


        //判断梦想互助是否结束
        $RunningResult = DreamPoolManager::IsPoolRunning($redInfo['pid']);

        if($RunningResult['result']=="false"){
            return RESPONDINSTANCE('5');//梦想池失效（完成互助或到时）
        }

        //判断用户是否绑定手机号
        if(!UserManager::IdentifyTeleUser($uid)){
            return RESPONDINSTANCE('11');//若未绑定手机即会提示先绑定手机
        }

        //判断用户是否提交过梦想
        $firstDream = DreamManager::UserFirstSubmitedDream($uid);
        if(empty($firstDream)){
            return RESPONDINSTANCE('71');//用户未提交梦想
        }
        $firstDream = $firstDream[0];

        $unitBill = $redInfo['bill']/$redInfo['rcount'];

        //生成红包购买订单
        $order = self::GenerateRedPackageOrder($uid,$redInfo['pid'],$unitBill,$firstDream['did']);

        $aCount = $redInfo['acount'];

        //用户当日购买份数+1,参与数量+1
        $DSM = new DreamServersManager();
        UserManager::UpdateUserOrderInfo($uid,$DSM->CountUserJoinedPool($uid),$aCount);


        //创建编号
        $PoolResult = DreamPoolManager::BuyPoolPieceSuccess($redInfo['pid'],$aCount);

        $startIndex = $PoolResult['PoolInfo']['startIndex'];//开始编号

        $endIndex = $PoolResult['PoolInfo']['endIndex'];//结束编号

        $numbers = AwardManager::PayOrderAndCreateLottery($redInfo['pid'],$uid,$firstDream['did'],$order['oid'],$startIndex,$endIndex);

        //红包订单已领份数+1
        $this->UpdateDataByQuery($this->TName('tROrder'),"`gcount` = `gcount`+1",self::FieldIsValue('rid',$rid));

        //若红包无剩余量
        if($packLess-$aCount<=0) {
            $this->UpdateDataToTableByQuery(
                $this->TName('tROrder'),
                ['state'=>"FINISHED"],
                self::FieldIsValue('rid',$rid)
            );
        }

        //生成红包领取记录
        self::OnUserOpenPackage($uid,$rid,$aCount,$order['oid'],$aCount*$unitBill);


        $userInfo = UserManager::GetUsersInfoByString($redInfo['uid'])[$redInfo['uid']];
		
		
        //发送通知给用户'sender','lid','pid'
		NoticeManager::CreateNotice($uid,//创建通知——购买梦想
                NOTICE_RED_BUY,
                [
					'sender'=>$userInfo['nickname'],
					'lid'=>key($numbers),
					'pid'=>$redInfo['pid']
                ],
                NoticeManager::CreateAction(
                    'redpack',
                    [
                        'pid'=>$redInfo['pid']
                    ]
                )
        );

        $backMsg = RESPONDINSTANCE('0');
        $backMsg['nums'] = $numbers;
        $backMsg['order'] = $order;
        $backMsg['pool'] = $PoolResult;
        $backMsg['sender']['headicon'] = $userInfo['headicon'];
        $backMsg['sender']['nickname'] = $userInfo['nickname'];
        $backMsg['pid'] = $redInfo['pid'];
        $backMsg['totalBill'] = $poolTotalBill;
        return $backMsg;
    }

    //获取红包信息
    public function GetRedPacksInfo($pid,$seek,$count){
        $redPack = DBResultToArray(
            $this->SelectDataByQuery(
                $this->TName("tROrder"),
                self::Limit(
                    self::FieldIsValue('pid',$pid),
                    $seek,
                    $count
                )
            ),true
        );

        $userinfo = [];
        foreach($redPack as $pack) {
            $userinfo[$pack['uid']]=UserManager::GetUsersInfoByString($pack['uid'])[$pack['uid']];
        }

        foreach($redPack as $key=>$pack){
            if(isset($userinfo[$pack['uid']])){
                $redPack[$key]['nickname'] = $userinfo[$pack['uid']]['nickname'];
                $redPack[$key]['tele'] = $userinfo[$pack['uid']]['tele'];
            }
        }

        $backMsg = RESPONDINSTANCE('0');
        $backMsg['redpack'] = $redPack;

        if(isset($_REQUEST['type']) && $_REQUEST['type'] == "listview"){
            $redPackInfo = DBResultToArray(
                $this->SelectDataByQuery(
                    $this->TName("tROrder"),
                    self::FieldIsValue('pid',$pid),
                    false,
                    "COUNT(*)"
                ),true
            );
            if(!empty($redPackInfo)){
                $backMsg['total'] = $redPackInfo[0]['COUNT(*)'];
                $backMsg['totalpage'] = intval($backMsg['total']/$count);
                $backMsg['currentpage'] = intval($seek/$count);
            }
        }
        return $backMsg;
    }
	
	//整理退款记录(以梦想互助为单位)
	public function CollectRefundInfo($pid){//参数一定要为互助结束的梦想互助
		$poolStatus = DreamPoolManager::IsPoolRunning($pid);
		if($poolStatus['code']!="0" && $poolStatus['code']!="5"){
			$backMsg = RESPONDINSTANCE('5');
			$backMsg['pid'] = $pid;
			return RESPONDINSTANCE('5');
		}
		
		$backMsg = RESPONDINSTANCE('0');
		$backMsg['pid'] = $pid;
		if(isset($poolStatus['poolInfo']) && $poolStatus['poolInfo']['state'] == "RUNNING"){
			$backMsg = RESPONDINSTANCE('73');
			$backMsg['pid'] = $pid;
			$backMsg['refund'] = [];
			return $backMsg;
		}
		
		
		
		$BackRefund = DBResultToArray($this->SelectDataByQuery(
				$this->TName("tROrder"),
				self::C_And(
					self::FieldIsValue('pid',$pid),
					"`rcount`>`gcount`"
				)
			),true
		);
		
		$refundList = [];
		$userIndex = [];
		foreach($BackRefund as $refund){
			$unitBill = $refund['bill']/$refund['rcount'];
			if(!isset($userIndex[$refund['uid']])){
				$userIndex[$refund['uid']] = true;
			}
			$refundList[$refund['rid']] = 
			[
				'uid'=>$refund['uid'],
				'unit'=>$unitBill,
				'rcount'=>$refund['rcount'],
				'gcount'=>$refund['gcount'],
				'less'=> $refund['rcount'] - $refund['gcount'],
				'lbill'=> $refund['bill']- $refund['gcount']*$unitBill
			];
		}
		
		$userinfo = [];
		foreach($userIndex as $uid=>$result){
			$userinfo[$uid]=UserManager::GetUsersInfoByString($uid)[$uid];
		}
		
		$result = $refundList;
		foreach($refundList as $rid=>$refund){
			if(isset($userinfo[$refund['uid']])){
				$result[$rid]['nickname'] = $userinfo[$refund['uid']]['nickname'];
				$result[$rid]['tele'] = $userinfo[$refund['uid']]['tele'];
			}
		}
		
		
		$backMsg['refund'] = $result;
		return $backMsg;
	}

    //用户打开红包记录
    public static function OnUserOpenPackage($uid,$rid,$pcount,$oid,$pbill){
        $RPM = new RedPackManage();
        $infos = self::GenerateRedPackageRecInfo($rid);
        $redrecordArray = [
            "rpid"=>$infos['rpid'],
            "uid"=>$uid,
            "rid"=>$rid,
            "gtime"=>PRC_TIME(),
            "pcount"=>$pcount,
            "oid"=>$oid,
            "pbill"=>$pbill,
            "index"=>$infos['index'],
        ];
        $RPM->InsertDataToTable($RPM->TName('tRReco'),$redrecordArray);

    }


	public function RedPackManage(){

    }
}
?>