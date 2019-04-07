<?php
	header("Content-Type: text/html;charset=utf-8"); 
	LIB($_GET['act']);

	Responds($_GET['act'],(new OperationManager()),
    [
        'inf'=>R('info'),//模块信息
        'joi'=>R('JoinContract',['cid','uid']),//参加合约
        'jof'=>R('JoinContractComplete',['cid','oid','uid','theme']),//完成支付后成功参与合约，创建行动实例
    ]);
?>