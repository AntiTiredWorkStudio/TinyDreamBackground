<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-12-20
 * Time: 上午 12:28
 */

?>
<h1>近20天互助、支付记录</h1>
<?php
    //$verifyArray = $pageData['verify'];
    //$btnStyle = $pageData['btnStyle'];
	foreach($pageData['recs'] as $key=>$value){
		
?>
<h4>
<?php 
	echo '20'.$value['date'].': 参与互助 ['.$value['join'].']人次，支付 ['.$value['paid'].']人次<button type="button">查看人数</button></br>';
?>
</h4>

<?php }?>
<div style="padding-bottom:100px"></div>
<script>
    if(document.OnPartLoad){
        document.OnPartLoad(<?php echo json_encode($pageData);?>);
    }
</script>