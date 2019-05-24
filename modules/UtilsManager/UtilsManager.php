<?php
//引用此页面前需先引用conf.php
LIB('db');
error_reporting(E_ALL ^ E_DEPRECATED);
include_once "public/Res/autoload.php";
use Qiniu\Auth;
//表单对象
class Table{
	public $fieldsArray = [];
	public $datas = [];
	public $indexObject = [];
	public $seek = 0;
	public $total = 1;
	public $size = 1;
	public $args = [];
	
	public function LoadField($fields){
		foreach($fields as $key){
			array_push($this->fieldsArray,$key);
		}
		return $this;
	}

	//需要返回数组包含属性:需要方法格式: function():[datas,seek,size,total]
	public function LoadDatasHandle($func){
	    $standard = $func($this->args[0]);
        $this->datas = $standard['datas'];
        $this->seek = $standard['seek'];
        $this->size = $standard['size'];
        $this->total = $standard['total'];
        return $this;
    }

	public function LoadDatas($tData,$seek,$count,$total){
		$this->datas = $tData;
		$this->seek = $seek;
		$this->size = $count;
		$this->total = $total;
		return $this;
	}

	//需要方法格式: function($key,$value):$value
	public function DataEachHandle($func){
		if(empty($this->datas)){
			return $this;
		}
		foreach($this->datas as $key=>$value){
		    $targetValue = $func($key,$value);
		    if(empty($targetValue)){
                unset($this->datas[$key]);
            }else {
                $this->datas[$key] = $targetValue;
            }
		}
		return $this;
	}

    //需要方法格式: function($data):$value
    public function DataHandle($func){
        if(empty($this->datas)){
            return $this;
        }
        $this->datas = $func($this->datas);
        return $this;
    }

	//为表格添加翻页索引
	public function DataFinished(){
		$this->indexObject = UtilsManager::BuildPageIndex($this->seek,$this->total,$this->size);
		return $this;
	}

	//封口转换为请求返回
	public function ToRespond(){
		$backMsg = RESPONDINSTANCE('0');
		$backMsg['data'] = $this->datas;
		$backMsg['index'] = $this->indexObject;
		$backMsg['fields'] = $this->fieldsArray;
		$backMsg['count'] = $this->total;
		return $backMsg;
	}

	public function ToArray(){
        $backMsg = [];
        $backMsg['data'] = $this->datas;
        $backMsg['index'] = $this->indexObject;
        if(!empty($this->fieldsArray)) {
            $backMsg['fields'] = $this->fieldsArray;
        }
        $backMsg['count'] = $this->total;
        return $backMsg;
    }
	
	public function Table(){
        $this->args = func_get_args();
    }
}
//数据对象
class DataManager{
    public static $ROOT = 'datas';//默认缓存文件夹路径

    //获取完整的路径
    public static function Data_Dir($path){
        $path = self::$ROOT.'\\'.$path;
        if(!is_dir($path)){
            mkdir($path, 0777, true);
        }
        return $path;
    }

    //获取完整文件路径
    public static function Data_File($path,$name,$suffix){
        return self::Data_Dir($path).'\\'.$name.$suffix;
    }

    //判断文件是否存在
    public static function File_Exist($path,$name,$suffix){
        return file_exists(self::Data_File($path,$name,$suffix));
    }
}
//上传管理器
class UploadObject{
	public static function GenerateFileName($id,$type){
        return $type.'_'.sha1($id);
    }

	public static function uploadURLFromRegionCode($code) {
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

	public $id;
	public $type;
	public function UploadObject($id,$type){
		$this->id = $id;
		$this->type = $type;
	}
	
	public function GetTokenInfo(){
		$conf = $GLOBALS['options']['cloud'];
        $auth = new Auth($conf['ak'], $conf['sk']);
        $token = $auth->uploadToken($conf['bucket']);
        $timeStamp = PRC_TIME();
		$backMsg = [];
        $backMsg['uptoken']=$token;
        $backMsg['upurl']= self::uploadURLFromRegionCode($conf['region']);
        $backMsg['domain']=$conf['domain'];
        $backMsg['timeStamp']=$timeStamp;
        $backMsg['fileName'] = self::GenerateFileName($this->id,$this->type);
		return $backMsg;
	}
}
//二维码对象
class QrcodeObject{
    public $path;
    public function QrcodeObject($name){
        $this->path=DataManager::Data_File('qrcode',$name,'.png');
    }


    public function MakeQrcode($text){
        include "phpqrcode.php";
        QRcode::png($text,$this->path,'L',6,2);
        return $this;
    }

    public function UrlLink(){
        return "https://".$_SERVER['HTTP_HOST'].'/'.$this->path;
    }
}
//Json对象
class JsonObject{
	public $path;
	public $data;
	public function JsonObject($tUrl=''){
		$this->path = $tUrl;
		$this->data = [];
		$this->Loading();
	}
	
	public function Loading($tUrl=''){
		if($tUrl!=''){
			$this->path = $tUrl;
		}
		if(!empty($this->path)){
			if(file_exists($path)){
				$this->data = json_decode(file_get_contents($path),true);
			}else{
				file_put_contents($this->path,json_encode($this->data,JSON_UNESCAPED_UNICODE));
			}
		}
		return $this;
	}
	public function Write($writeHandle){
		if(!empty($this->data))
			$this->data = $writeHandle($this->data);
		return $this->Save();
	}
	public function Save(){
		if($this->path!='')
			file_put_contents($this->path,json_encode($this->data,JSON_UNESCAPED_UNICODE));
		return $this;
	}
	public function Release(){
		unlink($this->path);
		$this->path = "";
		$this->data = [];
		return $this;
	}
}

class UtilsManager extends DBManager{
    public function info()
    {
		$json = new JsonObject();
        //self::MakeQrcode();
        return "UtilsManager"; // TODO: Change the autogenerated stub
    }

	public function UtilsManager(){
	}

	
	//创建目录导航
	public static function BuildPageIndex($seek,$count,$size,$HalfPageMax = 3){
		$pageIndex = [];
		$currentPage = Ceil($seek/$size);
		
		
		$totalPage = Ceil($count/$size);
		
		$startIndex = ($currentPage - $HalfPageMax)<0?0: ($currentPage - $HalfPageMax);
		$endIndex = ($currentPage + $HalfPageMax)>$totalPage?$totalPage: ($currentPage + $HalfPageMax);
		
		$pageIndex['allowLast'] = $startIndex>0;
		$pageIndex['allowNext'] = $endIndex<$totalPage;
		
		for($i=$startIndex;$i<$endIndex;$i++){
			$pageIndex['list'][$i] = $i*$size;
		}
		$pageIndex['current'] = $currentPage;
		$pageIndex['count'] = $count;
		$pageIndex['size'] = $size;
		return $pageIndex;
	}
	
	public static function CreateTable(){
		return new Table(func_get_args());
	}
	
	public function TryTable($state,$seek,$count){
        return RESPONDINSTANCE('0');
	}


    public function TryQrcode($text){
        echo (new QrcodeObject(sha1('qr'.PRC_TIME())))->MakeQrcode($text)->UrlLink();
    }
		
	//获取上传凭证
	public function UploadToken($id){
		$backMsg = RESPONDINSTANCE('0');
		$backMsg['token'] = (new UploadObject($id, FREE_PARS('type','DEFAULT')))->GetTokenInfo();
		return $backMsg;
	}
	//获取多个上传凭证
	public function UploadTokens($id_list){
		$ids = json_decode($id_list);
		if(empty($ids)){
			return RESPONDINSTANCE('100','JSON格式有误');
		}
		$array = [];
		foreach($ids as $id){
			$token = (new UploadObject($id, FREE_PARS('type','DEFAULT')))->GetTokenInfo();
			$array[$id] = $token;
		}
		$backMsg = RESPONDINSTANCE('0');
		$backMsg['token'] = $array;
		return $backMsg;
	}
}
?>