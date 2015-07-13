<!--purchase_statistics-->
<table width="100%" cellpadding="1" cellspacing="0" border="0" class="widefat form-table" >
	<thead>
		<tr>
			<th scope="col"><?php _e('Post Title','mgm')?></th>
			<th scope="col"><?php _e('Purchased','mgm')?></th>
		</tr>
	</thead>
	<tbody>
	<?php if($data['posts']):	foreach ($data['posts'] as $post) :?>
		<tr class="<?php echo ($alt = ($alt=='') ? 'alternate': '');?>">
			<td valign="top"><?php echo $post->title?></td>
			<td valign="top"><?php echo $post->count?></td>
		</tr>			
	<?php endforeach; else:?> 
		<tr>
			<td colspan="2" align="center"><?php _e('No posts have been sold yet','mgm')?></td>
		</tr>
	<?php endif;?>
	<tbody>
</table>
<br />