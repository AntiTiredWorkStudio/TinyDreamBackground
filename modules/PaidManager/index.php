<?php
	header("Content-Type: text/html;charset=utf-8"); 
	LIB($_GET['act']);

	Responds($_GET['act'],(new PaidManager()),
    [
        'inf'=>R('info'),//模块信息
		'of'=>R('OrderFinished',['oid','traid','bill','state']),//完成订单
    ]);
?>