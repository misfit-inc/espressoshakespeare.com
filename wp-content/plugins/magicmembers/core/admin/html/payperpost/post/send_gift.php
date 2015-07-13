<!--send_gift-->
<form name="frmpostgift" id="frmpostgift" method="POST" action="admin.php?page=mgm/admin/payperpost&method=post_send_gift">
	<table width="100%" cellpadding="1" cellspacing="0" border="0" class="widefat form-table">		
		<tr>
			<td valign="top" width="20%"><?php _e('Pick a User', 'mgm') ?>:</td>
			<td valign="top">
				<select name="user_id" style="width:70%">
					<?php echo mgm_make_combo_options($data['users'], 1, MGM_KEY_VALUE);?>
				</select>
			</td>
		</tr>
		<tr>	
			<td valign="top">
				<?php _e('Select a post/page', 'mgm') ?>:
			</td>
			<td valign="top">
				<select name="post_id" style="width:70%">
					<?php echo mgm_make_combo_options($data['posts'], 1, MGM_KEY_VALUE);?>
				</select>
			</td>
		</tr>	
		<tr>	
			<td valign="top"></td>
			<td valign="top">
				<input type="checkbox" name="is_expire" value="N" />&nbsp;<?php _e('Override PPP expiration date', 'mgm') ?>
			</td>
		</tr>	
		<tr>
			<td valign="top" colspan="2">
				<input class="button" type="submit" name="submit" value="<?php _e('Send Gift', 'mgm') ?>" <?php echo (count($data['posts'])==0 ? 'disabled="disabled"':"")?>/>
			</td>
		</tr>
	</table><br />
	<input type="hidden" name="send_gift" value="true" />
</form>
<script language="javascript">
	<!--	
	// onready
	jQuery(document).ready(function(){   
		// add : form validation
		jQuery("#frmpostgift").validate({
			submitHandler: function(form) {					    					
				jQuery("#frmpostgift").ajaxSubmit({type: "POST",
				  url: 'admin.php?page=mgm/admin/payperpost&method=post_send_gift',
				  dataType: 'json',		
				  iframe: false,									 
				  beforeSubmit: function(){	
				  	// show message
					mgm_show_message('#post_send_gift', {status:'running', message:'<?php _e('Processing','mgm')?>...'}, true);						
				  },
				  success: function(data){	
					// message																				
					mgm_show_message('#post_send_gift', data);	
					// reload
					mgm_post_purchase_gifts();																																	
				  }}); // end   		
				return false;											
			},
			rules: {			
				user_id: "required",										
				post_id: "required"	
			},
			messages: {			
				user_id: "<?php _e('Please select user','mgm')?>",
				post_id: "<?php _e('Please select post','mgm')?>"
			},
			errorClass: 'invalid',
			errorPlacement:function(error, element) {				
				error.insertAfter(element);					
			}
		});	
	});	
	//-->	
</script>