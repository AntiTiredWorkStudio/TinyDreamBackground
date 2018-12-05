<?php
	header("Content-Type: text/html;charset=utf-8");

	LIB($_GET['act']);

    Responds($_GET['act'],(new UserManager()),
    [
        'inf'=>R('info'),//模块信息
        'enter'=>R('EnterApp',['uid','nickname','headicon']),//进入小程序
        'rnames'=>R('RealNameIdentifyStart',['uid']),//实名认证准备
        'rnamef'=>R('RealNameIdentifyFinished',['uid','ccardnum','icardnum','signal']),//实名认证提交
        'rnamea'=>R('RealNameAudit',['uid','state']),//实名认证审核
        'verify'=>R('ViewAllVerifyInfo'),//显示所有需要审核的信息
        'selfinfo'=>R('SelfInfo',['uid']),//获取个人信息
        'rnameg'=>R('GetUserRealNameIdentify',['uid']),//获取单一用户的实名认证信息
        'gakt'=>R('GetAccessToken',['code'])
    ]);
?>