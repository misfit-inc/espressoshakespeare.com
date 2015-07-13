<!--setup-->
<div id="post_settings_message"></div>
<?php mgm_box_top('Manage Post/Page(s) Access & Protection Settings');?>
	<form name="frmpostsaccess" id="frmpostsaccess" method="post" action="admin.php?page=mgm/admin/settings&method=post_settings_delete">		
		<table width="100%" cellpadding="1" cellspacing="0" border="0" class="widefat form-table">
			<thead>
				<tr>
					<th scope="col"><b><?php _e('Post/Page','mgm')?></b></th>	
					<th scope="col"><b><?php _e('Memberships','mgm')?></b></th>	
					<th scope="col"><b><?php _e('Action','mgm')?></b></th>				
				</tr>
			</thead>
			<tbody id="posts_access_list">
				<?php include('posts/posts_access.php');?>
			</tbody>
		</table>
	</form>
<?php mgm_box_bottom();?>

<?php mgm_box_top('Manage Direct URL Access & Protection Settings');?>
	<form name="frmdirecturlsaccess" id="frmdirecturlsaccess" method="post" action="admin.php?page=mgm/admin/settings&method=post_settings_delete">
		<table width="100%" cellpadding="1" cellspacing="0" border="0" class="widefat form-table">
			<thead>
				<tr>
					<th scope="col"><b><?php _e('URL','mgm')?></b></th>	
					<th scope="col"><b><?php _e('Memberships','mgm')?></b></th>	
					<th scope="col"><b><?php _e('Action','mgm')?></b></th>				
				</tr>
			</thead>
			<tbody id="direct_urls_access_list">
				<?php include('posts/direct_urls_access.php');?>
			</tbody>
		</table>
	</form>
<?php mgm_box_bottom();?>
			
<?php mgm_box_top('Add/Edit Post/Page(s) Access & Protection Settings');?>
	<form name="frmsetupposts" id="frmsetupposts" method="post" action="admin.php?page=mgm/admin/settings&method=posts">
		<table width="100%" cellpadding="1" cellspacing="0" border="0">
			<tr>
				<td valign="top" style="border-bottom:1px solid #D7E5EE; height:25px;" width="20px">
					<input type="checkbox" name="check_all" value="access_membership_types[]" title="<?php _e('Select all','mgm'); ?>" /> 				
				</td>
				<td valign="top"  <?php if(count($data['posts']) == 0):?>colspan="2"<?php endif;?> style="border-bottom:1px solid #D7E5EE; height:25px; ">
					<b><?php _e('Please select one or more membership type','mgm'); ?>:</b>
				</td>
			</tr>
			<tr>
				<td valign="top" colspan="2" style="padding-top:10px">
					<?php if(count($data['membership_types']) > 0):
						echo mgm_make_checkbox_group('access_membership_types[]', $data['membership_types'], '', MGM_KEY_VALUE);
					else:
						echo __('Sorry, no membership types available.', 'mgm');
					endif;?>
				</td>
			</tr>
			<tr>
				<td colspan="2" height="10px">&nbsp;</td>
			</tr>
			<tr>
				<?php if(count($data['posts']) > 0):?>
				<td valign="top" style="border-bottom:1px solid #D7E5EE; height:25px; " width="20px">				
					<input type="checkbox" name="check_all" value="posts[]" <?php _e('Select all','mgm'); ?>/> 				
				</td>
				<?php endif;?>
				<td valign="top" <?php if(count($data['posts']) == 0):?>colspan="2"<?php endif;?> style="border-bottom:1px solid #D7E5EE; height:25px; ">
					<b><?php _e('Please select one or more POSTS to attach the selected membership types','mgm'); ?>:</b>
				</td>
			</tr>			
			<tr>
				<td valign="top" colspan="2" style="padding-top:10px" >
					<?php if(count($data['posts']) == 0): _e('There are no posts in the database or all marked as private.', 'mgm'); else:?>	
					<?php $post_chunks = array_chunk($data['posts'], ceil(count($data['posts'])/2), true);?>				
					<table width="100%" cellpadding="1" cellspacing="0" border="0">					
						<tr>
							<td width="50%">
								<?php if(isset($post_chunks[0])): foreach($post_chunks[0] as $post_id => $post_title):?>
								<input type="checkbox" name="posts[]" value="<?php echo $post_id?>" />
								<?php echo mgm_ellipsize($post_title,50)?> <br />
								<?php endforeach; endif;?>
							</td>
							<td>
								<?php if(isset($post_chunks[1])): foreach($post_chunks[1] as $post_id => $post_title):?>
								<input type="checkbox" name="posts[]" value="<?php echo $post_id?>" />
								<?php echo mgm_ellipsize($post_title,50)?> <br />
								<?php endforeach; endif;?>
							</td>
						</tr>					
					</table>						
					<?php endif;?>
				</td>
			</tr>
			<tr>
				<td colspan="2" height="10px">&nbsp;</td>
			</tr>
			<tr>
				<?php if(count($data['pages']) > 0):?>
				<td valign="top" style="border-bottom:1px solid #D7E5EE; height:25px; " width="20px">				
					<input type="checkbox" name="check_all" value="pages[]" title="<?php _e('Select all','mgm'); ?>" /> 				
				</td>
				<?php endif;?>
				<td valign="top" <?php if(count($data['pages']) == 0):?>colspan="2"<?php endif;?> style="border-bottom:1px solid #D7E5EE; height:25px; ">
					<b><?php _e('Please select one or more PAGES to attach the selected membership types','mgm'); ?>:</b>
				</td>
			</tr>			
			<tr>
				<td valign="top" colspan="2" style="padding-top:10px">
					<?php if(count($data['pages']) == 0): _e('There are no pages in the database or all marked as private.', 'mgm'); else:?>	
					<?php $post_chunks = array_chunk($data['pages'], ceil(count($data['pages'])/2), true);?>				
					<table width="100%" cellpadding="1" cellspacing="0" border="0">					
						<tr>
							<td width="50%">
								<?php if(isset($post_chunks[0])): foreach($post_chunks[0] as $post_id => $post_title):?>
								<input type="checkbox" name="pages[]" value="<?php echo $post_id?>" />
								<?php echo mgm_ellipsize($post_title,50)?> <br />
								<?php endforeach; endif;?>
							</td>
							<td>
								<?php if(isset($post_chunks[1])): foreach($post_chunks[1] as $post_id => $post_title):?>
								<input type="checkbox" name="pages[]" value="<?php echo $post_id?>" />
								<?php echo mgm_ellipsize($post_title,50)?> <br />
								<?php endforeach; endif;?>
							</td>
						</tr>					
					</table>					
					<?php endif;?>				
				</td>
			</tr>	
			<tr>
				<td colspan="2" height="10px">&nbsp;</td>
			</tr>
			<tr>			
				<td valign="top" colspan="2"style="border-bottom:1px solid #D7E5EE; height:25px; ">
					<b><?php _e('Please add Direct URLs to attach the selected membership types','mgm'); ?>:</b>
				</td>
			</tr>
			<tr>
				<td colspan="2">					
					<table>	
						<tr>
							<td valign="top" style="padding-top:10px">
								<b><?php _e('New URL','mgm');?>:</b> 
							</td>
							<td valign="top" style="padding-top:10px">								
								<input type="text" name="direct_urls[0]" id="direct_urls_0" size="100" value="" />								
							</td>
						</tr>
					</table>
				</td>
			</tr>		
			<tr>
				<td colspan="2" height="10px">
					<div class="tips"><?php _e('<b>Available Wildcards:</b> All Sub pages - [URL]<b>:any</b> OR [URL]<b>*</b>')?></div>
				</td>
			</tr>
			<?php if(mgm_protect_content() == false):?>
			<tr>
				<td valign="middle" height="50" colspan="2">				
					<div class="information"><?php echo sprintf(__('<a href="%s">Content Protection</a> is <b>%s</b>. Make sure its enabled to Protect Post/Page(s).','mgm'), 'javascript:mgm_set_tab_url(2,0)', (mgm_protect_content() ? 'enabled' :'disabled'))?></div>
				</td>
			</tr>	
			<?php endif;?>
			<tr>
				<td colspan="2">
					<p class="submit" style="float:left;">
						<?php if (count($data['membership_types']) && (count($data['posts']) || count($data['pages'])) ) :?>
						<input type="button" name="btn_setup_posts" value="<?php _e('Setup Posts','mgm') ?> &raquo;" onclick="mgm_setup_posts()" />
						<?php endif;?>	
						<!--<input type="button" name="btn_undo_setup_posts" value="&laquo; <?php _e('Undo','mgm') ?> " onclick="mgm_setup_posts('Y')" />-->
						<!--<input type="button" name="manage_posts" value="<?php _e('Manage','mgm') ?> &raquo;" onclick="mgm_manage_posts()" />-->
					</p>
				</td>
			</tr>			
		</table>		
		<input type="hidden" name="post_setup_save" value="true" />	
		<!--<input type="hidden" name="undo_post_setup" value="N" />-->		
	</form>
<?php mgm_box_bottom();?>
<script language="javascript">
	<!--
	jQuery(document).ready(function(){			
		// post_setup
		mgm_setup_posts= function(undo){
			// undo
			var undo = undo || 'N';
			// set
			jQuery("#frmsetupposts :input[name='undo_post_setup']").val(undo);
			// add : form validation
			jQuery("#frmsetupposts").validate({
				submitHandler: function(form) {					    					
					jQuery("#frmsetupposts").ajaxSubmit({type: "POST",										  
					  dataType: 'json',		
					  iframe: false,									 
					  beforeSubmit: function(){	
						// show message
						mgm_show_message('#frmsetupposts', {status:'running', message:'<?php _e('Processing','mgm')?>...'},true);													
					  },
					  success: function(data){	
					  	// show message
						mgm_show_message('#frmsetupposts', data);
						// reset 
						jQuery('#direct_urls_0').val('');
						jQuery("#frmsetupposts :input[type='checkbox']").attr('checked', false);
						// reload
						mgm_load_posts_access_list();	
						mgm_load_direct_urls_access_list();												
					  }}); // end   		
					return false;											
				},			
				errorClass: 'invalid'
			});	
			// trigger
			jQuery('#frmsetupposts').submit();
		}
		// load posts_access_list
		mgm_load_posts_access_list=function(){
			// html
			_html = '<tr><td colspan="3" height="25" valign="middle"><img src="<?php echo MGM_ASSETS_URL?>images/ajax/fb-loader.gif"> <?php _e('Refreshing...','mgm')?></td></tr>';
			// load
			jQuery('#posts_access_list').html(_html).load('admin.php?page=mgm/admin/settings&method=post_posts_access_list');
		}
		// load direct_urls_access_list
		mgm_load_direct_urls_access_list=function(){
			// html
			_html = '<tr><td colspan="3" height="25" valign="middle"><img src="<?php echo MGM_ASSETS_URL?>images/ajax/fb-loader.gif"> <?php _e('Refreshing...','mgm')?></td></tr>';
			// load
			jQuery('#direct_urls_access_list').html(_html).load('admin.php?page=mgm/admin/settings&method=post_direct_urls_access');
		}
		// delete
		mgm_delete_protected_url=function(id, type){
			if (confirm("<?php _e('Are you sure you want to delete selected access setting?', 'mgm')?>")) {
				jQuery.ajax({url:'admin.php?page=mgm/admin/settings&method=post_settings_delete', type: 'POST', dataType: 'json', cache: false, data :{id: id}, 
				 beforeSend: function(){	
					// show message
					mgm_show_message('#post_settings_message', {status:'running', message:'<?php _e('Processing','mgm')?>...'},true);									
				 },
				 success:function(data){
					// show message
					mgm_show_message('#post_settings_message', data);																						
					// success	
					if(data.status=='success'){																																			
						// delete row
						jQuery('#'+type+'_row_'+id).remove();											
					}
				 }
				});
			}
		}
		// check bind
		jQuery("#frmsetupposts :checkbox[name='check_all']").bind('click',function(){
			var checked = (jQuery(this).attr('checked') == 'checked') ? true : false;
			// switch checked state
			jQuery("#frmsetupposts :checkbox[name='"+jQuery(this).val()+"']").attr('checked', checked);			
		});	
	});
	//-->
</script>