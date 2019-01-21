<?php
	header("Content-Type: text/html;charset=utf-8"); 
	LIB($_GET['act']);

	Responds($_GET['act'],(new NoticeManager()),
    [
        'inf'=>R('info'),//模块信息
        'nc'=>R('NoticeCount',['uid']),//获取用户的未读消息数量
        'ng'=>R('GetUserUnReadNotice',['uid','seek','count']),//获取用户未读消息
        'ngl'=>R('GetUserAllNotice',['uid','seek','count']),//获取用户未读消息
        'nr'=>R('ReadNotice',['nid']),//阅读消息
        'ta'=>R('TestAction')
    ]);
?>