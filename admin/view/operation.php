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
                            <h3 class="panel-title">查询行动</h3>
                        </div>
                        <div class="panel-body">
							<div class="row">
								<div class="col-lg-7" style="margin: 15px auto;display: block;text-align: center;">
									<span><button id="ALL" type="button" class="btn btn-primary" style="width: 10%;">所有</button></span>
									<span><button id="DOING" type="button" class="btn btn-primary" style="width: 10%;">进行中</button></span>
									<span><button id="SUCCESS" type="button" class="btn btn-primary" style="width: 10%;">完成</button></span>
									<span><button id="FAILED" type="button" class="btn btn-primary" style="width: 10%;">失败</button></span>
								</div>
							</div>
                        </div>
                    </div>
                </div>
				<!-- 输入框结束 -->
				<div class="col-md-12">
					<!-- BASIC TABLE -->
					<div class="panel">
						<div class="panel-heading">
							<h3 class="panel-title">行动列表:</h3>
						</div>
					
					<!-- 页面切换导航 -->
					<?php include_once("admin/view/table.php");?>
					<!-- 页面切换导航 -->
					</div>
					<!-- END BASIC TABLE -->
				</div>
			</div>
<script>
    if(document.OnPartLoad) {
        document.OnPartLoad(<?php echo json_encode($pageData);?>);
    }
</script>