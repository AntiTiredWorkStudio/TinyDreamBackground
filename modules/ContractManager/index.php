<?php
header("Content-Type: text/html;charset=utf-8");
LIB($_GET['act']);
//模块co 请求示例：co=list、co=info
Responds($_GET['act'],(new ContractManager()),
    [
        'inf'=>R('info'),//模块信息
        'list'=>R('ContractList'),//获取合约类型表（信息）无参数
        'info'=>R('ContractInfo',['cid']),//通过id获取合约类型表（信息） 参数[cid:合约id]
        'set'=>R('SetContract',['cid',"#title","#price","#durnation","#refund","#backrule","#attrule","#description"]),//设置合约信息,参数[cid:合约id],需设修改的置属性及属性值以&key=value追加至请求后部即可,无法设置cid,不追加任何设置参数将会返回属性列表
        'apa'=>R('AddPublicAccount',["title","icon","qrcode","description","type"]),//增加公众号信息,参数[title:公众号名字,icon:公众号图标,qrcode:公众号二维码内容链接,description:公众号简介,type:公众号类型id]
    ]);
?>