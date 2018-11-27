<?php
//引用此页面前需先引用conf.php
error_reporting(E_ALL ^ E_DEPRECATED);

LIB('db');
LIB('us');
LIB('dr');
LIB('dp');
LIB('aw');

//梦想池服务中心,购票等功能
class DreamServersManager extends DBManager {
    public function info()
    {
        //echo self::GenerateOrderID();
        return "DreamServersManager"; // TODO: Change the autogenerated stub
    }

    public function ListInfo(){
        $dtid = null;
        $itype = 'All';
        if(isset($_REQUEST['dtid'])){
            $dtid = $_REQUEST['dtid'];
        }
        if(isset($_REQUEST['itype'])){
            $itype = $_REQUEST['itype'];
        }
        return DreamServersManager::GetDreamInfo($dtid,$itype);
    }

    //生成订单号
    public static function GenerateOrderID(){
        $DSM = new DreamServersManager();
        //生成订单号
        do{
            $newOrderID = 100000000000+((PRC_TIME()%999999).(rand(10000,99999)));
        }while($DSM->SelectDataFromTable('tOrder',['oid'=>$newOrderID,'_logic'=>' ']));
        return $newOrderID;
    }

    //获得首页滚动购买信息
    public static function GetMainOrders(){
        //未实现
        $DSM = new DreamServersManager();
        $sql = 'SELECT * FROM `order` WHERE `state`="SUCCESS" order By `ptime` DESC LIMIT 0,8';
        $sresult = mysql_query($sql,$DSM->DBLink());
        $array = DBResultToArray($sresult,true);
        //echo $sql;
        return $array;
    }

    //删除用户未完成的订单
    public static function ClearSubmitOrder($uid){
        $DSM = new DreamServersManager();
        $condition = [
            'uid'=>$uid,
            'state'=>'SUBMIT',
            '_logic' => 'AND'
        ];
        $DSM->DeletDataFromTable($DSM->TName('tOrder'),$condition);
    }

    public static function GetDreamInfo($dtid = null,$itype = "All"){
        $DSM = new DreamServersManager();
        $array = [];
        $dTypeCondition = [
            'online'=>true,
            '_logic'=>' '
        ];

        $dServerCondition = [
            'online'=>true,
            '_logic'=>' '
        ];

        if(!empty($dtid)){
            $dTypeCondition['dtid']=$dtid;
            $dServerCondition['dtid']=$dtid;
            $dTypeCondition['_logic']="AND";
            $dServerCondition['_logic']="AND";
        }


        if($itype == "All" || $itype == "type") {
            $dtResult = $DSM->SelectDataFromTable($DSM->TName('tDreamType'),
                $dTypeCondition
            );
            $array['dType'] = DBResultToArray($dtResult);
        }


        if($itype == "All" || $itype == "server") {
            $dsResult = $DSM->SelectDataFromTable($DSM->TName('tDreamServer'),
                $dServerCondition);
            $array['dServer'] = DBResultToArray($dsResult);
        }
        return $array;
    }


    //下单动作开始【在任意梦想池点击参与互助或继续互助】
    public function PlaceOrderInADreamPoolStart($uid,$pid){
        $RunningResult = DreamPoolManager::IsPoolRunning($pid);
        if(!$RunningResult['result']){
            return RESPONDINSTANCE('5');//梦想池失效（完成互助或到时）
        }
		
		$dayLimit = UserManager::CheckDayBoughtLimit($uid);
        //检测当日购买量【实现】
        if($dayLimit<=0){
            return RESPONDINSTANCE('18');//用户当日购买量超过上限
        }

        //检查手机绑定情况
        if(!UserManager::IdentifyTeleUser($uid)){
            return RESPONDINSTANCE('11');//若未绑定手机即会提示先绑定手机
        }

        $backMsg = RESPONDINSTANCE('0');

        if(!DreamManager::HasSubmitedDream($uid)){
            //跳转至编辑梦想页面
            $backMsg['actions'] = [
                'editdream'=>['uid'=>$uid],//编辑梦想
                'selectdream'=>['uid'=>$uid],//选择梦想
                'buy'=>['uid'=>$uid,'pid'=>$pid,'dayLim'=>$dayLimit,'less'=>$RunningResult['pless']]//购买互助
            ];
        }else {
            //跳转至选择梦想界面
            $backMsg['actions'] = [
                'selectdream'=>['uid'=>$uid],//选择梦想
                'buy'=>['uid'=>$uid,'pid'=>$pid,'dayLim'=>$dayLimit,'less'=>$RunningResult['pless']]//购买互助
            ];
        }
        return $backMsg;
    }

    //进入下单界面返回信息
    public function PlaceOrderInADreamPoolPrepare($pid){
        $backMsg = RESPONDINSTANCE('0');
        $backMsg['pool'] = DBResultToArray($this->SelectDataFromTable($this->TName('tPool'),['pid'=>$pid]))[$pid];
        $backMsg['overplus'] = ($backMsg['pool']['ptime']+$backMsg['pool']['duration']) - PRC_TIME();
        return $backMsg;
    }

    //下单动作准备支付,订单创建
    public function PlaceOrderInADreamPoolCreate($action){
        $actionList = json_decode($action,true);
        if(!(isset($actionList['buy']) && isset($actionList['buy']['pid']) &&  isset($actionList['buy']['dream']) && isset($actionList['buy']['less']))){
            return RESPONDINSTANCE('17',"购买信息或梦想信息错误,无法创建订单");
        }

        $pless = $actionList['buy']['less'];

        DreamServersManager::ClearSubmitOrder($actionList['buy']['dream']['uid']);

        $orderArray = [
            "oid"=>self::GenerateOrderID(),
            "uid"=>$actionList['buy']['dream']['uid'],
            "pid"=>$actionList['buy']['pid'],
            "bill"=>0,
            "ctime"=>PRC_TIME(),
            "ptime"=>0,
            "state"=>"SUBMIT",
            "dcount"=>0,
            "did"=>$actionList['buy']['dream']['did']
        ];
        $insresult = $this->InsertDataToTable($this->TName('tOrder'),$orderArray);
        if($insresult){
            $backMsg = RESPONDINSTANCE('0');
            $backMsg['actions'] = [
                "pay"=>[
                    "info"=>[
                        //返回基本商户信息及平台信息（后续配置）
                    ],
                    "oid"=>$orderArray['oid'],
                    "pid"=>$orderArray['pid'],
                    "did"=>$orderArray['did'],
                    'pless'=>$pless
                ]
            ];
            $backMsg['order'] = $orderArray;
            return $backMsg;
        }else{
            return RESPONDINSTANCE('19');
        }
    }

    //下单动作支付完成,更新订单
    public function PlaceOrderInADreamPoolPay($uid,$oid,$bill,$pcount,$action)
    {
        $actionList = json_decode($action,true);
        if(!(isset($actionList['pay']) && isset($actionList['pay']['pid']))){
            return RESPONDINSTANCE('17',"购买信息或梦想信息错误,无法完成订单");
        }

        //更新订单信息
        $condition = [
            'uid'=>$uid,
            'oid'=>$oid,
            'state'=>'SUBMIT',
            '_logic' => 'AND'
        ];

        if(!DBResultExist($this->SelectDataFromTable($this->TName('tOrder'),$condition))){
            return RESPONDINSTANCE('20');
        }

        $result = $this->UpdateDataToTable(
            $this->TName('tOrder'),
            [
                'state'=>'SUCCESS',
                'bill'=>$bill,
                'dcount'=>$pcount,
                'ptime'=>PRC_TIME()
            ],
            $condition
        );

        //修改DreamPoolManager,标记更新梦想池购买记录
        $PoolResult = DreamPoolManager::BuyPoolPieceSuccess($actionList['pay']['pid'],$pcount);

        $startIndex = $PoolResult['PoolInfo']['startIndex'];//开始编号

        $endIndex = $PoolResult['PoolInfo']['endIndex'];//结束编号

        //修改UserManager，标记更新用户购买记录
        UserManager::UpdateUserOrderInfo($uid,$this->CountUserJoinedPool($uid),$pcount);

        //修改AwardManager,为用户添加梦想编号
        $NumberArray = AwardManager::PayOrderAndCreateLottery($actionList['pay']['pid'],$uid,$actionList['pay']['did'],$oid,$startIndex,$endIndex);
        if($result && !empty($NumberArray)){
            $backMsg = RESPONDINSTANCE('0');
            $backMsg['numbers'] = $NumberArray;
            $backMsg['actions'] = 'clear';
            return $backMsg;
        }
        return RESPONDINSTANCE('20');
    }

    //获取用户参与的梦想池数量
    public function CountUserJoinedPool($uid){
        $orders = $this->GetAllOrdersUser($uid);
        if(empty($orders)){
            return 0;
        }else{
            $orders = $orders[0];
        }
        $contains = [];
        $count = 0;

        foreach ($orders as $key=>$value) {

            if(array_key_exists($orders['pid'],$contains)){
                continue;
            }
            $contains[$orders['pid']] = true;
            $count++;
        }
        return $count;
    }

    //获取用户的全部订单
    public function GetAllOrdersUser($uid){
        $array = DBResultToArray($this->SelectDataFromTable($this->TName('tOrder'),
            ['uid'=>$uid,'_logic'=>' ']),true);
        return $array;
    }



    //进入梦想池页面调用
    public function ShowPoolsInfoStart(){
        $link = $this->DBLink();
        $sql = 'SELECT COUNT(*) FROM `dreampool` WHERE 1';

        $cResult = mysql_fetch_array(mysql_query($sql,$link))[0];
        $backMsg = RESPONDINSTANCE('0');
        $backMsg['poolCount'] = $cResult;
        return $backMsg;
    }


    //用户获取全部梦想池信息及参与信息
    public function GetPoolsInfoByRange($uid,$min,$max){
        //未实现
        $link = $this->DBLink();

        $sql = "SELECT * FROM `dreampool` WHERE 1 ORDER BY `ptime` DESC LIMIT $min,$max";

        $cResult = DBResultToArray(mysql_query($sql,$link),true);
        $tResult = [];
        $type = 'ALL';
        if(isset($_REQUEST['type'])){
            $type = $_REQUEST['type'];
        }

        foreach ($cResult as $key=>$item) {

            if(AwardManager::GetUserLottery($item['pid'],$uid)>0){//参加
                //用户是否参加
                if($item['state'] == 'FINISHED'){//结束
                    //是否中奖
                    $awardArray = DBResultToArray($this->SelectDataFromTable($this->TName('tAward'),
                    [
                        'pid'=>$item['pid'],
                        'uid'=>$uid
                    ]
                    ));

                    if(!empty($awardArray)){
                        //参加中奖
                        $cResult[$key]['ustatus'] = "JOIN|AWARD";
                    }else{
                        //参加没中奖
                        $cResult[$key]['ustatus'] = "JOIN|NOTAWARD";
                    }
                    if($type == "FINISHED"){
                        $tResult[$key] = $cResult[$key];
                    }
                }else{
                    //未结束但是参加
                    $cResult[$key]['ustatus'] = "JOIN";
                    if($type == "RUNNING"){
                        $tResult[$key] = $cResult[$key];
                    }
                }
                if($type == "JOIN"){
                    $tResult[$key] = $cResult[$key];
                }
            }else{//未参加
                if($item['state'] == 'RUNNING') {//未参加没结束
                    $cResult[$key]['ustatus'] = "NONE";
                    if($type == "RUNNING"){
                        $tResult[$key] = $cResult[$key];
                    }
                }else{//未参加结束
                    $cResult[$key]['ustatus'] = "NONE";
                    if($type == "FINISHED"){
                        $tResult[$key] = $cResult[$key];
                    }
                }
            }

            if($type=='ALL'){
                $tResult = $cResult;
            }
        }
        $backMsg = RESPONDINSTANCE('0');
        $backMsg['Pools'] = $tResult;
        return $backMsg;
    }

    //进入参与记录页面调用
    public function ShowOrdersInPoolStart($pid){
        $link = $this->DBLink();
        $sql = 'SELECT COUNT(*) FROM `order` WHERE `pid`="'.$pid.'"';

        $cResult = mysql_fetch_array(mysql_query($sql,$link))[0];
        $backMsg = RESPONDINSTANCE('0');
        $backMsg['ordCount'] = $cResult;
        return $backMsg;
    }

    //根据范围获取订单及用户信息
    public function GetOrdersInPoolByRange($pid,$min,$max){
        $link = $this->DBLink();

        $sql = 'SELECT * FROM `order` WHERE `pid`="'.$pid.'" LIMIT '.$min.','.$max;

        $cResult = DBResultToArray(mysql_query($sql,$link),true);

        $user = [];

        $uids = "";

        $i = 0;

        foreach ($cResult as $item) {
            $user[$i++] = $item['uid'];
            $uids = $uids.$item['uid'].'|';
        }

        $array = DBResultToArray($this->SelectDatasFromTable($this->TName('tUser'),
            [
                'uid'=>$uids
            ]));

        $user=[];

        foreach ($array as $key => $item) {
            $user[$item['uid']] = $item['tele'];
        }

        foreach ($cResult as $key => $item) {
            $cResult[$key]['tele'] = $user[$cResult[$key]['uid']];
        }

        $backMsg = RESPONDINSTANCE('0');

        $backMsg['orders'] = $cResult;

        return $backMsg;
    }

    public function DreamServersManager(){
        parent::__construct();
	}
}
?>