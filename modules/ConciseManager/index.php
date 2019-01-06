<?php
	header("Content-Type: text/html;charset=utf-8"); 
	LIB($_GET['act']);

	Responds($_GET['act'],(new ConciseManager()),
    [
        'inf'=>R('info'),//模块信息
        'sp'=>R('SubmitDreamAndRequestPayment',['uid','title','content','server','bill']),
        'paid'=>R('OrderPaid',['uid','hid']),
        'ud'=>R('UserDream',['uid'])
    ]);
?>