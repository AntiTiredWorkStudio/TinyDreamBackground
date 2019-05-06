<?php
	header("Content-Type: text/html;charset=utf-8"); 
	LIB($_GET['act']);

	Responds($_GET['act'],(new BackgroundController()),
    [
        'inf'=>R('info',[]),//模块信息
		'pinfo'=>R('BuildPersonalInfo',["uid"],PERMISSION_AUTH_FORCE,false),//创建个人信息块
        'bnav'=>R('BuildNavigator',null,PERMISSION_AUTH_FORCE,false),//创建导航栏
        'a_post'=>R('BuildPostDream',null,PERMISSION_AUTH_FORCE,false),//引用发布梦想池
        'a_verify'=>R('BuildVerify',null,PERMISSION_AUTH_FORCE,false),//引用审核
		'a_data'=>R('BuildDatas',null,PERMISSION_AUTH_FORCE,false),//引用审核
		'a_order'=>R('BuildOrders',null,PERMISSION_AUTH_FORCE,false),//引用订单
		'a_activity'=>R('BuildActivity',null,PERMISSION_AUTH_FORCE,false),//引用订单
		'a_refund'=>R('BuildRefund',null,PERMISSION_AUTH_FORCE,false),//引用退款
		'a_redpack'=>R('BuildRedPackage',null,PERMISSION_AUTH_FORCE,false),//引用红包
        'a_operation'=>R('BuildOperation',null,PERMISSION_AUTH_FORCE,false),//引用行动
    ]);
?>