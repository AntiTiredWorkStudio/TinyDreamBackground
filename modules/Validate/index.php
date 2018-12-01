<?php
	header("Content-Type: text/html;charset=utf-8"); 
	LIB($_GET['act']);

	/*
	生成验证码 :va=gen&tele=13439955175
	核实验证码:va=con&tele=13439955175&val=123456
	*/
	$VAM = (new ValidateManager());

    Responds($_GET['act'],(new ValidateManager()),
    [
        'inf'=>R('info'),//模块信息
        'pbind'=>R('PrepareBindingTele',['uid']),//进入验证手机号页面调用
        'gcode'=>R('GenerateCode',['tele']),//生成验证码
        'bind'=>R('BindingTele',['uid','tele','code']),//绑定手机号
    ]);

?>