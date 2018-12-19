<?php
//引用此页面前需先引用conf.php
error_reporting(E_ALL ^ E_DEPRECATED);

define('POOL_TITLE_PREFIX','梦想互助');
define('POOL_TITLE_POSTFIX','期');

define('MAIN_POOL_ORDER_BY','ptime');//主梦想池的排序字段
define('MAIN_POOL_ORDER_RULE','DESC');//主梦想池的筛选规则

LIB('db');
LIB('us');
class DreamPoolManager extends DBManager{
    //生成梦想id号
    public static function GeneratePoolID(){
        $DPM = new DreamPoolManager();
        return (100000 + ($DPM->CountTableRow($DPM->TName('tPool'))+1));
    }
	
	//通过梦想池的期号生成ID
	public static function GeneratePoolIDAuto(){
		$ftime = GetFirstMonthDayStamp();
		$sql = 'SELECT COUNT(*) FROM `dreampool` WHERE `ptime`>"'."$ftime".'"';
		$DPM = new DreamPoolManager();
		$link = $DPM->DBLink();
		$index = DBResultToArray(mysql_query($sql,$link),true)[0]['COUNT(*)']+1;
		if($index <10){
			$index = '0'.$index;
		}
		$date = GetFirstMonthDayObject();
		return $date['y'].$date['m'].$index;
	}

    //获取全部未开奖梦想池
    public static function GetAllUnAwardPools(){
        $DPM = new DreamPoolManager();
        $aResult = $DPM->SelectDataFromTable($DPM->TName('tPool'),
            ['award'=>'NO','_logic'=> ' ']
            );
        $PoolList = DBResultToArray($aResult,false);

        return $PoolList;
    }

    //获取全部梦想池
    public static function GetAllPools(){
        $DPM = new DreamPoolManager();
        return $DPM->ListAllPool();
    }

    //获取单个梦想池
    public static function Pool($pid){
        $DPM = new DreamPoolManager();
        $pools = DBResultToArray($DPM->SelectDataFromTable($DPM->TName('tPool'),
            [
                'pid'=>$pid,
                '_logic'=>' '
            ]),true);
        if(!empty($pools)){
            return $pools[0];
        }
        return $pools;
    }

    //池存在并且未完成互助
    public static function IsPoolRunning($pid){
        $DPM = new DreamPoolManager();
        return $DPM->UpdatePool($pid);
    }

    //查询首页显示的梦想信息
    public static function GetMainPool(){
        $DPM = new DreamPoolManager();
        $selresult = $DPM->SelectDataFromTable($DPM->TName('tPool'),
            [
                "state"=>'RUNNING',
                "_logic"=>' ',
                "_orderby"=>MAIN_POOL_ORDER_BY,
                "_orderrule"=>MAIN_POOL_ORDER_RULE,
                "_Limfrom"=>0,
                '_Limto'=>1
            ]
        );
        $resultArray = DBResultToArray($selresult,true);
        if(!empty($resultArray) && isset($resultArray[0])){
            return $resultArray[0];
        }
        return [];
    }

    //购买梦想池支付成功后调用
    public static function BuyPoolPieceSuccess($pid,$piece){
        $DPM = new DreamPoolManager();
        $selresult = $DPM->SelectDataFromTable($DPM->TName('tPool'),
            [
                "pid"=>$pid,
                "state"=>'RUNNING',
                "_logic"=>'AND'
            ]
        );

        $array = DBResultToArray($selresult,true);
        if(empty($array)){
            return RESPONDINSTANCE('6','没有生效的梦想池'.$pid);
        }
        $array = $array[0];
        $ubill = $array['ubill'];
        $pcount = $array['pcount'];
        $cbill = $array['cbill'];
        $tbill = $array['tbill'];
        if(!$selresult){
            return RESPONDINSTANCE('5');
        }
        $cbillNext = $cbill+$piece*$ubill;
        if($cbillNext>$tbill){
            $DPM->FinishedPool($pid);
            return RESPONDINSTANCE('7');
        }
        $updateresult = $DPM->UpdateDataToTable($DPM->TName('tPool'),
        [
            'pcount'=>($pcount+$piece),
            'cbill'=>$cbillNext
        ],
        [
            "pid"=>$pid,
            "state"=>'RUNNING',
            "_logic"=>'AND'
        ]);
        if(!$updateresult){
            return RESPONDINSTANCE('6','没有生效的梦想池'.$pid);
        }
        $backMsg = RESPONDINSTANCE('0');
        $backMsg['PoolInfo'] = [
            'startIndex'=>$pcount,
            'endIndex'=>($pcount+$piece),
            'cbill'=>$cbillNext
        ];
        return $backMsg;
    }

    //刷新所有梦想池,返回被更新的梦想池的全部信息
    public static function UpdateAllPools(){
        $DPM = new DreamPoolManager();
        $poolInfo = DBResultToArray($DPM->SelectDataFromTable($DPM->TName('tPool'),['state'=>'RUNNING','_logic'=>' ']));
        $condition = '';

        $length = count($poolInfo);
        $seek = 0;

        $returnMsg = [];

        foreach ($poolInfo as $key=>$value){
            ++$seek;
            if(self::HasPoolFinished($value['ptime'],$value['duration'],$value['cbill'],$value['tbill'])){
                /*if(($seek) >= $length-1){
                    $condition = $condition.'`pid`="'.$value['pid'].'"';
                }else{*/
                $condition = $condition.'`pid`="'.$value['pid'].'" OR ';
                //}
                $returnMsg[$value['pid']]['state'] = 'FINISHED';
                $returnMsg[$value['pid']]['info'] = $value;
            }else{
                $returnMsg[$value['pid']]['state']  = 'RUNNING';
                $returnMsg[$value['pid']]['info']  = $value;
            }
        }

        $condition = substr($condition, 0, -4);
        //echo $condition;
        if(!empty($condition)){
            $DPM->UpdateDataToTableByQuery($DPM->TName('tPool'),['state'=>'FINISHED'],$condition);
        }

        return $returnMsg;
    }

    //梦想池是否完成
    static function HasPoolFinished($ptime,$duration,$cbill,$tbill){
        return (PRC_TIME()>=($ptime + $duration)) || $cbill>=$tbill;
    }

    public function info()
    {
        return "梦想池模块"; // TODO: Change the autogenerated stub
    }

    public function DreamPoolManager(){
        parent::__construct();
    }

    public function ForceUpdateAllPools(){
        return self::UpdateAllPools();
    }
    //更新梦想池
    public function UpdatePool($pid){
        $poolInfo = DBResultToArray($this->SelectDataFromTable($this->TName('tPool'),['pid'=>$pid,'state'=>'RUNNING','_logic'=>'AND']));

        if(!empty($poolInfo)){
            $duration = $poolInfo[$pid]['duration'];
            $ptime = $poolInfo[$pid]['ptime'];
            $tbill = $poolInfo[$pid]['tbill'];
            $cbill = $poolInfo[$pid]['cbill'];
            $ubill = $poolInfo[$pid]['ubill'];


            if(self::HasPoolFinished($ptime,$duration,$cbill,$tbill)){
                $this->UpdateDataToTable($this->TName('tPool'),['state'=>'FINISHED'],['pid'=>$pid,'state'=>'RUNNING','_logic'=>'AND']);
                return RESPONDINSTANCE('5');
            }else{
                if($ubill == 0){
                    $pless = 10000;
                }else{
                    $pless = ($tbill - $cbill)/$ubill;
                }
            }

            $backMsg = RESPONDINSTANCE('0');
            $backMsg['poolInfo'] = $poolInfo[$pid];
            $backMsg['pless'] = $pless;

            return $backMsg;
        }else{
            return RESPONDINSTANCE('5');
        }
    }

    //梦想池结束生命周期
    public function FinishedPool($pid){
        $updateresult = $this->UpdateDataToTable($this->TName('tPool'),
            [
                'state'=>'FINISHED',
            ],
            [
                "pid"=>$pid,
                "state"=>'RUNNING',
                "_logic"=>'AND'
            ]);
        if(!$updateresult){
            return RESPONDINSTANCE('6','没有生效的梦想池'.$pid);
        }
        return RESPONDINSTANCE('0');
    }
	
	//测试id生成
	public function gid(){
		return self::GeneratePoolIDAuto();
	}
	
	//通过天数增加梦想池
	public function AddPoolByDay($uid,$tbill,$ubill,$day){
		if(!UserManager::CheckIdentity($uid,"User")){
            return RESPONDINSTANCE('8');
        }
		
        $pid = self::GeneratePoolIDAuto();
		$title= POOL_TITLE_PREFIX.$pid.POOL_TITLE_POSTFIX;
		$duration = GetDayLessTime()+86400*$day;//今天的剩余时间+day天
		
        $insresult = $this->InsertDataToTable($this->TName('tPool'),[
            "pid"=>$pid,
            "ptitle"=>$title,
            "uid"=>$uid,
            "state"=>'RUNNING',
            "tbill"=>$tbill,
            "cbill"=>0,
            "ubill"=>$ubill,
            "duration"=>$duration,
            "ptime"=>PRC_TIME(),
            "pcount"=>0,
            "award" =>'NO'
        ]);
        if($insresult){
            return RESPONDINSTANCE('0');
        }else{
            return RESPONDINSTANCE('1');
        }
	}
	
	//通过期号和持续天数增加梦想池
	public function AddPoolByIndex($index,$uid,$tbill,$ubill,$day){
		//SELECT COUNT(*) FROM `dreampool` WHERE `ptime`>"1543593600" 
        //校验身份
        if(!UserManager::CheckIdentity($uid,"User")){
            return RESPONDINSTANCE('8');
        }
		
        $pid = $index;
		$title= POOL_TITLE_PREFIX.$index.POOL_TITLE_POSTFIX;
		$duration = GetDayLessTime()+86400*$day;//今天的剩余时间+day天
		
        $insresult = $this->InsertDataToTable($this->TName('tPool'),[
            "pid"=>$pid,
            "ptitle"=>$title,
            "uid"=>$uid,
            "state"=>'RUNNING',
            "tbill"=>$tbill,
            "cbill"=>0,
            "ubill"=>$ubill,
            "duration"=>$duration,
            "ptime"=>PRC_TIME(),
            "pcount"=>0,
            "award" =>'NO'
        ]);
        if($insresult){
            return RESPONDINSTANCE('0');
        }else{
            return RESPONDINSTANCE('1');
        }
	}
	
	//获取当天剩余时间
	public function GetDayTimeLess(){
		$second = GetDayLessTime();
		if(!isset($_REQUEST['formate'])){
			return $second;
		}
		$hours = ($second - $second%3600)/3600;
		$minutes = ($second%3600 - ($second%3600)%60)/60;
		$sec = $second%60;
		return $hours.'-'.$minutes.'-'.$sec;
	}
	
	//获取本月第一天的时间戳
	public function FirstMonthDay(){
		return GetFirstMonthDay();
	}
	
    //【请求】增加梦想池
    public function Add($ptitle,$uid,$tbill,$ubill,$duration){

        //校验身份
        if(!UserManager::CheckIdentity($uid,"User")){
            return RESPONDINSTANCE('8');
        }

        $pid = self::GeneratePoolID();
        $insresult = $this->InsertDataToTable($this->TName('tPool'),[
            "pid"=>$pid,
            "ptitle"=>$ptitle,
            "uid"=>$uid,
            "state"=>'RUNNING',
            "tbill"=>$tbill,
            "cbill"=>0,
            "ubill"=>$ubill,
            "duration"=>$duration,
            "ptime"=>PRC_TIME(),
            "pcount"=>0,
            "award" =>'NO'
        ]);
        if($insresult){
            return RESPONDINSTANCE('0');
        }else{
            return RESPONDINSTANCE('1');
        }
    }

    //【请求】获取全部梦想池
    public function ListAllPool(){
        $condArray = [];
        //DreamPoolManager::BuyPoolPieceSuccess('p01',10);
        return DBResultToArray($this->SelectDataFromTable($this->TName('tPool'),$condArray));
    }


    public function CountPools(){
        $link = $this->DBLink();

        $sql = "SELECT COUNT(*) FROM `dreampool`";

        mysql_query($sql,$link);


        $cResult = DBResultToArray(mysql_query($sql,$link),true);

        $backMsg = RESPONDINSTANCE('0');
        $backMsg['count'] = $cResult[0]["COUNT(*)"];
        return $backMsg;
    }

    //用户获取全部梦想池信息及参与信息
    public function ListPoolsByRange($seek,$count){
        //未实现
        $link = $this->DBLink();

        $sql = "SELECT * FROM `dreampool` WHERE 1 ORDER BY `ptime` DESC LIMIT $seek,$count";

        $cResult = DBResultToArray(mysql_query($sql,$link),true);

        $backMsg = RESPONDINSTANCE('0');
        $backMsg['Pools'] = $cResult;
        return $backMsg;
    }
}
?>