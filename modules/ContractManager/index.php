<?php
	header("Content-Type: text/html;charset=utf-8"); 
	LIB($_GET['act']);
	//模块co 请求示例：co=list、co=info
	Responds($_GET['act'],(new ContractManager()),
    [
        'inf'=>R('info'),//模块信息
        'list'=>R('ContractList'),//获取合约类型表（信息）无参数
        'info'=>R('ContractInfo',['cid']),//通过id获取合约类型表（信息） 参数[cid:合约id]
        'set'=>R('SetContract',['cid']),//设置合约信息,参数[cid:合约id],需设修改的置属性及属性值以&key=value追加至请求后部即可,无法设置cid,不追加任何设置参数将会返回属性列表
    ]);
?>