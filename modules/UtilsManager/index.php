<?php
    /*
     * 定义参数列表中以#开头为可选参数
    */
	header("Content-Type: text/html;charset=utf-8"); 
	LIB($_GET['act']);

	Responds($_GET['act'],(new UtilsManager()),
    [
        'inf'=>R('info'),//模块信息
		'try'=>R('TryTable',['state','seek','count']),//测试Table
        'qrcode'=>R('TryQrcode',['text']),//生成二维码
    ]);
?>