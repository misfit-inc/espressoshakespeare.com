<!--access-->
<div id="content_access">
	<form name="frmaccss" id="frmaccss" action="admin.php?page=mgm/admin/contents&method=access" method="post">	
		<?php /*?><?php mgm_box_top('Registration Control')?>
			<table id="regctl-table" width="100%">
				<tr>
					<td><p><?php _e('Would you like to use modified registration process?','mgm'); ?></p></td>
				</tr>
				<tr>
					<td valign="top">
						<b><input type="radio" name="modified_registration" value="Y" <?php if ($data['system']->setting['modified_registration'] == 'Y') { echo 'checked="true"'; } ?>/> <?php _e('Yes','mgm')?></b>
						<b><input type="radio" name="modified_registration" value="N"  <?php if ($data['system']->setting['modified_registration'] == 'N') { echo 'checked="true"'; } ?>/> <?php _e('No','mgm')?></b>
					</td>
				</tr>
			</table>
		<?php mgm_box_bottom()?><?php */?>
		
		<?php mgm_box_top('Full Content Protection Settings')?>	
			<table id="fllctpset-table" width="100%">
				<tr>
					<td colspan="2"><p><?php _e('Would you like to hide all of your content?','mgm'); ?></p></td>
				</tr>
				<tr>
					<td width="20%" valign="top"><strong><?php _e('Content Protection','mgm'); ?></strong></td>
					<td align="left" valign="top">
						<b><input type="radio" name="content_protection" value="full" <?php if ($data['system']->setting['content_protection'] == 'full') { echo 'checked="true"'; } ?>/> <?php _e('FULL','mgm')?></b>
						<b><input type="radio" name="content_protection" value="partly"  <?php if ($data['system']->setting['content_protection'] == 'partly') { echo 'checked="true"'; } ?>/> <?php _e('PARTLY','mgm')?></b>
						<b><input type="radio" name="content_protection" value="none"  <?php if ($data['system']->setting['content_protection'] == 'none') { echo 'checked="true"'; } ?>/> <?php _e('NONE','mgm')?></b>
						<div id="content_protection_partly" style="display:<?php echo ($data['system']->setting['content_protection'] == 'partly') ? '' :'none'; ?>; padding:10px 0 10px 0">
							<?php _e('Word Limit for Public Access','mgm'); ?>: <input type="text" size="10" value="<?php echo $data['system']->setting['public_content_words']?>" name="public_content_words"/> 
						</div>
					</td>
				</tr>				
				<tr>
					<td colspan="2" valign="top">
						<div class="tips" style="width:97%">
							<?php _e('<p><strong>Protect your contents</strong> <br />
							             <u>FULL</u> = Protects contents automatically. All Contents will be protected and users have to login before viewing. [private] tags added manually will be honored.<br /><br> 
										 <u>PARTLY</u> = Protects contents automatically. Part of the content will be free and rest will be viewable after login. [private] tags added manually will be honored.<br><br>
										 <u>NONE</u> =  No protection will be applied. Any settings in post/page setup and [private] tags added manually will be disregarded unless post/page is set as Purchasable.<br><br>										 
										 Purchasable Post will always require login irrespective of Content Protection settings.
									 </p>','mgm'); ?>
						</div>
					</td>	
				</tr>
				<tr><td colspan="2" height="10"></td></tr>
				<tr>					
					<td align="left" valign="top" colspan="2">						
						<b><?php _e('Extended Content Protection:','mgm'); ?></b><br /><br />
						<?php _e('Hide Content By Membership','mgm'); ?>
						<b><input type="radio" name="content_hide_by_membership" value="Y" <?php if ($data['system']->setting['content_hide_by_membership'] == 'Y') { echo 'checked="true"'; } ?>/> <?php _e('Yes','mgm')?></b>
						<b><input type="radio" name="content_hide_by_membership" value="N"  <?php if ($data['system']->setting['content_hide_by_membership'] == 'N') { echo 'checked="true"'; } ?>/> <?php _e('No','mgm')?></b>
					</td>
				</tr>
				<tr>
					<td colspan="2" valign="top">
						<div class="tips" style="padding-top:10px;width:97%">
							<?php _e('<p>Controls wheather posts/page are hidden by current user membership type.</p>','mgm'); ?>
						</div>
					</td>	
				</tr>
			</table>
		<?php mgm_box_bottom()?>
		
		<?php mgm_box_top('Private Tag Redirection Settings')?>
			<table id="privtagredir-table" width="100%">
				<tr>
					<td colspan="2">
						<p><?php _e('You can use the following options to override the no access messages normally shown between [private][/private] tags.','mgm'); ?></p>
					</td>
				</tr>
				<tr>
					<td width="30%" valign="top"><?php _e('No access URL for logged in users','mgm'); ?></td>
					<td align="left" valign="top">
						<input type="text" size="90" name="no_access_redirect_loggedin_users" value="<?php echo esc_html($data['system']->setting['no_access_redirect_loggedin_users']); ?>" /> <br />
						<div class="tips">(<?php _e("Don't forget to type HTTP if it's an external link",'mgm'); ?>)</div>
					</td>
				</tr>
				<tr>
					<td valign="top">
						<?php _e('No access URL for logged out users','mgm'); ?>
					</td>
					<td valign="top">
						<input type="text" size="90" name="no_access_redirect_loggedout_users" value="<?php echo esc_html($data['system']->setting['no_access_redirect_loggedout_users']); ?>" /><br />
						<div class="tips">(<?php _e("Don't forget to type HTTP if it's an external link",'mgm'); ?>)</div>
					</td>
				</tr>
				<tr>
					<td valign="top">
						<?php _e('Would you like to redirect users on homepage?','mgm'); ?>
					</td>
					<td valign="top">
						<b><input type="radio" name="redirect_on_homepage" value="Y" <?php if ($data['system']->setting['redirect_on_homepage'] == 'Y') { echo 'checked="true"'; } ?>/> <?php _e('Yes','mgm') ?></b>
						<b><input type="radio" name="redirect_on_homepage" value="N"  <?php if ($data['system']->setting['redirect_on_homepage'] == 'N') { echo 'checked="true"'; } ?>/> <?php _e('No','mgm') ?></b>
					</td>
				</tr>
			</table>
		<?php mgm_box_bottom()?>
		
		<?php mgm_box_top('RSS Token Settings')?>	
			<table id="regctl-table" width="100%">
				<tr>
					<td><p><?php _e('Activate RSS Token - If selected "No", full content view in RSS feeds will be disabled for members only content.','mgm'); ?></p></td>
				</tr>
				<tr>
					<td valign="top">
						<b><input type="radio" name="use_rss_token" value="Y" <?php if ($data['system']->setting['use_rss_token'] == 'Y') { echo 'checked="true"'; } ?>/> <?php _e('Yes','mgm') ?></b>
						<b><input type="radio" name="use_rss_token" value="N"  <?php if ($data['system']->setting['use_rss_token'] == 'N') { echo 'checked="true"'; } ?>/> <?php _e('No','mgm') ?></b>
					</td>
				</tr>
			</table>
		<?php mgm_box_bottom()?>
		
		<p class="submit">
			<input type="submit" name="update" value="<?php _e('Save','mgm') ?> &raquo;" />
		</p>
	</form>	
</div>	
<script language="javascript">
	<!--
	// onready
	jQuery(document).ready(function(){
		// add : form validation
		jQuery("#frmaccss").validate({
			submitHandler: function(form) {   
				jQuery("#frmaccss").ajaxSubmit({type: "POST",
				  url: 'admin.php?page=mgm/admin/contents&method=access',
				  dataType: 'json',			
				  iframe: false,								 
				  beforeSubmit: function(){	
				  	// show message
					mgm_show_message('#content_access', {status:'running', message:'<?php _e('Processing','mgm')?>...'});					
					// focus
					jQuery.scrollTo('#frmaccss',400);
				  },
				  success: function(data){	
					// message																				
					mgm_show_message('#content_access', data);																	
				  }});// end 
				  return false;															
			}
		});			
		
		// attach
		jQuery("#frmaccss :radio[name='content_protection']").bind('click',function(){
			if(jQuery(this).val() == 'partly'){
				jQuery('#content_protection_partly').slideDown();
			}else{
				jQuery('#content_protection_partly').hide();
			}
		});				  
	});	
	//-->
</script>