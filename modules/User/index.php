<?php
	header("Content-Type: text/html;charset=utf-8");

	LIB($_GET['act']);

    Responds($_GET['act'],(new UserManager()),
    [
        'inf'=>R('info'),//模块信息
        'enter'=>R('EnterApp',['uid','nickname','headicon']),//进入小程序
    ]);
?>