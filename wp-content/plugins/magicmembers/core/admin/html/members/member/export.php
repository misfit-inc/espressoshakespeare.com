<?php mgm_box_top('Export User Data');?>
	<form name="mgmexportfrm" id="mgmexportfrm" method="POST" action="admin.php?page=mgm/admin/members&method=member_export">
		<table width="100%" cellpadding="1" cellspacing="0" class="form-table widefat">
			<tr>
				<td valign="top" width="20%"><b><?php _e('Membership Type:','mgm') ?></b></td>
				<td valign="top">
					<select name="bk_membership_type" onchange="this.form.bk_inactive.checked=(this.value!='all');" style="width:200px">
						<option value="all"><?php _e('All','mgm') ?></option>
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
				<td valign="top"><b><?php _e('Membership Expires:','mgm') ?></b></td>
				<td valign="top">
					<input type="text" name="bk_msexp_dur_unit" size="3" maxlength="3" />
					<select name="bk_msexp_dur">
						<option value="day"><?php _e('Days','mgm') ?></option>
						<option value="week"><?php _e('Weeks','mgm') ?></option>
						<option value="month"><?php _e('Months','mgm') ?></option>
					</select>
				</td>
			</tr>			
			<tr>
				<td valign="top"><b><?php _e('Date Range:','mgm') ?></b></td>
				<td valign="top">
					<?php _e('Start', 'mgm')?>: <input type="text" name="bk_date_start" size="10"/> <?php _e('End', 'mgm')?> <input type="text" name="bk_date_end" size="10"/>
				</td>
			</tr>
			<tr>
				<td valign="top"></td>
				<td valign="top">
					<input type="checkbox" class="checkbox" name="bk_inactive" value="1" /> <?php _e('Exclude Expired Users','mgm') ?>
				</td>
			</tr>
		</table>
		<div>		
			<p class="submit">
				<input class="button" type="submit" name="export_member_info" value="<?php _e('Export &raquo;','mgm') ?>" />
			</p>
		</div>	
	</form>
	<iframe id="ifrm_backup" src="#" allowtransparency="true" width="0" height="0" frameborder="0"></iframe>
<?php mgm_box_bottom();?>
<script type="text/javascript" language="javascript">		
	<!--
	jQuery(document).ready(function(){		
		// dates		
		mgm_date_picker("#mgmexportfrm :input[name='bk_date_start']",'<?php echo MGM_ASSETS_URL?>');
		mgm_date_picker("#mgmexportfrm :input[name='bk_date_end']",'<?php echo MGM_ASSETS_URL?>');
		
		// submit
		jQuery("#mgmexportfrm").validate({
			submitHandler: function(form) {   
				jQuery("#mgmexportfrm").ajaxSubmit({type: "POST",
				  url: 'admin.php?page=mgm/admin/members&method=member_export',
				  dataType: 'json',			
				  iframe: false,								 
				  beforeSubmit: function(){	
					// show message
					mgm_show_message('#members', {status:'running', message:'<?php _e('Processing','mgm')?>...'});					
					// focus
					jQuery.scrollTo('#mgmexportfrm',400);
				  },
				  success: function(data){	
					// message																				
					mgm_show_message('#members', data);					
					// set backup
					jQuery('#ifrm_backup').attr('src', data.src);																							
				  }});// end 			  
			  return false;
			}
		});	
		//issue #: 219
		/*if(typeof(update_lb_members) != 'undefined') {
			update_lb_members++;
			if(update_lb_members == 3) {
				mgm_attach_tips();
			}    
		}*/
	});
	//-->
</script>