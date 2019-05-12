<?php
//框架公有库(GLOBALS作用域,只用于写方法)
//引用请求接口


define('TIME_ZONE',8);


define('CONFIG_ACCESS_LIST','access_list');
//控制器基类
class Manager{
    public function info(){
        return "控制器";
    }
}
$ACCESS_LIST = [];

include "public_tools.php";
include "public_permission.php";
include "public_monitor.php";
include "public_wechat.php";


function REQUEST($key){
    if($key==MONITOR_COMMAND) {
        MonitorBuilder($key);
        return;
    }
	
	if(isset($GLOBALS['WebApp'][$key])){
		$GLOBALS['WebApp'][$key]();
		return;
	}
	try{
		if(!isset($GLOBALS['modules'][$key])){
			die(json_encode(RESPONDINSTANCE('99','不存在模块:'.$key),JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		}
		$_GET['act'] = $key;
		include_once($GLOBALS['modules'][$key]['rq']);
	}catch(Exception $err){
		die($err);
	}
}

//判断字段请求是否存在
function RequestedFields($fields,$freefieldcheck){
    $currentRequest = $_REQUEST;
    unset($currentRequest[array_keys($currentRequest)[0]]);
    $result = [
        'result'=>'miss',
        'field'=>''
    ];
    if(!empty($fields)) {
        foreach ($fields as $key) {
            if(PublicTools::StartWith($key,'#')){
                continue;
            }
            if (!isset($currentRequest[$key])) {
                $result['result'] = 'miss';
                $result['field'] = $key;
                return $result;
            }else{
                unset($currentRequest[$key]);
            }
        }
    }
    //判断参数无用
    if($freefieldcheck) {
        $NoCheckArray =["signal","openid","uid"];//与签名相关的缺省参数不予进行判断
        foreach ($currentRequest as $key => $value) {
            if(in_array($key,$NoCheckArray)){
                continue;//与签名相关的缺省参数不予进行判断
            }
            $free_field = '#' . $key;
            if (!in_array($free_field, $fields)) {
                $result['result'] = 'useless';
                $result['field'] = $key;
                return $result;
            }
        }
    }
    return null;
}

//引用库接口
function LIB($key){
	try{
	    if($key == "all"){
            foreach ($GLOBALS['modules'] as $key=>$module) {
                include_once($GLOBALS['modules'][$key]['lib']);
	        }
        }

		if(!isset($GLOBALS['modules'][$key])){
			die(json_encode(RESPONDINSTANCE('98',$key),JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		}
		include_once($GLOBALS['modules'][$key]['lib']);
	}catch(Exception $err){
		die($err);
	}
}

//请求失败
function FAILED($key,$context =''){
	//$GLOBALS['FALLBACKTEXT'] = $contex;
	$result = [];
	if(!isset($GLOBALS['fallbacks'][$key])){
		$result['result'] = 'false';
		$result['code'] = '-1';
		$result['context'] = '没有该类错误:'.$key;
	}
	$result['result'] = 'false';
	$result['code'] = $key;
	$result['context'] = str_replace('#FALLTEXT#',$context,$GLOBALS['fallbacks'][$key]);
	return json_encode($result,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}

//请求成功
function SUCCESS($infoArray){
	$result = [];
	if(!isset($infoArray['result'])){
		$result['result'] = 'true';
	}
	if(!isset($infoArray['code'])){
		$result['code'] = '0';
	}
	if(!isset($infoArray['context'])){
		$result['context'] = '请求成功';
	}
	foreach($infoArray as $key=>$value){
		$result[$key] = $value;
	}
	return json_encode($result);
}

//消息返回模板
function RESPONDINSTANCE($code = 0,$fallContext='',$infoArray = null){
	$result = [];
	if($code == 0){
		$result = [
			'result'=>'true',
			'code'=>$code,
			'context'=>'请求成功'
		];
	}else{
		$result = [
			'result'=>'false',
			'code'=>$code,
			'context'=>$GLOBALS['fallbacks'][$code]
		];
	}
	
	$result['context'] = str_replace('#FALLTEXT#',$fallContext,$result['context']);
	
	if($infoArray != null){
		foreach($infoArray as $key=>$value){
			$result[$key] = $value;
		}
	}
	return $result;
}


//计算时间戳开始点的时间戳（向过去取整）
function DAY_START_FLOOR($tStamp){
    $fixedtStamp = $tStamp + TIME_ZONE*3600;
    return $fixedtStamp - $fixedtStamp%86400 - TIME_ZONE*3600;
}

//计算时间戳开始点的时间戳（向未来取整）
function DAY_START_CELL($tStamp){
    //echo $tStamp.'</br>';
    $fixedtStamp = $tStamp + TIME_ZONE*3600;
   // echo $fixedtStamp;
    return $fixedtStamp + (86400 - $fixedtStamp%86400) - TIME_ZONE*3600;
}

//通过时间戳计算天数
function DAY($tStamp){
    $fixedtStamp = $tStamp + TIME_ZONE*3600;//时区问题需要在手动计算天数时考虑
    return ($fixedtStamp - $fixedtStamp%86400)/86400;
}

//判断通用返回模板的返回结果是否成功
function ISSUCCESS($backMsg){
    return is_array($backMsg) && key_exists('result',$backMsg) && $backMsg['result'];
}

//中国时间
function PRC_TIME(){
    return time();
}

function MoitorTime(){
    return (PRC_TIME()+8*3600);
}

//获取可选参数及设置默认值
function FREE_PARS($field,$noset='null'){
    return isset($_REQUEST[$field])?$_REQUEST[$field]:$noset;
}

//设置模块的响应动作
function Responds($action, $manager, $actionArray,$permission=PERMISSION_ALL){//此处permission可被R中权限覆盖
    $GLOBALS['ACCESS_LIST'][$action] = $actionArray;
    if(!isset($_REQUEST[$action])){
        return;
    }
    $targetPermission = $permission;
    if(isset($actionArray[$_REQUEST[$action]]['permission']) && $actionArray[$_REQUEST[$action]]['permission']!=PERMISSION_ALL){
        $targetPermission = $actionArray[$_REQUEST[$action]]['permission'];
    }

    $PM = new PermissionManager($targetPermission);


    if(!$PM->CheckServerName($_SERVER['SERVER_NAME']) || !$PM->UserAuth()){
        die("无此权限");
    }


	if(empty($_REQUEST[$action]) || $_REQUEST[$action] ==''){
		die(json_encode($actionArray));
	}
    if(!array_key_exists($_REQUEST[$action],$actionArray)){
        die(json_encode(RESPONDINSTANCE('99',"请求模块'".$action."'不包含动作 '".$_REQUEST[$action]."''"),JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }


    if(!$PM->AuthFree($GLOBALS['options']['auth'])){//请求签名校验
        Adapter::AuthMethord($action,$_REQUEST[$action],$_REQUEST);
    }


    if(array_key_exists('pars',$actionArray[$_REQUEST[$action]])
        && array_key_exists('func',$actionArray[$_REQUEST[$action]])
    ){
        $fieldCheck = RequestedFields($actionArray[$_REQUEST[$action]]['pars'],$GLOBALS['options']['free_field_check']);
        $paras = $_REQUEST;
        unset($paras[$action]);
        $paras = array_values($paras);

        if(empty($fieldCheck)){
            if(method_exists($manager,$actionArray[$_REQUEST[$action]]['func'])) {//请求方法
                $result = $manager->$actionArray[$_REQUEST[$action]]['func'](...$paras);//调用功能
            }else{//无请求方法
                echo json_encode(RESPONDINSTANCE('100',"请求模块'".$action."'未定义方法 '".$actionArray[$_REQUEST[$action]]['func']."''"),JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                die();
            }

            if(!isset($actionArray[$_REQUEST[$action]]['backMsg']) || !$actionArray[$_REQUEST[$action]]['backMsg']){
                return;
            }

            if(is_null($result)){//无返回值
                echo '<h3>执行结果</h3><p>'.json_encode(
                    [
                        '模块'=>$action,
                        '动作'=>$_REQUEST[$action],
                        '参数'=>$actionArray[$_REQUEST[$action]]['pars'],
                        '方法'=>$actionArray[$_REQUEST[$action]]['func'],
                        '返回'=>'null'
                    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
                ).'</p>';//请求无返回值

            }else{//返回正确消息

                echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);//请求正确
            }
        }else{
            $state = $fieldCheck['result'] =="miss"?"缺少参数":"无用的参数";
            $fieldName = $fieldCheck['field'];
            echo json_encode(RESPONDINSTANCE('100',$state."'".$fieldName."''"),JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);//请求格式正确,参数不全
        }
    }else {
        echo json_encode(RESPONDINSTANCE('99',"请求格式错误"),JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);//请求格式错误
    }
}

//创建响应结构
function R($funcName, $pars = null,$permission = PERMISSION_ALL,$return=true,$codes=[]){//此处permission若填写可覆盖Respond中定义的权限
    return ['func'=>$funcName,'pars'=>$pars,'permission'=>$permission,'backMsg'=>$return,'backcode'=>$codes];
}

function DBResultArrayExist($array){
    return !empty($array) && !empty(array_keys($array));
}

function DBResultExist($dbResult){
    return !empty(mysql_fetch_array($dbResult));
}

//遍历并处理
function DBResultHandle($dbResult,$func){
    while($single = mysql_fetch_array($dbResult)){
        foreach($single as $key=>$value){
            if(is_numeric($key)){
                continue;
            }
            $func($key,$value);
        }
    }
}

//遍历并转换成表
function DBResultToArray($dbResult, $NumKey = false,$keepNum = false){
    $resultArray = [];
    if(empty($dbResult)){
        return $resultArray;
    }
    $seek = 0;
    while($single = mysql_fetch_array($dbResult)){
        $rowKey = "";
        if($NumKey){
            $rowKey = $seek;
        }else{
            $rowKey = $single[0];
        }
        $resultArray[$rowKey] = [];
        foreach($single as $key=>$value){
            if(!$keepNum && is_numeric($key)){
                continue;
            }
            $resultArray[$rowKey][$key] = $value;
        }
        $seek++;
    }
    return $resultArray;
}




?>