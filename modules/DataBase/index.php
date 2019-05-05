<?php
	header("Content-Type: text/html;charset=utf-8");

	include_once("DBManager.php");

    Responds($_GET['act'],(new DBManager()),
        [
            'inf'=>R('info'),//信息
            'ini'=>R('InitDB'),//初始化数据库
            'getf'=>R('GetTableFields',['tname']),//获取数据库字段
            'template'=>R('DBPHPTemplate'),
            //'test'=>R('FIV'),
        ],PERMISSION_LOCAL | PERMISSION_AUTH_FREE);

?>