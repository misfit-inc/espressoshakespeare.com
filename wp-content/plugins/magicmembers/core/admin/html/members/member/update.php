<?php mgm_box_top('Update User Accounts');?>			
	<p class="desc">
		<a href="javascript:void(0);" onclick="check_uncheck(true)" class="small"><?php _e('Select All','mgm') ?></a> &nbsp; 
		<a href="javascript:void(0);" onclick="check_uncheck(false)" class="small"><?php _e('Deselect All','mgm') ?></a>
	</p>			
	<table width="100%" cellpadding="1" cellspacing="0" class="form-table widefat">
		<tr>
			<td valign="top" width="20%">
				<input type="checkbox" class="checkbox" name="update_opt[]" value="status" /> <b><?php _e('Status','mgm') ?>:</b>
			</td>
			<td valign="top">	
				<select name="upd_status" id="upd_status" disabled="disabled" style="width:150px">
					<option value="-"><?php _e('Select','mgm') ?></option>
					<option value="<?php echo MGM_STATUS_NULL ?>"><?php echo esc_html(MGM_STATUS_NULL) ?></option>
					<option value="<?php echo MGM_STATUS_ACTIVE ?>"><?php echo esc_html(MGM_STATUS_ACTIVE) ?></option>
					<option value="<?php echo MGM_STATUS_EXPIRED ?>"><?php echo esc_html(MGM_STATUS_EXPIRED) ?></option>
					<option value="<?php echo MGM_STATUS_PENDING ?>"><?php echo esc_html(MGM_STATUS_PENDING) ?></option>
					<option value="<?php echo MGM_STATUS_ERROR ?>"><?php echo esc_html(MGM_STATUS_ERROR) ?></option>
				</select>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<input type="checkbox" class="checkbox" name="update_opt[]" value="membership_type" /> <b><?php _e('Membership Type','mgm') ?>:</b>
			</td>
			<td valign="top">
				<select name="upd_membership_type" id="upd_membership_type" disabled="disabled" style="width:200px">
					<option value="-"><?php _e('Select','mgm') ?></option>	
					<?php
					$mgm_membership_types = mgm_get_class('membership_types');
					$strTypes = '';
					
					foreach ($mgm_membership_types->membership_types as $type_code=>$type_name) {
						if ($type_code == 'guest') {
							continue;
						}
						
						$strTypes .= '<option value="'. $type_code .'">'. __(mgm_stripslashes_deep($type_name), 'mgm') .'</option>';
					}
					
					echo $strTypes;
					?>
				</select>	
			</td>
		</tr>	
		<tr>
			<td valign="top">
				<input type="checkbox" class="checkbox" name="update_opt[]" value="expire_date" /> <b><?php _e('Expiration Date','mgm') ?>:</b> 
			</td>
			<td valign="top">
				<input type="text" name="upd_expire_date" id="upd_expire_date" size="12" disabled="disabled"/>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<input type="checkbox" class="checkbox" name="update_opt[]" value="hide_old_content" /> <b><?php _e('Hide Private Content Prior to Join','mgm') ?>:</b>
			</td>
			<td valign="top">
				<select name="upd_hide_old_content" id="upd_hide_old_content" disabled="disabled" style="width:100px">
					<option value="1" ><?php echo __('Yes','mgm')?></option>
					<option value="0" selected="selected"><?php echo __('No','mgm')?></option>
				</select>	
			</td>
		</tr>		
		<tr>
			<td valign="top" width="20%">
				<input type="checkbox" class="checkbox" name="update_opt[]" value="pack_key" /> <b><?php _e('Subscription Pack','mgm') ?>:</b>
			</td>
			<td valign="top">	
				<select name="upd_pack_key" id="upd_pack_key" disabled="disabled" style="width:250px">
					<option value="-"><?php _e('Select','mgm') ?></option>
					<?php $packages = mgm_get_subscription_packages();
					foreach($packages as $pack):
						echo '<option value="'.$pack['key'].'">'.$pack['label'].'</option>';
					endforeach;?>
				</select>
				<input disabled="disabled" type="checkbox" class="checkbox" name="insert_new_level" id="insert_new_level" value="new" /> <b><?php _e('Apply as new subscription','mgm') ?></b>&nbsp;&nbsp;<input disabled="disabled" <?php if($data['enable_multiple_level_purchase']){ ?> type="checkbox" class="checkbox" <?php }else{ ?> type="hidden" <?php } ?> name="highlight_role" id="highlight_role" value="highlight" /> <b><?php if($data['enable_multiple_level_purchase']){ _e('Highlight this packs\'s role','mgm'); } ?></b>
			</td>
		</tr>	
	</table>						
	<div id="err_update_select"></div>		
	<p class="submit"><input type="submit" name="update_member_info" value="<?php _e('Update &raquo;','mgm') ?>" /> <input type="button" id="delete_member" name="delete_member" value="<?php _e('Delete &raquo;','mgm') ?>" /></p>
<?php mgm_box_bottom();?>
<script type="text/javascript" language="javascript">
	<!--
	// onready
	jQuery(document).ready(function(){		
		// check
		check_uncheck=function(check){							
			jQuery("#mgmmembersfrm :checkbox[name='ps[]']").each(function(){
				jQuery(this).attr('checked',check);
			});						
			return false;
		}
		// submit
		jQuery("#mgmmembersfrm").validate({
			submitHandler: function(form) {   
				jQuery("#mgmmembersfrm").ajaxSubmit({type: "POST",
				  url: 'admin.php?page=mgm/admin/members&method=member_update',
				  dataType: 'json',				
				  iframe: false,							 
				  beforeSubmit: function(){	
					// show message
					mgm_show_message('#members', {status:'running', message:'<?php _e('Processing','mgm')?>...'});					
					// focus
					jQuery.scrollTo('#mgmmembersfrm',400);
				  },
				  success: function(data){	
					// message																				
					mgm_show_message('#members', data);	
					// list update
					mgm_member_list();																			
				  }});// end 
				  return false;															
			},
			rules: {			
				'ps[]': {required:true, minlength: 1},
				'update_opt[]':	{required:true, minlength: 1}		
			},
			messages: {
				'ps[]': {required:"<?php _e('Please select one member to update ','mgm')?>",minlength:"<?php _e('Please select one member to update','mgm')?>"},
				'update_opt[]':	{required:"<?php _e('Please select one action to perform','mgm')?>",minlength:"<?php _e('Please select one action to perform','mgm')?>"}				
			},
			errorClass: 'invalid',
			errorPlacement:function(error, element) {				
				if(element.is("#mgmmembersfrm :checkbox[name='ps[]']") || element.is(":checkbox[name='update_opt[]']"))
					error.appendTo('#err_update_select');										
				else		
					error.insertAfter(element);					
			}
		});	
		// bind update
		jQuery("#mgmmembersfrm :checkbox[name='update_opt[]']").bind('click', function(){
			jQuery('#upd_'+jQuery(this).val()).attr('disabled', !jQuery(this).attr('checked'));
			jQuery('#insert_new_level').attr('disabled',!jQuery(this).attr('checked'));	
			jQuery('#highlight_role').attr('disabled',!jQuery(this).attr('checked'));	
			if(jQuery(this).val() == 'expire_date')
				mgm_date_picker("#mgmmembersfrm :input[name='upd_expire_date']",'<?php echo MGM_ASSETS_URL?>');			
		});
		// datepicker
		//mgm_date_picker("#mgmmembersfrm :input[name='upd_expire_date']",'<?php echo MGM_ASSETS_URL?>');
		
		//issue #: 219
		/*if(typeof(update_lb_members) != 'undefined') {
			update_lb_members++;
			if(update_lb_members == 3) {
				mgm_attach_tips();
			}    
		}*/
		
		var delete_user = function() { 
			if(!(jQuery("#mgmmembersfrm input[name='ps[]']:checked")).length) {
				jQuery('#err_update_select').html('<label class=\'invalid\'><?php _e('Please select one member to delete','mgm'); ?></label>');
				return ;
			}
			jQuery('#err_update_select').html('');
			if(confirm('<?php _e('Are you sure, selected user(s) will be permanently deleted from database and you will need to \n\rmanually delete from recurring subscription/autoresponder if exists?', 'mgm') ?>')) {
				var arr_post = jQuery("#mgmmembersfrm").serialize();
				jQuery.ajax({ 	url: 'admin.php?page=mgm/admin/members&method=member_delete', 
								dataType: 'json',
								type: 'POST',
								data: arr_post+'&submit_delete=1',												
								success: function(data) { 
									mgm_show_message('#members', data);	
									jQuery.scrollTo('#members',400);									
									if(data.status == 'success') {
										// load new list											
										mgm_member_list();
									}								
								},
								failure:  function() {
									mgm_show_message('#members', {status:'error', message:'<?php _e('Error while deleting user','mgm')?>'});	
								}
						});
				}
			}
			jQuery('#delete_member').bind('click', delete_user );
		
	});		
	//-->		
</script>	