<?php mgm_box_top(sprintf("Posts within Pack %s", $data['postpack']->name));?>		
	<div id="postpack_post_list"></div>	
<?php mgm_box_bottom();?>	
	<p>&nbsp;</p>
<?php mgm_box_top('Post to Pack Association');?>
	<div id="postpack_post_add">
		<form name="frmpostpackpposts" id="frmpostpackpposts" method="POST" action="admin.php?page=mgm/admin/payperpost&method=postpack_posts">
			<table width="100%"  cellpadding="1" cellspacing="0" border="0" class="widefat form-table">
				<thead>
					<tr>
						<th colspan="3"><?php _e('Add new Posts to this Pack','mgm')?></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td valign="top">
							<?php _e('Post','mgm')?> : 
						</td>
						<td valign="top">	
							<select name="posts[]" style="width:80%">
								<?php echo mgm_make_combo_options(mgm_get_purchasable_posts($data['exclude_posts']), '', MGM_KEY_VALUE);?>
							</select>
							<div class="tips">
								<?php _e("This dropdown will populate with posts as you mark them as purchasable","mgm");?>
							</div>						
						</td>
						<td style="text-align: right;">
							<input class="button" type="button" onclick="mgm_postpack_post_add()" value="<?php _e('Save', 'mgm') ?> &raquo;" />
						</td>
					</tr>
				</tbody>
				<tfoot>
					<tr>
						<td valign="middle" colspan="3">					
							<div style="float: left;">	
								<input type="button" class="button" onclick="mgm_postpack_posts(false)" value="&laquo; <?php _e('Back to Post Packs', 'mgm') ?>" />
							</div>			
						</td>
					</tr>
				</tfoot>
			</table>		
			<input type="hidden" name="save_postpack_post" value="true"	 />
			<input type="hidden" name="pack_id" value="<?php echo $data['postpack']->id?>">
		</form>
	</div>	
<?php mgm_box_bottom();?>	
<script language="javascript">
	<!--	
	// onready
	jQuery(document).ready(function(){   	
		// load list
		mgm_postpack_post_list=function(id){
			jQuery('#postpack_post_list').load('admin.php?page=mgm/admin/payperpost&method=postpack_post_list', {pack_id:id}); 
		}	
		// delete post pack post
		mgm_postpack_post_delete=function(id){
			if (confirm("<?php _e('Are you sure you want to delete this postpack post association?', 'mgm')?>")) {
				jQuery.ajax({url:'admin.php?page=mgm/admin/payperpost&method=postpack_post_delete', 
							 type: 'POST', 
							 dataType: 'json', 
							 data :{id: id}, 
							 cache: false,
							 beforeSend: function(){
							 	// show message
								mgm_show_message('#postpack_post_list', {status:'running', message:'<?php _e('Processing','mgm')?>...'}, true);								
							 },
							 success:function(data){
								// show message
								mgm_show_message('#postpack_post_list', data);								
																				
								// success	
								if(data.status=='success'){																																
									// delete row
									jQuery('#ppp_row_'+id).remove();
									// update pack list
									mgm_postpack_list();											
								}	
							 }
				});
			} 
		}				
		// add post pack post
		mgm_postpack_post_add=function(){
			// form validation
			jQuery("#frmpostpackpposts").validate({
				submitHandler: function(form) {					    					
					jQuery("#frmpostpackpposts").ajaxSubmit({type: "POST",
					  url: 'admin.php?page=mgm/admin/payperpost&method=postpack_posts',
					  dataType: 'json',	
					  iframe: false,										 
					  beforeSubmit: function(){	
						// show message
						mgm_show_message('#postpack_post_add', {status:'running', message:'<?php _e('Processing','mgm')?>...'}, true);											
					  },
					  success: function(data){	
							// show message
							mgm_show_message('#postpack_post_add', data);													
							// success	
							if(data.status=='success'){							
								// load new list	
								mgm_postpack_post_list(<?php echo $data['postpack']->id?>);		
								// update pack list
								mgm_postpack_list();										
							}														
					  }}); // end   		
					return false;											
				},
				rules: {			
					'posts[]': "required"
				},
				messages: {			
					'posts[]': "<?php _e('Please select posts to associate.','mgm')?>"
				},
				errorClass: 'invalid',
				errorPlacement:function(error, element) {				
					error.insertAfter(element);					
				}
			});	
			// submit
			jQuery("#frmpostpackpposts").submit();
		}	
		
		// load list default
		mgm_postpack_post_list(<?php echo $data['postpack']->id?>);		
	});
	//-->
</script>		