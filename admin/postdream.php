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
				<div class="row">
					<div class="col-md-2">
						<div class="input-group">
							<span class="input-group-addon">时限(天)</span>
							<input id="input_day" type="number" class="form-control" placeholder="">
						</div>
					</div>
					<div class="col-md-3">
						<div class="input-group">
							<span class="input-group-addon">互助目标(元)</span>
							<input id="input_tbill" type="number" class="form-control" placeholder="">
						</div>
					</div>
					<div class="col-md-3">
						<div class="input-group">
							<span class="input-group-addon">价格(元/份)</span>
							<input id="input_ubill" type="number" class="form-control" placeholder="">
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12" style="margin: 15px auto;display: block;text-align: center;">
						<button id="btn_post" type="button" class="btn btn-primary" style="width: 10%;">发布梦想池</button>
					</div>
				</div>
				<!-- 输入框结束 -->
				<div class="col-md-12">
					<!-- BASIC TABLE -->
					<div class="panel">
						<div class="panel-heading">
							<h3 class="panel-title">互助梦想池人员列表</h3>
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
										<th>操作</th>
									</tr>
								</thead>
								<tbody>
                                <?php
                                    $pools = $pageData['pools'];
                                    $seek = 1;
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
                                            <td>
                                                <button id="edit" pid="<?php echo $pools['pid'];?>" type="button" class="btn btn-primary btn-sm">
                                                    <span class="lnr lnr-pencil"></span>
                                                </button>
                                                <button id="delete" pid="<?php echo $pools['pid'];?>" type="button" class="btn btn-danger btn-sm">
                                                    <span class="lnr lnr-trash"></span>
                                                </button>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                ?>
								</tbody>

							</table>
							<div class="desc" style="float: left;margin: 25px 0;">
								<p>每页显示<?php echo $pageData['psize'];?>条记录，总计<?php echo $pageData['count'];?>条记录</p>
							</div>
							<div class="pagination" style="float: right;display: block;">
                                <li><a href="#">&laquo;</a></li>
                                <?php
                                $pcount = $pageData['pages'];
                                for($i=0;$i<$pcount;$i++) {
                                    ?>
                                    <li><a seek="<?php echo $i*$pageData['psize'];?>" size="<?php echo $pageData['psize'];?>" href="#"><?php echo ($i+1);?></a></li>
                                    <?php
                                }
                                ?>
                                <li><a href="#">&raquo;</a></li>
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