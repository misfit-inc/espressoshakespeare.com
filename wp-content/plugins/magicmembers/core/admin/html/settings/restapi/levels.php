<form name="frmrestapilevels" id="frmrestapilevels" method="post" action="admin.php?page=mgm/admin/settings&method=restapi_levels">
	<table width="100%" cellpadding="1" cellspacing="0" border="0" class="widefat form-table">			
		<thead>
			<tr>
				<th scope="col"><b><?php _e('Level','mgm') ?></b></th>
				<th scope="col"><b><?php _e('Name','mgm') ?></b></th>
				<th scope="col"><b><?php _e('Permissions','mgm') ?></b></th>
				<th scope="col"><b><?php _e('Limits','mgm') ?></b></th>
				<th scope="col"><b><?php _e('Action','mgm') ?></b></th>
			</tr>
		</thead>
		<tbody>
		<?php if(count($data['levels'])>0): foreach($data['levels'] as $level):?>
			<tr class="<?php echo ($alt = ($alt=='') ? 'alternate': '');?>">					
			   <td>
					<?php echo $level->level ?>		   
			   </td>
			   <td>
					<?php echo $level->name ?>		   
			   </td>
			   <td>
					<?php if($permissions = implode(',',json_decode($level->permissions,true))): echo $permissions; else: _e('all','mgm'); endif; ?>		   
			   </td>
			   <td>
					<?php echo $level->limits ?>		   
			   </td>
			   <td>			   
				   <input class="button" name="edit" type="button" value="<?php _e('Edit', 'mgm') ?>" onclick="mgm_api_level_edit('<?php echo $level->id ?>');"/>
			   </td>	   
			</tr>
		<?php endforeach; else:?>
			<tr class="<?php echo ($alt = ($alt=='') ? 'alternate': '');?>">
				<td colspan="5" align="center">
				 <?php _e('No levels created','mgm')?>					 					
				</td>
			</tr>
		<?php endif;?>				
		</tbody>
	</table>
</form>