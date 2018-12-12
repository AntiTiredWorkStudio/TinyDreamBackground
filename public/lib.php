<?php
//框架公有库(GLOBALS作用域,只用于写方法)
//引用请求接口


define('PERMISSION_LOCAL','localhost');
//控制器基类
class Manager{
    public function info(){
        return "控制器";
    }
}

//监视器类
class Monitor extends Manager{
    public function info(){
        return "监视管理器";
    }

    public function AddTask(){

    }

    //启动监视器
    public function RunMonitor(){
        ignore_user_abort(true);
        set_time_limit(0);
        $interval = 1;
        $stop = 0;
        do {
            if( $stop == 10 ) break;
            file_put_contents('task'.time().'.txt',time());
            $stop++;
            sleep ($interval);
        } while (true);
    }
//创建模块
    public function BuildModule($module,$controller){
      //  if(empty(RequestedFields(['name']))){
            if(is_dir("modules/".$controller)){
                die("模块".$module."已存在!");
            }
            mkdir("modules/".$controller);
            $managerFile = file_get_contents('public/template/manager.txt');
            $respondFile = file_get_contents('public/template/index.txt');
            $managerFile = str_replace('#manager#',$controller,$managerFile);
            $respondFile = str_replace('#manager#',$controller,$respondFile);

            $configFile = file_get_contents('public/conf.php');

            $respondPath = "modules/".$controller.'/index.php';
            $managerPath = "modules/".$controller.'/'.$controller.'.php';

            $configFile = str_replace('#NEW_MODULES#',"
	,'".$module."' => ['rq'=>'".$respondPath."',//".$controller."
			'lib'=>'".$managerPath."']#NEW_MODULES#",$configFile);
            file_put_contents('public/conf.php',$configFile);

            file_put_contents($managerPath,$managerFile);
            file_put_contents($respondPath,$respondFile);
            die("模块".$module."创建完成!");
       // }else{
      //      die("模块".$_REQUEST['admd']."创建失败!");
      //  }

    }
}



function REQUEST($key){
    if($key=='moi') {
        Responds($key,(new Monitor()),
            [
                'inf'=>R('info'),//模块信息
                'build'=>R('BuildModule',['key','name']),
                'run'=>R('RunMonitor'),
            ],PERMISSION_LOCAL);
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
function RequestedFields($fields){
    if(!empty($fields)) {
        foreach ($fields as $key) {
            if (!isset($_REQUEST[$key])) {
                return $key;
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

//通过时间戳计算天数
function DAY($tStamp){
    return ($tStamp - $tStamp%86400)/86400;
}

//通过天数计算时间戳
function DAY2TIME($day){
    return $day*86400;
}

//判断通用返回模板的返回结果是否成功
function ISSUCCESS($backMsg){
    return is_array($backMsg) && key_exists('result',$backMsg) && $backMsg['result'];
}

//中国时间
function PRC_TIME(){
    return time();
}


//设置模块的响应动作
function Responds($action, $manager, $actionArray,$permission='all'){
    if($permission!='all'){
        if($permission == 'localhost' && $_SERVER['SERVER_NAME'] != 'localhost') {
            die("无此权限");
        }
    }
    if(!array_key_exists($_REQUEST[$action],$actionArray)){
        die(json_encode(RESPONDINSTANCE('99',"请求模块'".$action."'不包含动作 '".$_REQUEST[$action]."''"),JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }
    if(array_key_exists('pars',$actionArray[$_REQUEST[$action]])
        && array_key_exists('func',$actionArray[$_REQUEST[$action]])
    ){
        $fieldCheck = RequestedFields($actionArray[$_REQUEST[$action]]['pars']);
        $paras = $_REQUEST;
        unset($paras[$action]);
        $paras = array_values($paras);

        if(empty($fieldCheck)){
            if(method_exists($manager,$actionArray[$_REQUEST[$action]]['func'])) {
                $result = $manager->$actionArray[$_REQUEST[$action]]['func'](...$paras);//调用功能
            }else{
                echo json_encode(RESPONDINSTANCE('100',"请求模块'".$action."'未定义方法 '".$actionArray[$_REQUEST[$action]]['func']."''"),JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                die();
            }

            if(is_null($result)){
                echo '<h3>执行结果</h3><p>'.json_encode(
                    [
                        '模块'=>$action,
                        '动作'=>$_REQUEST[$action],
                        '参数'=>$actionArray[$_REQUEST[$action]]['pars'],
                        '方法'=>$actionArray[$_REQUEST[$action]]['func'],
                        '返回'=>'null'
                    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
                ).'</p>';//请求无返回值

            }else{

                echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);//请求正确
            }
        }else{
            echo json_encode(RESPONDINSTANCE('100',"缺少参数'".$fieldCheck."''"),JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);//请求格式正确,参数不全
        }
    }else {
        echo json_encode(RESPONDINSTANCE('99',"请求格式错误"),JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);//请求格式错误
    }
}

//创建响应结构
function R($funcName, $pars = null){
    return ['func'=>$funcName,'pars'=>$pars];
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