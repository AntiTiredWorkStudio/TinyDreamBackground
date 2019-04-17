<?php
	header("Content-Type: text/html;charset=utf-8"); 
	LIB($_GET['act']);
	//模块op 请求示例：op=joi、op=jof
	Responds($_GET['act'],(new OperationManager()),
    [
        'inf'=>R('info'),//模块信息
        'joi'=>R('JoinContract',['cid','uid']),//参加合约，参数：[cid:合约cid,uid:用户openid]
        'jof'=>R('JoinContractComplete',['cid','oid','uid','theme']),//'','uid','theme'
        //完成支付后成功参与合约，创建行动实例，参数：[cid:合约cid,oid:上个请求获得的订单号,uid:用户openid,theme:主题字符串,icode:页数(可选参数,分享页面的行动id(opid))]
		'mat'=>R('MakeAttendance',['opid','uid']),//打卡，创建打卡记录，参数：[opid:行动id,uid:用户openid]
        'cal'=>R('OperationCalendar',['uid']),//获取用户当前行动日历，参数：[uid:用户openid,seek:页数(可选参数，根据月份填写，不传默认全部返回)]
        'pat'=>R('PatchAttendance',['uid','date']),//用户补卡，参数：[uid:用户openid,date:日期字符串(Y:m:d)]
        'rep'=>R('Reply',["opid","date","uid"]),//用户转发,参数：[opid:行动id,date:日期字符串(Y:m:d),uid:用户openid]
        'oif'=>R('OperationInfo',['opid']),//获得行动概况(包含距离目标天数,连续打卡天数,已经打卡天数,缺卡天数,补卡天数,进度),参数：[opid:行动id]
        'olist'=>R('OperationList',['uid','seek','count']),//获得用户所有行动列表,参数：[uid:用户openid,seek:页数,count:每页个数]
        'uinfo'=>R('UserOperationInfo',['uid']),//用户行动信息,参数：[uid:用户openid]
        'ihics'=>R('UserInvitedUserHeadicons',['uid']), //获得被用户邀请的全部邀请者的头像[uid:用户openid]
		'oshar'=>R('OnShareOpen',['opid']),//打开分享页面,参数：[opid:行动id]
		'eomp'=>R('EnterOperationMainPage',['uid']),//进入行动派首页,参数：[uid:用户openid]
    ]);
?>