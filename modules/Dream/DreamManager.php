<?php
//引用此页面前需先引用conf.php
error_reporting(E_ALL ^ E_DEPRECATED);

LIB('db');
LIB('us');
LIB('aw');
LIB('dp');

define("MAX_DREAMS_COUNT",5);

class DreamManager extends DBManager{
    public function info()
    {
		//echo json_encode(DreamManager::UserFirstSubmitedDream('on8W94tv5jTTiItf1uJCBdLJPyic'));
        return "梦想模块"; // TODO: Change the autogenerated stub
    }
	public function DreamManager(){
		parent::__construct();
	}

	//梦想实施(AwardManager调用)
	public static function OnDreamDoing($did){
        //未实现
        $DRM = new DreamManager();
        $DRM->UpdateDataToTable($DRM->TName('tDream'),['state'=>'DOING'],['did'=>$did,'_logic'=>' ']);
    }


    //获取用户待完成中奖信息
    public static function UserDreamAwardingInfo($uid){
        $DRM = new DreamManager();
        $dresult = $DRM->SelectDataFromTable($DRM->TName('tDream'),['uid'=>$uid,'state'=>'DOING','_logic'=>'AND']);
        $dArray = DBResultToArray($dresult,true);
        $result = [];
        if(!empty($dArray)){
            $dArray = $dArray[0];
            $result['result'] = true;
            $result['dtitle'] = $dArray['title'];
        }else{
            $result['result'] = false;
        }
        return $result;
    }
	
	public static function UserFirstSubmitedDream($uid){
		//SELECT * FROM `dream` WHERE `uid`="on8W94tv5jTTiItf1uJCBdLJPyic" AND (`state`="SUBMIT" OR `state`="FAILED") LIMIT 0,1
		$DRM = new DreamManager();
		
        $link = $DRM->DBLink();
		$sql = 'SELECT * FROM `'.$DRM->TName('tDream').'` WHERE `uid`="'.$uid.'" AND (`state`="SUBMIT" OR `state`="FAILED") LIMIT 0,1';

        $uResult = DBResultToArray(mysql_query($sql,$link),true);
		
		return $uResult;
	}

    //梦想完成完善,提交审核
    public static function OnDreamVerify($did){
        $DRM = new DreamManager();
        return $DRM->UpdateDataToTable($DRM->TName('tDream'),['state'=>'VERIFY'],['did'=>$did,'state'=>'DOING','_logic'=>'AND']);
    }

    //梦想完成(UserManager 调用)
    public static function OnDreamSuccess($did){
        //未实现
        $DRM = new DreamManager();
        $DRM->UpdateDataToTable($DRM->TName('tDream'),['state'=>'SUCCESS'],['did'=>$did,'_logic'=>' ']);
    }

    //梦想失效或完成失败
    public static function OnDreamFailed($did){
        $DRM = new DreamManager();
        $DRM->UpdateDataToTable($DRM->TName('tDream'),['state'=>'FAILED'],['did'=>$did,'_logic'=>' ']);
    }

	//生成梦想id号
	public static function GenerateDreamID(){
        $DRM = new DreamManager();
        return 'DR'.(1000000000 + $DRM->CountTableRow($DRM->TName('tDream')));
    }

    //统计用户提交的梦想数（未中奖，未实现即为提交）
    public static function CountSubmitedDream($uid){
        $condition = [
            'uid' => $uid,
            'state'=> 'SUBMIT',
            '_logic' =>'AND'
        ];
        $DRM = new DreamManager();
        return count(DBResultToArray($DRM->SelectDataFromTable($DRM->TName('tDream'),$condition),true));
    }

	//判断用户是否有未中奖的梦想，有即可直接选择，无则调用梦想编辑
	public static function HasSubmitedDream($uid){
        $condition = [
            'uid' => $uid,
            'state'=> 'SUBMIT',
            '_logic' =>'AND'
        ];
        $DRM = new DreamManager();
        return DBResultExist($DRM->SelectDataFromTable($DRM->TName('tDream'),$condition));
    }
	
	//通过条件字符串批量获取梦想
	public static function GetDreamsByConditionStr($didStr){
		$DRM = new DreamManager();
		$dreams = DBResultToArray($DRM->SelectDatasFromTable($DRM->TName('tDream'),['did'=>$didStr]));
		return $dreams;
	}


    //打开梦想编辑页面
    public function PrepareEditDream($uid){
        if(DreamManager::CountSubmitedDream($uid)>=MAX_DREAMS_COUNT){//若已经提交的梦想数量超过上限（5个）
            return RESPONDINSTANCE('14');
        }
        return RESPONDINSTANCE('0');
    }

    //提交梦想信息
	public function OnEditDream($uid,$title,$content){

        if(UserManager::UserExist($uid)){
            return RESPONDINSTANCE('15');
        }

        if(DreamManager::CountSubmitedDream($uid)>=MAX_DREAMS_COUNT){//若已经提交的梦想数量超过上限（5个）
            return RESPONDINSTANCE('14');
        }

        $dreamArray = [
            "did"=>DreamManager::GenerateDreamID(),
            "uid"=>$uid,
            "dtypeid"=>"Enterprise|Learn|BodyBuild",
            "dserverid"=>"ENTSERVER01|LERSERVER01|BDBSERVER01",
            "title"=>$title,
            "content"=>$content,
            "videourl"=>"",
            "state"=>"SUBMIT",
        ];

        $insresult = $this->InsertDataToTable($this->TName('tDream'),$dreamArray);
        if($insresult){
            $backMsg = RESPONDINSTANCE('0');//梦想提交成功
            if(isset($_REQUEST['action'])){
                $actionList = json_decode($_REQUEST['action'],true);
                if(isset($actionList['editdream'])){
                    unset($actionList['editdream']);
					$backMsg['buy']['dream'] = $dreamArray;
                }
                $backMsg['actions'] = $actionList;
            }

            return $backMsg;
        }else{
            return RESPONDINSTANCE('13');//梦想提交失败
        }
        //return DreamManager::GenerateDreamID();
    }


   /* public function OnDreamVerify($uid,$did){
        $result = $this->UpdateDataToTable($this->TName('tDream'),
            ['state'=>'VERIFY'],
            ['uid'=>$uid,'did'=>$did,'_logic'=>' ']
            );
        return RESPONDINSTANCE('0');
    }*/

    //完善梦想信息
    public function OnEditingDream($uid,$did,$contentList){
        $contentList = json_decode($contentList,true);
        //未实现
        $targetList = [
            'title',
            'content',
            'videourl'
        ];
        $fixArray = [];
        foreach ($targetList as $item) {
            if(isset($contentList[$item])){
                $fixArray[$item] = $contentList[$item];
            }
        }
        if(empty($fixArray)){
            return RESPONDINSTANCE('44');//梦想更新失败
        }
        //echo json_encode($fixArray,JSON_UNESCAPED_UNICODE);

        $updateResult = $this->UpdateDataToTableByQuery($this->TName('tDream'),$fixArray,"`uid`=\"$uid\" AND `did`=\"$did\" AND (`state`=\"DOING\" OR `state`=\"SUBMIT\")");//['uid'=>$uid,'did'=>$did,''=>'','_logic'=>'AND']);


        if(!$updateResult){
            return RESPONDINSTANCE('44');//梦想更新失败
        }else{

            if(isset($contentList['videourl'])){
                //完成上传小视频
            }

            return RESPONDINSTANCE('0');//更新成功
        }

    }

    //选择梦想信息(必须要有action，因为选择梦想操作只在购买梦想池时需要做，一定为过程性动作)
    public function OnDreamSelected($uid,$did,$action){
        try {
            $actionList = json_decode($action,true);
        }catch (Exception $err){
            return RESPONDINSTANCE('17',$err);
        }
        if(isset($actionList["selectdream"])){
            unset($actionList["selectdream"]);
        }else{
            return RESPONDINSTANCE('17',"未包含selectdream动作");
        }

        $targetDream = $this->GetSingleDream($uid,$did);//获取梦想

        if(isset($actionList["buy"])){
            if(empty($targetDream)){
                return RESPONDINSTANCE('21');
            }
            $actionList["buy"]["dream"] = $targetDream;//设置选择的梦想信息
            $backMsg = RESPONDINSTANCE('0');
            $backMsg['actions'] = $actionList;
            return $backMsg;
        }else{
            return RESPONDINSTANCE('17',"未包含buy动作");
        }

    }

    //获取用户的单个梦想
    public function GetUserSingleDream($uid,$did){
        $backMsg = RESPONDINSTANCE('0');
        $state = 'SUBMIT';
        if(isset($_REQUEST['state'])){
            $state = $_REQUEST['state'];
        }
        $backMsg['dream'] = $this->GetSingleDream($uid,$did,$state);
        if(empty($backMsg['dream'])){
            return RESPONDINSTANCE('47');
        }
        return $backMsg;
    }
    //获取用户的单个梦想
    public function GetSingleDream($uid,$did,$state='all'){
        $condition = [
            'uid' => $uid,
            'did' => $did,
            'state'=>$state,
            '_logic' =>'AND'
        ];
        if($state == 'all'){
            unset($condition['state']);
        }
        $dreams = $this->SelectDataFromTable($this->TName('tDream'),$condition);
        $dreamArray = DBResultToArray($dreams,true);
        if(DBResultArrayExist($dreamArray)){
            $dreamArray = $dreamArray[0];
        }else{
            $dreamArray = [];
        }
        return $dreamArray;
    }

    //进入梦想列表
    public function OnDreamList($uid){
        $condition = [
            'uid' => $uid,
            '_logic' =>' '
        ];
        $dreams = $this->SelectDataFromTable($this->TName('tDream'),$condition);
        $dreamArray = DBResultToArray($dreams,true);
        if(DBResultArrayExist($dreamArray)){
            $dreamArray = $dreamArray;
        }else{
            $dreamArray = [];
        }

        foreach($dreamArray as $key=>$value){
            if($value['state']!='DOING' && $value['state']!='VERIFY'){
//                DreamPoolManager::Pool()
            }else{
                $lottery = AwardManager::GetAwardLotteryByDreamID($value['did']);
                $dreamArray[$key]['lottery'] = $lottery;
                if(!empty($lottery)){
                    $pool = DreamPoolManager::Pool($lottery['pid']);
                    $dreamArray[$key]['pool'] = $pool;
                }
            }
        }

        $backMsg = RESPONDINSTANCE('0');
        $backMsg['dreams'] = $dreamArray;
        $backMsg['dcount'] = self::PrepareEditDream($uid);
        return $backMsg;
    }

    //设置梦想状态（审核通过/结束）
    public function SetDreamStateByJson($did,$state){
        $stateArray = json_decode($state,true);
        $setArray = [];
        if(isset($stateArray['state'])){
            $setArray['state'] = $stateArray['state'];
        }
        if(isset($stateArray['payment'])){
            $setArray['payment'] = $stateArray['payment'];
        }
        $this->UpdateDataToTable($this->TName('tDream'),
            $setArray,
            ['did'=>$did]
        );
        return RESPONDINSTANCE('0');
    }
}
?>