<?php
// get flag
$action_flag=$_REQUEST['mode'];
// call process
mgk_call_process($action_flag,'f_members');

// define actions /////////////

// default: list of blocked members
function f_members(){
	global $wpdb;
	// pager
	$pager = new mgk_pager();
	// sql		  
	$sql = 'SELECT SQL_CALC_FOUND_ROWS u.ID, u.user_login, u.user_email, um.meta_value
			FROM ' . $wpdb->users . ' u	JOIN ' . $wpdb->usermeta . ' um ON (u.ID = um.user_id	
			AND um.meta_key = "mgk_locked_out"	) ORDER BY u.user_login '.$pager->get_query_limit(mgk_get_setting('pagination'));	 
	// echo $sql;		
	// rows	
	$blocked_users=$wpdb->get_results($sql);
	// get page links
	$page_links=$pager->get_pager_links('admin.php?page=mgk/admin&load=blockedlists');	
	?>
	<?php mgk_box_top('Blocked Members')?>
	<div align="right"><a href="javascript:mgk_reload_blocked_userlist()"><img src="<?php echo MGK_ASSETS_URL ?>images/icons/arrow_refresh.png" /></a></div> 	
	<div align="right"><?php if($page_links):?><div class="pager-wrap"><?php echo $page_links?></div><?php endif; ?></div>			
		<form name="frmblklist" id="frmblklist" method="post" action="admin.php?page=mgk/admin&load=blockedlists">																
			<table width="100%" cellpadding="1" cellspacing="0" border="0" class="form-table widefat">
				<thead>
					<tr>				
						<th scope="col"><b>#<?php _e('ID','mgk')?></b></th>							
						<th scope="col"><b><?php _e('Username','mgk');?></b></th>
						<th scope="col"><b><?php _e('Email','mgk');?></b></th>												
						<th scope="col"><b><?php _e('Locked Out Since','mgk');?></b></th>											
						<th scope="col"></th>
					</tr>
				</thead>
				<tbody>
					<?php if(count($blocked_users)==0):?>
					<tr class="<?php echo ($alt = ($alt=='') ? 'alternate': '');?>" id="row-0">		
						<td colspan="5" align="center"><?php _e('There are currently no locked out users.','mgk')?></td>
					</tr>
					<?php endif;						
					// show
					foreach ($blocked_users as $user) :?>
					<tr class="<?php echo ($alt = ($alt=='') ? 'alternate': '');?>" id="row-<?php echo $user->ID?>">		
						<td valign="top"><?php echo $user->ID?></td>									
						<td valign="top"><?php echo $user->user_login?></td>
						<td valign="top"><?php echo $user->user_email?></td>	
						<td valign="top"><?php echo ($user->meta_value > 1) ? date(mgk_get_setting('long_date_format'), $user->meta_value) : __('Email Activation', 'mgk')?></td>							
						<td valign="top" nowrap="nowrap">
							<a href="javascript:mgk_unlock_user('<?php echo $user->ID ?>')" title="<?php _e('Unlock Member', 'mgk')?>"><img src="<?php echo MGK_ASSETS_URL?>images/icons/lock_open.png" /></a>				
						</td>
					</tr>
					<?php endforeach;?>					
				</tbody>	
			</table>
		</form>		
		<div align="right"><?php if($page_links):?><div class="pager-wrap"><?php echo $page_links?></div><div class="clearfix"></div><?php endif; ?></div>
	<?php mgk_box_bottom()?>
	<script language="javascript">
		<!--
		// set pager
		mgk_set_pager('#admin_blockedlists');
		// reload
		mgk_reload_blocked_userlist=function(){
			jQuery('#admin_blockedlists .content-div').tabs('load',0);
		}		
		// mgk_unlock_user
		mgk_unlock_user=function(id){
			// confirm
			if(!confirm('<?php _e('You are about to unlock member, are you sure!','mgk')?>')){
				return ;
			}
			// send
			jQuery.ajax({url:'admin.php?page=mgk/admin&load=blockedlists',
						type:'POST',
						dataType:'json',
						data:{mode:'unlock_user',id:id},
						beforeSubmit: function(){	
							jQuery('#wrap-admin-blockedlists #message').remove();		
							jQuery('#wrap-admin-blockedlists').prepend('<div id="message" class="running"> <?php _e('Sending request...','mgk')?></div>');									  	
					    },
						success: function(data){											   														
							jQuery('#wrap-admin-blockedlists #message').remove();	
							jQuery('#wrap-admin-blockedlists').prepend('<div id="message"></div>');	
							jQuery('#wrap-admin-blockedlists #message').addClass(data.status).html(data.message);	
							jQuery.scrollTo('#admin_blockedlists',400);
							if(data.status=='success'){
								jQuery("#frmblklist #row-"+id).remove();
							}								   	
			  			}});
		}	
		//-->
	</script>
	<?php								
}

// unlock_user
function f_unlock_user(){
	global $wpdb;
	
	// get id
	$id = (int)$_POST['id'];
	
	// delete
	delete_user_meta($id, 'mgk_locked_out');

	// success
	echo json_encode(array('status'=>'success','message'=>__('Successfully unlocked selected member from blocked list.','mgk')));exit;
	
}

// list of blocked IPs
function f_ips(){
	global $wpdb;
	// pager
	$pager = new mgk_pager();
	// sql		  
	$sql = 'SELECT SQL_CALC_FOUND_ROWS `id`,`ip_address` FROM `' . TBL_MGK_BLOCKED_IPS . '` ORDER BY `ip_address` ASC '.$pager->get_query_limit(mgk_get_setting('pagination'));	 
	// echo $sql;		 		
	// rows	
	$blocked_ips=$wpdb->get_results($sql);
	// get page links
	$page_links=$pager->get_pager_links('admin.php?page=mgk/admin&load=blockedlists&mode=ips');	
	?>
	<?php mgk_box_top('Blocked IPs')?>
	<div align="right">
		<input type="button" class="button" name="ipblock_add" value="<?php _e('Add IP','mgk') . ' &raquo;'?>" onclick="mgk_blip_addnew()"/>
		<a href="javascript:mgk_reload_blocked_iplist()"><img src="<?php echo MGK_ASSETS_URL ?>images/icons/arrow_refresh.png" /></a>
	</div> 	
	<div align="right"><?php if($page_links):?><div class="pager-wrap"><?php echo $page_links?></div><?php endif; ?></div>			
		<form name="frmblkiplist" id="frmblkiplist" method="post" action="admin.php?page=mgk/admin&load=blockedlists&mode=ips">																
			<table width="100%" cellpadding="1" cellspacing="0" border="0" class="form-table widefat">
				<thead>
					<tr>				
						<th scope="col"><b>#<?php _e('ID','mgk')?></b></th>
						<th scope="col"><b><?php _e('IP','mgk')?></b></th>																							
						<th scope="col"></th>
					</tr>
				</thead>
				<tbody>
					<?php if(count($blocked_ips)==0):?>
					<tr class="<?php echo ($alt = ($alt=='') ? 'alternate': '');?>" id="row-0">		
						<td colspan="3" align="center"><?php _e('There are currently no locked out IPs.','mgk')?></td>
					</tr>
					<?php endif;	
					
					// show
					foreach ($blocked_ips as $blip) :?>
					<tr class="<?php echo ($alt = ($alt=='') ? 'alternate': '');?>" id="row-<?php echo $blip->id?>">		
						<td valign="top"><?php echo $blip->id?></td>									
						<td valign="top"><?php echo $blip->ip_address?></td>
						<td valign="top" nowrap="nowrap">
							<a href="javascript:mgk_blip_edit('<?php echo $blip->id?>')"><img src="<?php echo MGK_ASSETS_URL?>images/icons/edit.png" /></a>
							<a href="javascript:mgk_blip_delete('<?php echo $blip->id?>')"><img src="<?php echo MGK_ASSETS_URL?>images/icons/cross.png" /></a>				
						</td>
					</tr>
					<?php endforeach;?>					
				</tbody>	
			</table>
		</form>		
		<div align="right"><?php if($page_links):?><div class="pager-wrap"><?php echo $page_links?></div><div class="clearfix"></div><?php endif; ?></div>
	<?php mgk_box_bottom()?>
	<script language="javascript">
		<!--
		// onready
		jQuery(document).ready(function(){ 
			// set pager
			mgk_set_pager('#admin_blockedlists',1);
			// reload
			mgk_reload_blocked_iplist=function(){
				jQuery('#admin_blockedlists .content-div').tabs('load',1);
			}	
			// add
			mgk_blip_addnew=function(){		
				// create tab				
				jQuery('#admin_blockedlists .content-div').mgkAutoTabs({index: 2, label: '<?php _e('Add IP','mgk')?>', url: 'admin.php?page=mgk/admin&load=blockedlists&mode=add_ip_blocked'});						
			}
			// edit
			mgk_blip_edit=function(id){			
				// create tab
				jQuery('#admin_blockedlists .content-div').mgkAutoTabs({index: 2, label: '<?php _e('Edit IP','mgk')?>', url: 'admin.php?page=mgk/admin&load=blockedlists&mode=edit_ip_blocked&id='+id});						
			}
			// delete
			mgk_blip_delete=function(id){
				// confirm
				if(!confirm('<?php _e('You are about to delete one row, are you sure!','mgk')?>')){
					return ;
				}
				// send
				jQuery.ajax({
					url:'admin.php?page=mgk/admin&load=blockedlists',
					type:'POST',
					dataType:'json',
					data:{mode:'delete_ip_blocked',id:id},
					beforeSubmit: function(){	
						jQuery('#wrap-admin-blockedlists #message').remove();		
						jQuery('#wrap-admin-blockedlists').prepend('<div id="message" class="running"> <?php _e('Sending request...','mgk')?></div>');									  	
					},
					success: function(data){											   														
						jQuery('#wrap-admin-blockedlists #message').remove();	
						jQuery('#wrap-admin-blockedlists').prepend('<div id="message"></div>');	
						jQuery('#wrap-admin-blockedlists #message').addClass(data.status).html(data.message);	
						jQuery.scrollTo('#admin_blockedlists',400);
						if(data.status=='success'){
							jQuery("#frmblkiplist #row-"+id).remove();
						}								   	
					}});    			  
			}		
		});	
		//-->
	</script>
	<?php	
}

// delete blocked ip
function f_delete_ip_blocked(){
	global $wpdb;
	$sql = 'DELETE FROM `' . TBL_MGK_BLOCKED_IPS . '` WHERE `id` = "'.(int)$_POST['id'].'"';
	$r = $wpdb->query($sql);
	if($r){
		echo json_encode(array('status'=>'success','message'=>__('Successfully deleted selected IP from blocked list.','mgk')));exit;
	}
	// error
	echo json_encode(array('status'=>'success','message'=>__('Error while deleting IP.','mgk')));
}

// edit blocked ip 
function f_edit_ip_blocked(){	
	global $wpdb;
	$id = (int)$_GET['id'];
	// save
	if(isset($_POST['process']) && $_POST['process']=='true'){		
		extract($_POST);
		if(!empty($ip_address)){
			// check name unique
			if(mgk_is_duplicate(TBL_MGK_BLOCKED_IPS,array('ip_address'), "AND id<>'{$id}'")){
				$status  ='error';
				$message =__('IP Address is already in database, please provide a different ip address.','mgk');
			}else{
				$columns =array('ip_address' => $ip_address);			
				$success=$wpdb->update(TBL_MGK_BLOCKED_IPS, $columns, array('id'=>$id));
				if($success){
					$status  ='success';
					$message =__('IP block updated successfully.','mgk');
				}else{
					$status  ='error';
					$message =__('Database error occurred','mgk');
				}
			}	
		}else{
			$status  ='error';
			$message =__('IP Address not provided.','mgk');
		}		
		// the response
		echo json_encode(array('status'=>$status,'message'=>$message));
		exit();
	}
	
	$sql = "SELECT * FROM `" . TBL_MGK_BLOCKED_IPS . "` WHERE `id` = '{$id}'";
	$row = $wpdb->get_row($sql);
	// show form
	?>
	<?php mgk_box_top('Edit IP to BlockList')?>	
	<form name="frmblkipedit" id="frmblkipedit" method="post" action="admin.php?page=mgk/admin&load=blockedlists&mode=edit_ip_blocked&id=<?php echo $id?>">
		<table width="100%" cellpadding="1" cellspacing="0" border="0" class="form-table">		
			<tbody>				
				<tr>
					<td width="25%" valign="top"><span class="required-field"><?php _e('IP Address','mgk')?></span></td>
					<td valign="top">
						<input type="text" name="ip_address" id="ip_address" size="40" value="<?php echo $row->ip_address?>"/>
						<div class="tips"><?php _e('Please enter an IP address to block.', 'mgk')?></div>
					</td>
				</tr>								
				<tr>
					<td valign="top"></td>
					<td valign="top">
						<input type="submit" name="btn_save" value="<?php _e('Save','mgk')?>" class="button"/>
						<a href="javascript:mgk_blip_cancel()" class="button"><?php _e('Cancel','mgk')?></a>
					</td>
				</tr>
			</tbody>	
		</table>	
		<input type="hidden" name="process" value="true" />
	</form>											
	<br />	
	<?php mgk_box_bottom()?>
	<script language="javascript">
		<!--
		// onready
		jQuery(document).ready(function(){   
			// cacel
			mgk_blip_cancel=function(){
				jQuery('#admin_blockedlists .content-div').tabs('remove',2);
				jQuery('#admin_blockedlists .content-div').tabs('select',1);
			}			
			// first field focus 	
			jQuery("#frmblkipedit :input:first").focus();
			// ip check
			jQuery.validator.addMethod("ipaddress", function(value, element) { 
			  return this.optional(element) || /\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/.test(value); 
			}, "Please specify the correct ip address");
							
			// add login form validation
			jQuery("#frmblkipedit").validate({					
				submitHandler: function(form) {					    					
					jQuery("#frmblkipedit").ajaxSubmit({
					  type: "POST",					 
					  dataType: 'json',											 
					  beforeSubmit: function(){	
						jQuery('#wrap-admin-blockedlists #message').remove();		
						jQuery('#wrap-admin-blockedlists').prepend('<div id="message" class="running"> <?php _e('Saving','mgk');?>...</div>');									  	
					  },
					  success: function(data){	
						// remove 										   														
						jQuery('#wrap-admin-blockedlists #message').remove();	
						// cancel to list
						if(data.status=='success'){														
							jQuery("#frmblkipedit :input")
							.not(":input[type='hidden']")
							.not(":input[type='submit']")
							.val('');
							mgk_blip_cancel(data);	
						}
						// show message
						jQuery('#wrap-admin-blockedlists').prepend('<div id="message"></div>');	
						// show
						jQuery('#wrap-admin-blockedlists #message').addClass(data.status).html(data.message);	
						// focus
						jQuery.scrollTo('#admin_blockedlists',400);								   	
					  }});    		
					return false;											
				},
				rules: {			
					ip_address: {required: true, ipaddress:true}
				},
				messages: {			
					ip_address: {required:"Please enter ip address",ipaddress:"Please valid IP address"}
				},
				errorClass: 'validation-error'
			});				
		});	
		//-->		
	</script>
	<?php
}

// add blocked ip address 
function f_add_ip_blocked(){
	global $wpdb;
	// save
	if(isset($_POST['process']) && $_POST['process']=='true'){		
		extract($_POST);
		if(!empty($ip_address)){
			// check name unique
			if(mgk_is_duplicate(TBL_MGK_BLOCKED_IPS,array('ip_address'))){
				$status  ='error';
				$message =__('IP Address is already in database, please provide a different ip address.','mgk');
			}else{
				$columns =array('ip_address' => $ip_address);			
				$success=$wpdb->insert(TBL_MGK_BLOCKED_IPS, $columns);
				if($success){
					$status  ='success';
					$message =__('IP blocked successfully.','mgk');
				}else{
					$status  ='error';
					$message =__('Database error occurred','mgk');
				}
			}	
		}else{
			$status  ='error';
			$message =__('IP Address not provided.','mgk');
		}		
		// the response
		echo json_encode(array('status'=>$status,'message'=>$message));
		exit();
	}
	
	// show form
	?>
	<?php mgk_box_top('Add IP to BlockList')?>	
	<form name="frmblkipadd" id="frmblkipadd" method="post" action="admin.php?page=mgk/admin&load=blockedlists&mode=add_ip_blocked">
		<table width="100%" cellpadding="1" cellspacing="0" border="0" class="form-table">		
			<tbody>				
				<tr>
					<td width="25%" valign="top"><span class="required-field"><?php _e('IP Address','mgk')?></span></td>
					<td valign="top">
						<input type="text" name="ip_address" id="ip_address" size="40"/>
						<div class="tips"><?php _e('Please enter an IP address to block.', 'mgk')?></div>
					</td>
				</tr>								
				<tr>
					<td valign="top"></td>
					<td valign="top">
						<input type="submit" name="btn_save" value="<?php _e('Save','mgk')?>" class="button"/>
						<a href="javascript:mgk_blip_cancel()" class="button"><?php _e('Cancel','mgk')?></a>
					</td>
				</tr>
			</tbody>	
		</table>	
		<input type="hidden" name="process" value="true" />
	</form>											
	<br />	
	<?php mgk_box_bottom()?>
	<script language="javascript">
		<!--
		// onready
		jQuery(document).ready(function(){   
			// cacel
			mgk_blip_cancel=function(){
				jQuery('#admin_blockedlists .content-div').tabs('remove',2);
				jQuery('#admin_blockedlists .content-div').tabs('select',1);
			}			
			// first field focus 	
			jQuery("#frmblkipadd :input:first").focus();
			// ip address check
			jQuery.validator.addMethod("ipaddress", function(value, element) { 
			  return this.optional(element) || /\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/.test(value); 
			}, "Please specify the correct ip address");
							
			// add login form validation
			jQuery("#frmblkipadd").validate({					
				submitHandler: function(form) {					    					
					jQuery("#frmblkipadd").ajaxSubmit({
					  type: "POST",					  
					  dataType: 'json',											 
					  beforeSubmit: function(){	
						jQuery('#wrap-admin-blockedlists #message').remove();		
						jQuery('#wrap-admin-blockedlists').prepend('<div id="message" class="running"> <?php _e('Saving','mgk');?>...</div>');									  	
					  },
					  success: function(data){	
						// remove 										   														
						jQuery('#wrap-admin-blockedlists #message').remove();	
						// cancel to list
						if(data.status=='success'){														
							jQuery("#frmblkipadd :input")
							.not(":input[type='hidden']")
							.not(":input[type='submit']")
							.val('');
							mgk_blip_cancel(data);	
						}
						// show message
						jQuery('#wrap-admin-blockedlists').prepend('<div id="message"></div>');	
						// show
						jQuery('#wrap-admin-blockedlists #message').addClass(data.status).html(data.message);	
						// focus
						jQuery.scrollTo('#admin_blockedlists',400);								   	
					  }});    		
					return false;											
				},
				rules: {			
					ip_address: {required: true, ipaddress:true}
				},
				messages: {			
					ip_address: {required:"Please enter ip address",ipaddress:"Please valid IP address"}
				},
				errorClass: 'validation-error'
			});				
		});	
		//-->		
	</script>
	<?php
}

// end of file