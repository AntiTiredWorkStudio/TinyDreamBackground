<?php
	header("Content-Type: text/html;charset=utf-8"); 
	LIB($_GET['act']);
	//模块op 请求示例：op=joi、op=jof
	Responds($_GET['act'],(new OperationManager()),
    [
        'inf'=>R('info'),//模块信息
        'joi'=>R('JoinContract',['cid','uid']),//参加合约，参数：[cid:合约cid,uid:用户openid]
        'jof'=>R('JoinContractComplete',['cid','oid','uid','theme']),//完成支付后成功参与合约，创建行动实例，参数：[cid:合约cid,oid:上个请求获得的订单号,uid:用户openid,theme:主题字符串]
		'mat'=>R('MakeAttendance',['opid','uid']),//完成支付后成功参与合约，创建行动实例，参数：[opid:行动id,uid:用户openid]
        'cal'=>R('OperationCalendar',['uid']),//获取用户当前行动日历
        'pat'=>R('PatchAttendance',['uid','date']),//用户补卡
    ]);
?>