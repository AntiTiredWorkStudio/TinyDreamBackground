<?php
	header("Content-Type: text/html;charset=utf-8"); 
	LIB($_GET['act']);

	Responds($_GET['act'],(new TradeManager()),
    [
        'inf'=>R('info'),//模块信息
        'adt'=>R('AddTradeInfo',['title','url','profit','ubill']),//增加小生意信息
        'gtp'=>R('GetTradeByPid',['pid']),
        'gtt'=>R('GetTradeByTid',['tid']),
        'gtpp'=>R('GetTradePPer',['uid','pid']),
    ]);
?>