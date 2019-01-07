<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-12-20
 * Time: 上午 12:28
 */


    $verifyArray = $pageData['verify'];
    $btnStyle = $pageData['btnStyle'];
?>



<!--中标用户审核-->
<div id='AuditOfSuccessfulBidder' class="tab-pane fade in active">
    <div class="row">
        <div class="col-md-2">
            <button id='btn_type_submit' type="button" class="<?php echo $btnStyle['submit'];?>" >待审核条目</button>
        </div>
        <div class="col-md-2">
            <button id='btn_type_unsubmit' type="button" class="<?php echo $btnStyle['unsubmit'];?>" >未提交审核条目</button>
        </div>
        <div class="col-md-2">
            <button id='btn_type_lose' type="button" class="<?php echo $btnStyle['lose'];?>" >失效条目</button>
        </div>
    </div>
    <div class="panel">
	
        <div class="panel-heading">
            <h3 class="panel-title">中标用户审核表</h3>
        </div>
        <div class="panel-body table-responsive">
            <table class="table table-bordered  text-nowrap" style=" text-align: center;"  >
                <thead >
                <tr>
                    <th class="th-index" style=" vertical-align: middle; width: 50px;">序号</th>
                    <th  style="text-align: center; vertical-align: middle; ">用户昵称</th>
                    <th  style="text-align: center; vertical-align: middle; margin: 0; padding: 0; width: 100px;">手机号码</th>
                    <th>梦想池名称</th>
                    <th>身份证号码</th>
                    <th>身份证正面</th>
                    <th>身份证反面</th>
                    <th>银行卡号</th>
                    <th>银行照片</th>
                    <th>梦想标题</th>
                    <th>梦想内容</th>
                    <th>互助公函</th>
                    <th>实名认证</th>
                    <th>梦想审核</th>
                    <th>打款</th>
                </tr>
                </thead>
                <tbody>
                <?php

                /*if(empty($verifyArray)){
                    return;
                }*/
				

                $seek = 0;
                foreach ($verifyArray as $key => $value) {
					
                    $look = "查看";
                    $hasSubmitVerify = !empty($value['identity']);
                    $hasVerifySuccess = $hasSubmitVerify ? ($value['identity']['state'] == "SUCCESS") : false;
					//echo $hasVerifySuccess.'</br>';
                    if (!$hasSubmitVerify ) {
                        $value['identity']['icardnum'] = "未提交";
                        $value['identity']['icardfurl'] = "#";
                        $value['identity']['icardburl'] = "#";
                        $value['identity']['ccardnum'] = "未提交";
                        $value['identity']['ccardfurl'] = "#";
                        $look = "未提交";
                    }
                    $index = 1 + ($seek++);
                    print <<<EOT
                <tr>
                    <td>{$index}</td>
                    <td>{$value['info']['nickname']}</td>
                    <td>{$value['info']['tele']}</td>
                    <td>梦想池{$value['award']['pid']}期</td>
                    <td>{$value['identity']['icardnum']}</td>
                    <td><a href="{$value['identity']['icardfurl']}">$look</a></td>
                    <td><a href="{$value['identity']['icardburl']}">$look</a></td>
                    <td>{$value['identity']['ccardnum']}</td>
                    <td><a href="{$value['identity']['ccardfurl']}">$look</a></td>
                    <td>{$value['dream']['title']}</td>
                    <td>{$value['dream']['content']}</td> 
                    <td><a href="{$value['dream']['videourl']}">$look</a></td>               
                           
EOT;
                    ?>

                    <?php if ($hasSubmitVerify && !$hasVerifySuccess) { ?>
                        <td>
                            <button id="id_success" uid="<?php echo $value['info']['uid'] ?>" type="button"
                                    class="btn btn-success">通过
                            </button>
                            <button id="id_failed" uid="<?php echo $value['info']['uid'] ?>" type="button"
                                    class="btn btn-danger">拒绝
                            </button>
                        </td>
                        <?php
                    } else if ($hasVerifySuccess) {
                        ?>
                        <td style="color:green">
                            实名认证通过
                        </td>
                        <?php
                    } else if (!$hasSubmitVerify) {
                        ?>
                        <td style="color:red">
                            用户未提交实名认证
                        </td>
                        <?php
                    }

                    if ($hasSubmitVerify && $hasVerifySuccess && ($value['dream']['state'] == "VERIFY")) {
                        ?>
                        <td>
                            <button id="dream_success" did="<?php echo $value['dream']['did'] ?>" type="button"
                                    class="btn btn-success">通过
                            </button>
                            <button id="dream_failed" did="<?php echo $value['dream']['did'] ?>" type="button"
                                    class="btn btn-danger">拒绝
                            </button>
                        </td>
                    <?php }else
                        if($hasSubmitVerify && $hasVerifySuccess && $value['dream']['state'] == "SUCCESS"){
                            ?>
                            <td style="color:green">
                                梦想审核通过
                            </td>
                            <?php
                    } else {
                        ?>
                        <td style="color:red">
                            用户未通过实名认证
                        </td>
                        <?php
                    }
                    if ($value['dream']['payment'] == 1) {
                        ?>
                        <td style="color:green">
                            已经打款
                        </td>
                        <?php
                    } else {
                        ?>
                        <td style="color:red">
                            <button id="dream_payment" did="<?php echo $value['dream']['did'] ?>" type="button"
                                    class="btn btn-success">标记打款
                            </button>
                        </td>
                        <?php
                    }
                }
                ?>
                </tr>
                </tbody>

            </table>
            <!--div class="desc" style="float: left;margin: 25px 0;">
                <p>每页显示10条记录，总计20条记录</p>
            </div>
            <div class="pagination" style="float: right;display: block;">
                <li><a href="#">&laquo;</a></li>
                <li><a href="#">1</a></li>
                <li><a href="#">2</a></li>
                <li><a href="#">3</a></li>
                <li><a href="#">4</a></li>
                <li><a href="#">5</a></li>
                <li><a href="#">&raquo;</a></li>
            </div-->
        </div>

    </div>

</div>

<script>
    if(document.OnPartLoad){
        document.OnPartLoad(<?php echo json_encode($pageData);?>);
    }
</script>