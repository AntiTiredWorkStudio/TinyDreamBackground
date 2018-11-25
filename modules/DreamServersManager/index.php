<?php
	header("Content-Type: text/html;charset=utf-8"); 
	LIB($_GET['act']);

	Responds($_GET['act'],(new DreamServersManager()),
    [
        'inf'=>R('info'),//模块信息
        'info'=>R('ListInfo'),
        'buy'=>R('PlaceOrderInADreamPoolStart',['uid','pid']),//购买梦想份数,可更改ACTION)
        'orp'=>R('PlaceOrderInADreamPoolPrepare',['pid']),//准备下单
        'ord'=>R('PlaceOrderInADreamPoolCreate',['action']),//开始下单【修改Action】
        'pay'=>R('PlaceOrderInADreamPoolPay',['uid','oid','bill','pcount','action']),//支付完成【修改action】
        'gap'=>R('GetAllPoolsInfo',['uid']),//玩家获取全部梦想池信息及参与信息
        'oinfo'=>R('GetAllOrdersUser',['uid']),//获取玩家的全部订单
    ]);
?>