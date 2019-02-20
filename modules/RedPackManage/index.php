<?php
	header("Content-Type: text/html;charset=utf-8"); 
	LIB($_GET['act']);

	Responds($_GET['act'],(new RedPackManage()),
    [
        'inf'=>R('info')//模块信息
    ]);
?>