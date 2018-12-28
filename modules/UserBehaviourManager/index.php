<?php
	header("Content-Type: text/html;charset=utf-8"); 
	LIB($_GET['act']);

	Responds($_GET['act'],(new UserBehaviourManager()),
    [
        'inf'=>R('info'),//模块信息
        'gdid'=>R('GenerateDayID',['typeid'],PERMISSION_LOCAL),//生成每日id
        'paid'=>R('paid',null,PERMISSION_LOCAL),//记录支付成功
        'join'=>R('joined',null,PERMISSION_LOCAL),//记录参与互助
        'gc'=>R('GetRecordsCount'),//获取记录数量
        'gr'=>R('GetRecordsRecordsByRange',['seek','count']),//通过范围获取记录
    ]);
?>