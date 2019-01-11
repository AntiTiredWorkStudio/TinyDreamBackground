<?php
//框架公有库(GLOBALS作用域,只用于写方法)
//引用请求接口


define('PERMISSION_LOCAL',1);//只允许用过localhost请求
define('PERMISSION_AUTH_FREE',2);//强制无需校验签名
define('PERMISSION_AUTH_FORCE',4);//强制校验签名
define('PERMISSION_USER_ADMIN',8);//超级管理员权限
define('PERMISSION_USER_OWNER',16);//管理员/池主权限
define('PERMISSION_USER_USER',32);//用户权限
define('PERMISSION_ALL','all');//无需权限可访问


define('MONITOR_COMMAND','moi');
define('WECHAT_COMBINE_COMMAND','signature');
define('WECHAT_GETUSERINFO_COMMAND','code');
define('TIME_ZONE',8);

//控制器基类
class Manager{
    public function info(){
        return "控制器";
    }
}


function TaskSort($a,$b)
{
	return ($a['daytime']<$b['daytime'])?-1:1;
}
//监视器类
class Monitor extends Manager{
    public function info(){
        return "监视管理器";
    }
    public function CheckDay(){
        return DAY(time()).'  '.DAY(time()+1300);//date('y-m-d',time());
    }
	
	

    //增加任务
    public function AddTask($confName,$dayTime,$module,$action,$pars){
//0-86400 24*3600
		$path = $confName.'.txt';
        $currentConf = json_decode(file_get_contents($path),true);//读取配置文件
		$taskArray = $currentConf['tasks'];
        $task = [
            'daytime'=>$dayTime,
            'module'=>$module,
            'action'=>$action,
            'pars'=>json_decode($pars,true)
        ];
		
		

        $taskArray[$dayTime] = $task;
		
		uasort($taskArray,"TaskSort");
		
		$currentConf['tasks'] = $taskArray;

        file_put_contents($path,json_encode($currentConf,JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));//保存配置文件
    }

	

    //启动监视器
    public function RunMonitor($duration = 300,$default=''){//默认5分钟1刷新
        ignore_user_abort(true);
        set_time_limit(0);
		LIB('all');

        if($default=='') {
            $confName = 'task' . PRC_TIME() . '.txt';
            $confContent = [
                'startTime' => date("y-m-d  h:i:s"),//启动时间
                'duration' => $duration,                 //检视时间
                'times' => 0,                         //监视次数
                'awake' => true,                      //监视器状态
                'pause' => false,                     //暂停
                'seek' => "",                         //动作位置
                'tasks' => [                          //任务列表

                ]
            ];
            file_put_contents($confName, json_encode($confContent));//保存配置文件
        }else{
            $confName = $default.'.txt';
        }
        do {//开始监视器
            $cdura = $duration;
            if(!file_exists($confName)){//配置文件被删除
                break;
            }else {//配置文件存在
                $currentConf = json_decode(file_get_contents($confName),true);//读取配置文件
				//var_export($currentConf);
                if(!$currentConf['awake']){//确定监视器处于关闭状态
                    unlink($confName);//注销监视器
					//echo $confName.'注销';
                    break;
                }
                if(!$currentConf['pause']) {//没暂停
                    $seek = $currentConf['seek'];//获取当前动作
                    $task = $currentConf['tasks'];//获取动作列表
                    if(count($task)>0) {//包含动作
                        if ($seek != '') {
                            //执行动作
							if(isset($task[$seek])){
								$runTask = $task[$seek];
								$_REQUEST[$runTask['module']] = $runTask['action'];
								foreach($runTask['pars'] as $key=>$value){
									$_REQUEST[$key] = $value;
								}
								REQUEST($runTask['module']);
							}
                            $seek='';
                        }
                        $passTime = GetDayPassTime();
                        $min = 86400;
                        foreach ($task as $key => $value) {
                            if ($key > $passTime) {
                                $condVal = abs($key - $passTime);
                                if ($min >= $condVal) {
                                    $seek = $key;
                                    $min = $condVal;
                                    file_put_contents('action.txt',$min);
                                }
                            }
                        }
                        $cdura = $min;
                        if ($seek == "") {
                            $cdura = GetDayLessTime() + current($task)['daytime'];
                            $seek = current($task)['daytime'];
                            file_put_contents('action.txt',$cdura);
                        }
						$currentConf['next'] = $cdura;
                        $currentConf['seek'] = $seek;
                    }else{
                        //不包含动作
                    }
                    $currentConf['times'] = $currentConf['times']+1;//增加执行次数
                }else{//监视器是暂停状态
                    $cdura = $currentConf['duration'];
                }
                file_put_contents($confName,json_encode($currentConf));//保存配置文件
            }
            sleep ($cdura);
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

//创建监视器
function MonitorBuilder($key){
    return Responds($key,(new Monitor()),
        [
            'inf'=>R('info'),//模块信息
            'build'=>R('BuildModule',['key','name']),
            'run'=>R('RunMonitor',['duration','default']),
            'cday'=>R('CheckDay'),//检查天
            'task'=>R('AddTask',['confName','dayTime','module','action','pars'])//增加任务
        ],PERMISSION_LOCAL | PERMISSION_AUTH_FREE);
}

function CombineWechatServer(){
	//{"signature":"083289d5f4f57622dec53bbffeeb84492a7125cc","echostr":"3749999956182686711","timestamp":"1547194837","nonce":"1203941899"}
	$signature = $_REQUEST['signature'];
	$echostr = $_REQUEST['echostr'];
	$timestamp = $_REQUEST['timestamp'];
	$nonce = $_REQUEST['nonce'];
	$token = 'konglf';
	$tmpArr = array($token,$timestamp, $nonce);
	sort($tmpArr, SORT_STRING);
	$tmpStr = implode($tmpArr);
	$tmpStr = sha1($tmpStr);

	if($signature == $tmpStr){
		return true;
	}else{
		return false;
	}
}

//微信公众号方法处理
$WebApp = [
	WECHAT_COMBINE_COMMAND=>function(){
		if(CombineWechatServer()){
			echo $_REQUEST['echostr'];
			return;
		}},
	WECHAT_GETUSERINFO_COMMAND =>function(){
		//echo json_encode($_REQUEST);
		Header("Location:https://tinydream.antit.top/admin/demo.html");
		setcookie('code',json_encode($_REQUEST,JSON_UNESCAPED_UNICODE),PRC_TIME()+3600);
		return;
	},
];

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
    $fixedtStamp = $tStamp + TIME_ZONE*3600;//时区问题需要在手动计算天数时考虑
    return ($fixedtStamp - $fixedtStamp%86400)/86400;
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

function MoitorTime(){
    return (PRC_TIME()+8*3600);
}

//获取当天已过时间
function GetDayPassTime(){
    return (PRC_TIME()+8*3600)%86400;
}

//获取当天剩余时间
function GetDayLessTime(){
	return 86400 - (PRC_TIME()+8*3600)%86400;
}

function GetFirstMonthDayObject(){
	$date = [];
	$date['y']=date("Y",time()); 
	$date['m']=date("m",time()); 
	$date['d'] = 1;
	return $date;
}

function GetFirstMonthDayStamp(){
	$y=date("Y",time()); 
	$m=date("m",time()); 
	$d = 1;
	return strtotime("$m/$d/$y");
}

class PermissionManager{
    public static function CheckPermissions($permission,$targetPermission){
        return ($targetPermission & $permission) == $permission;
    }

    public $permission_free = true;

    public $targetPermission = PERMISSION_ALL;
    public function PermissionManager($tPermission){
        $this->targetPermission = $tPermission;
        $this->permission_free = $this->targetPermission == PERMISSION_ALL;
    }

    public function CheckServerName($serverName){
        if($this->permission_free){
           return true;
        }

        return !self::CheckPermissions(PERMISSION_LOCAL,$this->targetPermission) || $serverName=='localhost';
    }

    public function AuthFree($auth_option){
        if($this->permission_free && !$auth_option){
            return true;
        }

        return (!$auth_option && !self::CheckPermissions(PERMISSION_AUTH_FORCE,$this->targetPermission)) || self::CheckPermissions(PERMISSION_AUTH_FREE,$this->targetPermission);
    }

    public function UserAuth(){
        if($this->permission_free){
            return true;
        }
        $admin = self::CheckPermissions(PERMISSION_USER_ADMIN,$this->targetPermission);
        $owner = self::CheckPermissions(PERMISSION_USER_OWNER,$this->targetPermission);
        $user = self::CheckPermissions(PERMISSION_USER_USER,$this->targetPermission);
        return Adapter::UserPermissionMethord($admin,$owner,$user);
    }
}



//设置模块的响应动作
function Responds($action, $manager, $actionArray,$permission=PERMISSION_ALL){//此处permission可被R中权限覆盖
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
        $fieldCheck = RequestedFields($actionArray[$_REQUEST[$action]]['pars']);
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
            echo json_encode(RESPONDINSTANCE('100',"缺少参数'".$fieldCheck."''"),JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);//请求格式正确,参数不全
        }
    }else {
        echo json_encode(RESPONDINSTANCE('99',"请求格式错误"),JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);//请求格式错误
    }
}

//创建响应结构
function R($funcName, $pars = null,$permission = PERMISSION_ALL,$return=true){//此处permission若填写可覆盖Respond中定义的权限
    return ['func'=>$funcName,'pars'=>$pars,'permission'=>$permission,'backMsg'=>$return];
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


function ConnectArrayByChar($numsArray,$char){
    $str = '';
    foreach ($numsArray as $num){
        $str = $str.$num.$char;
    }
    return rtrim($str,$char);
}

function HttpGet($url){
    $curl = curl_init();
        //设置抓取的url
    curl_setopt($curl, CURLOPT_URL, $url);
        //设置头文件的信息作为数据流输出
    curl_setopt($curl, CURLOPT_HEADER, 0);
        //设置获取的信息以文件流的形式返回，而不是直接输出。
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //执行命令
    $data = curl_exec($curl);
        //关闭URL请求
    curl_close($curl);
        //显示获得的数据
    return $data;
}
?>