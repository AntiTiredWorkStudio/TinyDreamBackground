var main = function () {
    if(!HasLogin()){
        window.location.href= "index.html";
    }else {

    }
    BuildNavigator();
}

var HasLogin = function () {
    if(!ExistStorage("login")){
        return false;
    }

    var accessToken = GetStorage("login");
    //校验AccessToken  (自己的身份信息)
    // sha1(tele+"-"+dayTime) == accessToken
    return true;
}


var BuildNavigator = function () {
    Page_Builder('admin','bnav',{},function (data) {
        $("#navigator").html(data);
    },function (code, data) {
        console.log(code, data);
    })
}

var SwitchTab = function (res) {
    //console.log(res.currentTarget.id);
    LoadWorkSpace(res.currentTarget.id,{});
}

var LoadWorkSpace = function (id,pars) {
    Page_Builder('admin',id,pars,
        function (data) {
            // console.log(data);
            $("#workspace").html(data);
        },function (code, data) {
            console.log(code, data);
        })
}

document.OnPartLoad = function (data) {
    console.log(data.id);
    switch(data.id){
        case "nav":
            NavigatorModule.init();
            break;
        case "post":
            PostModule.init();
            break;
        case "verf":
            VerfModule.init();
            break;
        default:
            break;
    }
}


var NavigatorModule = {
    init: function () {
        $("#a_post").click(SwitchTab);
        $("#a_verify").click(SwitchTab);
    },
}

var PostModule = {
    init: function () {
        var module = this;
        $("[seek]").click(
            module.switchPage
        );
    },
    switchPage:function (page) {
        LoadWorkSpace('a_post',{psize:$(page.currentTarget).attr('size'),seek:$(page.currentTarget).attr('seek')});
    }
}

var VerfModule = {
    init: function () {

    },
}


main();