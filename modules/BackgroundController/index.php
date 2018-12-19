<?php
	header("Content-Type: text/html;charset=utf-8"); 
	LIB($_GET['act']);

	Responds($_GET['act'],(new BackgroundController()),
    [
        'inf'=>R('info'),//模块信息
        'bnav'=>R('BuildNavigator',null,'all',false),//创建导航栏
        'a_post'=>R('BuildPostDream',null,'all',false),//引用发布梦想池
        'a_verify'=>R('BuildVerify',null,'all',false),//引用审核
    ]);
?>