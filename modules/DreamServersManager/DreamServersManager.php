<?php
//引用此页面前需先引用conf.php
error_reporting(E_ALL ^ E_DEPRECATED);

LIB('db');
LIB('us');
LIB('dr');
LIB('dp');
LIB('aw');

class WechatPay{

    public $minerPrice;
    public $App_ID;
    public $Mhc_ID;
    public $Mhc_Key;
    public $notify_Url;
    public $order_id;
    public function __construct($orderid,$mprice){
        $this->minerPrice = $mprice;
        $this->order_id = $orderid;

        $this->App_ID = $GLOBALS['options']['APP_ID'];
        $this->Mhc_Key = $GLOBALS['options']['MCH_KEY'];
        $this->Mhc_ID = $GLOBALS['options']['MCH_ID'];
        $this->notify_Url = $_SERVER["REMOTE_ADDR"];
        //var_export($this);
    }

    function generateNonce()
    {
        return strtoupper(md5(uniqid('', true)));
    }

    public function getPayResponse($oid,$uid){
        $backMsg = RESPONDINSTANCE('0');
            $prepay_Result = $this->generatePrepayId($oid,$uid);
            if($prepay_Result['result']!='true'){
                return $prepay_Result;
            }


            $appID = $prepay_Result['appid'];

            $timeStamp = time().'';
            $nonceStr = $prepay_Result['nonce_str'];
            $package = 'prepay_id='.$prepay_Result['prepay_id'];
            $signType = 'MD5';

            $mhc_secret = $this->Mhc_Key;

          //  echo "appId=$appID&nonceStr=$nonceStr&package=$package&signType=$signType&timeStamp=$timeStamp&key=$mhc_secret";

            $paySign = strtoupper(md5("appId=$appID&nonceStr=$nonceStr&package=$package&signType=$signType&timeStamp=$timeStamp&key=$mhc_secret"));
            $backMsg['timeStamp'] = $timeStamp;
            $backMsg['nonceStr'] =  $this->generateNonce();// $nonceStr;
            $backMsg['package'] = $package;
            $backMsg['signType'] = $signType;
            $backMsg['paySign'] = $paySign;

        file_put_contents('unifiedorder04.txt',json_encode($backMsg,JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            /*
             * 小程序签名参数：
             * appId,nonceStr,package,signType,timeStamp
             * */
           // $backMsg['paySign'] =
           // strtoupper(md5("appId=$this->App_ID&nonceStr=$noncestr&package=prepay_id=$prepay_id&signType=MD5&timeStamp=$timestamp&key=$mhc_secret"));
/*
 *
 *
 * timeStamp: data.pay.timestamp.toString(),
        nonceStr: data.pay.noncestr,
        package: 'prepayid='+data.pay.prepayid,
        signType: 'MD5',
        paySign: data.paySign,
 * */
            /*$response = array(
                'appid' => $this->App_ID,
                'partnerid' => $this->Mhc_ID,
                'prepayid' => $prepay_id,
                'package' => 'Sign=WXPay',
                'noncestr' => $this->generateNonce(),
                'timestamp' => time(),
            );
            $response['sign'] = $this->calculateSign($response, $this->App_Key);
            $backMsg['pay'] = $response;

            $noncestr = $response['noncestr'];
            $timestamp = $response['timestamp'];
            $mhc_secret = $this->App_Key;*/


            return $backMsg;
    }

    public function generatePrepayId($oid,$uid)
    {
        /*
         * <xml>
               <appid>wx2421b1c4370ec43b</appid>
               <attach>支付测试</attach>
               <body>JSAPI支付测试</body>
               <mch_id>10000100</mch_id>
               <detail><![CDATA[{ "goods_detail":[ { "goods_id":"iphone6s_16G", "wxpay_goods_id":"1001", "goods_name":"iPhone6s 16G", "quantity":1, "price":528800, "goods_category":"123456", "body":"苹果手机" }, { "goods_id":"iphone6s_32G", "wxpay_goods_id":"1002", "goods_name":"iPhone6s 32G", "quantity":1, "price":608800, "goods_category":"123789", "body":"苹果手机" } ] }]]></detail>
               <nonce_str>1add1a30ac87aa2db72f57a2375d8fec</nonce_str>
               <notify_url>http://wxpay.wxutil.com/pub_v2/pay/notify.v2.php</notify_url>
               <openid>oUpF8uMuAJO_M2pxb1Q9zNjWeS6o</openid>
               <out_trade_no>1415659990</out_trade_no>
               <spbill_create_ip>14.23.150.211</spbill_create_ip>
               <total_fee>1</total_fee>
               <trade_type>JSAPI</trade_type>
               <sign>0CB01533B8C1EF103065174F50BCA001</sign>
           </xml>
         * */

        /*
         *
         *1.小程序ID	appid	            有
         *2.商户号	    mch_id	            有
         *3.随机字符串	nonce_str	        有
         *4.签名	    sign                后加
         *5.商品描述	body                有
         *6.商户订单号	out_trade_no        有
         *7.标价金额	total_fee           有
         *8.终端IP	    spbill_create_ip    有
         *9.通知地址	notify_url          有
         *10.交易类型	trade_type          有
         *
         *
         *
         * */
        $params = array(
            'appid'            => $this->App_ID,
           // 'attach'           => '小梦想互助',
            'body'             => '小梦想互助-购买一个梦想',
            'mch_id'           => ''.$this->Mhc_ID,
            'nonce_str'        => $this->generateNonce(),
            'notify_url'       => "https://tinydream.antit.top/index.php",
            'openid'           => $uid,
            'out_trade_no'     => ''.$oid,
            'spbill_create_ip' => $this->notify_Url,
            'total_fee'        => intval($this->minerPrice),
            'trade_type'       => 'JSAPI',
        );



        //var_export($params);

        //var_export($params);

        $params['sign'] = $this->calculateSign($params, $this->Mhc_Key);

        $xml = $this->getXMLFromArray($params);


        file_put_contents('unifiedorder01.txt',$xml);

        $result =  $this->https_post("https://api.mch.weixin.qq.com/pay/unifiedorder",$xml);



        file_put_contents('unifiedorder02.txt',$result);

        $xml = simplexml_load_string($result);




        if($xml->return_code != "SUCCESS"){
            $errmsg = RESPONDINSTANCE('58');
            $errmsg['error']['return_code'] = (string)$xml->return_code;
            $errmsg['error']['return_msg'] = (string)$xml->return_msg;
            return $errmsg;
        }



        $backMsg = RESPONDINSTANCE('0');

        $backMsg['appid'] = (string)$xml->appid;
        $backMsg['mch_id'] = (string)$xml->mch_id;
        $backMsg['nonce_str'] = (string)$xml->nonce_str;
        $backMsg['sign'] = (string)$xml->sign;
        $backMsg['prepay_id'] = (string)$xml->prepay_id;
        $backMsg['trade_type'] = (string)$xml->trade_type;

//        var_export($backMsg);
        file_put_contents('unifiedorder03.txt',json_encode($backMsg, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return $backMsg;
    }

    function calculateSign($arr, $key)
    {
        ksort($arr);
        $buff = "";
        foreach ($arr as $k => $v) {
          //  if ($k != "sign" && $k != "key" && $v != "" && !is_array($v)){
                $buff .= $k . "=" . $v . "&";
          //  }

        //    echo $buff.'</br>';
        }
        $buff = trim($buff, "&");
        //echo 'result:'.$buff.'</br>';
        //var_export($arr);
        //echo $buff;
        file_put_contents("buff.txt",$buff);

        $result = strtoupper(md5($buff . "&key=" . $key));

        return $result;
    }
    /**
     * Get xml from array
     */
    function getXMLFromArray($arr)
    {
        $xml = "<xml>";
        foreach ($arr as $key=>$val) {
            if (is_numeric($val)) {
                $xml =$xml. '<'.$key.'>'.$val.'</'.$key.'>';
            } else {
                //$xml =$xml. '<'.$key.'><![CDATA['.$val.']]></'.$key.'>';
                $xml =$xml. '<'.$key.'>'.$val.'</'.$key.'>';
            }
        }
        $xml =$xml. '</xml>';
        return $xml;
    }



    private function https_post($url,$param)
    {
        $ch = curl_init();
        //如果$param是数组的话直接用
        curl_setopt($ch, CURLOPT_URL, $url);
        //如果$param是json格式的数据，则打开下面这个注释
        // curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        //         'Content-Type: application/json',
        //         'Content-Length: ' . strlen($param))
        // );
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        //如果用的协议是https则打开鞋面这个注释
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

        $data = curl_exec($ch);

        curl_close($ch);
        return $data;

    }

}



class WeixinPay {
    protected $appid;
    protected $mch_id;
    protected $key;
    protected $openid;
    protected $out_trade_no;
    protected $body;
    protected $total_fee;
    protected $spbill_create_ip;
    function __construct($appid, $openid, $mch_id, $key,$out_trade_no,$body,$total_fee,$spbill_create_ip) {
        $this->appid = $appid;
        $this->openid = $openid;
        $this->mch_id = $mch_id;
        $this->key = $key;
        $this->out_trade_no = $out_trade_no;
        $this->body = $body;
        $this->total_fee = $total_fee;
        $this->spbill_create_ip = $spbill_create_ip;
    }
    public function pay() {
        //统一下单接口
        $return = $this->weixinapp();
        return $return;
    }
    //统一下单接口
    private function unifiedorder() {
        $url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
        $parameters = array(
            'appid' => $this->appid, //小程序ID
            'mch_id' => $this->mch_id, //商户号
            'nonce_str' => $this->createNoncestr(), //随机字符串
//            'body' => 'test', //商品描述
            'body' => $this->body,
//            'out_trade_no' => '2015450806125348', //商户订单号
            'out_trade_no'=> $this->out_trade_no,
//            'total_fee' => floatval(0.01 * 100), //总金额 单位 分
            'total_fee' => $this->total_fee,
//            'spbill_create_ip' => $_SERVER['REMOTE_ADDR'], //终端IP
            'spbill_create_ip' => $this->spbill_create_ip, //终端IP
            'notify_url' => 'http://www.weixin.qq.com/wxpay/pay.php', //通知地址  确保外网能正常访问
            'openid' => $this->openid, //用户id
            'trade_type' => 'JSAPI'//交易类型
        );
        //统一下单签名
        $parameters['sign'] = $this->getSign($parameters);
        $xmlData = $this->arrayToXml($parameters);
        $return = $this->xmlToArray($this->postXmlCurl($xmlData, $url, 60));
        return $return;
    }
    private static function postXmlCurl($xml, $url, $second = 30)
    {
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); //严格校验
        //设置header
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        //post提交方式
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
        curl_setopt($ch, CURLOPT_TIMEOUT, 40);
        set_time_limit(0);
        //运行curl
        $data = curl_exec($ch);
        //返回结果
        if ($data) {
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            curl_close($ch);
            throw new WxPayException("curl出错，错误码:$error");
        }
    }
    //数组转换成xml
    private function arrayToXml($arr) {
        $xml = "<root>";
        foreach ($arr as $key => $val) {
            if (is_array($val)) {
                $xml .= "<" . $key . ">" . arrayToXml($val) . "</" . $key . ">";
            } else {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            }
        }
        $xml .= "</root>";
        return $xml;
    }
    //xml转换成数组
    private function xmlToArray($xml) {
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $xmlstring = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        $val = json_decode(json_encode($xmlstring), true);
        return $val;
    }
    //微信小程序接口
    private function weixinapp() {
        //统一下单接口
        $unifiedorder = $this->unifiedorder();
//        print_r($unifiedorder);

        $backMsg = RESPONDINSTANCE('0');
        $backMsg['appId'] = $this->appid;
        $backMsg['timeStamp'] = '' . time() . '';
        $backMsg['nonceStr'] = $this->createNoncestr();
        $backMsg['package'] = 'prepay_id='.$unifiedorder['prepay_id'];
        $backMsg['signType'] = 'MD5';
        $parameters = array(
            'appId' => $this->appid, //小程序ID
            'timeStamp' => '' . time() . '', //时间戳
            'nonceStr' => $this->createNoncestr(), //随机串
            'package' => 'prepay_id=' . $unifiedorder['prepay_id'], //数据包
            'signType' => 'MD5'//签名方式
        );
        //签名
        $backMsg['paySign'] = $this->getSign($parameters);
        return $backMsg;
    }
    //作用：产生随机字符串，不长于32位
    private function createNoncestr($length = 32) {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }
    //作用：生成签名
    private function getSign($Obj) {
        foreach ($Obj as $k => $v) {
            $Parameters[$k] = $v;
        }
        //签名步骤一：按字典序排序参数
        ksort($Parameters);
        $String = $this->formatBizQueryParaMap($Parameters, false);
        //签名步骤二：在string后加入KEY
        $String = $String . "&key=" . $this->key;
        //签名步骤三：MD5加密
        $String = md5($String);
        //签名步骤四：所有字符转为大写
        $result_ = strtoupper($String);
        return $result_;
    }
    ///作用：格式化参数，签名过程需要使用
    private function formatBizQueryParaMap($paraMap, $urlencode) {
        $buff = "";
        ksort($paraMap);
        foreach ($paraMap as $k => $v) {
            if ($urlencode) {
                $v = urlencode($v);
            }
            $buff .= $k . "=" . $v . "&";
        }
        $reqPar = '';
        if (strlen($buff) > 0) {
            $reqPar = substr($buff, 0, strlen($buff) - 1);
        }
        return $reqPar;
    }
}

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


        $uids ='';
        foreach ($array as $item) {
            $uids=  $uids.$item['uid'].'|';
        }

        $headIcons = DBResultToArray($DSM->SelectDatasFromTable($DSM->TName('tUser'),
            ['uid'=>$uids],'false','uid,nickname,headicon'));


        foreach ($array as $key=>$item) {
            $array[$key]['headicon'] = $headIcons[$item['uid']]['headicon'];
            $array[$key]['nickname'] = $headIcons[$item['uid']]['nickname'];
        }

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

        if($RunningResult['result']=="false"){
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
            $backMsg['pool'] = DreamPoolManager::Pool($actionList['buy']['pid']);
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

        $did = $actionList['pay']['did'];
        if(isset($_REQUEST['did'])){
            $did = $_REQUEST['did'];
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
                'did'=>$did,
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
        $NumberArray = AwardManager::PayOrderAndCreateLottery($actionList['pay']['pid'],$uid,$did,$oid,$startIndex,$endIndex);
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
        }/*else{
            $orders = $orders[0];
        }*/
        //echo json_encode($orders);
        $contains = [];
        $count = 0;

        foreach ($orders as $key=>$value) {

            if(array_key_exists($value['pid'],$contains)){
                continue;
            }
            $contains[$value['pid']] = true;
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

    //统一下单
    public function WxPay($oid,$bill,$uid){
        include 'init.php';
        //$payResult = require(__DIR__ . '/test/wxpay-miniapp.php');

        $config = [
            // 微信支付参数
            'wechat' => [
                // 沙箱模式
                'debug'      => false,
                // 应用ID
                'app_id'     => $GLOBALS['options']['APP_ID'],
                // 微信支付商户号
                'mch_id'     => $GLOBALS['options']['MCH_ID'],
                /*
                 // 子商户公众账号ID
                 'sub_appid'  => '子商户公众账号ID，需要的时候填写',
                 // 子商户号
                 'sub_mch_id' => '子商户号，需要的时候填写',
                */
                // 微信支付密钥
                'mch_key'    => $GLOBALS['options']['MCH_KEY'],
                // 微信证书 cert 文件
                'ssl_cer'    => __DIR__ . '/cert/apiclient_cert.pem',
                // 微信证书 key 文件
                'ssl_key'    => __DIR__ . '/cert/apiclient_key.pem',
                // 缓存目录配置
                'cache_path' => '',
                // 支付成功通知地址
                'notify_url' => '',
                // 网页支付回跳地址
                'return_url' => '',
            ]
        ];


        $options = [
            'out_trade_no'     => $oid, // 订单号
            'total_fee'        => $bill, // 订单金额，**单位：分**
            'body'             => '小梦想互助-购买梦想', // 订单描述
            'spbill_create_ip' => $_SERVER["REMOTE_ADDR"], // 支付人的 IP
            'openid'           => $uid , // 支付人的 openID
            'notify_url'       => 'http://localhost/notify.php', // 定义通知URL
        ];

// 实例支付对象
        $pay = new \Pay\Pay($config);

        try {
            $result = $pay->driver('wechat')->gateway('miniapp')->apply($options);
            //echo '<pre>';
            //var_export($result);
            $backMsg = RESPONDINSTANCE('0');
            foreach ($result as $key=>$item) {
                $backMsg[$key] = $item;
            }
            return $backMsg;

        } catch (Exception $e) {
//            echo $e->getMessage();
            $backMsg = RESPONDINSTANCE('58');
            $backMsg['error'] = $e->getMessage();
            return $backMsg;
        }
       // echo $oid.' '.$bill.' '.$uid;
        //return (new WechatPay($oid, $bill))->getPayResponse($oid,$uid);

       /* $payObject = new WeixinPay(
            $GLOBALS['options']['APP_ID'],
            $uid,
            $GLOBALS['options']['MCH_ID'],
            $GLOBALS['options']['MCH_KEY'],
            $oid,
            '小梦想互助-购买一个梦想',
            $bill,$_SERVER["REMOTE_ADDR"]);
        return $payObject->pay();*/
    }


    //获取各种梦想池数量
    public function GetPoolsCountInfo($uid){
        $link = $this->DBLink();

        $sqlr = "SELECT COUNT(*) FROM `dreampool` WHERE `state`='RUNNING'";
        $sqlf = "SELECT COUNT(*) FROM `dreampool` WHERE `state`='FINISHED'";
        $sqlu = "SELECT `pid` FROM `order` WHERE `uid`='$uid'";


        $rResult = DBResultToArray(mysql_query($sqlr,$link),true);
        $fResult = DBResultToArray(mysql_query($sqlf,$link),true);
        $uResult = DBResultToArray(mysql_query($sqlu,$link),true);


        $backMsg = RESPONDINSTANCE('0');
        if(!empty($rResult)) {
            $backMsg['rcount'] = $rResult[0]['COUNT(*)'];
        }else{
            $backMsg['rcount'] = 0;
        }
        if(!empty($uResult)) {

            $countArr = [];

            foreach ($uResult as $value){
                if(!array_key_exists($value['pid'],$countArr)) {
                    $countArr[$value['pid']] = true;
                }
            }


            $backMsg['ucount'] = count($countArr);
        }else{
            $backMsg['ucount'] = 0;
        }
        if(!empty($fResult)) {
            $backMsg['fcount'] = $fResult[0]['COUNT(*)'];
        }else{
            $backMsg['fcount'] = 0;
        }

        return $backMsg;
    }

    //用户获取参与中的梦想池
    public function GetRunningPoolInfoByRange($min,$count){
        $link = $this->DBLink();

        $sql = "SELECT * FROM `dreampool` WHERE `state`='RUNNING' ORDER BY `ptime` DESC LIMIT $min,$count";

        $tResult = DBResultToArray(mysql_query($sql,$link),true);

        $backMsg = RESPONDINSTANCE('0');
        $backMsg['Pools'] = $tResult;
        return $backMsg;

    }

    //用户获取参与中的梦想池
    public function GetFinishedPoolInfoByRange($min,$count){
        $link = $this->DBLink();

        $sql = "SELECT * FROM `dreampool` WHERE `state`='FINISHED' ORDER BY `ptime` DESC LIMIT $min,$count";

        $tResult = DBResultToArray(mysql_query($sql,$link),true);

        $backMsg = RESPONDINSTANCE('0');
        $backMsg['Pools'] = $tResult;
        return $backMsg;
    }

    //用户获取参与中的梦想池
    public function GetJoinedPoolInfoByRange($uid,$min,$count){
        $link = $this->DBLink();

        $sql = "SELECT `pid` FROM `order` WHERE `uid`='$uid'";

        $tResult = DBResultToArray(mysql_query($sql,$link),true);

        $condition = "";
        $seek = 0;
        $seekRecord = [];
        foreach ($tResult as $value){
            if(!isset($seekRecord[$value['pid']])) {
                $seekRecord[$value['pid']] = true;
            }else{
                continue;
            }
            if($seek>=$min && $seek<($min+$count)){
                //$condition = $condition.'`pid`="'.$value['pid'].'"';
                $condition = $condition.$value['pid'].'|';
            }
            $seek++;
        }

        $sresult = $this->SelectDatasFromTable($this->TName('tPool'),
                [
                    'pid'=>$condition
                ]
            );

        $array = DBResultToArray($sresult);

        $backMsg = RESPONDINSTANCE('0');
        $backMsg['Pools'] = $array;
        return $backMsg;

        echo $condition;
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
                    $cResult[$key]['ustatus'] = "NONE|NOTAWARD";
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

    //完善梦想后将中奖梦想提交审核
    public function SubmitDreamToVerify($uid,$did){
        //判断梦想是否中奖并没有超越时限
        if(!AwardManager::AwardedDreamVailid($did)){

            DreamManager::OnDreamFailed($did);//设置梦想失效

            return RESPONDINSTANCE('45');//不存在该中奖梦想或中奖梦想过期
        }

        //判断用户是否提交了实名认证
        if(!UserManager::IdentifyRealNameUser($uid)){
            return RESPONDINSTANCE('42');
        }

        //梦想状态需为DOING
        if(!DreamManager::OnDreamVerify($did)){
            return RESPONDINSTANCE('46');
        }

        return RESPONDINSTANCE('0');
    }

    //进入参与记录页面调用
    public function ShowOrdersInPoolStart($pid){
        $link = $this->DBLink();http:
        $sql = 'SELECT COUNT(*) FROM `order` WHERE `pid`="'.$pid.'" AND `state`="SUCCESS"';
        $result = mysql_query($sql,$link);
        if(!$result){
            $cResult = [];
        }else{
            $cResult = mysql_fetch_array($result)[0];
        }
        $backMsg = RESPONDINSTANCE('0');
        $backMsg['ordCount'] = $cResult;
        return $backMsg;
    }

    //根据范围获取订单及用户信息
    public function GetOrdersInPoolByRange($pid,$min,$max){
        $link = $this->DBLink();

        $sql = 'SELECT * FROM `order` WHERE `pid`="'.$pid.'" AND `state`="SUCCESS" LIMIT '.$min.','.$max;

        $cResult = DBResultToArray(mysql_query($sql,$link),true);

        $user = [];

        $uids = "";

        $dids = "";

        $i = 0;

        foreach ($cResult as $item) {
            $user[$i++] = $item['uid'];
            $uids = $uids.$item['uid'].'|';
            $dids = $dids.$item['did'].'|';
        }

       // echo json_encode($user);

        $array = DBResultToArray($this->SelectDatasFromTable($this->TName('tUser'),
            [
                'uid'=>$uids
            ]));

        $user=[];

        foreach ($array as $key => $item) {
            $user[$item['uid']] = $item['tele'];
        }

        $darray = DBResultToArray($this->SelectDatasFromTable($this->TName('tDream')
            ,[
                'did'=>$dids
            ]));
        //echo json_encode($darray);


        foreach ($cResult as $key => $item) {
            //echo 'kkkkkk:'.$key.'=>'.$item.'</br>';
            $cResult[$key]['tele'] = substr_replace($user[$cResult[$key]['uid']],'****',3,4);
            $cResult[$key]['dtitle'] = $this->subtext($darray[$cResult[$key]['did']]['title'],10);
        }

        $backMsg = RESPONDINSTANCE('0');

        $backMsg['orders'] = $cResult;

        return $backMsg;
    }


    public function subtext($text, $length)
    {
        if(mb_strlen($text, 'utf8') > $length) {
            return mb_substr($text, 0, $length, 'utf8').'...';
        } else {
            return $text;
        }

    }


    //查看梦想池某用户的详细信息
    public function ShowPoolDetails($uid,$pid){
        $resultArray = DBResultToArray($this->SelectDataFromTable(
            $this->TName('tLottery'),
            [
                'pid'=>$pid,
                'uid'=>$uid,
                '_logic'=>'AND'
            ]
        ));
        $backMsg = RESPONDINSTANCE('0');
        $backMsg['lottey']=$resultArray;
        return $backMsg;
    }

    public function DreamServersManager(){
        parent::__construct();
	}
}
?>