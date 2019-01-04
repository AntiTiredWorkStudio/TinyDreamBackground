<?php
	header("Content-Type: text/html;charset=utf-8"); 
	LIB($_GET['act']);


	Responds($_GET['act'],(new TestManager()),
    [
        'inf'=>R('info'),//模块信息
        't'=>R('PoolTest'),
        'u'=>R('CreateTestUser'),
        'rb'=>R('RandomBuyPool'),//随机用户购买某梦想池
        'cp'=>R('CreateDreamPool',['c']),//生成一定数量的梦想池
        'cu'=>R("CreateUserAndDream",['c']),//随机生成用户和梦想并绑定手机号
        'fi'=>R('FixDreamPoolUnrightbleFinished'),////检查梦想池非正常结束记录
		'fo'=>R('FixOrderDreamUndefine'),//检查订单中梦想编号未定义
		'fl'=>R('FixLottery'),//修复编号梦想undefined问题
        'fa'=>R('FixUserAwardMoney'),//修复中奖获得金额信息
		'rl'=>R('RebuildLotteryState'),//重新建立梦想编号状态
    ],PERMISSION_LOCAL);
?>