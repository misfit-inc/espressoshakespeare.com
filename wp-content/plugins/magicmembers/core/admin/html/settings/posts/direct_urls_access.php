<?php if(count($data['direct_urls_access'])>0): foreach($data['direct_urls_access'] as $direct_urls_access):?>
<tr class="<?php echo ($alt = ($alt=='') ? 'alternate': '');?>" id="direct_urls_access_row_<?php echo $direct_urls_access->id ?>">
	<td><?php echo $direct_urls_access->url?></td>
	<td><?php echo implode(', ',json_decode($direct_urls_access->membership_types,true))?></td>
	<td valign="top" colspan="2" style="padding-top:10px">
		<input type="button" class="button" value="<?php _e('Delete','mgm')?>" onclick="mgm_delete_protected_url('<?php echo $direct_urls_access->id ?>','direct_urls_access')" />
	</td>
</tr>
<?php endforeach; else:?>
<tr class="<?php echo ($alt = ($alt=='') ? 'alternate': '');?>">
	<td colspan="3" align="center"><?php _e('No access settings','mgm')?></td>
</tr>
<?php endif;?>