<?php

    header("Access-Control-Allow-Origin:*");

    header("Content-Type: text/html;charset=utf-8;");

	include_once("public/conf.php");//加载配置文件
	include_once("public/lib.php");//加载公有库
    include_once ("public/adapter.php");//加载适配器

	$requestArray = [];

	foreach($_REQUEST as $key=>$value){
		array_push($requestArray,$key);
	}
	
	//$postArr = $GLOBALS['HTTP_RAW_POST_DATA'];
    //$postObj = simplexml_load_string($postArr, 'SimpleXMLElement', LIBXML_NOCDATA);

	//file_put_contents("request.txt",json_encode($_REQUEST).json_encode($postObj));
	
	if(empty($requestArray)){
	    if(isset($GLOBALS['options']['ManageIndex'])){
            header('Location:'.$GLOBALS['options']['ManageIndex']);//进入管理后台
	        return;
        }else {
            die(FAILED('98'));
        }
	}

	REQUEST($requestArray[0]);
?>