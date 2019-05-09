<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019-5-6
 * Time: 上午 12:02
 */

include "public/conf.php";
include "public/adapter.php";
include "public/lib.php";
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
    "db=ini"=>"",
    "db=getf"=>"",
    "db=template"=>"",
    "aw=done"=>"",
    "aw=onums"=>"",
    "aw=anums"=>"",
    "aw=atnums"=>"",
    "aw=lfromp"=>"",
    "aw=gawap"=>"",
    "aw=gplu"=>"拉取中奖数据",
    "aw=cplu"=>"",
    "aw=calc"=>"",
    "aw=uplid"=>"",
    "aw=tsend"=>"",
    "aw=astart"=>"",
    "aw=aend"=>"",
    "aw=alive"=>"",
    "aw=gtai"=>"",
    "aw=gubp"=>"",
    "dr=dlist"=>"",
    "dr=pedit"=>"",
    "dr=dedit"=>"",
    "dr=sdream"=>"",
    "dr=uedit"=>"",
    "dr=gedit"=>"",
    "dr=gdream"=>"",
    "dr=sdjson"=>"",
    "dp=add"=>"",
    "dp=addi"=>"",
    "dp=apbd"=>"",
    "dp=list"=>"",
    "dp=listr"=>"",
    "dp=fua"=>"",
    "dp=fup"=>"",
    "dp=gdtl"=>"",
    "dp=gfmd"=>"",
    "dp=gid"=>"",
    "dp=cpool"=>"",
    "dp=pinfo"=>"",
    "dp=pidm"=>"",
    "us=enter"=>"",
    "us=enterpack"=>"",
    "us=selfinfo"=>"",
    "us=sibynn"=>"",
    "us=gakt"=>"",
    "us=gawt"=>"",
    "us=guif"=>"",
    "us=gjsc"=>"",
    "us=blogin"=>"",
    "us=ologin"=>"",
    "us=rnameg"=>"",
    "us=rnames"=>"",
    "us=rnamef"=>"",
    "us=rnamea"=>"",
    "us=verify"=>"",
    "us=rnamegx"=>"",
    "us=rnamesx"=>"",
    "us=rnamefx"=>"",
    "us=rnameax"=>"",
    "us=verifyx"=>"",
    "us=ver"=>"",
    "us=gubt"=>"",
    "va=pbind"=>"",
    "va=gcode"=>"",
    "va=bind"=>"",
    "ds=info"=>"",
    "ds=buy"=>"",
    "ds=orp"=>"",
    "ds=ord"=>"",
    "ds=pay"=>"",
    "ds=oinfo"=>"",
    "ds=precs"=>"",
    "ds=preco"=>"",
    "ds=plists"=>"",
    "ds=plistg"=>"",
    "ds=cup"=>"",
    "ds=sver"=>"",
    "ds=pdetial"=>"",
    "ds=wxpay"=>"",
    "ds=wxpayweb"=>"",
    "ds=plistr"=>"",
    "ds=plistf"=>"",
    "ds=plistj"=>"",
    "ds=pcount"=>"",
    "ds=oitc"=>"",
    "ds=oitd"=>"",
    "ds=refd"=>"",
    "ds=rinfo"=>"",
    "ds=transfer"=>"",
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
    "no=nc"=>"",
    "no=ng"=>"",
    "no=ngl"=>"",
    "no=nr"=>"",
    "no=ta"=>"",
    "cs=sp"=>"",
    "cs=paid"=>"",
    "cs=ud"=>"",
    "view=build"=>"",
    "view=builds"=>"",
    "view=build_dt"=>"",
    "view=tlist"=>"",
    "rp=crp"=>"",
    "rp=cprs"=>"",
    "rp=grp"=>"",
    "rp=gurps"=>"",
    "rp=gurpr"=>"",
    "rp=grpr"=>"",
    "rp=orp"=>"",
    "rp=refund"=>"",
    "rp=ginfo"=>"",
    "rp=drefund"=>"",
    "paid=of"=>"",
    "tr=adt"=>"",
    "tr=gtp"=>"",
    "tr=gtt"=>"",
    "tr=gtpp"=>"",
    "tr=ttp"=>"",
    "co=list"=>"",
    "co=info"=>"",
    "co=set"=>"",
    "op=joi"=>"",
    "op=jof"=>"",
    "op=mat"=>"",
    "op=cal"=>"",
    "op=pat"=>"",
    "op=rep"=>"",
    "op=oif"=>"",
    "op=olist"=>"",
    "op=uinfo"=>"",
    "op=ihics"=>"",
    "op=oshar"=>"",
    "op=eomp"=>"",
    "op=cls"=>"",
    "op=gudo"=>"",
    "op=reset"=>"",
    "op=gopdata"=>"",
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
	"#count"=>"获取数目"
];
function Comment($type,$key){
    if(isset($GLOBALS[$type][$key]) && $GLOBALS[$type][$key]!=""){
        return $GLOBALS[$type][$key];
    }
    return $key;
}


foreach ($GLOBALS['modules'] as $key=>$value){
    echo $value['rq'].'</br>';
    $_GET['act'] = $key;
    include_once ($value['rq']);
}
$index = 0;
$commitList = [];

foreach ($GLOBALS['ACCESS_LIST'] as $key=>$value){
    $comment_key = $key;
    if(!in_array($comment_key,$commitList)){
        $commitList['modules'][$comment_key] = "";
    }
    echo "<h5>".Comment("commentModules",$comment_key)."模块"."</h5>";
    foreach ($value as $k=>$v){
        if($k=="inf"){
            continue;
        }
        $comment_key = $key."=".$k;
        if(!in_array($comment_key,$commitList)){
            $commitList['rules'][$comment_key] = "";
        }
        $index++;
        echo "&nbsp;&nbsp;".$index.'.'.$comment_key."请求:".Comment("commentRules",$comment_key)."</br>";
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
				if(StartWith($comment_key,'#')){
					$argType = "(可选参数)";
				}
                echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(" . $seek . ')' . $comment_key.$argType. ":".Comment("commentPars",$comment_key)."</br>";
            }
        }
    }
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