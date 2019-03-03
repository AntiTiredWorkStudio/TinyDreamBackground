WebApp.InitUpload();

var main = function () {

    if(!HasLogin()){
        window.location.href= "index.html";
    }else {
        CheckAuthEnable(
            function (res) {
                if(res){
                    BuildNavigator();
                }else{
					Logout();
				}
            }
        );
    }
}

var Logout = function(){
    Options.Auth = null;
    SwitchPage('index.html');
}

var CheckAuthEnable = function(resfunc){
    TD_Request('auth','au',JSON.parse(Options.Auth),
        function (code, data) {
            resfunc(true);
        } ,
        function (code, data) {
            console.log(data);
            resfunc(false);
        }
    )
    //Options.Auth;
}


var BuildNavigator = function () {
    Page_Builder('admin','bnav',{},function (data) {
        $("#navigator").html(data);
    },function (code, data) {
        console.log(code, data);
    });
	
	Page_Builder('admin','pinfo',{uid:JSON.parse(Options.Auth).openid},function(data){
		$('#navbar-menu').html(data);
		$("#logout").click(Logout);
	},function(code,data){
		console.log(code,data);
	});
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
//创建页面控制器
document.OnPartLoad = function (data) {
	if(ModuleRegister.hasOwnProperty(data.id)){
		//console.log(ModuleRegister);
		ModuleRegister[data.id].init(data);
	}
}


var NavigatorModule = {
    init: function () {
//        $("#a_post").click(SwitchTab);
//        $("#a_verify").click(SwitchTab);
		var aList =  $('#navigator').find('a');
		//console.log(aList);
		aList.each(function(){
			console.log($(this));
			$(this).click(SwitchTab);
		});
		/*for(var key in aList){
			console.log(aList[key]);
		}*/
		//console.log($('a'));
    },
}

//梦想池管理模块
var PostModule = {
    init: function () {
        var module = this;
        $("[seek]").click(
            module.switchPage
        );
		$("#btn_post").click(
			module.postDreamPool
		);
		$("#edit").click(
		    module.editDreamPool
        );
        $("#delete").click(
            module.deleteDreamPool
        );
		this.postContent={
			input_Day:$("#input_day"),
			input_tBill:$("#input_tbill"),
			input_uBill:$("#input_ubill")
		}
		console.log(this.postContent);
    },
    editDreamPool:function (res) {
        var pid = $(res.currentTarget).attr('pid');

        str = ($(this).children($("span")).attr("class")=="lnr lnr-chevron-down")?"lnr lnr-pencil":"lnr lnr-chevron-down";
        console.log(str);
        $(this).val(str);   // 按钮被点击后，在“编辑”和“确定”之间切换

        $(this).children($("span")).attr("class", str);

        $(this).parent().siblings("td").each(
            function() {  // 获取当前行的其他单元格
                obj_text = $(this).find("input:text");    // 判断单元格下是否有文本框
                if (!obj_text.length)   // 如果没有文本框，则添加文本框使之可以编辑
                    $(this).html("<input class='edit_frame' type='text' value='" + $(this).text() + "'>");
                else   // 如果已经存在文本框，则将其显示为文本框修改的值
                    $(this).html(obj_text.val());
            }
        );

    },
    deleteDreamPool:function (res) {
        var pid = $(res.currentTarget).attr('pid');
    },
	postContent:null,
	postDreamPool :function(){
        var module = PostModule;
		console.log(module.postContent.input_Day.val());
		if(IsDays(module.postContent.input_Day.val())!=""){
			alert("天数不符合要求:"+IsDays(module.postContent.input_Day.val()));
			return;
		}
		if(IsBill(module.postContent.input_tBill.val())!=""){
			alert("目标金额不符合要求");
			return;
		}
		
		if(IsBill(module.postContent.input_uBill.val())!=""){
			alert("单位金额不符合要求");
			return;
		}
//		'apbd'=>R('AddPoolByDay',["uid","tbill","ubill","day"]),
		TD_Request('dp','apbd',
        {
            uid:	"a01",
			tbill:	module.postContent.input_tBill.val()*100,
			ubill:	module.postContent.input_uBill.val()*100,
			day:	module.postContent.input_Day.val()
        },
        function(code,data){
			console.log(data);
            alert(data.context);
			LoadWorkSpace('a_post',{psize:5,seek:0});
        },
        function (code,data){;
            console.log(code,data);
            alert(data.context)
        }
    );
		
		
	},
    switchPage:function (page) {
        LoadWorkSpace('a_post',{psize:$(page.currentTarget).attr('size'),seek:$(page.currentTarget).attr('seek')});
    }
}

//认证审核模块
var VerfModule = {
    init: function (option) {
        console.log(option);
        this.verifyInfo = option;
        var module = this;
        $("#id_success").click(module.idVerify);
        $("#id_failed").click(module.idVerify);
        $("#dream_success").click(module.dreamVerify);
        $("#dream_failed").click(module.dreamVerify);
        $("#dream_payment").click(module.dreamPayment);
        $("#btn_type_submit").click(module.switchInfoType);
        $("#btn_type_unsubmit").click(module.switchInfoType);
        $("#btn_type_lose").click(module.switchInfoType);
    },
    verifyInfo:null,
	switchInfoType : function(res){
		console.log(res.currentTarget.id);
		switch(res.currentTarget.id){
			case "btn_type_submit":
				LoadWorkSpace('a_verify',{tab:'submit'});
				break;
			case "btn_type_unsubmit":
				LoadWorkSpace('a_verify',{tab:'unsubmit'});
				break;
			case "btn_type_lose":
				LoadWorkSpace('a_verify',{tab:'lose'});
				break;
			default:
				break;
		}
	},
    idVerify:function (res) {
        var tuid = $(res.currentTarget).attr('uid');
        var tstate = "";
        if(res.currentTarget.id == "id_success"){
            tstate = "SUCCESS";
        }else {
            tstate = "FAILED";
        }
        TD_Request('us','rnameax',{uid:tuid,state:tstate}
        ,function (code, data) {
                console.log(data);
                LoadWorkSpace('a_verify');
                alert(data.context);
            },
            function (code, data) {
                console.log(data);
                alert(data.context);
            }
        )
        console.log(res.currentTarget);
    },
    dreamVerify:function (res) {
        var tdid = $(res.currentTarget).attr('did');
        var result = "";
        if(res.currentTarget.id == "dream_success"){
            result = "SUCCESS";
        }else {
            result = "DOING";
        }

        TD_Request('dr','sdjson',{did:tdid,state:JSON.stringify({state:result})},
            function (code, data) {
                console.log(data);
                LoadWorkSpace('a_verify');
                alert(data.context);
            },
            function (code, data) {
                console.log(data);
                alert(data.context);
            }
        )
    },
    dreamPayment:function (res) {
        var tdid = $(res.currentTarget).attr('did');

        TD_Request('dr','sdjson',{did:tdid,state:JSON.stringify({payment:true})},
            function (code, data) {
                console.log(data);
                LoadWorkSpace('a_verify');
                alert(data.context);
            },
            function (code, data) {
                console.log(data);
                alert(data.context);
            }
        )
    }
}

var DataModule = {
	autoListID:[],
	init:function(option){
		console.log(option);
		var module = this;
		for(var key in option.recs){
			console.log(option.recs[key].date);
			$("#btn_"+option.recs[key].date).click(module.OnPersonCountView);
			module.autoListID.push("#btn_"+option.recs[key].date);
			
		}
		console.log(module.autoListID);
		this.AutoLoad();
	},
	AutoLoad:function(){
		var module = this;
		if(module.autoListID!=[]){
			var targetID = module.autoListID[0];
			if(!targetID){
				return;
			}
			
			var targetID = targetID.replace("#btn_","");
			console.log(targetID);
			module.GetPersonCount(targetID,function(id,result){
				if(result){
					module.autoListID.splice(0, 1);
					module.AutoLoad();
				}
			});
		}
	},
	GetPersonCount:function(id,result){
		TD_Request("ub","gad",
			{date:id},
			function(code,data){
				$("#day_visit_"+id).html(data.stat.visit);
				$("#day_join_"+id).html(data.stat.join);
				$("#day_paid_"+id).html(data.stat.paid);
				$("#day_btn_"+id).html("已加载");
				result(id,true);
			},
			function(code,data){
				console.log(data);
				result(id,false);
			}
		);
	},
	OnPersonCountView:function (res){
		console.log(res.currentTarget.id);
		var module = this;
		var targetID = res.currentTarget.id.replace("btn_","");
		DataModule.GetPersonCount(targetID,function(id,result){
			if(!result){
				alert("查看失败:"+data.context);
			}
		});
	}
}

var OrderModule = {
	seek:0,
	count:0,
	size:10,
	init:function(option){
		var module = this;
		console.log(option.ordCount,option.seek,option.count);
		console.log(option);
		this.seek = option.seek;
		this.count = option.count;
		$("#startDayTime").datetimepicker({
			minView: "month", //选择日期后，不会再跳转去选择时分秒 
			language:  'zh-CN',
			format: 'yyyy-mm-dd',
			todayBtn:  1,
			autoclose: 1,
		});
		$("#endDayTime").datetimepicker({
			minView: "month", //选择日期后，不会再跳转去选择时分秒 
			language:  'zh-CN',
			format: 'yyyy-mm-dd',
			todayBtn:  1,
			autoclose: 1,
		});
		$("#search").click(function(){
			/*console.log($("#input_tele").val());
			console.log($("#startDayTime").val());
			console.log($("#endDayTime").val());*/
			LoadWorkSpace('a_order',
			{
				tele:$("#input_tele").val(),
				startTime:$("#startDayTime").val(),
				endTime:$("#endDayTime").val()
			});
		});
		$("[seek]").click(
            module.switchPage
        );
	},
	switchPage:function(page){
		//console.log(page);
        LoadWorkSpace('a_order',
			{
				tele:$("#input_tele").val(),
				startTime:$("#startDayTime").val(),
				endTime:$("#endDayTime").val(),
				seek:$(page.currentTarget).attr('seek')
			}
		);
	}
}

var ActivityModule = {
	init:function(option){
		for(var key in option.act){
			$("#ok_"+option.act[key].pid).click(this.Upload);
		}
       // 
	},
	Upload:function(res){
		var tPid = $(res.currentTarget).attr('pid');
		
		console.log("Upload",$("#file_"+tPid)[0].files[0]);
		var targetFile = $("#file_"+tPid)[0].files[0];
		if(targetFile == null){
			alert("未选择文件");
			return;
		}
		TD_Request("aw","astart",{pid:tPid},
			function(code,data){
				var realImgUrl = data.token.domain+"/"+data.token.fileName;
				 WebApp.UploadWithSDK(data.token.uptoken, data.token.upurl,targetFile,data.token.fileName,
                    function(result)
                    {
						if(result.result){
							TD_Request("aw","aend",{pid:tPid,url:realImgUrl},
								function(code,data){
									alert("上传成功!");
									LoadWorkSpace('a_activity');
								},function(code,data){
									alert(data.context);
								}
							);
						}else{
							alert(JSON.stringify(result));
						}
					});
			},
			function(code,data){
				console.log(data);
				alert(data.context);
			}
		);
	}
}

var RefundModule = {
	init :function(option){
		var module = this;
		console.log(option);
		$("#search").click(function(){
			/*console.log($("#input_tele").val());
			console.log($("#startDayTime").val());
			console.log($("#endDayTime").val());*/
			if($("#input_pid").val() == ""){
				alert("请输入要查询的梦想互助期号");
				return;
			}
			LoadWorkSpace('a_refund',
			{
				pid:$("#input_pid").val()
			});
		});
	}
}

var RedPackageModule = {
	seek:0,
	count:0,
	size:10,
	pid:"",
	init:function(option){
		var module = this;
		//this.seek = option.seek;
		//this.count = option.count;
		if(option.hasOwnProperty("packs") && option.packs.hasOwnProperty("pid")){
			module.pid = option.packs.pid;
		}
		$("#search").click(function(){
			/*console.log($("#input_tele").val());
			console.log($("#startDayTime").val());
			console.log($("#endDayTime").val());*/
			//alert($("#input_pid").val());
			LoadWorkSpace('a_redpack',
			{
				pid:$("#input_pid").val(),
				seek:module.seek,
				count:module.size,
				type:"listview"
			});
		});
		$("[seek]").click(
            module.switchPage
        );
	},
	switchPage:function(page){
		//console.log(page.currentTarget.attributes[0].value,page.currentTarget.attributes[1].value);
		//return;
		//var module = this;
			LoadWorkSpace('a_redpack',
			{
				pid:RedPackageModule.pid,
				seek:page.currentTarget.attributes[0].value,
				count:page.currentTarget.attributes[1].value,
				type:"listview",
			});
	}
}

var ModuleRegister = {
	"nav":NavigatorModule,
	"post":PostModule,
	"verf":VerfModule,
	"data":DataModule,
	"ord":OrderModule,
	"act":ActivityModule,
	"refund":RefundModule,
	"redpack":RedPackageModule,
}

Page.OnSignalFailed = function () {
    window.location.href = "index.html";
}


main();