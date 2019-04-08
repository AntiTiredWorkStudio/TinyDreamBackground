<?php
	header("Content-Type: text/html;charset=utf-8"); 
	LIB($_GET['act']);
	//模块co 请求示例：co=list、co=info
	Responds($_GET['act'],(new ContractManager()),
    [
        'inf'=>R('info'),//模块信息
        'list'=>R('ContractList'),//获取合约类型表（信息）无参数
        'info'=>R('ContractInfo',['cid']),//通过id获取合约类型表（信息） 参数[cid:合约id]
    ]);
?>