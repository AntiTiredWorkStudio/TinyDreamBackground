<?php
//引用此页面前需先引用conf.php
error_reporting(E_ALL ^ E_DEPRECATED);

LIB('db');
LIB('us');
LIB('dr');
LIB('dp');

//梦想池服务中心,购票等功能
class DreamServersManager extends DBManager {
    public function info()
    {
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

    //获得首页滚动购买信息
    public static function GetMainOrders(){
        return [];
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


    //下单动作开始
    public function PlaceOrderInADreamPoolStart($uid,$pid){

        if(!DreamPoolManager::IsPoolRunning($pid)){
            return RESPONDINSTANCE('5');//梦想池失效（完成互助或到时）
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
                'buy'=>['uid'=>$uid,'pid'=>$pid]//购买互助
            ];
        }else {
            //跳转至选择梦想界面
            $backMsg['actions'] = [
                'selectdream'=>['uid'=>$uid],//选择梦想
                'buy'=>['uid'=>$uid,'pid'=>$pid]//购买互助
            ];
        }
        return $backMsg;
    }


    public function DreamServersManager(){
        parent::__construct();
	}
}
?>