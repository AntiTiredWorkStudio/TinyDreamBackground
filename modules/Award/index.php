<?php
	header("Content-Type: text/html;charset=utf-8"); 
	LIB($_GET['act']);

	Responds($_GET['act'],(new AwardManager()),
    [
        'inf'=>R('info'),//模块信息
        'done'=>R('DoneAlottery',['dnum']),//开奖
        'onums'=>R('GetLotteryByOrder',['oid']),//通过订单获取
        'anums'=>R('AutoLottery'),//自动开奖
        'lfromp' => R('GetLotteryFromPid',['pid']),
        'gawap'=>R('GetUnawardPools'),
    ]);
?>