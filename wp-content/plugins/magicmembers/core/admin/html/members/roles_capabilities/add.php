<table width="100%" cellpadding="1" cellspacing="0" border="0" class="form-table widefat">	
	<thead>
		<tr>
			<th><b><?php _e('Create New Role','mgm')?></b></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>				
				<div class='mgm'>
					<form name="frmroleadd" id="frmroleadd" method="POST" action="admin.php?page=mgm/admin/members&method=roles_capabilities_add" style="margin: 0px; pading: 0px;">
						<table width="100%"  cellpadding="1" cellspacing="0" border="0" class="widefat form-table">							
							<tbody>
								<tr>				
									<td valign="top">
										<p><b><?php _e('Role','mgm')?>:</b>
										<input type="text" name="rolename" size="80" maxlength="100" value="<?php if(isset($data['rolename'])) echo $data['rolename']; ?>"/>
										</p>
									</td>
								</tr>								
								<tr>	
									<td valign="top">	
										<p><b><?php _e('Capabilities','mgm')?>:</b></p>
										<div class="capabilities" style="width:100%">
											<table width="100%" cellpadding="1" cellspacing="0" border="0" class="widefat form-table">
											<?php foreach ($data['capabilities'] as $i => $cap): $mod = ($i%3); if($mod == 0) echo "<tr>";?>
												<td <?php if(!isset($data['capabilities'][$i+1]) && $mod != 2 ) { echo 'colspan="'. (3 - $mod).'" '; } ?>  width="30%"><input value="<?php echo $cap['capability']; ?>" <?php if(isset($data['posted_capabilities']) && in_array($cap['capability'], $data['posted_capabilities'])) echo " checked='checked '"; ?> type="checkbox" name="chk_capability[]" id="chk_cap_<?php echo $cap['capability']; ?>"> <span><?php echo $cap['name']; ?></span></td>
											<?php if($mod == 2) echo "</tr>";endforeach ?>
											</table>								
										</div><br />
										<label id="labelchk" style="display:none;" for="chk_capability[]"></label>
									</td>
								</tr>																
							</tbody>	
							<tfoot>
								<tr>
									<td valign="middle">					
										<div style="float: left;">			
											<input class="button" type="submit" name="add_roles" value="<?php _e('Add', 'mgm') ?> &raquo;" />		
										</div>	
										<div style="float: right;">			
											<input class="button" type="button" id="cancel_roles" name="cancel_roles" value="<?php _e('Cancel', 'mgm') ?> &raquo;" />		
										</div>	
									</td>							
								</tr>
							</tfoot>	
						</table>
					</form>
				</div>
			</td>
		</tr>
	</tbody>		
</table>
<script language="javascript">
	<!--	
	// onready	
	jQuery(document).ready(function(){  
		jQuery('#cancel_roles').bind('click', load_roles_capabilities_add);		 
		// edit : form validation		
		jQuery('#frmroleadd').validate({														
			submitHandler: function(form) {				
				jQuery(form).ajaxSubmit({type: "POST",
				  url: 'admin.php?page=mgm/admin/members&method=roles_capabilities_add',
				  dataType: 'json',		
				  iframe: false,									 
				  beforeSubmit: function(){	
					// clear
					clear_message_divs();
					// show message
					mgm_show_message('#roles_capabilities_add_message', {status:'running', message:'<?php _e('Processing','mgm')?>...'}, true);					
				  },
				  success: function(data){																			
					// success	
					if(data.status=='success'){																										
						// reset form
						load_roles_capabilities_add();	
						//reload role list
						load_roles_capabilities_mgm();			
					}
					// show message		
					mgm_show_message('#roles_capabilities_add_message', data);																
				  }}); // end   		
				return false;											
			},
			rules: {			
				rolename: "required",
				'chk_capability[]': {required: true, minlength: 1}					
			},
			messages: {			
				rolename: "<?php _e('Please enter Role','mgm')?>",
				'chk_capability[]': {required: '<?php _e('Please select a Capability.','mgm')?>'}							
			},
			errorClass: 'invalid',
			errorPlacement:function(error, element) {	
				if(element.attr('name') == 'rolename')
					error.insertAfter(element);					
				else {
					error.insertAfter(jQuery('#labelchk'));	
				}
			}
		}
	);			
	});	
	//-->	
</script>