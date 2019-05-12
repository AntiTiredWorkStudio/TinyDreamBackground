<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019-5-12
 * Time: 下午 11:11
 */
class PublicTools{
    public static function StartWith($str,$pattern) {
        if(strpos($str,$pattern) === 0)
            return true;
        else
            return false;
    }
    public static function EndWith($haystack, $needle) {
        $length = strlen($needle);
        if($length == 0)
        {
            return true;
        }
        return (substr($haystack, -$length) === $needle);
    }

    public static function EachFunction($array,$func){
        $resultList = [];
        foreach ($array as $item) {
            array_push($resultList,$func($item));
        }
        return $resultList;
    }

    public static function AttributeToArray($obejct,$attribute){
        if(isset($obejct[$attribute])){
            return $obejct[$attribute];
        }else{
            return "";
        }
    }
    public static function ListAttributeToArray($objectList,$attribute){
        $result = [];
        foreach ($objectList as $item) {
            array_push($result,PublicTools::AttributeToArray($item,$attribute));
        }
        return $result;
    }

    public static function HttpGet($url){
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

    public static function https_request($url,$data = null){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }

    public static function ConnectArrayByChar($numsArray,$char){
        $str = '';
        foreach ($numsArray as $num){
            $str = $str.$num.$char;
        }
        return rtrim($str,$char);
    }

    public static function ROOT_DIR(){
        return __DIR__;
    }

    public static function GetFirstMonthDayStamp(){
        $y=date("Y",time());
        $m=date("m",time());
        $d = 1;
        return strtotime("$m/$d/$y");
    }

    public static function GetFirstMonthDayObject(){
        $date = [];
        $date['y']=date("Y",time());
        $date['m']=date("m",time());
        $date['d'] = 1;
        return $date;
    }

    //获取当天剩余时间
    public static function GetDayLessTime(){
        return 86400 - (PRC_TIME()+8*3600)%86400;
    }

    //获取当天已过时间
    public static function GetDayPassTime(){
        return (PRC_TIME()+8*3600)%86400;
    }

    //通过天数计算时间戳
    public static function DAY2TIME($day){
        return $day*86400;
    }

    //转码函数
    public static function unicode2utf8($str) { // unicode编码转化，用于显示emoji表情
        $str = '{"result_str":"' . $str . '"}'; // 组合成json格式
        $strarray = json_decode ( $str, true ); // json转换为数组，利用 JSON 对 \uXXXX 的支持来把转义符恢复为 Unicode 字符
        return $strarray ['result_str'];
    }
}
?>