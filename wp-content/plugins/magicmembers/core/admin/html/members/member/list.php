<script type="text/javascript">var arr_ul_ids = [];</script> 
<?php mgm_box_top('Advanced Search');?>
	<table id="search-table" width="100%" cellpadding="1" cellspacing="0" border="0" >
		<tr>
			<td valign="top">
				<?php _e('Page','mgm')?>
				<select name="pagenum" id="page_number" style="width:50px">
					<option>1</option>
				</select>
			</td>		
			<td valign="top" colspan="2">
				<?php _e('Rows per page: ','mgm');?>
				<input type="text" name="pagelen" value="<?php echo $data['pagelen']?>" size="2">
			</td>	
		</tr>
		<tr>		
			<td valign="top">	
				<?php _e('Search by: ','mgm')?>
				<select name='search_field_name' style='width:135px'>					
					<?php echo mgm_make_combo_options($data['search_fields'], $data['search_field_name'], MGM_KEY_VALUE);?>
				</select>				
				<span id="fld_wrapper"><input type="text" name="search_field_value" value="<?php echo $data['search_field_value']?>" size="10"></span>
			</td>		
			<td valign="top">	
				<?php _e('Sort by : ','mgm')?>
				<select name='sort_field_name' style='width:135px'>					
					<?php echo mgm_make_combo_options($data['sort_fields'], $data['sort_field'], MGM_KEY_VALUE);?>
				</select>		
				<select name='sort_type'>					
					<?php echo mgm_make_combo_options(array('asc'=>'ASC', 'desc'=>'DESC'), $data['sort_type'], MGM_VALUE_ONLY);?>
				</select>								
				<input type="button" name="reload" class="button" value="<?php _e('Search','mgm') ?>" onclick="search_member_list()" />
				<a href="javascript:mgm_member_list()" title="<?php _e('Refresh members list','mgm')?>"><img src="<?php echo MGM_ASSETS_URL ?>images/icons/arrow_refresh.png" /></a>
			</td>					
		</tr>
	</table>
<?php mgm_box_bottom();?>

<?php mgm_box_top('Registered Members');?>
	<table id="tree_container" width="100%" cellpadding="1" cellspacing="0" border="0" class="widefat form-table">
		<thead>
			<tr>
				<th scope="col">&nbsp;</th>
				<th scope="col" style="text-align:center"><b><?php _e('User','mgm') ?> [<?php _e('ID','mgm') ?>]</b></th>
				<th scope="col" style="text-align:center"><b><?php _e('Membership Type','mgm') ?></b></th>
				<th scope="col" style="text-align:center"><b><?php _e('Pack','mgm') ?></b></th>
				<th scope="col" style="text-align:center"><b><?php _e('Dates','mgm') ?></b></th>
				<th scope="col" style="text-align:center"><b><?php _e('Dates 2','mgm') ?></b></th>
				<th scope="col" style="text-align:center"><b><?php _e('Status','mgm') ?></b></th>
			</tr>
		</thead>
		<tbody>
			<?php if(count($data['users'])==0):?>
				<tr class="<?php echo ($alt = ($alt=='') ? 'alternate': '');?>">
					<td colspan="7" align="center">
					 <?php _e('No members','mgm')?>					 					
					</td>
				</tr>
				<?php endif;
				// packs
				$packs = mgm_get_class('subscription_packs');	
				// loop users		
				foreach($data['users'] as $user):
					// user object
					$user = get_userdata($user->ID);	
					// mgm member object
					$mgm_member = mgm_get_member($user->ID);	
					// pack desc
					//show for all:
					//issue #: 509
					//if (empty($mgm_member->amount) || empty($mgm_member->duration)) {					
					if (strtolower($mgm_member->membership_type) == 'guest') {
						$pack_desc = __('N/A','mgm');
					} else {
						// member data
						$amount          = esc_html($mgm_member->amount);
						$currency        = esc_html($mgm_member->currency);
						$duration        = esc_html($mgm_member->duration);
						$duration_type   = $mgm_member->duration_type;   
						$membership_type = esc_html($mgm_member->membership_type);
						$pack_id         = $mgm_member->pack_id;
						$num_cycles		 = (isset($mgm_member->active_num_cycles)) ? $mgm_member->active_num_cycles : null;						
						// get pack desc
						if((int)$pack_id>0){
							// get pack
							$pack      = $packs->get_pack($pack_id); 
							
							//update pack with mgm_member vars:
							$pack['duration'] 		= $duration;
							$pack['duration_type'] 	= $duration_type;
							$pack['cost'] 			= $amount;
							$pack['membership_type']= $membership_type;
							if(!is_null($num_cycles))
								$pack['num_cycles'] = $num_cycles;
								
							// desc
							$pack_desc = $packs->get_pack_desc($pack);
						}else{
						// use set
							$pack      = array('membership_type'=>$membership_type,'cost'=>$amount,'currency'=>$currency,'duration'=>$duration,'duration_type'=>$duration_type,'num_cycles'=>(!empty($num_cycles) ? $num_cycles :  0));
							// desc
							$pack_desc = $packs->get_pack_desc($pack);
						}   
						// hide                    
						if ($mgm_member->hide_old_content && $mgm_member->join_date) {
							$pack_desc .= '<div><span style="color: gray;">'.__('Limited PRE','mgm').':</span> ' . date(MGM_DATE_FORMAT, $mgm_member->join_date) . '</div>';
						}
					}
					// set other data
					$v_date       = '<div><span style="color: gray;">'.__('REGISTER','mgm').':</span> ' . date(MGM_DATE_FORMAT, strtotime($user->user_registered)) . '</div>';
					$v_date      .= '<div><span style="color: gray;">'.__('LAST PAY','mgm').':</span> ' . (empty($mgm_member->last_pay_date) ? __('N/A','mgm'):date(MGM_DATE_FORMAT, strtotime($mgm_member->last_pay_date))) . '</div>';
					$expire_date  = '<div><span style="color: gray;">'.__('EXPIRY','mgm').':</span> ' . (empty($mgm_member->expire_date) ? __('N/A','mgm'):date(MGM_DATE_FORMAT, strtotime($mgm_member->expire_date))) . '</div>';
					$expire_date .= '<div><span style="color: gray;">'.__('PACK JOIN','mgm').':</span> ' . (empty($mgm_member->join_date) ? __('N/A','mgm'):date(MGM_DATE_FORMAT, $mgm_member->join_date)) . '</div>';
					
					// build status value
					$v_status = esc_html($mgm_member->status);
					// status_str 
					if (!empty($mgm_member->status_str)) {
						$v_status .= '<br />' . esc_html($mgm_member->status_str);
					}
				?>
				<tr class="<?php echo ($alt = ($alt=='') ? 'alternate': '');?>">					
				   <td>
				   <input type="checkbox" name="ps[]" id="user_<?php echo $user->ID ?>" value="<?php echo $user->ID ?>" />				   
				   </td>
				   <td>
						<label for="user_<?php $user->ID ?>"><strong><a href="user-edit.php?user_id=<?php echo $user->ID?>"><?php echo esc_html($user->user_login) ?> </a></strong> [<?php echo $user->ID ?>]</label>
						<div><a href="mailto:<?php echo esc_html($user->user_email) ?>"><?php echo esc_html($user->user_email) ?></a></div>						
					</td>
					<td colspan="5">					
						<table width="100%" cellpadding="1" cellspacing="0" border="0">
					<tr class="<?php echo $alt;?>">
					<td><?php echo mgm_stripslashes_deep(mgm_get_class('membership_types')->get_type_name($mgm_member->membership_type));?></td>
					<td><?php echo $pack_desc?></td>
					<td><?php echo $v_date?></td>
					<td><?php echo $expire_date?></td>
					<td>
						<?php echo $v_status?>
						<?php if(isset($mgm_member->transaction_id) && ((int)$mgm_member->transaction_id>0)):?>
						<div><?php _e('Transaction','mgm')?>#<?php echo $mgm_member->transaction_id?></div>
						<?php endif;?>
					</td>
					</tr>
					</table>
					<?php																				
					if(isset($mgm_member->other_membership_types[0]) && is_array($mgm_member->other_membership_types[0]) && !empty($mgm_member->other_membership_types[0]) ) {
						?>						
						<ul id="membership_tree_<?php echo $user->ID; ?>">
						<li><strong><?php _e('Other Memberships', 'mgm') ?></strong>
						<ul>
						<?php 						
						foreach ($mgm_member->other_membership_types as $key => $other_member) {
							$other_member = mgm_convert_array_to_memberobj($other_member, $mgm_member->id);
							//if(isset($other_member->membership_type) && !empty($other_member->membership_type) && $other_member->status != MGM_STATUS_NULL) {								 
							if(isset($other_member->membership_type) && !empty($other_member->membership_type) && !in_array($other_member->membership_type, array('free','trial', 'guest'))) {								 
								
								//if (empty($other_member->amount) || empty($other_member->duration)) {
								//issue #: 509
								if (strtolower($other_member->membership_type) == 'guest') {
									$pack_desc_oth = __('N/A','mgm');
								} else {
									// member data
									$amount_oth      	 = esc_html($other_member->amount);
									$currency_oth    	 = esc_html($other_member->currency);
									$duration_oth        = esc_html($other_member->duration);
									$duration_type_oth   = $other_member->duration_type;   
									$membership_type_oth = esc_html($other_member->membership_type);
									$pack_id_oth         = $other_member->pack_id;	
									$num_cycles		 = (isset($mgm_member->active_num_cycles)) ? $mgm_member->active_num_cycles : null;							
									// get pack desc
									if((int)$pack_id_oth > 0 ){
										// get pack
										$pack_oth      = $packs->get_pack($pack_id_oth); 
										// desc
										$pack_desc_oth = $packs->get_pack_desc($pack_oth);
									}else{
									// use set
										$pack_oth      = array('membership_type'=>$membership_type_oth,'cost'=>$amount_oth,'currency'=>$currency_oth,'duration'=>$duration_oth,'duration_type'=>$duration_type_oth,'num_cycles'=> (!is_null($num_cycles) ? $num_cycles: 0) );
										// desc
										$pack_desc_oth = $packs->get_pack_desc($pack_oth);
									}   
									// hide                    
									if ($other_member->hide_old_content && $other_member->join_date) {
										$pack_desc_oth .= '<div><span style="color: gray;">'.__('Limited PRE','mgm').':</span> ' . date(MGM_DATE_FORMAT, $other_member->join_date) . '</div>';
									}
								}
								// set other data
								$v_date_oth       = '<div><span style="color: gray;">'.__('REGISTER','mgm').':</span> ' . date(MGM_DATE_FORMAT, strtotime($user->user_registered)) . '</div>';
								$v_date_oth      .= '<div><span style="color: gray;">'.__('LAST PAY','mgm').':</span> ' . (empty($other_member->last_pay_date) ? __('N/A','mgm'):date(MGM_DATE_FORMAT, strtotime($other_member->last_pay_date))) . '</div>';
								$expire_date_oth  = '<div><span style="color: gray;">'.__('EXPIRY','mgm').':</span> ' . (empty($other_member->expire_date) ? __('N/A','mgm'):date(MGM_DATE_FORMAT, strtotime($other_member->expire_date))) . '</div>';
								$expire_date_oth .= '<div><span style="color: gray;">'.__('PACK JOIN','mgm').':</span> ' . (empty($other_member->join_date) ? __('N/A','mgm'):date(MGM_DATE_FORMAT, $other_member->join_date)) . '</div>';
								
								// build status value
								$v_status_oth = esc_html($other_member->status);
								// status_str 
								if (!empty($other_member->status_str)) {
									$v_status_oth .= '<br />' . esc_html($other_member->status_str);
								}
								?>
								<li>
								<table width="100%" cellpadding="1" cellspacing="0" border="0">
								<tr class="<?php echo $alt;?>">
								
								<td>
								<input onclick="uncheck_other_memberships(this,'<?php echo $user->ID; ?>');" type="checkbox" name="ps_mem[<?php echo $user->ID ?>][]" id="user_mem_<?php echo $user->ID ?>_<?php echo $other_member->membership_type ?>" value="<?php echo $other_member->membership_type ?>" />
								<input type="hidden" name="ps_mem_index[<?php echo $user->ID ?>][<?php echo $other_member->membership_type ?>]" id="user_mem_index_<?php echo $user->ID ?>_<?php echo $key ?>" value="<?php echo $key ?>" />
								</td>
								<td><?php echo mgm_stripslashes_deep(mgm_get_class('membership_types')->get_type_name($other_member->membership_type));?></td>
								<td><?php echo $pack_desc_oth?></td>
								<td><?php echo $v_date_oth?></td>
								<td><?php echo $expire_date_oth?></td>
								<td>
									<?php echo $v_status_oth ?>
									<?php if(isset($other_member->transaction_id) && ((int)$other_member->transaction_id>0)):?>
									<div><?php _e('Transaction','mgm')?>#<?php echo $other_member->transaction_id?></div>
									<?php endif;?>
								</td>
								</tr>
								</table>
								</li>
								<?php 							
							}
						}
						?>
						</ul>
						</li>
						</ul>
						<script type="text/javascript">						
						arr_ul_ids[arr_ul_ids.length] = 'membership_tree_<?php echo $user->ID; ?>';											
						</script>
						<?php 
					}
					?>					
					</td>					
				</tr>
			<?php endforeach;?>
		</tbody>
	</table>
	<div class="clearfix"></div>
	<div align="right" style="height:30px"><?php if($data['page_links']):?><div class="pager-wrap"><?php echo $data['page_links']?></div><div class="clearfix"></div><?php endif; ?></div>	
	<div class="clearfix"></div>
<?php mgm_box_bottom();?>
<script language="javascript">
	<!--
	jQuery(document).ready(function(){
		// set pager
		mgm_set_pager('#member_list');
		// chnage search field
		var onchange_count = 0;
		var search_val = '<?php echo isset($data['search_field_value'])?$data['search_field_value']:""?>';			
		jQuery("select[name='search_field_name']").bind('change',function() {			
			jQuery(":input[name='search_field_value']").remove();		
			if(onchange_count > 0)
				search_val = '';	
			switch(jQuery(this).val()){
				case 'membership_type':
					var s=document.createElement('select');
						s.name='search_field_value';						
					<?php foreach(mgm_get_class('membership_types')->membership_types as $membership_type_value=>$membership_type_text):?>
						s.options[s.options.length]=new Option('<?php echo $membership_type_text?>','<?php echo $membership_type_value?>',false,<?php echo ($data['search_field_value']==$membership_type_value?'true':'false')?>);
					<?php endforeach?>
					jQuery('#fld_wrapper').html(s);
				break;
				case 'status':
					var s=document.createElement('select');
						s.name='search_field_value';
					<?php 
					$statuses=array('Inactive','Active','Expired','Pending','Free Account','Trial');
					foreach($statuses as $status):?>
						s.options[s.options.length]=new Option('<?php echo $status?>','<?php echo $status?>',false,<?php echo ($data['search_field_value']==$status?'true':'false')?>);
					<?php endforeach?>
					jQuery('#fld_wrapper').html(s);
				break;
				case 'reg_date':
				case 'last_payment':
				case 'expire_date':				
					////issue#: 219
					jQuery('#fld_wrapper').html('<input type="text" name="search_field_value" value="'+search_val+'" size="10">');
					if(!jQuery("#mgmmembersfrm :input[name='search_field_value']").hasClass('hasDatepicker')){					
						mgm_date_picker("#mgmmembersfrm :input[name='search_field_value']",'<?php echo MGM_ASSETS_URL?>');
					}
				break;
				default:					
					////issue#: 219
					jQuery('#fld_wrapper').html('<input type="text" name="search_field_value" value="'+search_val+'" size="10">');
				break;
			}
			onchange_count++;
		}).change();
		
		// reload
		search_member_list=function() {
			jQuery.ajax({url:'admin.php?page=mgm/admin/members&method=member_list', type: 'POST', cache:false, data : jQuery("#search-table :input").serialize(),
				beforeSend: function(){	
					// show message
					mgm_show_message('#members', {status:'running', message:'<?php _e('Processing','mgm')?>...'},true);														
				 },
				 success:function(data){
					// show message
					mgm_show_message('#members', {status:'success', message:'<?php _e('Search Result: ','mgm')?>'});																														
					// append 
					jQuery('#member_list').html(data);										 
				 }
			});
		}
		
		//render other membership tree:
		var arr_count = arr_ul_ids.length;
		if(arr_count > 0) {
			for(var i = 0; i < arr_count; i++)
				jQuery('ul#'+arr_ul_ids[i]).collapsibleCheckboxTree();	
		}
		
		uncheck_other_memberships = function(chk, user_id) {					
			if(chk.checked)
		    	jQuery('#user_'+user_id).attr('checked', true);
			jQuery('#membership_tree_'+user_id+' input[type="checkbox"]:checked').each(function() { 		       			        		        		        	
	        	if(chk.value != jQuery(this).val())
	        		jQuery(this).removeAttr('checked');		        	
		    });
		    
		}		
	});
	//-->	
</script>