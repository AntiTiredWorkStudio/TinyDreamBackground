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
        'inf'=>R('info')//模块信息
    ]);

?>