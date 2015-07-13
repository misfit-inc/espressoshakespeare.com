<!--subscription_options-->
<?php mgm_box_top('Subscription Packages/Options','subscriptionoptions')?>
	<form name="frmsubspkgs" id="frmsubspkgs" action="admin.php?page=mgm/admin/members&method=subscription_packages_update" method="post">
		<div id="subscription_packages_list">
			<!--pkgs list will be loaded here-->	
		</div>
	</form>	
<?php mgm_box_bottom()?>	

<?php mgm_box_top('Membership Types','magicmembershiptypes')?>
	<form name="frmmshiptypes" id="frmmshiptypes" action="admin.php?page=mgm/admin/members&method=membership_type_update" method="post">
		<table width="100%" cellpadding="1" cellspacing="0" border="0" class="form-table widefat">	
			<thead>
				<tr>
					<th scope="col" width="40%"><b><?php _e('Membership Type','mgm') ?></b></th>
					<th scope="col"><b><?php _e('Delete','mgm') ?></b></th>
				</tr>
			</thead>
			<tbody id="membership_types_list">
				<!--membership types list will be loaded here-->			
			</tbody>
		</table>	
		<br />
		<table width="100%" cellpadding="1" cellspacing="0" border="0" class="form-table widefat">
			<tr class="alternate" id="row-new">	
				<td valign="top" width="30%">
					<table>
					<tr>
					<td width="60%" valign="top">
					<input type="text" name="new_membership_type" size="80" maxlength="250"/>										
					</td>
					<td width="40%" valign="top">
					<?php _e('<b>New Membership Type</b>. (250 Characters max.)','mgm');?><br />					
					</td>
					</tr>
					<tr>
					<td width="60%" valign="top">								
					<input type="text" name="new_login_redirect_url" size="80" maxlength="1000"/>					
					</td>
					<td width="40%" valign="top">					
					<?php _e('<b>Login Redirect</b>','mgm');?>					
					</td>
					</tr>
					<tr>
					<td width="60%" valign="top">													
					<input type="text" name="new_logout_redirect_url" size="80" maxlength="1000"/>
					</td>
					<td width="40%" valign="top">										
					<?php _e('<b>Logout Redirect</b>','mgm');?>
					</td>
					</tr>
					</table>
				</td>
			</tr>			
			<tr>
				<td>
					<p>
						<div class="tips" style="width:95%">
							<?php _e('Please provide a new Membership Type and click on update. Please do not use any special characters in Membership Type name.','mgm'); ?>
						</div>
					</p>
				</td>
			</tr>
			<tr>
				<td align="left">
					 <input class="button" type="button" name="membership_type_update" value="<?php _e('Update &raquo;','mgm') ?>" onclick="update_membership_types()"/>
				</td>
			</tr>
		</table>
	</form>	
	<script language="javascript">
		<!--
		var arr_pack_role = new Array();
		jQuery(document).ready(function(){	
								
			// update membership types
			update_membership_types=function(){	
				var del_cnt = jQuery("#frmmshiptypes :checkbox[name='remove_membership_type[]']:checked").size();
				if(del_cnt>0){
					if(!confirm('<?php _e('Are you sure, membership types selected for deletion will also remove all the packs under it?')?>')){
						return;
					}
				}
				// proceed						
				jQuery("#frmmshiptypes").ajaxSubmit({type: "POST",
				  url: 'admin.php?page=mgm/admin/members&method=membership_type_update',
				  dataType: 'json',			
				  iframe: false,								 
				  beforeSubmit: function(){	
					// show message
					mgm_show_message('#frmmshiptypes', {status:'running', message:'<?php _e('Processing','mgm')?>...'},true);																			
				  },
				  success: function(data){	
						// message																				
						mgm_show_message('#frmmshiptypes', data);													
						// success	
						if(data.status=='success'){
							// clear fields
							jQuery("#frmmshiptypes :input[name='new_membership_type']").val('');		
							// clear fields
							jQuery("#frmmshiptypes :input[name='new_login_redirect_url']").val('');																				
							// clear fields
							jQuery("#frmmshiptypes :input[name='new_logout_redirect_url']").val('');																				
							// pkgs lists	
							load_subscription_packages_list();
							// types lists
							load_membership_types_list();												
						}														
				  }}); // end   				
			}
			
			// load_subscription_packages_list
			load_subscription_packages_list=function(){
				jQuery('#subscription_packages_list').load('admin.php?page=mgm/admin/members&method=subscription_packages_list', function(){
					// set up accordian
					jQuery("#subs_pkgs_panel").accordion({
						collapsible: true,
						autoHeight: true,
						fillSpace: false,
						clearStyle: true,
						active: false
					});					
				});	
			}
			
			// load_membership_types_list
			load_membership_types_list=function(){
				// load types list
				jQuery('#membership_types_list').load('admin.php?page=mgm/admin/members&method=membership_types_list', function(){
					// enable delete/move type
					jQuery("#membership_types_list :checkbox[name='remove_membership_type[]']").bind('click', function(){				
						//jQuery("select[name='move_membership_type_to["+jQuery(this).val().toString().keyslug()+"]']").attr('disabled', !jQuery(this).attr('checked'));
						jQuery("select[name='move_membership_type_to["+jQuery(this).val()+"]']").attr('disabled', !jQuery(this).attr('checked'));
					});	
					// enable/disable login redirect					
					jQuery("#membership_types_list :checkbox[name='update_login_redirect_url[]']").bind('click', function(){				
						//jQuery(":input[name='login_redirect_url["+jQuery(this).val().toString().keyslug()+"]']").attr('disabled', !jQuery(this).attr('checked'));
						jQuery(":input[name='login_redirect_url["+jQuery(this).val()+"]']").attr('disabled', !jQuery(this).attr('checked'));
					});
					// enable/disable logout redirect					
					jQuery("#membership_types_list :checkbox[name='update_logout_redirect_url[]']").bind('click', function(){										
						jQuery(":input[name='logout_redirect_url["+jQuery(this).val()+"]']").attr('disabled', !jQuery(this).attr('checked'));
					});										
				});	
			}	
			
			// mgm_toggle_mt_advanced
			mgm_toggle_mt_advanced= function(id){
				// img
				var img = jQuery('#'+id+'-trig').find("img");
				// show
				if(img.attr('src').indexOf('plus.png') != -1){
					// chnage image
					img.attr('src', img.attr('src').replace('plus.png','minus.png'));
					// show
					jQuery('#'+id).fadeIn('slow');
				}else if(img.attr('src').indexOf('minus.png') != -1){
					// change image
					img.attr('src', img.attr('src').replace('minus.png','plus.png'));
					// hide
					jQuery('#'+id).fadeOut('slow');
				}				
			}
			//check duration_type:
			check_duration = function(pack_ctr, type, duration, billing) {		
				if(type == 'l') {
					jQuery('input[name="packs['+pack_ctr+'][duration]"]').val('1');
					jQuery('input[name="packs['+pack_ctr+'][duration]"]').attr('readonly', true);	
					jQuery('select[name="packs['+pack_ctr+'][num_cycles]"]').val('1');											
				}else {
					jQuery('input[name="packs['+pack_ctr+'][duration]"]').val(duration);
					jQuery('input[name="packs['+pack_ctr+'][duration]"]').attr('readonly', false);					
					jQuery('select[name="packs['+pack_ctr+'][num_cycles]"]').val(billing);					
				}		
			}	
			
			// load packages
			load_subscription_packages_list();	
			// types	
			load_membership_types_list();
		});
		//-->
	</script>
<?php mgm_box_bottom()?>