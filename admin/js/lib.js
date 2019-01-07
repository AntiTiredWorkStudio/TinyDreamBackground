
var Options = {
    Url : "https://tinydream.antit.top"//http://localhost:8003 , https://tinydream.antit.top
};

var Page_Builder = function (module, action, paras, fResult,fFailed) {
    var postInfo = {};
    postInfo[module] = action;

    for(var k in paras){
        postInfo[k] = paras[k];
    }
    var ajaxObject ={
        url: Options.Url,
        type: "post",
        dataType: "html",
        data: postInfo,
        success: function (data) {
            fResult(data);
        },
        error: function (e) {
            if(e.status=='200'){
                fResult(e.responseText);
            }else {
                fFailed('-1', e);
            }
        }
    };
    $.ajax(ajaxObject);
}

//调用请求
var TD_Request = function(module,action,paras,fSuccess,fFailed) {

    var postInfo = {};
    postInfo[module] = action;

    for(var k in paras){
        postInfo[k] = paras[k];
    }


    var ajaxObject ={
        url: Options.Url,
        type: "post",
        dataType: "json",
        data: postInfo,
        success: function (data) {
            if(data["result"] == "true" || data['code']=="0"){
                fSuccess(data['code'],data);
            }else{
                fFailed(data['code'],data);
            }
        },
        error: function (e) {
            fFailed('-1',e);
        }
    };
    $.ajax(ajaxObject);
}

//检查验证码格式
var IsValidateCode = function(target){

    if(target == null || target==""){
        return "还未输入验证码";
    }

    if(!(/^\d{6}$/.test(target))){
        return "验证码格式有误，请重填";
    }

    return "";
}

//检查天数格式
var IsDays = function(target){

    if(target == null || target==""){
        return "还未输入天数";
    }

    if(!(/[1-99]\d|\d/.test(target))){
        return "天数格式有误，请重填";
    }

    return "";
}


//检查金额格式
var IsBill = function(target){

    if(target == null || target==""){
        return "还未输入金额";
    }

    if(!(/[1-10000000]\d|\d/.test(target))){
        return "金额格式有误，请重填";
    }

    return "";
}


//检查手机号格式
var IsCellPhoneNumber = function(target){
    if(target == null || target==""){
        return "还未输入手机号码";
    }

    if(!(/^1[34578]\d{9}$/.test(target))){
        return "手机号码有误，请重填";
    }

    return "";
}

var SaveStorage = function (key,value) {
    window.localStorage.setItem(key,value);
}

var RemoveStorage = function (key) {
    window.localStorage.removeItem(key);
}

var ExistStorage = function(key){
    return !(window.localStorage.getItem(key)==null);
}

var GetStorage = function(key){
    return window.localStorage.getItem(key);
}

var PRC_TIME = function () {
    return (new Date()).getTime();// + 8*3600000;
}

//php 时间戳转换为js时间戳
var PhpTimeToJsTime = function (time) {
    return (time - 8*3600)*1000;
}

//js 时间戳转换为php时间戳
var JSTimeToPHPTime = function (time) {
    return Math.floor(time/1000);
}