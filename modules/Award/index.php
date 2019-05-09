<?php
	header("Content-Type: text/html;charset=utf-8"); 
	LIB($_GET['act']);

	Responds($_GET['act'],(new AwardManager()),
    [
        'inf'=>R('info'),//模块信息
        'done'=>R('DoneAlottery',['dnum','#dblink']),//开奖
        'onums'=>R('GetLotteryByOrder',['oid']),//通过订单获取
        'anums'=>R('AutoLottery',null,PERMISSION_AUTH_FREE|PERMISSION_LOCAL),//自动开奖
        'atnums'=>R('AutoTryLottery'),//自动开奖测试
        'lfromp' => R('GetLotteryFromPid',['pid']),
        'gawap'=>R('GetUnawardPools'),
		'gplu'=>R('GetPreviousLuckyByRange',['seek','count','#awardtype']),//获取往期幸运者
		'cplu'=>R('CountPreviousLucky',['#awardtype']),//获取往期幸运者
        'calc'=>R('GetCalc',['pid']),//获取计算步骤
        'uplid'=>R('UpdateLottery',['lid']),//更新中奖编号
        'tsend'=>R('SendShortMsgToUser',['pid','au','al']),
        'astart'=>R('ActivityStart',['pid']),
        'aend'=>R('ActivityEnd',['pid','url']),
        'alive'=>R('ActivityLive'),
        'gtai'=>R('GetTradeAwardInfo',['pid','uid']),//获取幸运者详情页的收益分成信息
        'gubp'=>R('AwardUserByPid',['pid']),//通过pid获取中奖用户信息
    ]);
?>