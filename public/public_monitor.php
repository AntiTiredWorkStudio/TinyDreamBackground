<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019-5-12
 * Time: 下午 11:24
 */

define('MONITOR_COMMAND','moi');

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
                        $passTime = PublicTools::GetDayPassTime();
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
                            $cdura = PublicTools::GetDayLessTime() + current($task)['daytime'];
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

?>