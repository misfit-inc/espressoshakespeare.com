<!--purchase_gifts-->
<table width="100%" cellpadding="1" cellspacing="0" border="0" class="widefat form-table">
	<thead>
		<tr>
			<th scope="col"><?php _e('Member','mgm')?></th>
			<th scope="col"><?php _e('Post','mgm')?></th>
			<th scope="col"><?php _e('Type','mgm')?></th>
			<th scope="col"><?php _e('Expire Date','mgm')?></th>
			<th scope="col"><?php _e('Purchase/Gift Date','mgm')?></th>				
			<th scope="col"><?php echo _e('Action', 'mgm') ?></th>
		</tr>
	</thead>
	<tbody>	
	<?php if($data['posts']):	foreach ($data['posts'] as $post) :		
			// check is_expiry
			if($post->is_expire == 'N'):
				$expiry = __('indefinite', 'mgm');	
			else:
				$expiry = mgm_get_post($post->post_id)->get_access_duration();
				$expiry = (!$expiry) ? __('indefinite', 'mgm') : (date('d/m/Y',(86400*$expiry) + strtotime($post->purchase_dt)) . " (" . $expiry . __(' D','mgm').")");	
			endif;	
		?>
		<tr class="<?php echo ($alt = ($alt=='') ? 'alternate': '');?>" id="post_purchase_row_<?php echo $post->id ?>">
			<td valign="top"><?php echo $post->user_login ?></td>
			<td valign="top"><?php echo $post->post_title ?></td>
			<td valign="top"><?php echo $post->is_gift =='Y'? __('Gift','mgm') : __('Purchase','mgm') ?></td>
			<td valign="top"><?php echo $expiry ?></td>
			<td valign="top"><?php echo date('d/m/Y', strtotime($post->purchase_dt)) ?></td>
			<td valign="top">
				<input class="button" name="delete" type="button" value="<?php _e('Delete', 'mgm') ?>" onclick="mgm_purchase_delete('<?php echo $post->id ?>');"/>						
			</td>
		</tr>
	<?php endforeach; else:?> 
		<tr>
			<td colspan="6" align="center"><?php _e('No posts have been sold/gifted yet','mgm')?></td>
		</tr>
	<?php endif;?>
	</tbody>	
</table>
<br />
<script language="javascript">
<!--	
	jQuery(document).ready(function(){
		// delete
		mgm_purchase_delete=function(id) {
			if (confirm("<?php _e('Are you sure you want to delete this purchase record?', 'mgm') ?>")) {
				jQuery.ajax({url:'admin.php?page=mgm/admin/payperpost&method=post_purchase_delete', 
							 type: 'POST', 
							 dataType: 'json', 
							 data :{id: id}, 
							 cache: false,
							 beforeSend: function(){	
							 	// show message
								mgm_show_message('#post_purchase_gifts', {status:'running', message:'<?php _e('Processing','mgm')?>...'},true);
							 },
							 success:function(data){
								// message																				
								mgm_show_message('#post_purchase_gifts', data);											
																				
								// success	
								if(data.status=='success'){																																
									// delete row
									// jQuery('#post_purchase_row_'+id).remove();	
									// reload
									mgm_post_purchase_gifts();										
								}
							 }});
			}
		}
	});	
//-->
</script>