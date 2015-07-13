<!--coupons-->
<div id="coupons">
	<?php mgm_box_top('Coupon List');?>		
		<div id="coupon_list"></div>	
	<?php mgm_box_bottom();?>	
		<p>&nbsp;</p>
	<?php mgm_box_top('Coupon Create/Edit');?>
		<div id="coupon_manage"></div>
	<?php mgm_box_bottom();?>	
</div>
<div id="coupon_users"></div>	
<script language="javascript">
	<!--	
	// onready
	jQuery(document).ready(function(){   
		// load list
		mgm_coupon_list=function(){
			jQuery('#coupon_list').load('admin.php?page=mgm/admin/members&method=coupon_list'); 
		}
		// load add
		mgm_coupon_add=function(){
			jQuery('#coupon_manage').load('admin.php?page=mgm/admin/members&method=coupon_add'); 
		}	
		// edit
		mgm_coupon_edit=function(id) {
			// load add
			jQuery('#coupon_manage').load('admin.php?page=mgm/admin/members&method=coupon_edit',{id:id}); 
		}
		// load users
		mgm_coupon_users=function(id) {
			// id
			if(id){
				// hide
				jQuery('#coupons').slideUp();
				// load			
				jQuery('#coupon_users').load('admin.php?page=mgm/admin/members&method=coupon_users',{id:id}); 
			}else{
				// clear
				jQuery('#coupon_users').html('');
				// show
				jQuery('#coupons').slideDown();
			}
		}
		// delete	
		mgm_coupon_delete=function(id) {
			if (confirm("<?php _e('Are you sure you want to delete this coupon?', 'mgm')?>")) {
				jQuery.ajax({url:'admin.php?page=mgm/admin/members&method=coupon_delete', type: 'POST', dataType: 'json', cache: false, data :{id: id}, 
				 beforeSend: function(){	
					// show message
					mgm_show_message('#coupon_list', {status:'running', message:'<?php _e('Processing','mgm')?>...'},true);									
				 },
				 success:function(data){
					// show message
					mgm_show_message('#coupon_list', data);																						
					// success	
					if(data.status=='success'){																																
						// delete row
						jQuery('#coupon_row_'+id).remove();											
					}
				 }
				});
			}
		}	
		
		// list 
		mgm_coupon_list();
		// add 	
		mgm_coupon_add();	
	});		
	//-->
</script>
