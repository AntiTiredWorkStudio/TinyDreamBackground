<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-12-20
 * Time: 上午 12:28
 */
?>



<!--中标用户审核-->
<div id='AuditOfSuccessfulBidder' class="tab-pane fade in active">
    <div class="row">

        <div class="col-md-3">
            <div class="input-group">
                <span class="input-group-addon">用户手机号</span>
                <input type="number" class="form-control" placeholder="">

            </div>
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-primary" >搜索</button>
            <!--<div class="input-group">
                <span class="input-group-addon">互助目标(元)</span>
                <input type="number" class="form-control" placeholder="">
            </div>-->
        </div>
        <div class="col-md-3">
            <div class="input-group">
                <span class="input-group-addon">梦想池名称</span>
                <input type="text" class="form-control" placeholder="">

            </div>
            <!--<div class="input-group">
                <span class="input-group-addon">价格(元/份)</span>
                <input type="number" class="form-control" placeholder="">
            </div>-->
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-primary" >搜索</button>
        </div>
        <div class="col-md-2">

        </div>
    </div>
    <div class="row">
        <div class="col-md-12" style="margin: 15px auto;display: block;text-align: center;">
            <button type="button" class="btn btn-primary" >未通过认证的用户</button>
            <button type="button" class="btn btn-primary" >选择未获得互助金的用户</button>
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
                    <th>实名认证</th>
                    <th>梦想审核</th>
                </tr>
                </thead>
                <tbody>
                <?php


                $verifyArray = $pageData['verify'];




                $seek = 0;
                foreach ($verifyArray as $key => $value) {
                    $look = "查看";
                    if(empty($value['identity'])){
                        $value['identity']['icardnum'] = "未提交";
                        $value['identity']['icardfurl'] = "#";
                        $value['identity']['icardburl'] = "#";
                        $value['identity']['ccardnum'] = "未提交";
                        $value['identity']['ccardfurl'] = "#";
                        $look = "未提交";
                    }
                    $index = 1+($seek++);
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
EOT;

                    print <<<EOT
                    <td >
                        <button type="button" class="btn btn-success" >通过</button>
                        <button type="button" class="btn btn-danger" >拒绝</button>
                    </td>                
EOT;
                    print <<<EOT
                    <td >
                        <button type="button" class="btn btn-success" >通过</button>
                        <button type="button" class="btn btn-danger" >拒绝</button>
                    </td>           

                    EOT;

                    print <<<EOT
                </tr>
                    EOT;
                }
                ?>

                </tbody>

            </table>
            <div class="desc" style="float: left;margin: 25px 0;">
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
            </div>
        </div>

    </div>

</div>

<script>
    if(document.OnPartLoad) {
        document.OnPartLoad(<?php echo json_encode($pageData);?>);
    }
</script>