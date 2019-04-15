<?php
	header("Content-Type: text/html;charset=utf-8"); 
	LIB($_GET['act']);

	Responds($_GET['act'],(new DreamServersManager()),
    [
        'inf'=>R('info'),//模块信息
        'info'=>R('ListInfo'),
        'buy'=>R('PlaceOrderInADreamPoolStart',['uid','pid']),//购买梦想份数,可更改ACTION)
        'orp'=>R('PlaceOrderInADreamPoolPrepare',['pid']),//准备下单
        'ord'=>R('PlaceOrderInADreamPoolCreate',['action']),//开始下单【修改Action】
        'pay'=>R('PlaceOrderInADreamPoolPay',['uid','oid','bill','pcount','action']),//支付完成【修改action】
        'oinfo'=>R('GetAllOrdersUser',['uid']),//获取玩家的全部订单
        'precs'=>R('ShowOrdersInPoolStart',['pid']),//进入参与记录页面调用
        'preco'=>R('GetOrdersInPoolByRange',['pid','min','max']),//通过范围获取订单
        'plists'=>R('ShowPoolsInfoStart'),//进入梦想池页面调用,获取梦想池总数
        'plistg'=>R('GetPoolsInfoByRange',['uid','min','max']),//用户获取全部梦想池信息及参与信息,可选参数type(RUNNING,FINISHED,JOIN)
        'cup'=>R('CountUserJoinedPool',['uid']),//获取玩家加入梦想池的数量
        'sver'=>R('SubmitDreamToVerify',['uid','did']),//完善梦想后将中奖梦想提交审核
        'pdetial'=>R('ShowPoolDetails',['uid','pid']),//获取编号
        'wxpay'=>R('WxPay',['oid','bill','uid']),//统一下单
        'wxpayweb'=>R('WxPayWeb',['oid','bill','uid']),//统一下单公众号
        'plistr'=>R('GetRunningPoolInfoByRange',['seek','count']),//用户获取进行中的梦想池
        'plistf'=>R('GetFinishedPoolInfoByRange',['seek','count']),//用户获取结束的梦想池
        'plistj'=>R('GetJoinedPoolInfoByRange',['uid','seek','count']),//用户获取参与中的梦想池
        'pcount'=>R('GetPoolsCountInfo',['uid']),
		'oitc'=>R('GetOrderCountByTeleORDate'),//根据电话或日期获取订单数量
		'oitd'=>R('GetOrdersByTeleORDate',['seek','count']),//根据电话或日期获取范围订单
        'refd'=>R('WxRefund',['oid']),//退款
    ]);
?>