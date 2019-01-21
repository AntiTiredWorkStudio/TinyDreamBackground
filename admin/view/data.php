<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-12-20
 * Time: 上午 12:28
 */


    //$verifyArray = $pageData['verify'];
    //$btnStyle = $pageData['btnStyle'];
	echo json_encode($pageData);
?>
<script>
    if(document.OnPartLoad){
        document.OnPartLoad(<?php echo json_encode($pageData);?>);
    }
</script>