<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-12-20
 * Time: 上午 12:28
 */
?>


<!-- 发布梦想池 class="main-content"-->
			<div  id='ReleaseDreamPool'>
				<!-- 输入框开始 -->
                <div class="col-md-12">
                    <div class="panel">
                        <div class="panel-heading">
                            <h3 class="panel-title">发布梦想池</h3>
                        </div>
                        <div class="panel-body">

                            <div class="row">
                                <div class="col-lg-7">
                                    <div class="input-group">
                                        <span class="input-group-addon">时限(天)</span>
                                        <input id="input_day" type="number" class="form-control" placeholder="">
                                    </div>
                                </div>
                            </div>
                            <div style="padding: 10px"></div>
                            <div class="row">
                                <div class="col-lg-7">
                                    <div class="input-group">
                                        <span class="input-group-addon">互助目标(元)</span>
                                        <input id="input_tbill" type="number" class="form-control" placeholder="">
                                    </div>
                                </div>
                            </div>
                            <div style="padding: 10px"></div>
                            <div class="row">
                                <div class="col-lg-7">
                                    <div class="input-group">
                                        <span class="input-group-addon">价格(元/份)</span>
                                        <input id="input_ubill" type="number" class="form-control" placeholder="">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-7" style="margin: 15px auto;display: block;text-align: center;">
                                <button id="btn_post" type="button" class="btn btn-primary" >发布</button>
                            </div>
                        </div>
                    </div>
					 <div class="panel">
                        <div class="panel-heading">
                            <h3 class="panel-title">发布小生意</h3>
                        </div>
                        <div class="panel-body">

                            <div class="row">
                                <div class="col-lg-7">
                                    <div class="input-group">
                                        <span class="input-group-addon">小生意标题</span>
                                        <input id="tr_input_day" type="text" class="form-control" placeholder="">
                                    </div>
                                </div>
                            </div>
                            <div style="padding: 10px"></div>
                            <div class="row">
                                <div class="col-lg-7">
                                    <div class="input-group">
                                        <span class="input-group-addon">小生意页面id</span>
                                        <input id="tr_input_tbill" type="text" class="form-control" placeholder="">
                                    </div>
                                </div>
                            </div>
                            <div style="padding: 10px"></div>
                            <div class="row">
                                <div class="col-lg-7">
                                    <div class="input-group">
                                        <span class="input-group-addon">互助目标(元)</span>
                                        <input id="tr_input_tbill" type="number" class="form-control" placeholder="">
                                    </div>
                                </div>
                            </div>
                            <div style="padding: 10px"></div>
                            <div class="row">
                                <div class="col-lg-7">
                                    <div class="input-group">
                                        <span class="input-group-addon">价格(元/份)</span>
                                        <input id="tr_input_ubill" type="number" class="form-control" placeholder="">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-7" style="margin: 15px auto;display: block;text-align: center;">
                                <button id="tr_btn_post" type="button" class="btn btn-primary" >发布</button>
                            </div>
                        </div>
                    </div>
                </div>
				<!-- 输入框结束 -->
				<div class="col-md-12">
					<!-- BASIC TABLE -->
					<div class="panel">
						<div class="panel-heading">
							<h3 class="panel-title">梦想池互助列表</h3>
						</div>
						<div class="panel-body">
							<table class="table table-bordered ">
								<thead >
									<tr>
										<th>序号</th>
										<th>梦想池名称</th>
										<th>时限（天）</th>
										<th>互助目标（元）</th>
										<th>价格（元/份）</th>
                                        <th>发布时间</th>
										<!--th>操作</th-->
									</tr>
								</thead>
								<tbody>
                                <?php
                                    $pools = $pageData['pools'];
                                    $seek = 1;
                                    $pageSeek = ($pageData['psize']==0)?0:$pageData['seek']/($pageData['psize']);
                                    $lastSeek = ($pageSeek-1)*$pageData['psize'];
                                    $nextSeek = ($pageSeek+1)*$pageData['psize'];
                                    $allowLast = true;
                                    $allowNext = true;
                                    if($nextSeek>=$pageData['count']){
                                        $allowNext = false;
                                    }
                                    if($lastSeek<0){
                                        $allowLast = false;
                                    }


                                    foreach ($pools as $key=>$pools) {
                                        ?>
                                        <tr>
                                            <td><?php echo ($pageData['seek']+($seek++));?></td>
                                            <td><?php echo $pools['ptitle'];?></td>
                                            <td>
                                                <?php
                                                $duration = $pools['duration'];
                                                $day = ($duration - $duration%86400)/86400;
                                                echo $day;
                                                ?></td>
                                            <td><?php echo $pools['tbill']*0.01;?></td>
                                            <td><?php echo $pools['ubill']*0.01;?></td>
                                            <td><?php echo date("y-m-d H:i:s",$pools['ptime']);?></td>
                                            <!--td>
                                                <button id="edit" pid="<?php //echo $pools['pid'];?>" type="button" class="btn btn-primary btn-sm">
                                                    <span class="lnr lnr-pencil"></span>
                                                </button>
                                                <button id="delete" pid="<?php //echo $pools['pid'];?>" type="button" class="btn btn-danger btn-sm">
                                                    <span class="lnr lnr-trash"></span>
                                                </button>
                                            </td-->
                                        </tr>
                                        <?php
                                    }
                                ?>
								</tbody>

							</table>
							<div class="desc" style="float: left;margin: 25px 0;">
								<p>每页显示<?php echo $pageData['psize'];?>条记录，总计<?php echo $pageData['count'];?>条记录 当前第<?php echo $pageSeek+1;?>页</p>
							</div>
							<div class="pagination" style="float: right;display: block;">
                                <?php if($allowLast){ ?>
                                <li><a seek="<?php echo $lastSeek;?>" size="<?php echo $pageData['psize'];?>" href="#">&laquo;</a></li>
                                <?php }?>
                                <?php
                                $pcount = $pageData['pages'];
                                for($i=0;$i<$pcount;$i++) {
                                    if(abs($i - $pageSeek)>3){
                                        continue;
                                    }
                                    ?>
                                    <li>
                                        <a
                                            <?php if($i == $pageSeek){ ?>
                                                style="font-weight:bold;color:#d43f3a"
                                            <?php  }  ?>
                                                seek="<?php echo $i*$pageData['psize'];?>" size="<?php echo $pageData['psize'];?>" href="#"><?php echo ($i+1);?></a></li>
                                    <?php
                                }
                                ?>
                                <?php if($allowNext){ ?>
                                <li><a seek="<?php echo $nextSeek;?>" size="<?php echo $pageData['psize'];?>" href="#">&raquo;</a></li>
                                <?php }?>
							</div>
						</div>

					</div>
					<!-- END BASIC TABLE -->
				</div>
			</div>

<script>
    if(document.OnPartLoad) {
        document.OnPartLoad(<?php echo json_encode($pageData);?>);
    }
</script>