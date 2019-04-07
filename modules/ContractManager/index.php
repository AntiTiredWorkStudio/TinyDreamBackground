<?php
	header("Content-Type: text/html;charset=utf-8"); 
	LIB($_GET['act']);

	Responds($_GET['act'],(new ContractManager()),
    [
        'inf'=>R('info'),//模块信息
        'list'=>R('ContractList'),//获取合约类型表（信息）
        'info'=>R('ContractInfo',['cid']),//获取合约类型表（信息）
    ]);
?>