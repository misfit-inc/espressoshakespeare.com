<!--posts-->
<?php mgm_box_top('Post Purchase Statistics');?>
	<div id="post_purchase_statistics"></div>
<?php mgm_box_bottom();?>
<?php mgm_box_top('Post Purchase/Gifts');?>
	<div id="post_purchase_gifts"></div>
<?php mgm_box_bottom();?>
<?php mgm_box_top('Gift a Post/Page');?>
	<div id="post_send_gift"></div>
<?php mgm_box_bottom();?>
<script language="javascript">
	<!--	
	// onready
	jQuery(document).ready(function(){   
		// load post_purchase_statistics
		mgm_post_purchase_statistics=function(){
			jQuery('#post_purchase_statistics').load('admin.php?page=mgm/admin/payperpost&method=post_purchase_statistics'); 
		}
		// load post_purchase_gifts
		mgm_post_purchase_gifts=function(){
			jQuery('#post_purchase_gifts').load('admin.php?page=mgm/admin/payperpost&method=post_purchase_gifts'); 
		}
		// load post_send_gift
		mgm_post_send_gift=function() {			
			jQuery('#post_send_gift').load('admin.php?page=mgm/admin/payperpost&method=post_send_gift'); 
		}		
		// mgm_post_purchase_statistics 
		mgm_post_purchase_statistics();
		// mgm_post_purchase_gifts 	
		mgm_post_purchase_gifts();	
		// post_send_gift
		mgm_post_send_gift();			
	});		
	//-->
</script>		