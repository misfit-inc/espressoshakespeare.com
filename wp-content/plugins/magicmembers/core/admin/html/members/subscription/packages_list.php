<table width="100%" cellpadding="1" cellspacing="0" border="0" class="form-table widefat">	
	<thead>
		<tr>
			<th><b><?php _e('Subscription Packages','mgm')?></b></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>				
				<div class='mgm'>
					<div id="subs_pkgs_panel">
						<?php 			
						// loop membership types
						foreach($data['membership_types'] as $type_code=>$type) :	
							// not for guest
							if($type_code == 'guest') continue;
							
							// for inactive free/trial hide the tab
							if(in_array($type_code, array('free','trial'))): if(!in_array('mgm_'.$type_code,$data['payment_modules'])) continue; endif;				
						?>		
						<h3><a href="#"><b><?php echo mgm_stripslashes_deep($type)?></b></a></h3>
						<div>
							<p>							
								<!-- new package-->
								<div id="pkgs_<?php echo $type_code?>">
									<?php echo mgm_stripslashes_deep($data['membership'][$type_code])?>
								</div>
								<p>
									<div>	
										<a class="button" href="javascript:add_pack('<?php echo $type_code; ?>')"><?php _e('Add New Package &raquo;','mgm') ?></a>								
									</div>			
								</p>
								<div class="clearfix"></div>
							</p>
						</div>
						<?php endforeach?>		
					</div>			
				</div>
			</td>
		</tr>
	</tbody>	
	<tfoot>
		<tr>
			<td valign="middle">				
				<div style="float:left">
					<input type="button" class="button" onclick="update_packs()" value="<?php _e('Update Packages','mgm') ?> &raquo;" />
				</div>	
			</td>
		</tr>
	</tfoot>
</table>			
<script language="javascript">
<!--
jQuery(document).ready(function(){		
	// add pack
	add_pack = function(type_code){		
		jQuery.ajax({ url: 'admin.php?page=mgm/admin/members&method=subscription_package',
			 type: 'POST',
			 cache: false,
			 dataType: 'html',
			 data: {type: type_code},
			 beforeSend: function(){	
				// show message
				mgm_show_message('#frmsubspkgs', {status:'running', message:'<?php _e('Processing','mgm')?>...'},true);																			
			 },
			 success: function(data){	
			 	// message																				
				mgm_show_message('#frmsubspkgs', {status:'success', message:'<?php _e('Successfully added new membership package','mgm')?>.'});
				// appaned						 	
				jQuery('#pkgs_'+type_code).append(data);
			 }
		});
	}	
	// update pack
	update_packs = function(){		
		// update
		jQuery("#frmsubspkgs").ajaxSubmit({type: "POST",
		   url: 'admin.php?page=mgm/admin/members&method=subscription_packages_update',
		   dataType: 'json',			
		   iframe: false,								 
		   beforeSubmit: function(){
		   		//free module check:
		   		var freemodule = <?php echo $data['free_module_enabled'] ?>;
		   		if(!freemodule) {
			   		var confirm_zero_cost = false;		   		
			   		jQuery("#frmsubspkgs select").each(function() {
			   			if(null != (this.id).match(/packs_membership_type_/) ) {		   				
							var arr_id = (this.id).split('packs_membership_type_');
							if(arr_id[1] != '') {
								var type = this.value;
								var cost = jQuery('#packs_cost_'+arr_id[1]).val();
								if(parseInt(cost) <= 0 && (type != 'free'&& type != 'trial') ) {								
									confirm_zero_cost = true;								
								}
							}
						}		   		
			   		});	 
			   		//check different role selected:
			   		var confirm_role = false;
			   		if(arr_pack_role.length > 0) {
			   			for(var r in arr_pack_role) {
			   				var current_role = jQuery('select[name="packs['+r+'][role]"]').val();			   				
			   				if(arr_pack_role[r] != current_role) {
			   					confirm_role = true;
			   				}
			   			}
			   			
			   			//if zero is entered for a paid subscription
				   		if(confirm_role) {
				   			if(!confirm('<?php _e('Are you sure, you have selected a different role for subscription pack. This will cause a mass update to the associated user data?','mgm')?>')) {
								return false;	   				
				   			}
				   		}
			   		}
			   		
			   		//if zero is entered for a paid subscription
			   		if(confirm_zero_cost) {
			   			if(!confirm('<?php _e('Are you sure, you have selected a zero cost for subscription and Free Payment module needs to be enabled for this to be processed?','mgm') ?>')) {
							return false;	   				
			   			}
			   		}
			   }
			   //duplicate check for move member's pack on expiry/cancellation
			   <?php if($data['enable_multiple_level_purchase'] == 'Y') { ?>
			   var arr_movepacks = [];
			   var error_movepack = false;
			   jQuery("#frmsubspkgs select[name*='move_members_pack']").each( function() {
					if(this.value != '') {
			   			if(typeof(arr_movepacks[this.value]) == 'undefined')
			   				arr_movepacks[this.value] = true;
			   			else{
			   				error_movepack = true;
			   				return;
			   			}
					}					
				}				
				);
				
				if(error_movepack) {
					alert('<?php _e('Please select different packs for "When expired/cancelled, move members to:"?','mgm') ?>');
					return false;	
				}
			   <?php } ?>   		
				// show message
				mgm_show_message('#frmsubspkgs', {status:'running', message:'<?php _e('Processing','mgm')?>...'},true);					
			 },
			 success: function(data){	
				// message																				
				mgm_show_message('#frmsubspkgs', data);																		
			 }}); 
	  // end 
	}	
	// delete pack
	delete_pack	= function(index, id){
		// warn
		if(confirm('<?php _e('Are sure you want to delete selected package?','mgm') ?>')){			
			jQuery.ajax({ url: 'admin.php?page=mgm/admin/members&method=subscription_package_delete',
				 type: 'POST',
				 cache: false,
				 dataType: 'json',
				 data: {index: (index-1), id: id},
				 beforeSend: function(){	
					// show message
					mgm_show_message('#frmsubspkgs', {status:'running', message:'<?php _e('Processing','mgm')?>...'},true);																			
				 },
				 success: function(data){	
				 	// message																				
					mgm_show_message('#frmsubspkgs', data);
				 	// success				 	
					if(data.status=='success'){
						// delete row
						jQuery('#subscription_packages_list #mgm_pack_'+id).fadeOut('slow').remove();
					}
				 }
			});
		}
	}		
});
//-->
</script>	