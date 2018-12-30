<?php
//引用此页面前需先引用conf.php
error_reporting(E_ALL ^ E_DEPRECATED);
LIB('db');

define('STAT','total');
define('PAY','paid');
define('JOIN','join');

class UserBehaviourManager extends DBManager{

    public static $instance = null;
    public static function Instance(){
        if(self::$instance == null){
            self::$instance = new UserBehaviourManager();
        }
        return self::$instance;
    }

    public static function GetAddRecordSql($ubid,$typeid,$type){
        $table = self::Instance()->TName('tBehave');
        return "UPDATE `$table` SET `$type`=`$type`+1 WHERE `ubid`='$ubid' AND `typeid`='$typeid'";
    }

    public static function OnBehave($typeid,$behave){
        if($typeid != STAT){
            self::OnBehave(STAT,$behave);
        }
        $ubid = self::GenerateDayID($typeid);
        $sql = self::GetAddRecordSql($ubid,$typeid,$behave);
        $link = self::Instance()->DBLink();
        mysql_query($sql,$link);
        if(mysql_affected_rows($link)){
            return;
        }
        $table = self::Instance()->TName('tBehave');
        self::Instance()->InsertDataToTable($table,self::GenerateDayInfo($typeid));
        mysql_query($sql,$link);
        if(mysql_affected_rows($link)){
            return;
        }
    }

    public static function GenerateDayID($typeid){
        return $typeid.'_'.DAY(PRC_TIME());
    }

    public static function GenerateDayInfo($typeid){
        return [
            'ubid'=>self::GenerateDayID($typeid),
            'date'=>date('y-m-d',PRC_TIME()),
            'typeid'=>$typeid,
            'join'=>0,
            'paid'=>0
        ];
    }

    public function info()
    {
        return "UserBehaviourManager"; // TODO: Change the autogenerated stub
    }

	public function UserBehaviourManager(){
		
	}

    //获取记录数量
	public function GetRecordsCount(){
        $table = $this->TName('tBehave');
        $typeid = STAT;
        $sql = "SELECT COUNT(*) FROM `$table` WHERE `typeid`='$typeid'";
        $link = $this->DBLink();
        $resultArray = DBResultToArray(mysql_query($sql,$link),true)[0]['COUNT(*)'];
        $backMsg = RESPONDINSTANCE('0');
        $backMsg['count'] = $resultArray;
        return $backMsg;
    }

    //通过范围获取记录
    public function GetRecordsRecordsByRange($seek,$count){
        $table = $this->TName('tBehave');
        $typeid = STAT;
        $sql = "SELECT * FROM `$table` WHERE `typeid`='$typeid' ORDER BY `date` DESC LIMIT $seek,$count";
        $link = $this->DBLink();
        $resultArray = DBResultToArray(mysql_query($sql,$link),true);
        $backMsg = RESPONDINSTANCE('0');
        $backMsg['recs'] = $resultArray;
        return $backMsg;
    }


    public function paid(){
        self::OnBehave('a01',PAY);
    }

    public function joined(){
        self::OnBehave(STAT,JOIN);
    }
}
?>