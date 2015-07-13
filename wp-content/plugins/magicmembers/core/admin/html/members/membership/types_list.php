<?php 
$arr_types = array();
foreach($data['mgm_membership_types']->membership_types as $mt => $mtc)
	$arr_types[$mt] = mgm_stripslashes_deep($mtc);
foreach (mgm_get_class('membership_types')->membership_types as $type_code=>$type) :	?>			
	<tr class="<?php echo ($alt = ($alt=='') ? 'alternate': '');?>" id="row-<?php echo $type_code; ?>">	
		<td valign="top" width="40%">
			<div style="float:left">
				<b><?php echo mgm_stripslashes_deep(esc_html($type)); ?></b>
			</div>
			<div style="float:right">
				<a href="javascript:mgm_toggle_mt_advanced('adv-<?php echo $type_code; ?>')" id="adv-<?php echo $type_code; ?>-trig" title="<?php _e('Advanced Settings','mgm')?>">
					<img src="<?php echo MGM_ASSETS_URL ?>images/icons/plus.png" />
				</a>
			</div>
			<div class="clearfix"></div>			
		</td>
		<td valign="top">
			<?php if(in_array($type_code, array('free','trial','guest'))):
				 	echo '<span style="color:red;font-weight:bold;">'.__('System defined.','mgm').'</span>';
				  else:?>
			<input type="checkbox" name="remove_membership_type[]" value="<?php echo $type_code?>" />
			<?php _e('Delete and move this membership type\'s members to ','mgm') ?><br />
			<select name="move_membership_type_to[<?php echo $type_code?>]" style="width:40%" disabled="disabled">
			  	<option value="none">--none--</option>
				<?php echo mgm_make_combo_options($arr_types, '', MGM_KEY_VALUE, array('guest', 'trial', $type_code));?>
			</select> 
			<?php endif;?>	<br /><br /> 
			<?php $redirect_url = $data['mgm_membership_types']->get_login_redirect($type_code)?>
			<input type="checkbox" name="update_login_redirect_url[]" value="<?php echo $type_code?>" <?php echo ($redirect_url!='')?'checked':''?>/>
			<?php _e('Login Redirect URL:','mgm') ?><br /> 	
			<input type="text" name="login_redirect_url[<?php echo $type_code?>]" size="80" maxlength="1000" value="<?php echo $redirect_url?>" <?php echo ($redirect_url!='')?'':'disabled="disabled"'?>/>
			<br />
			<?php $logout_redirect_url = $data['mgm_membership_types']->get_logout_redirect($type_code)?>
			<input type="checkbox" name="update_logout_redirect_url[]" value="<?php echo $type_code?>" <?php echo ($logout_redirect_url!='')?'checked':''?>/>
			<?php _e('Logout Redirect URL:','mgm') ?><br /> 	
			<input type="text" name="logout_redirect_url[<?php echo $type_code?>]" size="80" maxlength="1000" value="<?php echo $logout_redirect_url?>" <?php echo ($logout_redirect_url!='')?'':'disabled="disabled"'?>/>
		</td>
	</tr>	
	<tr class="<?php echo $alt ;?>" id="adv-<?php echo $type_code; ?>" style="display:none; ">	
		<td colspan="2">
			<?php 
				// membership					
				$membership	    = $type_code;
				$membership_enc = base64_encode($membership);
			?>
			<div style="padding:10px">										
				<table width="100%" cellpadding="1" cellspacing="1" border="0" class="form-table widefat">
					<thead>
						<tr>
							<th scope="col" colspan="3"><b><?php _e('Membership Register URLs/Tag','mgm')?></b> </th>
						</tr>	
					</thead>
					<tbody>
						<tr>
							<td width="15%"><?php _e('Custom URL','mgm')?></td>
							<td valign="top" align="center" width="5">:</td>
							<td><?php echo mgm_get_custom_url('register',false,array('membership'=>$membership_enc));?></td>
						</tr>	
						<tr>
							<td width="15%"><?php _e('Wordpress URL','mgm')?></td>
							<td valign="top" align="center" width="5">:</td>
							<td><?php echo mgm_get_custom_url('register',true,array('membership'=>$membership_enc));?></td>
						</tr>
						<tr>
							<td width="15%"><?php _e('Tag','mgm')?></td>
							<td valign="top" align="center" width="5">:</td>
							<td><?php echo sprintf('[user_register membership=%s]',$membership);?></td>
						</tr>
					</tbody>		
				</table>						
			</div>
		</td>
	</tr>
<?php endforeach;
unset($arr_types);
?>	