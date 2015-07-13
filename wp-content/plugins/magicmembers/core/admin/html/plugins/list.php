<fieldset class="autoresponderlist" id="mgm_autoresponder_<?php echo $data['pack_ctr'] ?>"><legend><?php echo sprintf(__('Package #%d','mgm'),$data['pack_ctr'] )?></legend>	
	<input type="hidden" name="packs[<?php echo ($data['pack_ctr']-1) ?>][id]" value="<?php echo $data['pack']['id']?>"/>
	<table width="100%" cellpadding="1" cellspacing="1" border="0" class="form-table">					
		<tr>
			<td colspan="3" align="left" height="30" valign="top"><div class="subscription-heading"><?php _e('Basic Settings','mgm') ?></div></td>
		</tr>
		<tr>
			<td valign="top" align="left" width="20%"><?php _e('Membership Type','mgm')?></td>
			<td valign="top" align="center" width="5">:</td>
			<td valign="top" align="left">
				<select name="packs[<?php echo ($data['pack_ctr']-1) ?>][membership_type]" style="width: 150px;">
					<option value="<?php echo $data['pack']['membership_type'] ?>"><?php echo mgm_get_class('membership_types')->get_type_name($data['pack']['membership_type']) ?></option>
				</select>
			</td>
		</tr>	
		<tr>
			<td valign="top" align="left"><?php _e('Duration','mgm')?></td>
			<td valign="top" align="center">:</td>
			<td valign="top" align="left">
				<input type="text" size="5" name="packs[<?php echo ($data['pack_ctr']-1) ?>][duration]" value="<?php echo esc_html($data['pack']['duration']) ?>" maxlength="10"/>
				<select name="packs[<?php echo ($data['pack_ctr']-1) ?>][duration_type]" style="width:100px">
				<?php foreach (mgm_get_class('subscription_packs')->duration_str as $value=>$text):
						  $selected = ($value == $data['pack']['duration_type'] ? 'selected="selected"':'');
						  echo '<option value="'. $value .'" '. $selected .'>'. $text .'</option>';
					  endforeach;?>
				</select>
			</td>
		</tr>		
		<tr>
			<td valign="top" align="left"><?php _e('Cost','mgm')?></td>
			<td valign="top" align="center">:</td>
			<td valign="top" align="left">
				<input type="text" size="10" name="packs[<?php echo ($data['pack_ctr']-1) ?>][cost]" value="<?php echo esc_html($data['pack']['cost']) ?>" maxlength="15"/>
			</td>
		</tr>		
		<tr>
			<td valign="top" align="left" ><?php _e('Billing','mgm')?></td>
			<td valign="top" align="center">:</td>
			<td valign="top" align="left">
				<select name="packs[<?php echo ($data['pack_ctr']-1) ?>][num_cycles]" style="width:80px">		
				<?php foreach (range(0, 99) as $i) :
						$name = (!$i ? __('Ongoing', 'mgm') : $i);
						echo '<option value="' . $i . '" ' . ($data['pack']['num_cycles'] == $i ? 'selected="selected"':'') . '>' . $name . '</option>';
				endforeach;?>
				</select>
			</td>
		</tr>
		<tr>
			<td valign="top" align="left" ><?php _e('Role','mgm')?></td>
			<td valign="top" align="center">:</td>
			<td valign="top" align="left">
				<select name="packs[<?php echo ($data['pack_ctr']-1) ?>][role]" style="width: 120px;">
				<?php						
				foreach ($data['roles'] as $role=>$name) {
					$selected = '';
					if ($data['pack']['role'] == $role) {
						$selected = 'selected="selected"';
					}
					echo '<option value="' . $role . '" ' . $selected . '>' . $name . '</option>';
				}			
				?>
				</select>	
			</td>
		</tr>	
		<tr>
			<td valign="top" align="left" ><?php _e('Default','mgm')?></td>
			<td valign="top" align="center">:</td>
			<td valign="top" align="left">
				<select name="packs[<?php echo ($data['pack_ctr']-1) ?>][default]" style="width:60px">
					<option value="1" <?php echo ($data['pack']['default'] ? 'selected="selected"':'') ?>><?php _e('Yes','mgm')?></option>
					<option value="0" <?php echo (!$data['pack']['default'] ? 'selected="selected"':'') ?>><?php _e('No','mgm')?></option>
				</select>
			</td>
		</tr>	
		<tr>
			<td valign="top" align="left" ><?php _e('Description','mgm')?></td>
			<td valign="top" align="center">:</td>
			<td valign="top" align="left">
				<textarea cols="50" rows="5" name="packs[<?php echo ($data['pack_ctr']-1) ?>][description]"><?php echo esc_html(stripslashes($data['pack']['description'])) ?></textarea>
			</td>
		</tr>
		<tr>
			<td valign="top" align="left" ><?php _e('Hide Private Content Prior to Join','mgm')?></td>
			<td valign="top" align="center">:</td>
			<td valign="top" align="left">
				<select name="packs[<?php echo ($data['pack_ctr']-1) ?>][hide_old_content]" style="width:60px">
					<option value="1" <?php echo ((int)$data['pack']['hide_old_content'] ? 'selected="selected"':'') ?>><?php _e('Yes','mgm')?></option>
					<option value="0" <?php echo (!(int)$data['pack']['hide_old_content'] ? 'selected="selected"':'') ?>><?php _e('No','mgm')?></option>
				</select>  
				<div class="tips"><?php _e('If selected Yes, members can access only the content which are published after their registration date.','mgm')?></div>
			</td>
		</tr>
		<tr>
			<td colspan="3" align="left" height="30" valign="top"><div class="subscription-heading"><?php _e('Display Settings','mgm') ?></div></td>
		</tr>	
		<tr>
			<td valign="top" align="left" ><?php _e('Active','mgm')?></td>
			<td valign="top" align="center">:</td>
			<td valign="top" align="left">
				<select name="packs[<?php echo ($data['pack_ctr']-1) ?>][active]" style="width:60px">
					<option value="1" <?php echo ($data['pack']['active'] ? 'selected="selected"':'') ?>><?php _e('Yes','mgm')?></option>
					<option value="0" <?php echo (!$data['pack']['active'] ? 'selected="selected"':'') ?>><?php _e('No','mgm')?></option>
				</select>
			</td>
		</tr>
		<tr>
			<td valign="top" align="left"><?php _e('Sort Order','mgm')?></td>
			<td valign="top" align="center">:</td>
			<td valign="top" align="left">
				<input type="text" size="10" name="packs[<?php echo ($data['pack_ctr']-1) ?>][sort]" value="<?php echo esc_html($data['pack']['sort']) ?>" maxlength="10"/>
			</td>
		</tr>					
		<?php if(!in_array($data['pack']['membership_type'], array('trial','free'))):
				 if ($data['supports_trial'] === true):?>
		<tr>
			<td colspan="3" align="left" height="30" valign="top"><div class="subscription-heading"><?php _e('Trial Settings','mgm') ?></div></td>
		</tr>
		<tr>
			<td valign="top" align="left"><?php _e('Use Trial','mgm')?></td>
			<td valign="top" align="center" >:</td>
			<td valign="top" align="left">
				<select name="packs[<?php echo ($data['pack_ctr']-1) ?>][trial_on]" onchange="mgm_toggle_trial(this)" style="width:60px">
					<option value="1" <?php echo ((int)$data['pack']['trial_on'] ? 'selected="selected"':'') ?>><?php _e('Yes','mgm')?></option>
					<option value="0" <?php echo (!(int)$data['pack']['trial_on'] ? 'selected="selected"':'') ?>><?php _e('No','mgm')?></option>
				</select>
			</td>
		</tr>
		<tr class="pack_trial_<?php echo ($data['pack_ctr']-1) ?>" <?php echo ((int)$data['pack']['trial_on'] ? '':'style="display:none"') ?> >
			<td valign="top" align="left"><?php _e('Trial Duration','mgm')?></td>
			<td valign="top" align="center">:</td>
			<td valign="top" align="left">
				<input size="5" type="text" name="packs[<?php echo ($data['pack_ctr']-1) ?>][trial_duration]" value="<?php echo (int)$data['pack']['trial_duration'] ?>" maxlength="10"/>
				<select name="packs[<?php echo ($data['pack_ctr']-1) ?>][trial_duration_type]" style="width:100px">
				<?php
				foreach (mgm_get_class('subscription_packs')->duration_str as $value=>$text) {
					$selected = ($value == $data['pack']['trial_duration_type'] ? 'selected="selected"':'');
					echo '<option value="'. $value .'" '. $selected .'>'. $text .'</option>';
				}?>	
				</select>
			</td>
		</tr>
		<tr class="pack_trial_<?php echo ($data['pack_ctr']-1) ?>" <?php echo ((int)$data['pack']['trial_on'] ? '':'style="display:none"') ?>>
			<td valign="top" align="left"><?php _e('Trial Cost','mgm')?></td>
			<td valign="top" align="center">:</td>
			<td valign="top" align="left">
				<input size="5" type="text" name="packs[<?php echo ($data['pack_ctr']-1) ?>][trial_cost]" value="<?php echo esc_html($data['pack']['trial_cost']) ?>" maxlength="10"/>
			</td>
		</tr>
		<tr class="pack_trial_<?php echo ($data['pack_ctr']-1) ?>" <?php echo ((int)$data['pack']['trial_on'] ? '':'style="display:none"') ?>>
			<td valign="top" align="left"><?php _e('Trial Occurrences','mgm')?></td>
			<td valign="top" align="center">:</td>
			<td valign="top" align="left">				
				<select name="packs[<?php echo ($data['pack_ctr']-1) ?>][trial_num_cycles]" style="width:80px">		
				<?php 
				foreach (range(1, 99) as $i) :
					echo '<option value="' . $i . '" ' . ($data['pack']['trial_num_cycles'] == $i ? 'selected="selected"':'') . '>' . $i . '</option>';
				endforeach;?>
				</select>
				<div class="tips" style="width:520px"><?php _e('Please use "Trial Occurrences" to configure number of times "Subscription Package Duration" to be treated as "Trial Period". Authorize.Net Payment Gateway requires you to set up "Trial Period" same as "Subscription Package Duration" .', 'mgm')?></div>
			</td>
		</tr>		
		<?php 
			endif; // end trial settings
		
			// post purchase settings
			if($data['payment_modules']):		
				foreach($data['payment_modules'] as $payment_module) :
					echo mgm_get_module($payment_module)->settings_subscription_package($data);
				endforeach;		
			endif;?>
		<tr>
			<td colspan="3" align="left" height="30" valign="top"><div class="subscription-heading"><?php _e('Module Settings','mgm') ?></div></td>
		</tr>
		<tr>
			<td valign="top" align="left"><?php _e('Use Module','mgm')?></td>
			<td valign="top" align="center" >:</td>
			<td valign="top" align="left">
				<?php if($data['payment_modules']):		
						foreach($data['payment_modules'] as $payment_module) :
							if(!in_array($payment_module, array('mgm_trial','mgm_free'))):
						?>
						<input type="checkbox" name="packs[<?php echo ($data['pack_ctr']-1) ?>][modules][]" value="<?php echo $payment_module?>" <?php echo (in_array($payment_module,(array)$data['pack']['modules']))?'checked':''?> /> <?php echo mgm_get_module($payment_module)->name?>
				<?php	
							endif;
						endforeach;		
				endif;?>
			</td>
		</tr>
		<?php elseif($data['pack']['membership_type'] == 'trial'):?>
		<tr>
			<td colspan="3" align="left" height="30" valign="top"><div class="subscription-heading"><?php _e('Module Settings','mgm') ?></div></td>
		</tr>
		<tr>
			<td valign="top" align="left"><?php _e('Use Module','mgm')?></td>
			<td valign="top" align="center" >:</td>
			<td valign="top" align="left">
				<?php if($data['payment_modules']):		
						foreach($data['payment_modules'] as $payment_module) :
							if(!in_array($payment_module, array('mgm_free'))):
						?>
						<input type="checkbox" name="packs[<?php echo ($data['pack_ctr']-1) ?>][modules][]" value="<?php echo $payment_module?>" <?php echo (in_array($payment_module,(array)$data['pack']['modules']))?'checked':''?> /> <?php echo mgm_get_module($payment_module)->name?>
				<?php	
							endif;
						endforeach;
				endif;?>
			</td>
		</tr>
		<?php endif; // end type free/trial check?>	
		<tr>
			<td colspan="3" align="left" height="30" valign="top"><div class="subscription-heading"><?php _e('Package Register URLs/Tag','mgm') ?></div></td>
		</tr>	
		<?php 
			// package					
			$package     = $data['pack']['membership_type'].'#'.$data['pack']['id'];	
			$package_enc = base64_encode($package);							
		?>
		<tr>			
			<td valign="top" align="left" colspan="3">		
				<div style="padding:10px">		
					<table width="100%" cellpadding="1" cellspacing="1" border="0" class="form-table widefat">
						<tr>
							<td width="15%"><?php _e('Custom URL','mgm')?></td>
							<td valign="top" align="center" width="5">:</td>
							<td><?php echo add_query_arg(array('method'=>'register','package'=>$package_enc), mgm_home_url('subscribe'));?></td>
						</tr>	
						<tr>
							<td><?php _e('Wordpress URL','mgm')?></td>
							<td valign="top" align="center" >:</td>
							<td><?php echo add_query_arg(array('package'=>$package_enc), site_url('wp-login.php?action=register', 'login'));?></td>
						</tr>
						<tr>						
							<td><?php _e('Tag','mgm')?></td>
							<td valign="top" align="center" >:</td>
							<td><?php echo sprintf('[user_register package=%s]',$package);?></td>		
						</tr>				
					</table>		
				</div>	
			</td>
		</tr>	
		<tr>
			<td colspan="3" height="10px">
				<p>					
					<a class="button" href="javascript:delete_pack('<?php echo $data['pack']['membership_type'] ?>','<?php echo $data['pack_ctr']?>')"><?php _e('Delete Package &raquo;','mgm') ?></a>
				</p>
			</td>
		</tr>			
	</table>		
</fieldset>
