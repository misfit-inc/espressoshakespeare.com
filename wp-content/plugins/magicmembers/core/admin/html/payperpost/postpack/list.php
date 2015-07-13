<table width="100%" cellpadding="1" cellspacing="0" border="0" class="widefat form-table">
	<thead>
		<tr>
			<th scope="col"><?php _e('ID','mgm')?></th>
			<th scope="col"><?php _e('Shortcode','mgm')?></th>
			<th scope="col"><?php _e('Name','mgm')?></th>
			<th scope="col"><?php _e('Cost','mgm')?></th>
			<th scope="col"><?php _e('Description','mgm')?></th>
			<th scope="col"><?php _e('Date Created','mgm')?></th>
			<th scope="col"><?php _e('Action','mgm')?></th>
		</tr>
	</thead>
	<tbody>
	<?php if($data['postpacks']): foreach ($data['postpacks'] as $i=>$postpack) :		
			// count
			$posts_count = mgm_get_postpack_posts($postpack->id, true); ?>
		<tr class="<?php echo ($alt = ($alt=='') ? 'alternate': '');?>" id="postpack_row_<?php echo $postpack->id ?>">
			<td width="5%"><?php echo $postpack->id ?></td>
			<td width="10%">[payperpost_pack#<?php echo $postpack->id ?>]</td>
			<td width="15%"><?php echo $postpack->name ?></td>
			<td width="10%"><?php echo mgm_format_currency($postpack->cost) . ' ' . $data['currency']?></td>
			<td width="15%"><?php echo $postpack->description ?></td>
			<td width="10%"><?php echo date(MGM_DATE_FORMAT_SHORT, strtotime($postpack->create_dt)) ?></td>
			<td width="30%">
				<input class="button" name="edit" type="button" value="<?php _e('Edit', 'mgm') ?>" onclick="mgm_postpack_edit('<?php echo $postpack->id ?>');"/>
				<input class="button" name="delete" type="button" value="<?php _e('Delete', 'mgm') ?>" onclick="mgm_postpack_delete('<?php echo $postpack->id ?>');"/>
				<input class="button" name="posts" type="button" value="<?php echo $posts_count . ' ' .__(($posts_count == 1 ? 'Post':'Posts'), 'mgm') ?>" onclick="mgm_postpack_posts('<?php echo $postpack->id ?>');"/>	 
			</td>
		</tr>
	<?php endforeach;else:?>
		<tr>
			<td colspan="6" align="center"><?php _e('You haven\'t created any postpack yet.','mgm')?></td>
		</tr>
	<?php endif;?>
	</tbody>
</table>	