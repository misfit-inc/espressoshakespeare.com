<div style="margin-bottom: 10px;">
	<table width="100%" cellpadding="1" cellspacing="0" border="0" class="widefat form-table">
		<thead>
			 <tr>
				<th scope="col"><?php _e('Post Title','mgm')?></th>
				<th scope="col"><?php _e('Date Added','mgm')?></th>
				<th scope="col" style="width: 150px;"><?php _e('Action','mgm')?></th>
			</tr>
		</thead>
		<tbody>
		<?php if($data['postpack_posts']):
				foreach ($data['postpack_posts'] as $i=>$postpack) :
    	        $post = get_post($postpack->post_id);?>
			<tr class="<?php echo ($alt = ($alt=='') ? 'alternate': '');?>" id="ppp_row_<?php echo $postpack->id ?>">
                <td><?php echo $post->post_title ?></td>
                <td><?php echo date('d/m/Y H:i', strtotime($postpack->create_dt)) ?></td>
                <td><input onclick="mgm_postpack_post_delete(<?php echo $postpack->id ?>);" class="button" type="button" value="<?php _e('Delete', 'mgm') ?>" /></td>
            </tr>
			<?php
				endforeach;			
			else:?>
			<tr>
				<td colspan="3"><?php _e('There are currently no posts associated to this pack.','mgm')?></td>
			</tr>
		<?php 
		endif;?>
		</tbody>
	</table>
</div>