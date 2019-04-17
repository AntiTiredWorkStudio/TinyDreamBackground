<?php
//引用此页面前需先引用conf.php
error_reporting(E_ALL ^ E_DEPRECATED);

LIB('db');
LIB('us');
LIB('dr');
LIB('dp');
LIB('aw');
LIB('ub');
LIB('no');
LIB('tr');

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
		echo json_encode(DreamServersManager::GetOrderLikeTypeByIndex("CO%",0,8));
        //return self::GenerateOrderToRefundOid('101541939435');
        //echo ConnectArrayByChar(['1','2','3'],'、');
        //echo self::GenerateOrderID();
        return "DreamServersManager"; // TODO: Change the autogenerated stub
    }
	
	//下单动作支付完成,更新订单
    /*public function OrderPaid($uid,$oid,$bill,$pcount)
    {

        //更新订单信息
        $condition = [
            'uid'=>$uid,
            'oid'=>$oid,
            'state'=>'SUBMIT',
            '_logic' => 'AND'
        ];

        if(!DBResultExist($this->SelectDataFromTable($this->TName('tOrder'),$condition))){
			
			$conditionSuccess = [
				'uid'=>$uid,
				'oid'=>$oid,
				'state'=>'SUCCESS',
				'_logic' => 'AND'
			];
			if(DBResultExist($this->SelectDataFromTable($this->TName('tOrder'),$conditionSuccess))){
				//更新梦想did
				return RESPONDINSTANCE('75');
			}
            return RESPONDINSTANCE('20');
        }

		//更新订单信息
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
            UserBehaviourManager::OnBehave($uid,PAY);
            $numArray = [];
            foreach ($NumberArray as $key => $value) {
                array_push($numArray,$value['lid']);
            }
            NoticeManager::CreateNotice($uid,//创建通知——购买梦想
                NOTICE_BUY,
                [
                    'ptitle'=>'梦想互助'.$actionList['pay']['pid'].'期',
                    'lids'=>ConnectArrayByChar($numArray,'、')
                ],
                NoticeManager::CreateAction(
                    'buy',
                    [
                        'pid'=>$actionList['pay']['pid']
                    ]
                )
            );

            $backMsg = RESPONDINSTANCE('0');
            $backMsg['numbers'] = $NumberArray;
            $backMsg['actions'] = 'clear';
            return $backMsg;
        }
        return RESPONDINSTANCE('20');
    }*/

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

    //创建未支付订单
    public static function GenerateEmptyOrder($uid,$pid,$did,$bill,$oidType=1,$dcount=0){
        $DSM = new DreamServersManager();
        $orderArray = [
            "oid"=>self::GenerateOrderID($oidType),
            "uid"=>$uid,
            "pid"=>$pid,
            "bill"=>$bill,
            "ctime"=>PRC_TIME(),//创建时间
            "ptime"=>0,
            "state"=>"SUBMIT",
            "dcount"=>0,
            "did"=>$did,
            "traid"=>""
        ];
        $DSM->InsertDataToTable($DSM->TName('tOrder'),$orderArray);
        return $orderArray;
    }
	
	//通过范围获取订单
	public static function GetOrderLikeTypeByIndex($type,$seek,$count){ 
		$DSM = new DreamServersManager();
        $orderArray = DBResultToArray($DSM->SelectDataByQuery($DSM->TName('tOrder'),
			self::Limit(
					self::C_And(
						self::FieldIsValue('state','SUCCESS'),
						self::FieldLikeValue('did',$type)
					),
					$seek,
					$count
				)
		),true);
        return $orderArray;
	}

    //便捷版退款
    public static function Refund($oid,$rebill=-1,$reid="",$reason=""){
        $DSM = new DreamServersManager();
        return $DSM->WxRefund($oid,$rebill,$reid,$reason);
    }

    //便捷版统一下单
    public static function UnifiedOrder($oid,$bill,$uid,$type="web"){
        $target = [
            'web'=>function($oid,$bill,$uid){
                $DSM = new DreamServersManager();
                return $DSM->WxPayWeb($oid,$bill,$uid);
            },
            'miniapp'=>function($oid,$bill,$uid){
                $DSM = new DreamServersManager();
                return $DSM->WxPay($oid,$bill,$uid);
            }
        ];
        return $target[$type]($oid,$bill,$uid);
    }

    //订单完成
    public static function OrderFinished($oid,$pars=[]){
		$traid = "";
        if(file_exists($oid.'.txt')){
            $traid = file_get_contents($oid.'.txt');
            unlink($oid.'.txt');
        }
        $pars = $pars;
        $pars['ptime'] = PRC_TIME();
        $pars['traid'] = $traid;
        $DSM = new DreamServersManager();


        if(!DBResultExist($DSM->SelectDataByQuery($DSM->TName('tOrder'),self::C_And(self::FieldIsValue('oid',$oid),self::FieldIsValue('state',"SUBMIT"))))){
            return false;
        }

        //更新订单信息
        $DSM->UpdateDataToTableByQuery(
            $DSM->TName('tOrder'),
            $pars,
            self::FieldIsValue('oid',$oid)
        );
        return true;
    }

    //计算退款单号
    public static function GenerateOrderToRefundOid($oid){

        $sub01 = 1000+(1536%substr($oid,0,4));
        $sub02 = 1000+(2049%substr($oid,5,4));
        $sub03 = 1000+(3356%substr($oid,9,4));

        return $sub01.$sub02.$sub03;
    }
    //生成订单号
    public static function GenerateOrderID($i=1){
        $DSM = new DreamServersManager();
        //生成订单号
        do{
            $newOrderID = $i*100000000000+((PRC_TIME()%999999).(rand(10000,99999)));
        }while($DSM->SelectDataFromTable('tOrder',['oid'=>$newOrderID,'_logic'=>' ']));
        return $newOrderID;
    }

    //获得首页滚动购买信息
    public static function GetMainOrders(){
        //未实现
        $DSM = new DreamServersManager();
        $sql = 'SELECT * FROM `order` WHERE `state`="SUCCESS" AND `did` NOT LIKE "CO%" order By `ptime` DESC LIMIT 0,8';
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
			if(self::DidFlag($item['did'],"DR")){
                $array[$key]['ptype'] = "STANDARD";
            }else if(self::DidFlag($item['did'],"TR")){
                $array[$key]['ptype'] = "TRADE";
            }
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
        $pool = DreamPoolManager::Pool($pid);


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

        UserBehaviourManager::OnBehave($uid,JOIN);

        $backMsg = RESPONDINSTANCE('0');
		
        if(isset($pool['ptype']) && $pool['ptype'] == "TRADE"){//小生意互助添加
            $trade = TradeManager::GetTradeInfoByPid($pid);
            $backMsg['actions'] = [
                //'selectdream'=>['uid'=>$uid],//选择梦想
                'buy' => ['uid' => $uid, 'pid' => $pid, 'dayLim' => $dayLimit, 'less' => $RunningResult['pless'], 'dream' => $trade]//购买互助
            ];
        }else {//普通梦想互助
            if (!DreamManager::HasSubmitedDream($uid)) {
                //跳转至编辑梦想页面
                $backMsg['actions'] = [
                    'editdream' => ['uid' => $uid],//编辑梦想
                    //'selectdream'=>['uid'=>$uid],//选择梦想
                    'buy' => ['uid' => $uid, 'pid' => $pid, 'dayLim' => $dayLimit, 'less' => $RunningResult['pless']]//购买互助
                ];
            } else {
                $userFirstDream = DreamManager::UserFirstSubmitedDream($uid);
                if (!empty($userFirstDream)) {
                    $userFirstDream = $userFirstDream[0];
                }
                //跳转至选择梦想界面
                $backMsg['actions'] = [
                    //'selectdream'=>['uid'=>$uid],//选择梦想
                    'buy' => ['uid' => $uid, 'pid' => $pid, 'dayLim' => $dayLimit, 'less' => $RunningResult['pless'], 'dream' => $userFirstDream]//购买互助
                ];
            }
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

        DreamServersManager::ClearSubmitOrder($actionList['buy']['uid']);//修改清除选项

        $did = "";
        $orderType = "";
        if(isset($actionList['buy']['dream']['tid'])){//小生意互助添加
            $did = $actionList['buy']['dream']['tid'];
            $orderType = "TRADE";
        }else if(isset($actionList['buy']['dream']['did'])){//小梦想互助
            $did = $actionList['buy']['dream']['did'];
            $orderType = "STANDARD";
        }

        $orderArray = [
            "oid"=>self::GenerateOrderID(),
            "uid"=>$actionList['buy']['uid'],
            "pid"=>$actionList['buy']['pid'],
            "bill"=>0,
            "ctime"=>PRC_TIME(),
            "ptime"=>0,
            "state"=>"SUBMIT",
            "dcount"=>0,
            "did"=>$did
        ];
        $insresult = $this->InsertDataToTable($this->TName('tOrder'),$orderArray);
        if($insresult){
            $backMsg = RESPONDINSTANCE('0');
            $backMsg['actions'] = [
                "pay"=>[
                    "info"=>[
                        //返回基本商户信息及平台信息（后续配置）
                    ],
                    "orderType"=>$orderType,
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
        $noticePrefix = "梦想互助";
        if(isset($actionList['pay']['orderType']) && $actionList['pay']['orderType']=="TRADE"){
            $noticePrefix = "小生意互助";
        }else{
            if(isset($_REQUEST['did'])){
                $did = $_REQUEST['did'];
            }
            $noticePrefix = "梦想互助";
        }

        //更新订单信息
        $condition = [
            'uid'=>$uid,
            'oid'=>$oid,
            'state'=>'SUBMIT',
            '_logic' => 'AND'
        ];

        if(!DBResultExist($this->SelectDataFromTable($this->TName('tOrder'),$condition))){
			
			$conditionSuccess = [
				'uid'=>$uid,
				'oid'=>$oid,
				'state'=>'SUCCESS',
				'_logic' => 'AND'
			];
			if(DBResultExist($this->SelectDataFromTable($this->TName('tOrder'),$conditionSuccess))){
				//更新梦想did
				$result = $this->UpdateDataToTable(
					$this->TName('tOrder'),
					[
						'dcount'=>$pcount,
						'did'=>$did,
						'ptime'=>PRC_TIME()
					],
					$conditionSuccess
				);
				return RESPONDINSTANCE('75');
			}
            return RESPONDINSTANCE('20');
        }
		
		$traid = "";
		if(file_exists($oid.'.txt')){
			$traid = file_get_contents($oid.'.txt');
			unlink($oid.'.txt');
		}

		//更新订单信息
        $result = $this->UpdateDataToTable(
            $this->TName('tOrder'),
            [
                'state'=>'SUCCESS',
                'bill'=>$bill,
                'dcount'=>$pcount,
                'did'=>$did,
                'ptime'=>PRC_TIME(),
				'traid'=>$traid
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
            UserBehaviourManager::OnBehave($uid,PAY);
            $numArray = [];
            foreach ($NumberArray as $key => $value) {
                array_push($numArray,$value['lid']);
            }
            NoticeManager::CreateNotice($uid,//创建通知——购买梦想
                NOTICE_BUY,
                [
                    'ptitle'=>$noticePrefix.$actionList['pay']['pid'].'期',
                    'lids'=>ConnectArrayByChar($numArray,'、')
                ],
                NoticeManager::CreateAction(
                    'buy',
                    [
                        'pid'=>$actionList['pay']['pid']
                    ]
                )
            );

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
		$condition = self::FieldIsValue('uid',$uid);
		$total = -1;
		if(isset($_REQUEST['seek']) && isset($_REQUEST['count'])){
			$total = $this->CountTableRowByQuery($this->TName('tOrder'),$condition);
			$condition = self::Limit($condition,$_REQUEST['seek'],$_REQUEST['count']);
		}
        $array = DBResultToArray($this->SelectDataByQuery($this->TName('tOrder'),
           $condition),true);
		if($total != -1){
			$backMsg = RESPONDINSTANCE('0');
			$backMsg['total'] = $total;
			$backMsg['orders'] = $array;
			return $backMsg;
		}   
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

    //退款
    public function WxRefund($oid,$refundBill = -1,$reid="",$reason=""){
        $order = DBResultToArray($this->SelectDataByQuery($this->TName('tOrder'),self::FieldIsValue('oid',$oid)),true);
        if(!empty($order)){
            $order = $order[0];
        }else{
            return RESPONDINSTANCE('95');
        }

        $oid = $order['oid'];
        $traid = $order['traid'];
        $bill = $order['bill'];
        $refundid = ($reid=="")?self::GenerateOrderToRefundOid($oid):$reid;

        include 'init.php';

        // 加载配置参数
        $config = [
            'wechat'=>[
                // 沙箱模式
                'debug'      => false,
                // 应用ID
                'app_id'     => $GLOBALS['options']['WEB_APP_ID'],
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
                'ssl_cer'    => ROOT_DIR().'/cert/apiclient_cert.pem',
                // 微信证书 key 文件
                'ssl_key'    =>  ROOT_DIR().'/cert/apiclient_key.pem',
                // 缓存目录配置
                'cache_path' => '',
                // 支付成功通知地址
                'notify_url' => 'https://tinydream.antit.top/paid.php',
                // 网页支付回跳地址
                'return_url' => '',
            ]
        ];

        if($refundBill == -1){
            $refundBill = $bill;
        }



// 实例支付对象
        $pay = new \Pay\Pay($config);
// 订单退款参数
        $options = [
            'out_trade_no'  => $oid, // 原商户订单号
            'out_refund_no' => $refundid, // 退款订单号
            'total_fee'     => $bill,   // 原订单交易总金额
            'refund_fee'    => $refundBill,  // 申请退款金额
        ];
        try {
            $result = $pay->driver('wechat')->gateway('transfer')->refund($options);
            $backMsg = RESPONDINSTANCE('0');
            foreach ($result as $key=>$item) {
                $backMsg[$key] = $item;
            }
			self::CreateRefundRecord($refundid,$oid,$refundBill,$reason,"SUCCESS");
            return $backMsg;
        } catch (Exception $e) {
            return RESPONDINSTANCE('96',":退款异常");
            $backMsg['error'] = $e->getMessage();
			self::CreateRefundRecord($refundid,$oid,0,$reason,"FAILED");
            return $backMsg;
        }
    }
	
	//用户退款列表信息
	public function RefundList($uid,$seek,$count){
		$array = DBResultToArray($this->SelectDataByQuery($this->TName('tRefund'),
			self::Limit(
				self::C_And(
					self::FieldIsValue('state','SUCCESS'),
					self::FieldIsValue('uid',$uid)
				),
				$seek,
				$count
			)
		),true);
        $CountRefund = $this->CountTableRowByQuery($this->TName('tOperation'),
			self::C_And(
				self::FieldIsValue('state','SUCCESS'),
				self::FieldIsValue('uid',$uid)
			));
		$backMsg = RESPONDINSTANCE('0');
		$backMsg['count'] =  $CountRefund;
		$backMsg['refund'] = $array;
		return $backMsg;
	}
	
	//创建退款记录
	public static function CreateRefundRecord($reid,$oid,$bill,$reason,$state){
		$DSM = new DreamServersManager();
		$refundArray = [
			"reid"=>$reid,
			"oid"=>$oid,
			"bill"=>$bill,
			"time"=>PRC_TIME(),
			"state"=>$state,
			"reason"=>$reason,
		];
		$DSM->InsertDataToTable($DSM->TName('tRefund'),$refundArray);
	}

    //统一下单公众号
    public function WxPayWeb($oid,$bill,$uid){
        include 'init.php';

        // 加载配置参数
        $config = [
            'wechat'=>[
                // 沙箱模式
                'debug'      => false,
                // 应用ID
                'app_id'     => $GLOBALS['options']['WEB_APP_ID'],
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
                'notify_url' => 'https://tinydream.antit.top/paid.php',
                // 网页支付回跳地址
                'return_url' => '',
            ]
        ];

// 支付参数
        $options = [
            'out_trade_no'     => $oid, // 订单号
            'total_fee'        => $bill, // 订单金额，**单位：分**
            'body'             => '小梦想互助-购买梦想', // 订单描述
            'spbill_create_ip' => $_SERVER["REMOTE_ADDR"], // 支付人的 IP
            'openid'           => $uid, // 支付人的 openID
            'notify_url'       => 'https://tinydream.antit.top/paid.php', // 定义通知URL
        ];


// 实例支付对象
        $pay = new \Pay\Pay($config);
        try {
            $result = $pay->driver('wechat')->gateway('mp')->apply($options);
            $backMsg = RESPONDINSTANCE('0');
            foreach ($result as $key=>$item) {
                $backMsg[$key] = $item;
            }
            return $backMsg;
        } catch (Exception $e) {
            $backMsg = RESPONDINSTANCE('58');
            $backMsg['error'] = $e->getMessage();
            return $backMsg;
        }
    }

    //统一下单
    public function WxPay($oid,$bill,$uid){
        include 'init.php';

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
                'notify_url' => 'https://tinydream.antit.top/paid.php',
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
            'notify_url'       => 'https://tinydream.antit.top/paid.php', // 定义通知URL
        ];

// 实例支付对象
        $pay = new \Pay\Pay($config);

        try {
            $result = $pay->driver('wechat')->gateway('miniapp')->apply($options);
            $backMsg = RESPONDINSTANCE('0');
            foreach ($result as $key=>$item) {
                $backMsg[$key] = $item;
            }
            return $backMsg;

        } catch (Exception $e) {
            $backMsg = RESPONDINSTANCE('58');
            $backMsg['error'] = $e->getMessage();
            return $backMsg;
        }
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

        DreamPoolManager::UpdateAllRunningPool();//获取前更新梦想池状态

        $link = $this->DBLink();

        $sql = "SELECT * FROM `dreampool` WHERE `state`='RUNNING' ORDER BY `ptime` DESC LIMIT $min,$count";
        if(isset($_REQUEST['redpack'])) {
            $sql = "SELECT * FROM `dreampool` WHERE `state`='RUNNING' AND `ptype`='STANDARD' ORDER BY `ptime` DESC LIMIT $min,$count";
        }

        $tResult = DBResultToArray(mysql_query($sql,$link),true);

        $backMsg = RESPONDINSTANCE('0');
        $backMsg['Pools'] = $tResult;
        return $backMsg;

    }

    //用户获取参与中的梦想池
    public function GetFinishedPoolInfoByRange($min,$count){

        DreamPoolManager::UpdateAllRunningPool();//获取前更新梦想池状态

        $link = $this->DBLink();

        $sql = "SELECT * FROM `dreampool` WHERE `state`='FINISHED' ORDER BY `ptime` DESC LIMIT $min,$count";

        $tResult = DBResultToArray(mysql_query($sql,$link),true);

        $backMsg = RESPONDINSTANCE('0');
        $backMsg['Pools'] = $tResult;
        return $backMsg;
    }

    //用户获取参与中的梦想池
    public function GetJoinedPoolInfoByRange($uid,$min,$count){

        DreamPoolManager::UpdateAllRunningPool();//获取前更新梦想池状态

        $link = $this->DBLink();

        $sql = "SELECT `pid` FROM `order` WHERE `uid`='$uid' AND `state`='SUCCESS' ORDER BY `ptime` DESC";

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
                ],false,'*',
				[
					'by'=>'ptime',
					'rule'=>'DESC'
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

        if(!DreamManager::IsDreamState($did,'DOING')){
            return RESPONDINSTANCE('46');
        }
        //梦想状态需为DOING
        if(!DreamManager::OnDreamVerify($did)){
            return RESPONDINSTANCE('46');
        }

        $teleInfo = UserManager::GetUserTele($uid);

        $backMsg = RESPONDINSTANCE('0');
        $backMsg['tele'] = $teleInfo;

        return $backMsg;
    }

    //进入参与记录页面调用
    public function ShowOrdersInPoolStart($pid){
        $link = $this->DBLink();
        $sql = 'SELECT COUNT(*) FROM `order` WHERE `pid`="'.$pid.'" AND `state`="SUCCESS"';
		
//        $sql = 'SELECT SUM(`dcount`) FROM `order` WHERE `pid`="'.$pid.'" AND `state`="SUCCESS"';
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

    public static function DidFlag($sources,$flag){
        return substr($sources,0,strlen($flag)) == $flag;
    }

    //根据范围获取订单及用户信息
    public function GetOrdersInPoolByRange($pid,$min,$max){
        $link = $this->DBLink();

        $sql = 'SELECT * FROM `order` WHERE `pid`="'.$pid.'" AND `state`="SUCCESS" LIMIT '.$min.','.$max;

        $cResult = DBResultToArray(mysql_query($sql,$link),true);

        $user = [];

        $uids = "";

        $dids = "";

        $tids = "";

        $i = 0;

        foreach ($cResult as $item) {
            //echo "did:".substr($item['did'], 0,2)."</br>";
            $user[$i++] = $item['uid'];
            $uids = $uids.$item['uid'].'|';

            if(self::DidFlag($item['did'],"DR")){
                $dids = $dids.$item['did'].'|';
            }else if(self::DidFlag($item['did'],"TR")){
                $tids = $dids.$item['did'].'|';
            }
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

        $tarray = DBResultToArray($this->SelectDatasFromTable($this->TName('tTrade')
            ,[
                'tid'=>$tids
            ]));
        //echo json_encode($darray);


        foreach ($cResult as $key => $item) {
            //echo 'kkkkkk:'.$key.'=>'.$item.'</br>';
            $cResult[$key]['tele'] = substr_replace($user[$cResult[$key]['uid']],'****',3,4);
            if(isset($darray[$cResult[$key]['did']])){
                $cResult[$key]['dtitle'] = $this->subtext($darray[$cResult[$key]['did']]['title'],10);
            }
            if(isset($tarray[$cResult[$key]['did']])){
                $cResult[$key]['dtitle'] = $this->subtext($tarray[$cResult[$key]['did']]['title'],10);
            }
    }

        $backMsg = RESPONDINSTANCE('0');

        $backMsg['orders'] = $cResult;

        return $backMsg;
    }


    //根据电话或日期获取范围订单
    public function GetOrderCountByTeleORDate(){
        if(isset($_REQUEST['tele']) && $_REQUEST['tele'] != ""){
            $tele = $_REQUEST['tele'];
            $userObject = UserManager::GetUserByTele($tele);

            $userCondition = "";
            foreach ($userObject as $key=>$value){
                $userCondition = $userCondition.$value->uid.'|';
            }
            $userCondition = rtrim($userCondition, "|");
            $userCondition = self::FieldIsValue('uid',$userCondition);
            $condStr = $userCondition;
        }else{
            $condStr = "1";
        }

        if(isset($_REQUEST['date']) && $_REQUEST['date'] != ""){
            $timeMin = strtotime($_REQUEST['date']);
            if(isset($_REQUEST['datemax']) && $_REQUEST['datemax'] != ""){
                $timeMax = strtotime($_REQUEST['datemax']);
            }else {
                $timeMax = strtotime($_REQUEST['date']) + 86400;
            }
            $timeCond = self::C_And(
                self::FieldIsValue('ctime',$timeMin,'>'),
                self::FieldIsValue('ctime',$timeMax,'<')
            );
            if($condStr =="1"){
                $condStr = $timeCond;
            }else {
                $condStr = self::C_And($condStr, $timeCond);
            }
        }
		$condStr = self::C_And($condStr,self::FieldIsValue('state','SUCCESS'));
		
        $orders = DBResultToArray($this->SelectDataByQuery($this->TName('tOrder'),$condStr,false,"COUNT(*),SUM(`bill`)"),true);
		$orderCount = 0;
		$totalBill = 0;
        if(!empty($orders)){
            $orderCount = $orders[0]['COUNT(*)'];
            $totalBill = $orders[0]['SUM(`bill`)']*0.01;
        }
        $backMsg = RESPONDINSTANCE('0');
        $backMsg['ordCount'] = $orderCount;
		$backMsg['totalBill'] = $totalBill;
        return $backMsg;
    }

	//根据电话或日期获取范围订单
	public function GetOrdersByTeleORDate($seek,$count){
		if(isset($_REQUEST['tele']) && $_REQUEST['tele'] != ""){
			$tele = $_REQUEST['tele'];
			$userObject = UserManager::GetUserByTele($tele);

			$userCondition = "";
			foreach ($userObject as $key=>$value){
                $userCondition = $userCondition.$value->uid.'|';
            }
            $userCondition = rtrim($userCondition, "|");
            $userCondition = self::FieldIsValue('uid',$userCondition);
            $condStr = $userCondition;
		}else{
            $condStr = "1";
        }

		if(isset($_REQUEST['date']) && $_REQUEST['date'] != ""){
            $timeMin = strtotime($_REQUEST['date']);

            if(isset($_REQUEST['datemax']) && $_REQUEST['datemax'] != ""){
                $timeMax = strtotime($_REQUEST['datemax']);
            }else {
                $timeMax = strtotime($_REQUEST['date']) + 86400;
            }
            $timeCond = self::C_And(
                self::FieldIsValue('ctime',$timeMin,'>'),
                self::FieldIsValue('ctime',$timeMax,'<')
            );
            if($condStr =="1"){
                $condStr = $timeCond;
            }else {
                $condStr = self::C_And($condStr, $timeCond);
            }
        }

		$condStr = self::Limit(self::OrderBy(self::C_And($condStr,self::FieldIsValue('state','SUCCESS')),"ctime","DESC"),$seek,$count);
       // echo $condStr;
		$orders = DBResultToArray($this->SelectDataByQuery($this->TName('tOrder'),$condStr,false,
		self::LogicString(
			[
				self::SqlField('oid'),
				self::SqlField('uid'),
				self::SqlField('pid'),
				self::SqlField('bill'),
				self::SqlField('ctime'),
				self::SqlField('ptime'),
			],',')
		),false);
		
		$useridList = [];
		foreach($orders as $key=>$value){
			$orders[$key]['ctime'] = date('Y-m-d H:i:s',$orders[$key]['ctime']);
			$orders[$key]['ptime'] = date('Y-m-d H:i:s',$orders[$key]['ptime']);
			$orders[$key]['bill'] = $orders[$key]['bill']*0.01;
			if(!in_array($value['uid'],$useridList)){
				array_push($useridList,$value['uid']);
			}
		}
		$users = DBResultToArray($this->SelectDataByQuery($this->TName('tUser'),self::FieldIsValue('uid',self::LogicString($useridList)),false,
		self::LogicString([self::SqlField('uid'),self::SqlField('nickname'),self::SqlField('tele')],',')));
		
		//echo json_encode($users);
		
		
		foreach($orders as $key=>$value){
			if(array_key_exists($value['uid'],$users)){
				foreach($users[$value['uid']] as $key01=>$info){
					$orders[$key][$key01] = $info;
				}
			}
		}
		
		$backMsg = RESPONDINSTANCE('0');
		$backMsg['orders'] = $orders;
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