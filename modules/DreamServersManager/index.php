<?php
	header("Content-Type: text/html;charset=utf-8"); 
	LIB($_GET['act']);

	Responds($_GET['act'],(new DreamServersManager()),
    [
        'inf'=>R('info'),//模块信息
        'info'=>R('ListInfo'),
        'buy'=>R('PlaceOrderInADreamPoolStart',['uid','pid'])//购买梦想份数,可更改ACTION)
    ]);
?>