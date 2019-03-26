<?php
	header("Content-Type: text/html;charset=utf-8");

	LIB($_GET['act']);

    Responds($_GET['act'],(new UserManager()),
    [
        'inf'=>R('info',null,PERMISSION_USER_OWNER),//模块信息
        /*--------------------有关用户个人信息--------------------*/
        'enter'=>R('EnterApp',['uid','nickname','headicon']),//进入小程序
        'enterpack'=>R('EnterAppInRedPackage',['uid','nickname','headicon']),//通过红包进入页面
        'selfinfo'=>R('SelfInfo',['uid']),//获取个人信息
		'sibynn'=>R('SelfInfoByNickName',['nickname']),//通过昵称获取个人信息
        'gakt'=>R('GetAccessToken',['code'],PERMISSION_AUTH_FREE),//获取access_token【小程序入口,返回secret及openid】
        'gawt'=>R('GetAccessTokenWeb',['code'],PERMISSION_AUTH_FREE),//获取AccessToken（公众号鉴权入口,获取openid）
		'guif'=>R('GetUserInfoWeb',['atoken','uid']),//获取用户资料(公众号)
        'gjsc'=>R('GetJSConfig',['url'],PERMISSION_AUTH_FREE),//获取JSAPI 配置参数
        /*--------------------后台登录--------------------*/
        'blogin'=>R('OnGetLoginCode',['tele'],PERMISSION_AUTH_FREE),//后台用户登录
        'ologin'=>R('OnBackgroundLogin',['tele','code'],PERMISSION_AUTH_FREE),//后台登录，校验验证码【后台入口,返回secret及openid】
        /*--------------------旧版实名认证请求--------------------*/
        'rnameg'=>R('GetUserRealNameIdentify',['uid']),//获取单一用户的实名认证信息
        'rnames'=>R('RealNameIdentifyStart',['uid']),//实名认证准备
        'rnamef'=>R('RealNameIdentifyFinished',['uid','ccardnum','icardnum','signal']),//实名认证提交
        'rnamea'=>R('RealNameAudit',['uid','state']),//实名认证审核
        'verify'=>R('ViewAllVerifyInfo'),//显示所有需要审核的信息
        /*--------------------新版实名认证请求--------------------*/
        'rnamegx'=>R('GetUserRealNameIdentifyx',['uid']),//获取单一用户的实名认证信息
        'rnamesx'=>R('RealNameIdentifyStartx',['uid']),//实名认证准备
        'rnamefx'=>R('RealNameIdentifyFinishedx',['uid','realname','ccardnum','icardnum','bank','openbank']),//实名认证提交
        'rnameax'=>R('RealNameAuditx',['uid','state']),//实名认证审核
        'verifyx'=>R('ViewAllVerifyInfox'),//显示所有需要审核的信息
        'ver'=>R('VersionControl'),//获取要进入的版本
		'gubt'=>R('GetUserByTelephone',['tele']),//通过手机号查找用户信息
    ]);
?>