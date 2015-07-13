<?php mgm_box_top('All Downloads');?>
	<table width="100%" cellpadding="1" cellspacing="0" border="0" class="form-table widefat">	
		<thead>
			<tr>
				<th scope="col" width="30%"><b><?php _e('ID','mgm') ?></b></th>
				<th scope="col"><b><?php _e('Title','mgm') ?></b></th>
				<th scope="col"><b><?php _e('File','mgm') ?></b></th>
				<th scope="col"><b><?php _e('Limited Access','mgm') ?></b></th>
				<th scope="col"><b><?php _e('File Exists?','mgm') ?></b></th>
				<th scope="col"><b><?php _e('Expires','mgm') ?></b></th>
				<th scope="col"><b><?php _e('Posted','mgm') ?></b></th>
				<th scope="col"><b><?php _e('Action','mgm') ?></b></th>
			</tr>
		</thead>
		<tbody id="download_list">
		<?php
		if ($data['downloads']) :
			$wp_date_format = get_option('date_format');
			foreach ($data['downloads'] as $download) :
				$path = get_option('siteurl') . "/wp-content/uploads/";
				// real name
				if($download->real_filename){
					$file = $download->real_filename;
				}else{
					$file = str_replace($path, "", $download->filename);
				}
				$links = explode("/",$file);
				$file  = end($links);
				$user  = get_userdata($download->user_id);
				$file_url = mgm_get_file_url($download->filename);
				$expire_dt = intval($download->expire_dt)>0? date(MGM_DATE_FORMAT_SHORT, strtotime($download->expire_dt)):__('Never','mgm');
				$post_date = date($wp_date_format, strtotime($download->post_date));
				?>
			<tr class="<?php echo ($alt = ($alt=='') ? 'alternate': '');?>" id="row-<?php echo $download->id?>">
				<td><?php echo $download->id?></td>
				<td><?php echo $download->title?></td>
				<td><?php echo $file?></td>
				<td style="text-align:center; font-weight: bold;">
					<?php echo ($download->members_only =='Y' ? __('<span style="color: green;">Yes</span>', 'mgm') : __('<span style="color: red;">No</span>','mgm')) ?>
				</td>
				<td style="text-align:center; font-weight: bold;">
					<?php echo (file_exists($file_url) ? __('<span style="color: green;">Yes</span>', 'mgm'):__('<span style="color: red;">No</span>','mgm')) ?>
				</td>
				<td><?php echo $expire_dt?></td>
				<td><?php echo $post_date.' by '.$user->user_login?></td>
				<td style="line-height: 2em;">
					<input type="button" class="button" onclick="mgm_download_edit('<?php echo $download->id?>')" value="<?php _e('Edit', 'mgm') ?>" />
					<input type="button" class="button" onclick="mgm_download_delete('<?php echo $download->id?>')" value="<?php _e('Delete', 'mgm') ?>" />
				</td>
			</tr>		
		<?php
			endforeach;
		else:?>
			<tr>
				<td colspan="8"><?php _e('You haven\'t add any downloads yet.','mgm')?></td>
			</tr>
		<?php 
		endif;?>	
		</tbody>
		<tfoot>
			<tr>
				<td valign="middle" colspan="7">	
					<div style="float: left;">
						<input type="button" class="button" name="btn_adddl" onclick="mgm_download_add()" value="<?php echo __('Add New Download','mgm') ?> &raquo;" />					
					</div>							
				</td>
			</tr>
		</tfoot>	
	</table>
<?php mgm_box_bottom()?>