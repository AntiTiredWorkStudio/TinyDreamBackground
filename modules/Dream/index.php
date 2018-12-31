<?php
	header("Content-Type: text/html;charset=utf-8");



	LIB($_GET['act']);

	/*
	生成验证码 :va=gen&tele=13439955175
	核实验证码:va=con&tele=13439955175&val=123456
	*/

    Responds($_GET['act'],(new DreamManager()),
    [
        'inf'=>R('info'),//模块信息
        'dlist'=>R('OnDreamList',['uid']),//查看梦想列表
        'pedit'=>R('PrepareEditDream',['uid']),//准备编辑梦想
        'dedit'=>R('OnEditDream',['uid','title','content']),//编辑梦想(可更改ACTION)
        'sdream'=>R('OnDreamSelected',['uid','did','action']),//选择梦想(可更改ACTION)
        'uedit'=>R('UpdateDream',['uid','did']),//修改梦想
        'gedit'=>R('OnEditingDream',['uid','did','contentList']),//完善梦想
        'gdream'=>R('GetUserSingleDream',['uid','did']),//获取单一梦想
        'sdjson'=>R('SetDreamStateByJson',['did','state']),//通过JSON 修改梦想状态
        //'gdname'=>R('GetDreamUid',['did'])
    ]);
?>