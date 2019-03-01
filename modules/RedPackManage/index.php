<?php
	header("Content-Type: text/html;charset=utf-8"); 
	LIB($_GET['act']);

	Responds($_GET['act'],(new RedPackManage()),
    [
        'inf'=>R('info'),//模块信息
        'crp'=>R("CreateRedPackae",["pid","rcount","content","bill","uid"]),//发红包
        'cprs'=>R("CreateRedPackSuccess",["uid","rid"]),//红包支付创建成功
        'grp'=>R("GetRedPack",["rid"]),//获取红包信息
        'gurps'=>R("GetUserRedPacksSend",["uid","seek","count"]),//获取用户红包列表（发出）
        'gurpr'=>R("GetUserRedPacksRecive",["uid","seek","count"]),//获取用户红包列表（收到）
        'grpr'=>R("GetRedPackrecive",["rid","seek","count"]),//获取红包领取记录
        'orp'=>R("OpenRedPack",["uid","rid"]),//获取用户红包列表（收到）
		'refund'=>R("CollectRefundInfo",['pid']),//整理退款记录(以梦想互助为单位)
    ]);
?>