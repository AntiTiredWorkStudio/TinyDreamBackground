<?php
//引用此页面前需先引用conf.php
LIB('db');
error_reporting(E_ALL ^ E_DEPRECATED);

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
	
	public function Table(){
        $this->args = func_get_args();
    }
}

class QrcodeObject{
    public $path;
    public function QrcodeObject($name,$suffix='.png'){
        $cachePath = 'datas\qrcode';
        if(!is_dir($cachePath)){
            mkdir($cachePath);
        }
        $this->path=$cachePath.'\\'.$name.$suffix;
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
class UtilsManager extends DBManager{
    public function info()
    {
        self::MakeQrcode();
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
}
?>