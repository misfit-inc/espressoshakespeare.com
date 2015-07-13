<form name="frmrestapikeys" id="frmrestapikeys" method="post" action="admin.php?page=mgm/admin/settings&method=restapi_keys">
	<table width="100%" cellpadding="1" cellspacing="0" border="0" class="widefat form-table">			
		<thead>
			<tr>
				<th scope="col"><b><?php _e('Api Key','mgm') ?></b></th>
				<th scope="col"><b><?php _e('Level','mgm') ?></b></th>
				<th scope="col"><b><?php _e('Create Date','mgm') ?></b></th>
				<th scope="col"><b><?php _e('Action','mgm') ?></b></th>
			</tr>
		</thead>
		<tbody>
		<?php if(count($data['keys'])>0): foreach($data['keys'] as $key):?>
			<tr class="<?php echo ($alt = ($alt=='') ? 'alternate': '');?>">					
			   <td>
					<?php echo $key->api_key?>		   
			   </td>
			   <td>
					<?php echo $key->level?>		   
			   </td>			   
			   <td>
					<?php echo $key->create_dt?>		   
			   </td>
			   <td>			   
				   <input class="button" name="edit" type="button" value="<?php _e('Edit', 'mgm') ?>" onclick="mgm_api_key_edit('<?php echo $key->id ?>');"/>
			   </td>	   
			</tr>
		<?php endforeach; else:?>
			<tr class="<?php echo ($alt = ($alt=='') ? 'alternate': '');?>">
				<td colspan="4" align="center">
				 <?php _e('No keys created','mgm')?>					 					
				</td>
			</tr>
		<?php endif;?>				
		</tbody>
	</table>
</form>