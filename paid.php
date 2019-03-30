<?php 

	header("Access-Control-Allow-Origin:*");

	header("Content-Type: text/html;charset=utf-8;");

	include_once("public/conf.php");//加载配置文件
	include_once("public/lib.php");//加载公有库
	include_once ("public/adapter.php");//加载适配器


	if(isset($GLOBALS['HTTP_RAW_POST_DATA'])){
		$postArr = $GLOBALS['HTTP_RAW_POST_DATA'];
		$postStr = simplexml_load_string($postArr, 'SimpleXMLElement', LIBXML_NOCDATA);
		//$paid['HTTP_RAW_POST_DATA'] = $postStr;
		$postStr = json_decode(json_encode($postStr),true);
		//file_put_contents("paid_info.txt",);
		
		if($postStr['appid'] != $GLOBALS['options']['WEB_APP_ID']){
			return RESPONDINSTANCE('101');
		}
		
		$_REQUEST = [
						"paid"=> "of",
						"oid"=>$postStr['out_trade_no'],
                        "traid"=>$postStr['transaction_id'],
						"bill"=>$postStr['total_fee'],
						"state"=>$postStr['result_code']
					];
		
		REQUEST("paid");
	}
?>