<?php if(count($data['posts_access'])>0): foreach($data['posts_access'] as $posts_access):?>
<tr class="<?php echo ($alt = ($alt=='') ? 'alternate': '');?>" id="posts_access_row_<?php echo $posts_access->id ?>">
	<td><?php echo !is_null($posts_access->post_id) ? get_post($posts_access->post_id)->post_title: __('N/A','mgm')?></td>
	<td><?php echo implode(', ',json_decode($posts_access->membership_types,true))?></td>
	<td valign="top" colspan="2" style="padding-top:10px">
		<input type="button" class="button" value="<?php _e('Delete','mgm')?>" onclick="mgm_delete_protected_url('<?php echo $posts_access->id ?>','posts_access')" />
	</td>
</tr>
<?php endforeach; else:?>
<tr class="<?php echo ($alt = ($alt=='') ? 'alternate': '');?>">
	<td colspan="3" align="center"><?php _e('No access settings','mgm')?></td>
</tr>
<?php endif;?>