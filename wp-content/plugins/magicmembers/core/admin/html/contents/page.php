<!--page page_access-->
<div id="content_page_access">
	<form name="frmpageaccss" id="frmpageaccss" action="admin.php?page=mgm/admin/contents&method=page" method="post">	
		<?php mgm_box_top('Page Exclude Settings');?>
			<table width="100%">				
				<tr>
					<td valign="top">
						<?php echo mgm_make_checkbox_group('excluded_pages[]', $data['posts'], $data['excluded_pages'], MGM_KEY_VALUE);?>
					</td>
				</tr>
			</table>			
		<?php mgm_box_bottom()?>
		<p class="submit">
			<input type="submit" name="update" value="<?php _e('Save','mgm') ?> &raquo;" />
		</p>
	</form>
</div>
<script language="javascript">
	<!--
	// onready
	jQuery(document).ready(function(){
		// add : form validation
		jQuery("#frmpageaccss").validate({
			submitHandler: function(form) {   
				jQuery("#frmpageaccss").ajaxSubmit({type: "POST",
				  url: 'admin.php?page=mgm/admin/contents&method=page',
				  dataType: 'json',			
				  iframe: false,								 
				  beforeSubmit: function(){	
				  	// show message
					mgm_show_message('#content_page_access', {status:'running', message:'<?php _e('Processing','mgm')?>...'});					
					// focus
					jQuery.scrollTo('#frmpageaccss',400);
				  },
				  success: function(data){	
			  		// message																				
					mgm_show_message('#content_page_access', data);																				
				  }});// end 
				  return false;															
			}
		});							  
	});	
	//-->
</script>