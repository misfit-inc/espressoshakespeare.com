<tr class="<?php echo ($alt = ($alt=='') ? 'alternate': '');?>" id="<?php echo $active ? 'active_':'inactive_' ?>userfield_row_<?php echo $id ?>">
	<td width="2%"><input type="checkbox" name="userfields[]" value="<?php echo $id ?>" <?php echo $active ? 'checked':'' ?>></td>	
	<td width="25%"><?php echo mgm_stripslashes_deep($field['label']) ?></td>
	<td width="25%"><?php echo $field['name'] ?></td>
	<td width="10%"><?php echo $field['type'] ?></td>
	<td width="20%"><!--href="javascript:mgm_toggle_uf('others_<?php echo $id ?>')" -->
		<a href="javascript://" rel="#others_<?php echo $id ?>">Show</a>
		<div id="others_<?php echo $id ?>" class="apple_overlay">
			<table width="100%" cellpadding="1" cellspacing="0" border="0" class="widefat form-table">
				<?php if($field['type'] != 'html'): ?>
				<tr><td align="right" style="font-weight:bold"><?php _e('Required?','mgm')?></td><td> <?php echo ($field['attributes']['required']==true) ? 'Yes' : 'No'; ?></td></tr>
				<tr><td align="right" style="font-weight:bold"><?php _e('ReadOnly?','mgm')?> </td><td> <?php echo ($field['attributes']['readonly']==true) ? 'Yes' : 'No';?></td></tr>
				<tr><td align="right" style="font-weight:bold"><?php _e('Hide Label?','mgm')?> </td><td> <?php echo ($field['attributes']['hide_label']==true) ? 'Yes' : 'No';?></td></tr>
				<?php endif; ?>
				<tr><td align="right" style="font-weight:bold"><?php _e('Register?','mgm')?></td><td> <?php echo ($field['display']['on_register']==true) ? 'Yes' : 'No';?></td></tr>				
				<?php if($field['type'] != 'html'): ?>				
				<tr><td align="right" style="font-weight:bold"><?php _e('Profile?','mgm')?></td><td> <?php echo ($field['display']['on_profile']==true) ? 'Yes' : 'No';?></td></tr>				
				<tr><td align="right" style="font-weight:bold"><?php _e('Payment?','mgm')?></td><td> <?php echo ($field['display']['on_payment']==true) ? 'Yes' : 'No';?></td></tr>
				<tr><td align="right" style="font-weight:bold"><?php _e('Public Profile?','mgm')?></td><td> <?php echo ($field['display']['on_public_profile']==true) ? 'Yes' : 'No';?></td></tr>
				<?php endif; ?>
				
				<?php if($field['name'] == 'coupon'): ?>	
				<tr><td align="right" style="font-weight:bold"><?php _e('Upgrade?','mgm')?></td><td> <?php echo ($field['display']['on_upgrade']==true) ? 'Yes' : 'No';?></td></tr>				
				<tr><td align="right" style="font-weight:bold"><?php _e('Extend?','mgm')?></td><td> <?php echo ($field['display']['on_extend']==true) ? 'Yes' : 'No';?></td></tr>
				<tr><td align="right" style="font-weight:bold"><?php _e('Post Purchase?','mgm')?></td><td> <?php echo ($field['display']['on_postpurchase']==true) ? 'Yes' : 'No';?></td></tr>								
				<?php endif; ?>
			</table>
			<br />
		</div>
	</td>	
	<td width="20%">	
		<?php 
		switch($field['name']):		
			/*case 'terms_conditions':
			case 'subscription_introduction':?>
				<input class="button" name="edit" type="button" value="<?php _e('Setup', 'mgm') ?>" onClick="mgm_set_tab_url(5, 2);"/>
				<input class="button" name="edit" type="button" value="<?php _e('Edit', 'mgm') ?>" onClick="mgm_userfield_edit('<?php echo $id ?>');"/>
			<?php
			break;
			?>
				<input class="button" name="edit" type="button" value="<?php _e('Setup', 'mgm') ?>" onClick="mgm_set_tab_url(1, 1);"/>
				<input class="button" name="edit" type="button" value="<?php _e('Edit', 'mgm') ?>" onClick="mgm_userfield_edit('<?php echo $id ?>');"/>
			<?php
			break;*/
			case 'subscription_options':?>
				<input class="button" name="setup" type="button" value="<?php _e('Setup', 'mgm') ?>" onClick="mgm_set_tab_url(1, 1);" title="<?php _e('Setup Subscriptions','mgm')?>"/>
				<input class="button" name="edit" type="button" value="<?php _e('Edit', 'mgm') ?>" onClick="mgm_userfield_edit('<?php echo $id ?>');"/>
			<?php	
			break;	
			case 'autoresponder':?>
				<input class="button" name="setup" type="button" value="<?php _e('Setup', 'mgm') ?>" onClick="mgm_set_tab_url(5, 4);" title="<?php _e('Setup Autoresponders','mgm')?>"/>
				<input class="button" name="edit" type="button" value="<?php _e('Edit', 'mgm') ?>" onClick="mgm_userfield_edit('<?php echo $id ?>');"/>
			<?php
			break;				
			default:?>
				<input class="button" name="edit" type="button" value="<?php _e('Edit', 'mgm') ?>" onClick="mgm_userfield_edit('<?php echo $id ?>');"/>
				<?php if($field['system'] == true) :?>
				<span style="color:#FF0000; font-weight:bold"><?php _e('System', 'mgm')?></span>
		   		<?php elseif($field['system'] == false) :?>
					<input class="button" name="delete" type="button" value="<?php _e('Delete', 'mgm') ?>" onClick="mgm_userfield_delete('<?php echo $id ?>');"/>
		   		<?php endif;?>
			<?php	
		    break;
		endswitch;?>
	</td>
</tr>