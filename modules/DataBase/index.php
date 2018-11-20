<?php
	header("Content-Type: text/html;charset=utf-8");

	include_once("DBManager.php");

    if($_SERVER['SERVER_NAME'] != 'localhost') {
        die("无此权限");
    }
    Responds($_GET['act'],(new DBManager()),
        [
            'ini'=>R('InitDB'),//初始化数据库
            'getf'=>R('GetTableFields',['tname']),
            'template'=>R('DBPHPTemplate')
        ]);

?>