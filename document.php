<?php
include "public/conf.php";
include "public/adapter.php";
include "public/lib.php";

$targetModule = FREE_PARS('module',null);
$targetAction = FREE_PARS('action',null);
$debugModule = $targetAction != null && $targetModule !=null;
?>
<html>
<head>
    <title>
        <?php
            if($targetAction==null || $targetModule==null){
                echo "接口文档";
            }else {
                echo "测试$targetModule=$targetAction";
            }
        ?>
    </title>
</head>
<body>
<script src="admin/js/jquery-1.11.3.min.js"></script>
<script src="admin/js/lib.js"></script>
<script src="admin/js/login.js"></script>
<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019-5-6
 * Time: 上午 12:02
 */



$commentModules=[
    "db"=>"数据库模块",
    "aw"=>"开奖模块",
    "dr"=>"梦想模块",
    "dp"=>"梦想池模块",
    "us"=>"用户模块",
    "va"=>"短息模块",
    "ds"=>"梦想服务模块",
    "ts"=>"单元测试模块",
    "auth"=>"鉴权模块",
    "admin"=>"后台模块",
    "ub"=>"用户行为模块",
    "no"=>"通知模块",
    "cs"=>"demo模块",
    "view"=>"模板片段模块",
    "rp"=>"红包模块",
    "paid"=>"支付回调模块",
    "tr"=>"小生意模块",
    "co"=>"合约模块",
    "op"=>"行动模块",
];
$commentRules=[
    "db=ini"=>"初始化数据库",
    "db=getf"=>"获取数据库字段",
    "db=template"=>"查看数据库模板",
    "aw=done"=>"开奖",
    "aw=onums"=>"通过订单获取",
    "aw=anums"=>"自动开奖",
    "aw=atnums"=>"自动开奖测试",
    "aw=lfromp"=>"获取梦想池开奖号",
    "aw=gawap"=>"获取全部未开奖梦想池",
    "aw=gplu"=>"拉取中奖数据",
    "aw=cplu"=>"获取往期幸运者数",
    "aw=calc"=>"获取计算步骤",
    "aw=uplid"=>"更新中奖编号",
    "aw=tsend"=>"",
    "aw=astart"=>"准备上传活动照片",
    "aw=aend"=>"上传活动照片完毕",
    "aw=alive"=>"",
    "aw=gtai"=>"获取幸运者详情页的收益分成信息",
    "aw=gubp"=>"通过pid获取中奖用户信息",
    "dr=dlist"=>"查看梦想列表",
    "dr=pedit"=>"准备编辑梦想",
    "dr=dedit"=>"编辑梦想(可更改ACTION)",
    "dr=sdream"=>"选择梦想(可更改ACTION)",
    "dr=uedit"=>"修改梦想",
    "dr=gedit"=>"完善梦想",
    "dr=gdream"=>"获取单一梦想",
    "dr=sdjson"=>"通过JSON 修改梦想状态state:{'state':'SUCCESS','payment':0/1}",
    "dp=add"=>"【请求】增加梦想池",
    "dp=addi"=>"通过期号和持续天数增加梦想池",
    "dp=apbd"=>"通过天数增加梦想池",
    "dp=list"=>"列出全部梦想池",
    "dp=listr"=>"按范围列出梦想池",
    "dp=fua"=>"强制刷新全部梦想池",
    "dp=fup"=>"强制更新梦想互助",
    "dp=gdtl"=>"获取当天剩余时间",
    "dp=gfmd"=>"获取本月第1天",
    "dp=gid"=>"自动生成id",
    "dp=cpool"=>"返回梦想池数量",
    "dp=pinfo"=>"单独获得梦想池信息",
    "dp=pidm"=>"获取近一月梦想互助id列表",
    "us=enter"=>"进入应用",
    "us=enterpack"=>"通过红包进入页面",
    "us=selfinfo"=>"获取个人信息",
    "us=sibynn"=>"通过昵称获取个人信息",
    "us=gakt"=>"获取access_token【小程序入口,返回secret及openid】",
    "us=gawt"=>"获取AccessToken（公众号鉴权入口,获取openid）",
    "us=guif"=>"获取用户资料(公众号)",
    "us=gjsc"=>"获取JSAPI 配置参数",
    "us=blogin"=>"后台用户登录",
    "us=ologin"=>"后台登录，校验验证码【后台入口,返回secret及openid】",
    "us=rnameg"=>"旧版实名认证请求【废弃】",
    "us=rnames"=>"旧版实名认证请求【废弃】",
    "us=rnamef"=>"旧版实名认证请求【废弃】",
    "us=rnamea"=>"旧版实名认证请求【废弃】",
    "us=verify"=>"旧版实名认证请求【废弃】",
    "us=rnamegx"=>"获取单一用户的实名认证信息",
    "us=rnamesx"=>"实名认证准备",
    "us=rnamefx"=>"实名认证提交",
    "us=rnameax"=>"实名认证审核",
    "us=verifyx"=>"显示所有需要审核的信息",
    "us=ver"=>"获取要进入的版本",
    "us=gubt"=>"通过手机号查找用户信息",
    "va=pbind"=>"进入验证手机号页面调用",
    "va=gcode"=>"生成验证码",
    "va=bind"=>"绑定手机号",
    "ds=info"=>"",
    "ds=buy"=>"购买梦想份数,可更改ACTION)",
    "ds=orp"=>"准备下单",
    "ds=ord"=>"开始下单【修改Action】",
    "ds=pay"=>"支付完成【修改action】",
    "ds=oinfo"=>"获取玩家的全部订单",
    "ds=precs"=>"进入参与记录页面调用",
    "ds=preco"=>"通过范围获取订单",
    "ds=plists"=>"进入梦想池页面调用,获取梦想池总数",
    "ds=plistg"=>"用户获取全部梦想池信息及参与信息,可选参数type(RUNNING,FINISHED,JOIN)",
    "ds=cup"=>"获取玩家加入梦想池的数量",
    "ds=sver"=>"完善梦想后将中奖梦想提交审核",
    "ds=pdetial"=>"获取编号",
    "ds=wxpay"=>"统一下单",
    "ds=wxpayweb"=>"统一下单公众号",
    "ds=plistr"=>"用户获取进行中的梦想池",
    "ds=plistf"=>"用户获取结束的梦想池",
    "ds=plistj"=>"用户获取参与中的梦想池",
    "ds=pcount"=>"获取各类梦想池数量",
    "ds=oitc"=>"根据电话或日期获取订单数量",
    "ds=oitd"=>"根据电话或日期获取范围订单",
    "ds=refd"=>"退款",
    "ds=rinfo"=>"退款列表",
    "ds=transfer"=>"转账给用户",
    "ts=t"=>"",
    "ts=u"=>"",
    "ts=rb"=>"",
    "ts=cp"=>"",
    "ts=cu"=>"",
    "ts=fi"=>"",
    "ts=fo"=>"",
    "ts=fl"=>"",
    "ts=fa"=>"",
    "ts=rl"=>"",
    "ts=rd"=>"",
    "ts=twl"=>"",
    "ts=cvr"=>"",
    "ts=tn"=>"",
    "ts=time"=>"",
    "ts=udebug"=>"",
    "ts=ref"=>"",
    "ts=ts"=>"",
    "auth=au"=>"",
    "admin=pinfo"=>"",
    "admin=bnav"=>"",
    "admin=a_post"=>"",
    "admin=a_verify"=>"",
    "admin=a_data"=>"",
    "admin=a_order"=>"",
    "admin=a_activity"=>"",
    "admin=a_refund"=>"",
    "admin=a_redpack"=>"",
    "ub=gdid"=>"",
    "ub=paid"=>"",
    "ub=join"=>"",
    "ub=gc"=>"",
    "ub=gr"=>"",
    "ub=gad"=>"",
    "no=nc"=>"获取用户的未读消息数量",
    "no=ng"=>"获取用户未读消息",
    "no=ngl"=>"获取用户全部消息",
    "no=nr"=>"阅读消息",
    "no=ta"=>"",
    "cs=sp"=>"",
    "cs=paid"=>"",
    "cs=ud"=>"",
    "view=build"=>"",
    "view=builds"=>"",
    "view=build_dt"=>"",
    "view=tlist"=>"",
    "rp=crp"=>"发红包",
    "rp=cprs"=>"红包支付创建成功",
    "rp=grp"=>"获取红包信息",
    "rp=gurps"=>"获取用户红包列表（发出）",
    "rp=gurpr"=>"获取用户红包列表（收到）",
    "rp=grpr"=>"获取红包领取记录",
    "rp=orp"=>"领取红包",
    "rp=refund"=>"整理退款记录(以梦想互助为单位)",
    "rp=ginfo"=>"通过pid获取红包信息",
    "rp=drefund"=>"执行退款",
    "paid=of"=>"",
    "tr=adt"=>"增加小生意信息",
    "tr=gtp"=>"通过互助期号获取小生意信息",
    "tr=gtt"=>"通过小生意id获取小生意信息",
    "tr=gtpp"=>"获取收益分成百分比",
    "tr=ttp"=>"获取收益分成百分比",
    "co=list"=>"获取合约类型表（信息）无参数",
    "co=info"=>"通过id获取合约类型表（信息） 参数[cid:合约id]",
    "co=set"=>"设置合约信息,参数[cid:合约id],需设修改的置属性及属性值以&key=value追加至请求后部即可,无法设置cid,不追加任何设置参数将会返回属性列表",
    "op=joi"=>"参加合约，参数：[cid:合约cid,uid:用户openid]",
    "op=jof"=>"完成支付后成功参与合约，创建行动实例，参数：[cid:合约cid,oid:上个请求获得的订单号,uid:用户openid,theme:主题字符串,icode:页数(可选参数,分享页面的行动id(opid))]",
    "op=mat"=>"打卡，创建打卡记录，参数：[opid:行动id,uid:用户openid]",
    "op=cal"=>"获取用户当前行动日历，参数：[uid:用户openid,seek:页数(可选参数，根据月份填写，不传默认全部返回)]",
    "op=pat"=>"用户补卡，参数：[uid:用户openid,date:日期字符串(Y:m:d)]",
    "op=rep"=>"用户转发,参数：[opid:行动id,date:日期字符串(Y:m:d),uid:用户openid]",
    "op=oif"=>"获得行动概况(包含距离目标天数,连续打卡天数,已经打卡天数,缺卡天数,补卡天数,进度),参数：[opid:行动id]",
    "op=olist"=>"获得用户所有行动列表,参数：[uid:用户openid,seek:页数,count:每页个数]",
    "op=uinfo"=>"用户行动信息,参数：[uid:用户openid]",
    "op=ihics"=>"获得被用户邀请的全部邀请者的头像[uid:用户openid]",
    "op=oshar"=>"打开分享页面,参数：[opid:行动id]",
    "op=eomp"=>"进入行动派首页,参数：[uid:用户openid]",
    "op=cls"=>"清理行动及打卡数据（包括：行动信息、打卡记录、邀请信息、行动订单）,参数：[uid:用户openid](可不加，不加为清除全部)",
    "op=gudo"=>"获取用户正在参加的行动,参数:[uid:用户openid,secret:sha1('追梦行动派')]",
    "op=reset"=>"重置用户正在进行的行动数据及打卡记录,参数:[uid:用户openid]",
    "op=gopd"=>"获取用户行动数据",
    "op=gatd"=>"获取用户打卡数据",
    "op=gind"=>"获取用户邀请记录",
    "op=opt"=>"获取行动工具,参数[uid:用户openid,catalog(可选):可填写值为true/false(为true时不论是否有用户合约,都会返回无合约时工具页面内容,不填时默认为true),select(可选):筛选主题的类别(为typelist数组中的值,不填时默认为合约主题对应的类别),seek(可选):公众号列表起始下标(不填默认为0),count:公众号列表单位获取长度(不填默认为5)]"
];
$commentPars=[
    "tname"=>"数据表代号",
    "dnum"=>"开奖号码",
    "oid"=>"订单号",
    "pid"=>"梦想互助期号",
    "seek"=>"起始下标",
    "count"=>"获取数目",
    "lid"=>"梦想编号",
    "au"=>"",
    "al"=>"",
    "url"=>"",
    "uid"=>"用户openid",
    "title"=>"标题",
    "content"=>"内容",
    "did"=>"梦想id",
    "action"=>"动作json",
    "contentList"=>"",
    "state"=>"状态",
    "ptitle"=>"梦想互助标题",
    "tbill"=>"总价",
    "ubill"=>"单价",
    "duration"=>"持续时间",
    "index"=>"",
    "day"=>"",
    "nickname"=>"用户昵称",
    "headicon"=>"用户头像",
    "code"=>"微信鉴权code",
    "atoken"=>"微信accesstoken",
    "tele"=>"用户电话",
    "ccardnum"=>"身份证号码",
    "icardnum"=>"银行卡号码",
    "signal"=>"签名",
    "realname"=>"真实姓名",
    "bank"=>"银行",
    "openbank"=>"开户行",
    "bill"=>"金额",
    "pcount"=>"",
    "min"=>"",
    "max"=>"",
    "reid"=>"退款id",
    "refundbill"=>"退款金额",
    "reason"=>"退款理由",
    "c"=>"",
    "secret"=>"用户密钥",
    "timeStamp"=>"时间戳",
    "openid"=>"用户openid",
    "typeid"=>"行为类别",
    "date"=>"日期",
    "nid"=>"通知id",
    "server"=>"服务",
    "hid"=>"",
    "name"=>"模板名称",
    "data"=>"模板数据",
    "datas"=>"模板数据数组",
    "turl"=>"模板地址",
    "rcount"=>"红包个数",
    "rid"=>"红包id",
    "traid"=>"微信商户id",
    "profit"=>"利润",
    "tid"=>"生意id",
    "cid"=>"合约id",
    "theme"=>"行动主题字符串",
    "opid"=>"行动id",
	"#awardtype"=>"中奖类型字符,取值:TR/DR （小生意中奖/小梦想中奖）",
	"#dblink"=>"测试服务器标识,如果使用测试服务器,请加此参数并取值为test",
	"#state"=>"状态",
	"#pid"=>"梦想互助期号",
	"#seek"=>"起始下标",
	"#count"=>"获取数目",
    "#catalog"=>"是否获取目录",
    "#select"=>"筛选的主题类型,传入typelist中的值"
];


function Comment($type,$key){
    if(isset($GLOBALS[$type][$key]) && $GLOBALS[$type][$key]!=""){
        return $GLOBALS[$type][$key];
    }
    return $key;
}
$moduleSelect = FREE_PARS('m','all');
foreach ($GLOBALS['modules'] as $key=>$value){
    if($moduleSelect == 'all' || $key == $moduleSelect) {
        if (!$debugModule) {
            echo '<a href="#'.$key.'">'.$value['rq'] . '</a></br>';
        }
        $_GET['act'] = $key;
        include_once($value['rq']);
    }
}

if($debugModule){
    $parsList = $GLOBALS['ACCESS_LIST'][$targetModule][$targetAction]['pars'];
    //echo json_encode();
    echo "<span>接口测试</span></br><p id='result'></p></br>";
    if($parsList!=null)
    foreach ($parsList as $item) {
        echo '<span>参数<input type="text" name="'.$item.'" id="par_'.$item.'"></input></span>:'.$item.'</br>';
    }

    echo '<button id="submit">提交请求</button>';
    $requestLink = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'];
    $backLink = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].'/document.php?m='.$moduleSelect;
    echo "</br><a href='$backLink'>返回</a>";
    echo "<script>
var module = '$targetModule';
var action='$targetAction';
var requestUrl='$requestLink';
var debugData=".json_encode($GLOBALS['ACCESS_LIST'][$targetModule][$targetAction],JSON_UNESCAPED_UNICODE)."</script>";
    include "admin/view/snippets/document_debugger.html";
    return;
}

    $index = 0;
    $commitList = [];

    foreach ($GLOBALS['ACCESS_LIST'] as $key=>$value){
        $comment_key = $key;
        if(!in_array($comment_key,$commitList)){
            $commitList['modules'][$comment_key] = "";
        }
        $moduleLink = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].'/document.php?m='.$key;
        echo "<h5 id='$key'><span>".Comment("commentModules",$comment_key)."模块"."[<a href='".$moduleLink."'>仅查看</a>]</span></h5>";
        foreach ($value as $k=>$v){
            if($k=="inf"){
                continue;
            }
            $comment_key = $key."=".$k;
            if(!in_array($comment_key,$commitList)){
                $commitList['rules'][$comment_key] = "";
            }
            $index++;
            $testLink = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].'/document.php?m='.$moduleSelect.'&module='.$key.'&action='.$k;

            echo "&nbsp;&nbsp;".$index.'.<a href="'.$testLink.'">'.$comment_key."</a>请求:".Comment("commentRules",$comment_key)."</br>";
            $seek = 0;

            if(!empty($v['pars'])) {
                echo "&nbsp;&nbsp;&nbsp;&nbsp;【参数列表】</br>";
                foreach ($v['pars'] as $par) {
                    $seek++;
                    $comment_key = $par;
                    if(!in_array($comment_key,$commitList)){
                        $commitList['pars'][$comment_key] = "";
                    }
                    $argType = "";
                    if(PublicTools::StartWith($comment_key,'#')){
                        $argType = "(可选参数)";
                    }
                    echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(" . $seek . ')' . $comment_key.$argType. ":".Comment("commentPars",$comment_key)."</br>";
                }
            }
        }
    }
    if($moduleSelect != 'all') {
        $totalLink = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].'/document.php';
        echo "</br><a href='$totalLink'>显示全部</a>";
    }

/*echo '$commentModules=[</br>';
foreach ($commitList['modules'] as $key=>$value){
    echo '"'.$key.'"=>"",</br>';
}
echo "];</br>";
echo '$commentRules=[</br>';
foreach ($commitList['rules'] as $key=>$value){
    echo '"'.$key.'"=>"",</br>';
}
echo "];</br>";
echo '$commentPars=[</br>';
foreach ($commitList['pars'] as $key=>$value){
    echo '"'.$key.'"=>"",</br>';
}
echo "];</br>";*/

?>
</body>
</html>
