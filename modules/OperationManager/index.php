<?php
	header("Content-Type: text/html;charset=utf-8"); 
	LIB($_GET['act']);
	//模块op 请求示例：op=joi、op=jof
	Responds($_GET['act'],(new OperationManager()),
    [
        'inf'=>R('info'),//模块信息
        'joi'=>R('JoinContract',['cid','uid']),//参加合约，参数：[cid:合约cid,uid:用户openid]
        'jof'=>R('JoinContractComplete',['cid','oid','uid','theme','#icode']),//'','uid','theme'
        //完成支付后成功参与合约，创建行动实例，参数：[cid:合约cid,oid:上个请求获得的订单号,uid:用户openid,theme:主题字符串,icode:页数(可选参数,分享页面的行动id(opid))]
		'mat'=>R('MakeAttendance',['opid','uid','#dfs']),//打卡，创建打卡记录，参数：[opid:行动id,uid:用户openid]
        'cal'=>R('OperationCalendar',['uid','#dfs','#seek','#full']),//获取用户当前行动日历，参数：[uid:用户openid,seek:页数(可选参数，根据月份填写，不传默认全部返回)]
        'pat'=>R('PatchAttendance',['uid','date','#dfs']),//用户补卡，参数：[uid:用户openid,date:日期字符串(Y:m:d)]
        'rep'=>R('Reply',["opid","date","uid"]),//用户转发,参数：[opid:行动id,date:日期字符串(Y:m:d),uid:用户openid]
        'oif'=>R('OperationInfo',['opid']),//获得行动概况(包含距离目标天数,连续打卡天数,已经打卡天数,缺卡天数,补卡天数,进度),参数：[opid:行动id]
        'olist'=>R('OperationList',['uid','seek','count']),//获得用户所有行动列表,参数：[uid:用户openid,seek:页数,count:每页个数]
        'uinfo'=>R('UserOperationInfo',['uid']),//用户行动信息,参数：[uid:用户openid]
        'ihics'=>R('UserInvitedUserHeadicons',['uid']), //获得被用户邀请的全部邀请者的头像[uid:用户openid]
		'oshar'=>R('OnShareOpen',['opid']),//打开分享页面,参数：[opid:行动id]
		'eomp'=>R('EnterOperationMainPage',['uid']),//进入行动派首页,参数：[uid:用户openid]
		'cls'=>R('ClearAllOAInfo',['#type','#uid']),//清理行动及打卡数据（包括：行动信息、打卡记录、邀请信息、行动订单）,参数：[uid:用户openid](可不加，不加为清除全部)
		'gudo'=>R('GetUserDoingOperation',['uid','secret']),//获取用户正在参加的行动,参数:[uid:用户openid,secret:sha1("追梦行动派")]
        'reset'=>R('ResetOperation',['uid']),//重置用户正在进行的行动数据及打卡记录,参数:[uid:用户openid]
        'gopd'=>R('GetOperationData',['state','seek','count','#tele']),//获取用户行动数据
        'gatd'=>R('GetAttendenceData',['tele','seek','count']),//获取用户打卡数据
        'gind'=>R('GetInviteData',['seek','count']),//获取用户邀请记录
        'opt'=>R('GetOperationTools',['uid','#catalog','#select','#seek','#count']),//获取行动工具,参数[uid:用户openid,catalog(可选):可填写值为true/false(为true时不论是否有用户合约,都会返回无合约时工具页面内容,不填时默认为true),select(可选):筛选主题的类别(为typelist数组中的值,不填时默认为合约主题对应的类别),seek(可选):公众号列表起始下标(不填默认为0),count:公众号列表单位获取长度(不填默认为5)]
		'tat'=>R('GerContinuityAttendance',['opid']),//获取连续天数
		'tuo'=>R('TryUserOperation',['uid']),//测试用户行动
		'uop'=>R('UpdateOperationStat',['opid']),//根据打卡记录更新行动数据
    ]);
?>