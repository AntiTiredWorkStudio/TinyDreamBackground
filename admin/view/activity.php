<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-12-20
 * Time: 上午 12:28
 */
	echo json_encode($pageData);
?>
<h1>近20天互助、支付记录</h1>
  <table class="table table-bordered "> 
   <thead> 
    <tr> 
     <th>日期</th> 
     <th>访问人次</th> 
     <th>参与人次</th> 
     <th>支付人次</th> 
     <th>访问人数</th> 
     <th>参与人数</th> 
     <th>支付人数</th> 
     <th>加载人数</th> 
    </tr> 
   </thead> 
   <tbody> 
    
<?php
    //$verifyArray = $pageData['verify'];
    //$btnStyle = $pageData['btnStyle'];
	foreach($pageData['recs'] as $key=>$value){
		
?>
 <tr> 
     <td><?php echo "20".$value['date'];?></td> 
     <td><?php echo $value['visit'];?></td> 
     <td><?php echo $value['join'];?></td> 
     <td><?php echo $value['paid'];?></td> 
     <td id="<?php echo "day_visit_".$value['date'];?>">未加载</td> 
     <td id="<?php echo "day_join_".$value['date'];?>">未加载</td> 
     <td id="<?php echo "day_paid_".$value['date'];?>">未加载</td> 
     <td id="<?php echo "day_btn_".$value['date'];?>"><button id="<?php echo 'btn_'.$value['date'];?>" type="button">查看人数</button></td> 
 </tr> 
<?php }?>
   </tbody> 
  </table>
<div style="padding-bottom:100px"></div>
<script>
    if(document.OnPartLoad){
        document.OnPartLoad(<?php echo json_encode($pageData);?>);
    }
</script>