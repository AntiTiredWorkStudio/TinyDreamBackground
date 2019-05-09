<?php
	header("Content-Type: text/html;charset=utf-8"); 
	LIB($_GET['act']);

	/*
	生成验证码 :va=gen&tele=13439955175
	核实验证码:va=con&tele=13439955175&val=123456
	*/

    Responds($_GET['act'],(new DreamPoolManager()),
    [
        'inf'=>R('info'),//初始化数据库
        'add'=>R('add',["ptitle","uid","tbill","ubill","duration"]),//【请求】增加梦想池
		'addi'=>R('AddPoolByIndex',["index","uid","tbill","ubill","day"]),//通过期号和持续天数增加梦想池
        'apbd'=>R('AddPoolByDay',["uid","tbill","ubill","day"]),//通过天数增加梦想池
		'list'=>R('ListAllPool'),//列出全部梦想池
        'listr'=>R('ListPoolsByRange',['seek','count']),//按范围列出梦想池
        'fua'=>R('ForceUpdateAllPools'),//强制刷新全部梦想池
		'fup'=>R('UpdatePool',['pid']),//强制更新梦想互助
		'gdtl'=>R('GetDayTimeLess',['#formate']),//获取当天剩余时间
		'gfmd'=>R('FirstMonthDay'),//获取本月第1天
		'gid'=>R('gid'),//自动生成id
		'cpool'=>R('CountPools'),//返回梦想池数量
        'pinfo'=>R('GetPoolInfo',['pid']),//单独获得梦想池信息
		'pidm'=>R('PoolIdList'),//获取近一月梦想互助id列表
    ]);
?>