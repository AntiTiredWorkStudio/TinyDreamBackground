<?php
//引用此页面前需先引用conf.php
error_reporting(E_ALL ^ E_DEPRECATED);

require "Res/autoload.php";
use Qiniu\Auth;

LIB('db');
LIB('dp');
LIB('ds');
LIB('va');
LIB('no');
LIB('auth');
LIB('ub');

define('CARD_FRONT','card_f');
define('ID_FRONT','id_f');
define('ID_BACK','id_b');
define('PUB_LETTER','letter_');
define('PUB_ACTIVITY','activity_');

class UserIdentifier{
	public $uid;
	public $nickname;
	public $tele;
	public $totalReward;
	public $totalJoin;
	public $dayBuy;
	
    public function UserIdentifier(){}

	public function LoadDatas($array){
		if(	array_key_exists('uid',$array) &&
			array_key_exists('nickname',$array) &&
			array_key_exists('tele',$array) &&
			array_key_exists('totalReward',$array) &&
			array_key_exists('totalJoin',$array) &&
			array_key_exists('dayBuy',$array)
		){
			$this->uid = $array['uid'];
			$this->nickname = $array['nickname'];
			$this->tele = $array['tele'];
			$this->totalReward = $array['totalReward'];
			$this->totalJoin = $array['totalJoin'];
			$this->dayBuy = $array['dayBuy'];
			return true;
		}else{
			return false;
		}
	}
}

class UserManager extends DBManager{
    public static function UserExist($uid){
        $USM = new UserManager();
        return (!DBResultExist($USM->SelectDataFromTable($USM->TName('tUser'),
            [
                'uid'=>$uid,
                '_logic'=>' '
            ]
        )));
    }


    public static function GetUserTele($uid){
        $USM = new UserManager();
        return DBResultToArray($USM->SelectDataByQuery($USM->TName('tUser'),
            self::FieldIsValue('uid',$uid),
            false,
            'tele'
        ),true)[0]['tele'];
    }

    public static function GetTelesByUidList($uidlist){
        $USM = new UserManager();
        return DBResultToArray($USM->SelectDataByQuery($USM->TName('tUser'),
            self::FieldIsValue('uid',self::LogicString($uidlist)),
            false,
            'tele'
        ),true);
    }

    //生成活动照片上传token
    public static function GenerateActivityPhoto($uid){
        $USM = new UserManager();
        $auth = new Auth($USM->CloudOptions['ak'], $USM->CloudOptions['sk']);
        $token = $auth->uploadToken($USM->CloudOptions['bucket']);
        $timeStamp = PRC_TIME();
        $backMsg['uptoken']=$token;
        $backMsg['upurl']= $USM->uploadURLFromRegionCode($USM->CloudOptions['region']);
        $backMsg['domain']=$USM->CloudOptions['domain'];
        $backMsg['timeStamp']=$timeStamp;
        $backMsg['fileName'] = $USM->GenerateFileName($uid,PUB_ACTIVITY);
        return $backMsg;
    }

    //生成公函上传token
    public static function GeneratePublicLetter($uid){
        $USM = new UserManager();
        $auth = new Auth($USM->CloudOptions['ak'], $USM->CloudOptions['sk']);
        $token = $auth->uploadToken($USM->CloudOptions['bucket']);
        $timeStamp = PRC_TIME();
        $backMsg['uptoken']=$token;
        $backMsg['upurl']= $USM->uploadURLFromRegionCode($USM->CloudOptions['region']);
        $backMsg['domain']=$USM->CloudOptions['domain'];
        $backMsg['timeStamp']=$timeStamp;
        $backMsg['fileName'] = $USM->GenerateFileName($uid,PUB_LETTER);
        return $backMsg;
    }


    public static function UpdateUserOrderInfo($uid,$totalJoin,$pieces){
        $USM = new UserManager();
        $condition = [
            'uid'=>$uid,
            '_logic' => ' '
        ];

        $USM->UpdateDataToTable($USM->TName('tUser'),
            ['totalJoin'=>$totalJoin,'dayBuy'=>['field'=>'dayBuy','operator'=>'+','value'=>$pieces],'ltime'=>PRC_TIME()],
            $condition);
    }

    //当开奖时用户中奖调用,reward为中奖金额
    public static function OnUserReward($uid,$reward){
        $USM = new UserManager();
        $reward = $reward;//不除100
        $USM->UpdateDataToTable($USM->TName('tUser'),
            ['totalReward'=>['field'=>'totalReward','operator'=>'+','value'=>$reward]],
            ['uid'=>$uid,'_logic'=>' ']
        );
    }

    //检查用户每日购买数量,过日后自动清0
    public static function CheckDayBoughtLimit($uid){
		$USM = new UserManager();
       
	   $condition = [
            'uid'=>$uid,
            '_logic' => ' '
        ];

        $result = DBResultToArray($USM->SelectDataFromTable($USM->TName('tUser'),$condition),true);
		
		if(empty($result[0])){
			return false;
		}
		
		$lDAY = DAY($result[0]['ltime']);
		
		$cDAY = DAY(PRC_TIME());
		
		if($cDAY > $lDAY){
			$USM->UpdateDataToTable($USM->TName('tUser'),
            ['dayBuy'=>0],
            $condition);
			$result[0]['dayBuy'] = 0;
		}

		return 5-($result[0]['dayBuy']);
    }

	//通过手机号查找用户信息
	public static function GetUserByTele($tele){
		$USM = new UserManager();
		$result = DBResultToArray($USM->SelectDataByQuery($USM->TName('tUser'),self::FieldIsValue('tele',$tele),false,"`uid`,`nickname`,`tele`,`totalReward`,`totalJoin`,`dayBuy`"),false);
		$return = [];
		foreach($result as $key=>$value){
			$uObject = new UserIdentifier();
			$uObject->LoadDatas($value);
			array_push($return,$uObject);
		}
		return $return;
	}
	
	//通过手机号查找用户信息(外联接口)
	public function GetUserByTelephone($tele){
		$backMsg = RESPONDINSTANCE('0');
		$backMsg['userinfo'] = self::GetUserByTele($tele);
		return $backMsg;
	}

    public static function GetUserIdentity($uid){
        $USM = new UserManager();
        $result = DBResultToArray($USM->SelectDataByQuery($USM->TName('tUser'),self::FieldIsValue('uid',$uid),false,'identity'),true);
        if(!empty($result)){
            $result = $result[0];
        }
        return $result;
    }

    //检查身份
    public static function CheckIdentity($uid,$identity){
        $USM = new UserManager();
        if(!DBResultExist($USM->SelectDataFromTable($USM->TName('tUser'),
            [
                'uid'=>$uid,
                'identity'=>$identity,
                '_logic'=>'AND'
            ]
        ))){
            return RESPONDINSTANCE('8');
        }
        return RESPONDINSTANCE('0');
    }

    //检查用户是否绑定手机
    public static function IdentifyTeleUser($uid){
        $USM = new UserManager();

        $seleResult = $USM->SelectDataFromTable($USM->TName('tUser'),
            [
                'uid'=>$uid,
                '_logic'=>' '
            ]);

        $resultArray = DBResultToArray($seleResult,true);
        if(!empty($resultArray)){
            $resultArray = $resultArray[0];
        }
       /* if(DBResultArrayExist($resultArray)){
            $resultArray = $resultArray[0];
        }else{
            return false;
        }*/

        return (!empty($resultArray['tele'])) && $resultArray['tele']!="";
    }

    //检查用户是否提交实名认证
    public static function IdentifyRealNameUser($uid,$state="SUBMIT"){
        $USM = new UserManager();

        $seleResult = $USM->SelectDataFromTable($USM->TName('tIdx'),
            [
                'uid'=>$uid,
                '_logic'=>' '
            ]);

        $resultArray = DBResultToArray($seleResult,true);

        if(DBResultArrayExist($resultArray)){
            $resultArray = $resultArray[0];
        }else{
            return false;
        }
        return
            ($state == "SUBMIT" &&
                ($resultArray['state']=="SUBMIT" ||
                    $resultArray['state']=="SUCCESS")) ||
            ($state == "SUCCESS" &&
                ($resultArray['state']=="SUCCESS"));
    }




    //获取验证码
    public function OnGetLoginCode ($tele){
        $user = $this->SelectDataFromTable($this->TName('tUser'),['tele'=>$tele],false,'uid');
        $result = DBResultToArray($user,true);
        if(empty($result)){
            return RESPONDINSTANCE('60');
        }

        $uid = $result[0]['uid'];

        if(self::CheckIdentity($uid,'OWNER')['code']=='0' ||
            self::CheckIdentity($uid,'ADMIN')['code']=='0') {
            $VAM = new ValidateManager();
            return $VAM->GenerateCode($tele);
        }else{
            return RESPONDINSTANCE('61');
        }
    }

    //后台登录
    public function OnBackgroundLogin($tele,$code){
        $user = $this->SelectDataFromTable($this->TName('tUser'),['tele'=>$tele],false,'uid');
        $result = DBResultToArray($user,true);
        if(empty($user)){
            return RESPONDINSTANCE('60');
        }
        $uid = $result[0]['uid'];

        if(self::CheckIdentity($uid,'OWNER') && self::CheckIdentity($uid,'ADMIN')) {
            $VAM = new ValidateManager();
            $backMsg = $VAM->ConfirmCode($tele, $code);
            $backMsg['openid'] = $uid;
            $backMsg['auth'] = AuthManager::GenerateOrUpdateAuthToken($uid);
            return $backMsg;
        }else{
            return RESPONDINSTANCE('61');
        }
    }

	//快速获取用户信息
	public static function GetUsersInfoByString($uidStr){
		$USM = (new UserManager());
		$users = DBResultToArray($USM->SelectDatasFromTable($USM->TName('tUser'),['uid'=>$uidStr]));
		return $users;
	}
	//快速获取用户信息
	public static function GetUserInfo($uid){
		return (new UserManager())->SelfInfo($uid)['selfinfo'];
	}


    public function info()
    {
        return "用户管理器"; // TODO: Change the autogenerated stub
    }

    public function UserManager(){
		parent::__construct();
	}
	
	public function test(){
		return UserManager::CheckDayBoughtLimit("a01");
	}
	
	public function SelfInfoByNickName($nickname){
        $condition = [
            'nickname'=>$nickname,
            '_logic'=>' '
        ];
        $seleResult = $this->SelectDataFromTable($this->TName('tUser'),
            $condition);
        $userArray = DBResultToArray($seleResult,true);
        if(!empty($userArray)){
            if(count($userArray) == 1) {
                $userArray = $userArray[0];
            }
        }
        $backMsg = RESPONDINSTANCE('0');
        $backMsg['selfinfo'] = $userArray;
        return $backMsg;
	}

	//个人信息
	public function SelfInfo($uid){
		self::CheckDayBoughtLimit($uid);
        $condition = [
            'uid'=>$uid,
            '_logic'=>' '
        ];
        $seleResult = $this->SelectDataFromTable($this->TName('tUser'),
            $condition);
        $userArray = DBResultToArray($seleResult,true);
        if(!empty($userArray)){
            $userArray = $userArray[0];
        }
        $backMsg = RESPONDINSTANCE('0');
        $backMsg['selfinfo'] = $userArray;
        return $backMsg;
    }
	
	
	public function EnterAppInRedPackage($uid,$nickname,$headicon){
		 $condition = [
            'uid'=>$uid,
            '_logic'=>' '
        ];
        $seleResult = $this->SelectDataFromTable($this->TName('tUser'),
        $condition);
        $userArray = DBResultToArray($seleResult,true);
        $backMsg = RESPONDINSTANCE('0');
		//file_put_contents($uid."_nickname.txt",urlencode($nickname));
        if(empty($userArray)){//未注册
            $userArray = [
                "uid"=>$uid,
                "nickname"=>$nickname,
                "headicon"=>$headicon,
                "tele"=>"",
                "totalReward"=>0,
                "totalJoin"=>0,
                "dayBuy"=>0,
                "identity"=>"USER",
                "ltime"=>0,
            ];
            $insResult = $this->InsertDataToTable($this->TName('tUser'),$userArray);
            if(!$insResult){
                return RESPONDINSTANCE('9');
            }else{
                $backMsg['description'] = '注册成功';
            }
            //注册
        }else{//已经注册
            $userArray = $userArray[0];
            //检查更新信息
            $updateList = [];
            if($userArray['nickname'] != $nickname){
                $updateList['nickname'] = $nickname;
            }
            if($userArray['headicon'] != $headicon){
                $updateList['headicon'] = $headicon;
            }

            if(!empty($updateList)){
                $updateResult = $this->UpdateDataToTable($this->TName('tUser'),$updateList,$condition);
                if(!$updateResult){
                    return RESPONDINSTANCE('10');
                }else{
                    $backMsg['description'] = '信息更新,登录成功';
                }
            }else{
                $backMsg['description'] = '登录成功';
            }
        }

        UserBehaviourManager::OnBehave($uid,VISI);
        return $backMsg;
	}

    //微信登录返回用户openid,昵称,头像地址后调用 进入小程序验证身份
	public function EnterApp($uid,$nickname,$headicon){
        $condition = [
            'uid'=>$uid,
            '_logic'=>' '
        ];
        $seleResult = $this->SelectDataFromTable($this->TName('tUser'),
        $condition);
        $userArray = DBResultToArray($seleResult,true);
        $backMsg = RESPONDINSTANCE('0');
		//file_put_contents($uid."_nickname.txt",urlencode($nickname));
        if(empty($userArray)){//未注册
            $userArray = [
                "uid"=>$uid,
                "nickname"=>$nickname,
                "headicon"=>$headicon,
                "tele"=>"",
                "totalReward"=>0,
                "totalJoin"=>0,
                "dayBuy"=>0,
                "identity"=>"USER",
                "ltime"=>0,
            ];
            $insResult = $this->InsertDataToTable($this->TName('tUser'),$userArray);
            if(!$insResult){
                return RESPONDINSTANCE('9');
            }else{
                $backMsg['description'] = '注册成功';
            }
            //注册
        }else{//已经注册
            $userArray = $userArray[0];
            //检查更新信息
            $updateList = [];
            if($userArray['nickname'] != $nickname){
                $updateList['nickname'] = $nickname;
            }
            if($userArray['headicon'] != $headicon){
                $updateList['headicon'] = $headicon;
            }

            if(!empty($updateList)){
                $updateResult = $this->UpdateDataToTable($this->TName('tUser'),$updateList,$condition);
                if(!$updateResult){
                    return RESPONDINSTANCE('10');
                }else{
                    $backMsg['description'] = '信息更新,登录成功';
                }
            }else{
                $backMsg['description'] = '登录成功';
            }
        }

        UserBehaviourManager::OnBehave($uid,VISI);

        unset($userArray['nickname']);

        unset($userArray['headicon']);

        $backMsg['selfinfo'] = $userArray;//个人基本信息
        $backMsg['buyinfo'] = DreamServersManager::GetMainOrders();//购买滚动栏
        $backMsg['mainpool'] = DreamPoolManager::GetMainPool();//在主页显示的梦想池信息
        $backMsg['maintrade'] = DreamPoolManager::GetMainTrade();//在首页显示的小生意信息
        $backMsg['award'] = DreamManager::UserDreamAwardingInfo($uid);
        return $backMsg;
    }


    //云存储服务配置
    public $CloudOptions = [
        'ak'=>'d-SztTGFAV7_BX-dKRtM8y1diABoXe1zxCgd-2yi',
        'sk'=>'CWv29dzAFng2KZ15Cf21Pv6FoOoWtB3-nzh1zgJH',
        'domain'=>'https://tdream.antit.top',
        'bucket'=>'tinydream',
        'region'=>'ECN'
    ];

    public function uploadURLFromRegionCode($code) {
        $uploadURL = null;
        switch($code) {
            case 'ECN': $uploadURL = 'https://up.qbox.me'; break;
            case 'NCN': $uploadURL = 'https://up-z1.qbox.me'; break;
            case 'SCN': $uploadURL = 'https://up-z2.qbox.me'; break;
            case 'NA': $uploadURL = 'https://up-na0.qbox.me'; break;
            case 'ASG': $uploadURL = 'https://up-as0.qbox.me'; break;
            default: $uploadURL="";
        }
        return $uploadURL;
    }

    //生成文件名
    public function GenerateFileName($uid,$type){
        return $type.sha1($uid.'_'.PRC_TIME());
    }
    /*-----------------------------------新版实名认证-----------------------------------*/

    //获取某用户的实名认证信息（新版）
    public function GetUserRealNameIdentifyx($uid){
        $rNameResult = DBResultToArray($this->SelectDataFromTable($this->TName('tIdx'),
            [
                'uid'=>$uid,
                '_logic'=>' '
            ]));
        if(empty($rNameResult)){
            return RESPONDINSTANCE('12');
        }else{

            if($rNameResult[$uid]['state'] == 'NONE'){
                return RESPONDINSTANCE('12');
            }
            if($rNameResult[$uid]['state'] == 'FAILED'){
                return RESPONDINSTANCE('41');
            }


            $backMsg = RESPONDINSTANCE('0');
            $backMsg['realName'] = $rNameResult;
            return $backMsg;
        }
    }

    //开始实名认证（新版）
    public function RealNameIdentifyStartx($uid){

        $tIdentify = DBResultToArray($this->SelectDataFromTable($this->TName('tIdx'),
            [
                'uid'=>$uid,
                '_logic'=>' '
            ]),true);
        if(!empty($tIdentify)){
            if($tIdentify[0]['state'] != 'FAILED' && $tIdentify[0]['state'] != 'NONE'){
                $backMsg = RESPONDINSTANCE('37');
                $backMsg['state'] = $tIdentify[0]['state'];
                return $backMsg;//实名认证提交或审核
                //实名认证审核成功:只有在中奖时才会审核实名认证
            }else{
                $this->DeletDataFromTable($this->TName('tIdx'),[
                    'uid'=>$uid,
                    '_logic'=>' '
                ]);
            }
        }
        $backMsg = RESPONDINSTANCE('0');
        //未实现
        $auth = new Auth($this->CloudOptions['ak'], $this->CloudOptions['sk']);
        $token = $auth->uploadToken($this->CloudOptions['bucket']);
        $timeStamp = PRC_TIME();
        $backMsg['uptoken']=$token;
        $backMsg['upurl']= $this->uploadURLFromRegionCode($this->CloudOptions['region']);
        $backMsg['domain']=$this->CloudOptions['domain'];
        /*$backMsg['filename'][CARD_FRONT]=$this->CloudOptions['domain'].'/'.$this->GenerateFileName($uid,CARD_FRONT);
         $backMsg['filename'][ID_FRONT]=$this->CloudOptions['domain'].'/'.$this->GenerateFileName($uid,ID_FRONT);
         $backMsg['filename'][ID_BACK]=$this->CloudOptions['domain'].'/'.$this->GenerateFileName($uid,ID_BACK);*/
        $backMsg['timeStamp']=$timeStamp;

       // $backMsg['filename'][CARD_FRONT]=$this->GenerateFileName($uid,CARD_FRONT);
        $backMsg['filename'][ID_FRONT]=$this->GenerateFileName($uid,ID_FRONT);
        //$backMsg['filename'][ID_BACK]=$this->GenerateFileName($uid,ID_BACK);


        $this->InsertDataToTable($this->TName('tIdx'),
            [
                "uid"=>$uid,
                "realname"=>"",
                "icardnum"=>0,
                "ccardnum"=>0,
                "bank"=>"",
                "openbank"=>"",
                "icardfurl"=>$this->CloudOptions['domain'].'/'.$backMsg['filename'][ID_FRONT],
                "ftime"=>$timeStamp,
                "state"=>"NONE"
            ]
        );

        return $backMsg;
    }

    //实名认证提交成功(signal签名为用户id和时间戳字符串连接后的sha1值)（新版）
    public function RealNameIdentifyFinishedx($uid,$realname,$ccardnum,$icardnum,$bank,$openbank){
        //未实现
        $tIdentify = DBResultToArray($this->SelectDataFromTable($this->TName('tIdx'),
            [
                'uid'=>$uid,
                '_logic'=>' '
            ]),true);
        if(!empty($tIdentify)){
            /*if(sha1($uid.$tIdentify[0]['ftime']) != $signal){
                return RESPONDINSTANCE('40');//签名不正确
            }*/

            if($tIdentify[0]['state'] != 'NONE'){
                $backMsg = RESPONDINSTANCE('39');
                $backMsg['state'] = $tIdentify[0]['state'];
                return $backMsg;
            }else{
                $this->UpdateDataToTable($this->TName('tIdx'),
                    [
                        "realname"=>$realname,
                        'ccardnum'=>$ccardnum,
                        'icardnum'=>$icardnum,
                        "bank"=>$bank,
                        "openbank"=>$openbank,
                        'state'=>'SUBMIT',//修改为提交状态,前台提示已提交
                        'ftime'=>PRC_TIME()//修改时间,禁止签名复用
                    ]
                    ,
                    [
                        'uid'=>$uid,
                        '_logic'=>' '
                    ]);
                return RESPONDINSTANCE('0');
            }
        }else{
            $backMsg = RESPONDINSTANCE('39');
        }

        return $backMsg;
    }

    //实名认证审核（新版）
    public function RealNameAuditx($uid,$state){
        //未实现
        $tIdentify = DBResultToArray($this->SelectDataFromTable($this->TName('tIdx'),
            [
                'uid'=>$uid,
                'state'=>'SUBMIT',
                '_logic'=>'AND'
            ]),true);
        if(!empty($tIdentify)) {
            if($state == 'FAILED' || $state=='SUCCESS'){

                $this->UpdateDataToTable($this->TName('tIdx'),['state'=>$state],[
                    'uid'=>$uid,
                    'state'=>'SUBMIT',
                    '_logic'=>'AND'
                ]);

                if($state== 'FAILED'){
                    NoticeManager::CreateNotice($uid,NOTICE_FAIL.[],//创建通知——实名认证审核失败
                        NoticeManager::CreateAction('auth',
                            []
                        )
                    );
                }
                return RESPONDINSTANCE('0');
            }else{
                return RESPONDINSTANCE('43');
            }
        }else{
            return RESPONDINSTANCE('42');//必须是SUBMIT状态的实名认证信息才可通过
        }
    }
	
    //显示所有需要认证信息（新版）
    public function ViewAllVerifyInfox($type='submit'){
		$sqlConditions = [
			'submit'=>self::C_Or(
                    self::FieldIsValue('state','VERIFY'),
                    self::C_And(
                        self::FieldIsValue('state','SUCCESS'),
                        self::FieldIsValue('payment','0')
                    )
                ),
			'unsubmit'=>self::FieldIsValue('state','DOING'),
			'lose'=>self::FieldIsValue('state','FAILED')
		];
		
		
        //未实现

        //有中标梦想并提交了实名认证的用户在此查询并获取

        //提交了实名认证但无中标梦想的用户的实名认证不在此显示

        $array = DBResultToArray(
            $this->SelectDataByQuery($this->TName('tDream'),
                $sqlConditions[$type]
            //$this->SelectDataFromTable($this->TName('tDream'),['state'=>'VERIFY']
            ));


        $cond = '';

        $resultArray = [];
        foreach ($array as $key => $item) {
            /*if(!empty($item['videourl'])){
                $finishUser[$item['uid']] = [$item['videourl']];
            }*/
            $resultArray[$item['uid']]['dream'] = $item;
            $resultArray[$item['uid']]['identity'] = [];
            $cond = $cond.$item['uid'].'|';
        }

        $infoArray = DBResultToArray($this->SelectDatasFromTable($this->TName('tUser'),
            ['uid'=>$cond]));
        foreach ($infoArray as $key=>$item) {
            if(array_key_exists($item['uid'],$resultArray)){
                $resultArray[$item['uid']]['info'] =$item;
            }
        }

        $awardArray = DBResultToArray($this->SelectDatasFromTable($this->TName('tAward'),
            ['uid'=>$cond]));

        foreach ($awardArray as $key=>$item) {
            if(array_key_exists($item['uid'],$resultArray)){
                $resultArray[$item['uid']]['award'] =$item;
            }
        }


        $idArray = DBResultToArray($this->SelectDatasFromTable($this->TName('tIdx'),
            ['uid'=>$cond,
                'state'=>'SUBMIT|SUCCESS|FAILED']
        ));

        foreach ($idArray as $key=>$item) {
            if(array_key_exists($item['uid'],$resultArray)){
                $resultArray[$item['uid']]['identity'] =$item;
            }
        }

        $backMsg = RESPONDINSTANCE('0');
        $backMsg['verify'] = $resultArray;
        //$backMsg['dream'] = $finishUser;
        return $backMsg;
    }


    /*-----------------------------------旧版实名认证-----------------------------------*/

    //获取某用户的实名认证信息
    public function GetUserRealNameIdentify($uid){
        $rNameResult = DBResultToArray($this->SelectDataFromTable($this->TName('tId'),
            [
                'uid'=>$uid,
                '_logic'=>' '
            ]));
        if(empty($rNameResult)){
            return RESPONDINSTANCE('12');
        }else{

            if($rNameResult[$uid]['state'] == 'NONE'){
                return RESPONDINSTANCE('12');
            }
            if($rNameResult[$uid]['state'] == 'FAILED'){
                return RESPONDINSTANCE('41');
            }


            $backMsg = RESPONDINSTANCE('0');
            $backMsg['realName'] = $rNameResult;
            return $backMsg;
        }
    }

    //开始实名认证
    public function RealNameIdentifyStart($uid){

        $tIdentify = DBResultToArray($this->SelectDataFromTable($this->TName('tId'),
            [
                'uid'=>$uid,
                '_logic'=>' '
            ]),true);
        if(!empty($tIdentify)){
            if($tIdentify[0]['state'] != 'FAILED' && $tIdentify[0]['state'] != 'NONE'){
                $backMsg = RESPONDINSTANCE('37');
                $backMsg['state'] = $tIdentify[0]['state'];
                return $backMsg;//实名认证提交或审核
                //实名认证审核成功:只有在中奖时才会审核实名认证
            }else{
                $this->DeletDataFromTable($this->TName('tId'),[
                    'uid'=>$uid,
                    '_logic'=>' '
                ]);
            }
        }
        $backMsg = RESPONDINSTANCE('0');
        //未实现
        $auth = new Auth($this->CloudOptions['ak'], $this->CloudOptions['sk']);
        $token = $auth->uploadToken($this->CloudOptions['bucket']);
        $timeStamp = PRC_TIME();
        $backMsg['uptoken']=$token;
        $backMsg['upurl']= $this->uploadURLFromRegionCode($this->CloudOptions['region']);
        $backMsg['domain']=$this->CloudOptions['domain'];
       /*$backMsg['filename'][CARD_FRONT]=$this->CloudOptions['domain'].'/'.$this->GenerateFileName($uid,CARD_FRONT);
        $backMsg['filename'][ID_FRONT]=$this->CloudOptions['domain'].'/'.$this->GenerateFileName($uid,ID_FRONT);
        $backMsg['filename'][ID_BACK]=$this->CloudOptions['domain'].'/'.$this->GenerateFileName($uid,ID_BACK);*/
        $backMsg['timeStamp']=$timeStamp;

        $backMsg['filename'][CARD_FRONT]=$this->GenerateFileName($uid,CARD_FRONT);
        $backMsg['filename'][ID_FRONT]=$this->GenerateFileName($uid,ID_FRONT);
        $backMsg['filename'][ID_BACK]=$this->GenerateFileName($uid,ID_BACK);


        $this->InsertDataToTable($this->TName('tId'),
            [
                "uid"=>$uid,
                "ccardfurl"=>$this->CloudOptions['domain'].'/'.$backMsg['filename'][CARD_FRONT],
                "icardfurl"=>$this->CloudOptions['domain'].'/'.$backMsg['filename'][ID_FRONT],
                "icardburl"=>$this->CloudOptions['domain'].'/'.$backMsg['filename'][ID_BACK],
                "ccardnum"=>0,
                "icardnum"=>0,
                "ftime"=>$timeStamp,
                "state"=>"NONE",
            ]
        );

        return $backMsg;
    }

    //实名认证提交成功(signal签名为用户id和时间戳字符串连接后的sha1值)
    public function RealNameIdentifyFinished($uid,$ccardnum,$icardnum,$signal){
        //未实现
        $tIdentify = DBResultToArray($this->SelectDataFromTable($this->TName('tId'),
            [
                'uid'=>$uid,
                '_logic'=>' '
            ]),true);
        if(!empty($tIdentify)){
            if(sha1($uid.$tIdentify[0]['ftime']) != $signal){
                return RESPONDINSTANCE('40');//签名不正确
            }

            if($tIdentify[0]['state'] != 'NONE'){
                $backMsg = RESPONDINSTANCE('39');
                $backMsg['state'] = $tIdentify[0]['state'];
                return $backMsg;
            }else{
                $this->UpdateDataToTable($this->TName('tId'),
                    [
                        'ccardnum'=>$ccardnum,
                        'icardnum'=>$icardnum,
                        'state'=>'SUBMIT',//修改为提交状态,前台提示已提交
                        'ftime'=>PRC_TIME()//修改时间,禁止签名复用
                    ]
                    ,
                    [
                        'uid'=>$uid,
                        '_logic'=>' '
                    ]);
                return RESPONDINSTANCE('0');
            }
        }else{
            $backMsg = RESPONDINSTANCE('39');
        }

        return $backMsg;
    }

    //实名认证审核
    public function RealNameAudit($uid,$state){
        //未实现
        $tIdentify = DBResultToArray($this->SelectDataFromTable($this->TName('tId'),
            [
                'uid'=>$uid,
                'state'=>'SUBMIT',
                '_logic'=>'AND'
            ]),true);
        if(!empty($tIdentify)) {
            if($state == 'FAILED' || $state=='SUCCESS'){

                $this->UpdateDataToTable($this->TName('tId'),['state'=>$state],[
                    'uid'=>$uid,
                    'state'=>'SUBMIT',
                    '_logic'=>'AND'
                ]);

                if($state== 'FAILED'){
                    NoticeManager::CreateNotice($uid,NOTICE_FAIL.[]);//创建通知——实名认证（旧版）审核失败
                }
                return RESPONDINSTANCE('0');
            }else{
                return RESPONDINSTANCE('43');
            }
        }else{
            return RESPONDINSTANCE('42');//必须是SUBMIT状态的实名认证信息才可通过
        }
    }

    //显示所有需要认证信息
    public function ViewAllVerifyInfo(){
        //未实现

        //有中标梦想并提交了实名认证的用户在此查询并获取

        //提交了实名认证但无中标梦想的用户的实名认证不在此显示

        $array = DBResultToArray(
            $this->SelectDataByQuery($this->TName('tDream'),
                self::C_Or(
                    self::FieldIsValue('state','VERIFY'),
                    self::C_And(
                        self::FieldIsValue('state','SUCCESS'),
                        self::FieldIsValue('payment','0')
                    )
                )
            //$this->SelectDataFromTable($this->TName('tDream'),['state'=>'VERIFY']
        ));

        
        $cond = '';

        $resultArray = [];
        foreach ($array as $key => $item) {
            /*if(!empty($item['videourl'])){
                $finishUser[$item['uid']] = [$item['videourl']];
            }*/
            $resultArray[$item['uid']]['dream'] = $item;
            $resultArray[$item['uid']]['identity'] = [];
            $cond = $cond.$item['uid'].'|';
        }

        $infoArray = DBResultToArray($this->SelectDatasFromTable($this->TName('tUser'),
            ['uid'=>$cond]));
        foreach ($infoArray as $key=>$item) {
            if(array_key_exists($item['uid'],$resultArray)){
                $resultArray[$item['uid']]['info'] =$item;
            }
        }

        $awardArray = DBResultToArray($this->SelectDatasFromTable($this->TName('tAward'),
            ['uid'=>$cond]));

        foreach ($awardArray as $key=>$item) {
            if(array_key_exists($item['uid'],$resultArray)){
                $resultArray[$item['uid']]['award'] =$item;
            }
        }


        $idArray = DBResultToArray($this->SelectDatasFromTable($this->TName('tId'),
            ['uid'=>$cond,
             'state'=>'SUBMIT|SUCCESS|FAILED']
        ));

        foreach ($idArray as $key=>$item) {
            if(array_key_exists($item['uid'],$resultArray)){
                $resultArray[$item['uid']]['identity'] =$item;
            }
        }

        $backMsg = RESPONDINSTANCE('0');
        $backMsg['verify'] = $resultArray;
        //$backMsg['dream'] = $finishUser;
        return $backMsg;
    }

    //获取AccessToken（公众号鉴权入口,获取openid）
    public function GetAccessTokenWeb($code){
        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$GLOBALS['options']['WEB_APP_ID'].'&secret='.$GLOBALS['options']['WEB_APP_SECRET'].'&code='.$code.'&grant_type=authorization_code';
        $result = file_get_contents($url);
        $result = json_decode($result,true);
        $backMsg = RESPONDINSTANCE('0');
        if(!isset($result['openid'])){
            $backMsg = RESPONDINSTANCE('63');
            $backMsg['err'] = $result;
            return $backMsg;
        }
		/*access_token	网页授权接口调用凭证,注意：此access_token与基础支持的access_token不同
		expires_in	access_token接口调用凭证超时时间，单位（秒）
		refresh_token	用户刷新access_token*/
		$backMsg['access_token'] = $result['access_token'];
		$backMsg['expires_in'] = $result['expires_in'];
		$backMsg['refresh_token'] = $result['refresh_token'];
        $backMsg['openid'] = $result['openid'];
        $version = (isset($GLOBALS['options']['version'])?$GLOBALS['options']['version']:'full');
        $backMsg['version'] = $version;
        if($GLOBALS['options']['auth']){//如若需要验证auth_token,生成api_secret
            $auth = AuthManager::GenerateOrUpdateAuthToken($result['openid']);
            $backMsg['auth'] = $auth;
        }
        return $backMsg;
    }


	//获取用户资料(公众号)
	public function GetUserInfoWeb($atoken,$uid){
		$url = "https://api.weixin.qq.com/sns/userinfo?access_token=$atoken&openid=$uid&lang=zh_CN";
		$backMsg = RESPONDINSTANCE('0');
		$backMsg['info'] = HttpGet($url);
		return $backMsg;
	}
	
    //获取AccessToken（鉴权入口,获取openid）
    public function GetAccessToken($code){
        /*wx.request({//获取用户的openid
            url: 'https://api.weixin.qq.com/sns/jscode2session?appid=' + C.conf.appid + '&secret=' + C.conf.secret+'&js_code='+res.code+'&grant_type=authorization_code',
            success:function(res){
            app.globalData.openid = res.data.openid;
            if (app.currentPage && app.currentPage.onLogin){
                app.currentPage.onLogin(res.data.openid)//调用【当登录】事件
              }
            return;
        }
          })*/
        $url = 'https://api.weixin.qq.com/sns/jscode2session?appid='.$GLOBALS['options']['APP_ID'].'&secret='.$GLOBALS['options']['APP_SECRET'].'&js_code='.$code.'&grant_type=authorization_code';
        $result = file_get_contents($url);
        $result = json_decode($result,true);
        $backMsg = RESPONDINSTANCE('0');
		if(!isset($result['openid'])){
			$backMsg = RESPONDINSTANCE('63');
			$backMsg['err'] = $result;
			return $backMsg;
		}
        $backMsg['openid'] = $result['openid'];
        $version = (isset($GLOBALS['options']['version'])?$GLOBALS['options']['version']:'full');
        $backMsg['version'] = $version;
		if($GLOBALS['options']['auth']){//如若需要验证auth_token,生成api_secret
			$auth = AuthManager::GenerateOrUpdateAuthToken($result['openid']);
			$backMsg['auth'] = $auth;
		}
        return $backMsg;
    }


    //获取进入的版本入口
    public function VersionControl(){
        $version = (isset($GLOBALS['options']['version'])?$GLOBALS['options']['version']:'full');
        $backMsg = RESPONDINSTANCE('0');
        $backMsg['version'] = $version;
        return $backMsg;
    }

    //获取JSAPI 配置参数
    public function GetJSConfig($url){

        $appid = $GLOBALS['options']['WEB_APP_ID'];
        $appsecret = $GLOBALS['options']['WEB_APP_SECRET'];
        $accessToken = "";
        $ticket = "";
        $timeStamp = PRC_TIME();
        $accessFile = "access.json";
        $file = [];
        $GetAccessToken = true;
        $GetTicket = true;
        $atExpires_in = 7200;
        $ticketExpires_in = 7200;

        $accessExist = file_exists($accessFile);
        if($accessExist){
            $file = json_decode(file_get_contents($accessFile),true);
            $GetAccessToken = ($file['timeStamp'] + $file['ac_expires_in'])<$timeStamp;
            $GetTicket = ($file['timeStamp'] + $file['ti_expires_in'])<$timeStamp;
            $accessToken = $file['access_token'];
            $ticket = $file['ticket'];
            $atExpires_in = $file['ac_expires_in'];
            $ticketExpires_in = $file['ti_expires_in'];
        }

        if((!$accessExist || $GetAccessToken) && $GetTicket) {

            $aturl = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$appsecret";
            $atResult = file_get_contents($aturl);
            $atResult = json_decode($atResult, true);
            $atExpires_in = $atResult['expires_in'];
            $accessToken = $atResult['access_token'];
        }

        if((!$accessExist || $GetTicket)) {
            $ticketUrl = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=" . $accessToken . "&type=jsapi";
            $ticketResult = file_get_contents($ticketUrl);
            $ticketResult = json_decode($ticketResult, true);
            $ticket = $ticketResult['ticket'];
            $ticketExpires_in = $ticketResult['expires_in'];
        }


        $nonce = sha1($timeStamp);
        $fullUrl = $url;//"http://tinydream.antit.top/TinydreamWeb/demo.html";//'http://'.$_SERVER['HTTP_HOST'].'/TinydreamWeb/demo.html';
        $signStr = "jsapi_ticket=$ticket&noncestr=$nonce&timestamp=$timeStamp&url=$fullUrl";

        $config = [
            'debug'=>false,
            'appId'=>$appid,
            'timestamp'=> $timeStamp,
            'nonceStr' => $nonce,
            'signature'=> sha1($signStr),
            'jsApiList'=> [
                'updateAppMessageShareData',
                'updateTimelineShareData',
				'onMenuShareAppMessage',
				'onMenuShareTimeline'
            ]
        ];

        $file['access_token'] = $accessToken;
        $file['ticket'] = $ticket;
        if($GetTicket || $GetAccessToken) {
            $file['timeStamp'] = $timeStamp;
        }else{
            $file['timeStamp'] = $file['timeStamp'];
        }
        $file['ac_expires_in'] = $atExpires_in;
        $file['ti_expires_in'] = $ticketExpires_in;

        file_put_contents('access.json',json_encode($file,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));


        $backMsg = RESPONDINSTANCE('0');
        $backMsg['fullUrl'] = $fullUrl;
        $backMsg['signStr'] = $signStr;
        $backMsg['config'] = $config;
        return $backMsg;
    }
	
	
}
?>