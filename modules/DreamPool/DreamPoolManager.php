<?php
//引用此页面前需先引用conf.php
error_reporting(E_ALL ^ E_DEPRECATED);

define('POOL_TITLE_PREFIX','梦想互助');
define('POOL_TRADE_TITLE_PREFIX','小生意互助');
define('POOL_TITLE_POSTFIX','期');


define('MAIN_POOL_ORDER_BY','ptime');//主梦想池的排序字段
define('MAIN_POOL_ORDER_RULE','DESC');//主梦想池的筛选规则

LIB('db');
LIB('us');
LIB('tr');
class DreamPoolManager extends DBManager{
    //生成梦想id号
    public static function GeneratePoolID(){
        $DPM = new DreamPoolManager();
        return (100000 + ($DPM->CountTableRow($DPM->TName('tPool'))+1));
    }

    //获取梦想互助剩余编号数量
    public static function GetLessLotteryCount($pid){
        $DPM = new DreamPoolManager();
        $billInfo = DBResultToArray($DPM->SelectDataByQuery($DPM->TName('tPool'),self::FieldIsValue('pid',$pid),'false',
            self::LogicString([
                self::SqlField('tbill'),
                self::SqlField('cbill'),
                self::SqlField('ubill'),
            ],','
            )
        ),true);

        if(!empty($billInfo)){
            $billInfo = $billInfo[0];
        }else{
            return 0;
        }
        return ($billInfo['tbill'] - $billInfo['cbill'])/$billInfo['ubill'];
    }



    //更新所有在进行的梦想池
    public static function UpdateAllRunningPool(){
        $DPM = new DreamPoolManager();
        /*
         *
         * 小生意互助潜在修改位置
         *
         * */

        $StandardConidtion = self::C_And(
            self::C_And(
                self::FieldIsValue(
                    'state',
                    'RUNNING'),
                self::ExpressionIsValue(
                    self::Symbol(
                        self::SqlField('ptime'),
                        self::SqlField('duration'),
                        '+'
                    ),
                    time(),
                    '<'
                )),
            self::FieldIsValue(
                'ptype',
                'STANDARD')
        );

        //把该结束的梦想池结束了
        $DPM->UpdateDataToTableByQuery($DPM->TName('tPool'),
            ['state'=>'FINISHED'],
            $StandardConidtion
        );

        $TradeCondition =
        self::C_And(
            self::C_And(
                self::FieldIsValue('state','RUNNING'),
                self::Symbol(
                    self::SqlField('tbill'),
                    self::SqlField('cbill'),
                    '<=')
                ),
            self::FieldIsValue(
                'ptype',
                'TRADE')
        );
        //把该结束的梦想池结束了
        $DPM->UpdateDataToTableByQuery($DPM->TName('tPool'),
            ['state'=>'FINISHED'],
            $TradeCondition
        );
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

    //查询首页显示的小生意信息
    public static function GetMainTrade(){
        $DPM = new DreamPoolManager();
        $DPM->UpdateAllRunningPool();//获取前先更新梦想池信息


        $condition =
        self::Limit(
        self::OrderBy(
            self::C_And(
                self::FieldIsValue('state',"RUNNING"),
                self::FieldIsValue('ptype',"TRADE")
            ),
            MAIN_POOL_ORDER_BY,
            MAIN_POOL_ORDER_RULE
        ),0,1
        );


        $selresult = $DPM->SelectDataByQuery($DPM->TName('tPool'),
            $condition
        );
        $resultArray = DBResultToArray($selresult,true);
        if(!empty($resultArray) && isset($resultArray[0])){
            $resultArray = $resultArray[0];
            $resultArray['trade'] = TradeManager::GetTradeInfoByPid($resultArray['pid']);

            return $resultArray;
        }
        return [];
    }

    //查询首页显示的梦想信息
    public static function GetMainPool(){
        $DPM = new DreamPoolManager();
        $DPM->UpdateAllRunningPool();//获取前先更新梦想池信息
        /*
         *
         * 小生意互助潜在修改位置
         *
         * */
        $condition =
            self::Limit(
                self::OrderBy(
                    self::C_And(
                        self::FieldIsValue('state',"RUNNING"),
                        self::FieldIsValue('ptype',"STANDARD")
                    ),
                    MAIN_POOL_ORDER_BY,
                    MAIN_POOL_ORDER_RULE
                ),0,1
            );
        $selresult = $DPM->SelectDataByQuery($DPM->TName('tPool'),
            $condition
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
        /*
         *
         * 小生意互助潜在修改位置
         *
         * */
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
        /*
         *
         * 小生意互助潜在修改位置
         *
         * */
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
        /*
         *
         * 小生意互助潜在修改位置
         *
         * */
        $poolInfo = DBResultToArray($DPM->SelectDataFromTable($DPM->TName('tPool'),['state'=>'RUNNING','_logic'=>' ']));
        $condition = '';

        $length = count($poolInfo);
        $seek = 0;

        $returnMsg = [];

        foreach ($poolInfo as $key=>$value){
            if(!isset($value['ptype'])){
                $value['ptype'] = "STANDARD";
            }
            ++$seek;
            if(self::HasPoolFinished($value['ptime'],$value['duration'],$value['cbill'],$value['tbill'],$value['ptype'])){
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
    static function HasPoolFinished($ptime,$duration,$cbill,$tbill,$ptype="STANDARD"){
        if($ptype == "STANDARD"){
            return (PRC_TIME()>=($ptime + $duration)) || $cbill>=$tbill;

        }else{
            return $cbill >= $tbill;
        }
    }

    public function info()
    {
//        $this->UpdateAllRunningPool();
        echo json_encode(DreamPoolManager::GetMainTrade());
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
        /*
         *
         * 小生意互助潜在修改位置
         *
         * */
		 
        $poolInfo = DBResultToArray($this->SelectDataByQuery($this->TName('tPool'),self::C_And(self::FieldIsValue('pid',$pid),self::FieldIsValue('state','RUNNING'))));
		
        if(!empty($poolInfo)){
			
            $duration = $poolInfo[$pid]['duration'];
            $ptime = $poolInfo[$pid]['ptime'];
            $tbill = $poolInfo[$pid]['tbill'];
            $cbill = $poolInfo[$pid]['cbill'];
            $ubill = $poolInfo[$pid]['ubill'];
            if(!isset($poolInfo[$pid]['ptype'])){
                $ptype = $poolInfo[$pid]['ptype'] = "STANDARD";
            }else {
                $ptype = $poolInfo[$pid]['ptype'];
            }


            if(self::HasPoolFinished($ptime,$duration,$cbill,$tbill,$ptype)){
                /*
                 *
                 * 小生意互助潜在修改位置
                 *
                 * */
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
        /*
         *
         * 小生意互助潜在修改位置
         *
         * */
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
		$duration = GetDayLessTime()+86400*($day-1)+3600*21;//今天的剩余时间+day天
        /*
         *
         * 小生意互助潜在修改位置
         *
         * */
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
            return RESPONDINSTANCE('1',$pid);
        }
	}

    //通过小生意添加梦想池
	public static function AddTradePool($pid,$tbill,$ubill=500){
        $DPM = new DreamPoolManager();
        $title= POOL_TRADE_TITLE_PREFIX.$pid.POOL_TITLE_POSTFIX;
        $insresult = $DPM->InsertDataToTable($DPM->TName('tPool'),[
            "pid"=>$pid,
            "ptitle"=>$title,
            "uid"=>'a01',
            "state"=>'RUNNING',
            "tbill"=>$tbill,
            "cbill"=>0,
            "ubill"=>$ubill,
            "duration"=>0,
            "ptime"=>PRC_TIME(),
            "pcount"=>0,
            "award" =>'NO',
            "ptype"=>'TRADE'
        ]);
        if($insresult){
            return RESPONDINSTANCE('0');
        }else{
            return RESPONDINSTANCE('1',$pid);
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
        /*
         *
         * 小生意互助潜在修改位置
         *
         * */
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
        /*
         *
         * 小生意互助潜在修改位置
         *
         * */
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

    //单独获得梦想池信息
    public function GetPoolInfo($pid){
        $info = DBResultToArray($this->SelectDataByQuery($this->TName('tPool'),self::FieldIsValue('pid',$pid)),true);
        if(!empty($info)){
            $info = $info[0];
            $backMsg = RESPONDINSTANCE('0');
            $backMsg['pool'] = $info;
            return $backMsg;
        }else{
            return RESPONDINSTANCE('5');
        }
    }

    //获取梦想池数量
    public function CountPools(){
        $link = $this->DBLink();

        $sql = "SELECT COUNT(*) FROM `".$this->TName("tPool")."`";

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

        $sql = "SELECT * FROM `".$this->TName("tPool")."` WHERE 1 ORDER BY `ptime` DESC LIMIT $seek,$count";

        $cResult = DBResultToArray(mysql_query($sql,$link),true);

        $backMsg = RESPONDINSTANCE('0');
        $backMsg['Pools'] = $cResult;
        return $backMsg;
    }
	
	//获取近1个月内的梦想互助id号
	public function PoolIdList(){
		$backMsg = RESPONDINSTANCE('0');
		$backMsg['pids'] = ListAttributeToArray(DBResultToArray($this->SelectDataByQuery($this->TName("tPool"),
			self::FieldIsValue("ptime",(PRC_TIME()-(86400*30)),">"),
			false,
			'`pid`'
		),true),"pid");
		return $backMsg;
	}
}
?>