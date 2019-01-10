
var login= function(){
    $("#code_submit").click(onSubmit);
    $("#generate").click(onGenerate);
    if(HasLogin()){
        SwitchPage('main.html');
    }
}


var onSubmit = function (res) {
    console.log("on submit");

    var tele = $("#input_tele").val();
    var code = $("#input_code").val();

    var tResult = IsCellPhoneNumber(tele);
    if(tResult!=""){
        alert(tResult);
        return;
    }

    var vResult = IsValidateCode(code);
    if(vResult!=""){
        alert(vResult);
        return;
    }

    TD_Request('us','ologin',
        {
            tele:tele,
            code:code
        },
        function (code, data) {
            alert("登录成功");
            SwitchPage('main.html');
            //SaveStorage('login',data.access_token);
            //window.location.href = "main.html";
        },
        function (code, data) {
            alert(data.context);
        }
    );
}

var onGenerate = function (res) {
    TD_Request('us','blogin',
        {
            tele:$("#input_tele").val()
        },
        function(code,data){
            alert("发送成功")
        },
        function (code,data){;
            console.log(code,data);
            alert(data.context)
        }
    );
}
Page.OnSignalFailed = function () {
    window.location.href = "index.html";
}

login();
