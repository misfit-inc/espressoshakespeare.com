<!--general-->
<form name="frmsetgen" id="frmsetgen" method="post" action="admin.php?page=mgm/admin/settings&method=general">
	<?php mgm_box_top('Main Settings');?>
		<table width="100%" cellpadding="1" cellspacing="0" border="0">			
			<tr>
				<td valign="top"><p><b><?php _e('Administrator Email Address','mgm'); ?>:</b></p></td>
			</tr>
			<tr>
				<td valign="top">
					<input type="text" name="admin_email" value="<?php echo esc_html($data['system']->setting['admin_email']); ?>" size="50" />
					<p><div class="tips width90"><?php _e('Enter the email address where you will receive the notifications.','mgm'); ?></div></p>
				</td>
			</tr>						
			<tr>
				<td valign="top"><p><b><?php _e('Redirect on login','mgm'); ?>:</b></p></td>
			</tr>
			<tr>
				<td valign="top">
					<input type="text" name="login_redirect_url" value="<?php echo esc_html($data['system']->setting['login_redirect_url']); ?>" size="100" />
					<p><div class="tips width90"><?php _e('The link that the login page sends the user to (please leave blank for admin).','mgm'); ?></div></p>
				</td>
			</tr>
			<tr>
				<td valign="top"><p><b><?php _e('Redirect on logout','mgm'); ?>:</b></p></td>
			</tr>
			<tr>
				<td valign="top">
					<input type="text" name="logout_redirect_url" value="<?php echo esc_html($data['system']->setting['logout_redirect_url']); ?>" size="100" />
					<p><div class="tips width90"><?php _e('Url to the page which loads after logout.','mgm'); ?></div></p>
				</td>
			</tr>
			<tr>
				<td valign="top"><p><b><?php _e('Redirect if category access denied','mgm'); ?>:</b></p></td>
			</tr>
			<tr>
				<td valign="top">
					<input type="text" name="category_access_redirect_url" value="<?php echo esc_html($data['system']->setting['category_access_redirect_url']); ?>" size="100" />
					<p><div class="tips width90"><?php _e('Url to the page which loads if access denied to a category.','mgm'); ?></div></p>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<p><b><?php _e('Would you like to enable Redirecting to Post Url after login?','mgm'); ?>:</b></p>
					<p><em><?php _e('This allows the users to be redirected to the Post Url if coming from a Post.','mgm')?></em></p>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<input type="radio" name="enable_post_url_redirection" value="Y" <?php if ($data['system']->setting['enable_post_url_redirection'] == 'Y') { echo 'checked="true"'; } ?>/> <?php _e('Yes','mgm'); ?>
					<input type="radio" name="enable_post_url_redirection" value="N"  <?php if ($data['system']->setting['enable_post_url_redirection'] == 'N') { echo 'checked="true"'; } ?>/> <?php _e('No','mgm'); ?>					
					<p><div class="tips width90"><?php _e('Disable/Enable Post Url Redirection.','mgm'); ?></div></p>
				</td>
			</tr>				
			<tr>
				<td valign="top">
					<p><b><?php _e('Would you like to hide custom fields on registeration page?','mgm'); ?>:</b></p>
					<p><em><?php _e('In case you select "Yes", the custom fields will be hidden from the new user registration page, but they will be visible in the profile','mgm'); ?></em></p>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<input type="radio" name="hide_custom_fields" value="Y" <?php if ($data['system']->setting['hide_custom_fields'] == 'Y') { echo 'checked="true"'; } ?>/> <?php _e('Yes','mgm'); ?>
					<input type="radio" name="hide_custom_fields" value="N"  <?php if ($data['system']->setting['hide_custom_fields'] == 'N') { echo 'checked="true"'; } ?>/> <?php _e('No','mgm'); ?>					
					<p><div class="tips width90"><?php _e('Show/Hide custom user fields.','mgm'); ?></div></p>
				</td>
			</tr>			
			<tr>
				<td valign="top">
					<p><b><?php _e('Would you like to turn off emails from payment gateways?','mgm'); ?>:</b></p>
					<p><em><?php _e('This allows you to turn off the emails to the administrator in case an IPN occurs','mgm')?></em></p>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<input type="radio" name="disable_gateway_emails" value="Y" <?php if ($data['system']->setting['disable_gateway_emails'] == 'Y') { echo 'checked="true"'; } ?>/> <?php _e('Yes','mgm'); ?>
					<input type="radio" name="disable_gateway_emails" value="N"  <?php if ($data['system']->setting['disable_gateway_emails'] == 'N') { echo 'checked="true"'; } ?>/> <?php _e('No','mgm'); ?>					
					<p><div class="tips width90"><?php _e('Disable/Enable gateway emails.','mgm'); ?></div></p>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<p><b><?php _e('Would you like to enable Multiple Membership Level Purchase?','mgm'); ?>:</b></p>
					<p><em><?php _e('This allows the users to purchase Multiple membership levels.','mgm')?></em></p>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<input type="radio" name="enable_multiple_level_purchase" value="Y" <?php if ($data['system']->setting['enable_multiple_level_purchase'] == 'Y') { echo 'checked="true"'; } ?>/> <?php _e('Yes','mgm'); ?>
					<input type="radio" name="enable_multiple_level_purchase" value="N"  <?php if ($data['system']->setting['enable_multiple_level_purchase'] == 'N') { echo 'checked="true"'; } ?>/> <?php _e('No','mgm'); ?>					
					<p><div class="tips width90"><?php _e('Disable/Enable Multiple Membership Level Purchase.','mgm'); ?></div></p>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<p><b><?php _e('Would you like to enable Nested Shortcode Parsing?','mgm'); ?>:</b></p>
					<p><em><?php _e('This allows the users to use nested shortcodes.','mgm')?></em></p>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<input type="radio" name="enable_nested_shortcode_parsing" value="Y" <?php if ($data['system']->setting['enable_nested_shortcode_parsing'] == 'Y') { echo 'checked="true"'; } ?>/> <?php _e('Yes','mgm'); ?>
					<input type="radio" name="enable_nested_shortcode_parsing" value="N"  <?php if ($data['system']->setting['enable_nested_shortcode_parsing'] == 'N') { echo 'checked="true"'; } ?>/> <?php _e('No','mgm'); ?>					
					<p><div class="tips width90"><?php _e('Disable/Enable Nested Shortcode Parsing.','mgm'); ?></div></p>
				</td>
			</tr>						
		</table>
	<?php mgm_box_bottom();?>
	
	<?php mgm_box_top('Download Settings');?>
		<table width="100%" cellpadding="1" cellspacing="0" border="0">	
			<tr>
				<td valign="top"><p><b><?php _e('Download Manager Hook','mgm'); ?>:</b></p></td>
			</tr>
			<tr>
				<td valign="top">
					<input type="text" name="download_hook" value="<?php echo esc_html($data['system']->setting['download_hook']); ?>" size="50" />
					<p><div class="tips width90"><?php _e('The hook that the download manager looks for. Default is "download" which would form [download#1] within a post','mgm'); ?></div></p>
				</td>
			</tr>	
			<tr>
				<td valign="top"><p><b><?php _e('Download Slug','mgm'); ?>:</b></p></td>
			</tr>
			<tr>
				<td valign="top">
					<input type="text" name="download_slug" value="<?php echo esc_html($data['system']->setting['download_slug']); ?>" size="50" />
					<p><div class="tips width90"><?php _e('The slug that appears in download url. After editing, refresh rewrite cache by using permalink settings page and hit save once. Default is "download"','mgm'); ?></div></p>
				</td>
			</tr>
			<tr>
				<td valign="top"><p><b><?php _e('External Resource for Downloads','mgm'); ?>:</b></p></td>
			</tr>
			<tr>
				<td valign="top">
					<input type="checkbox" name="aws_enable_s3" id="aws_enable_s3" value="Y" <?php echo ($data['system']->setting['aws_enable_s3'] == 'Y') ? 'checked' : '';?>/> <?php _e('Enable Amazon s3 for Digital Downloads','mgm'); ?>.<br />
					<div id="aws_enable_s3_setting" style="display:<?php echo ($data['system']->setting['aws_enable_s3'] == 'Y') ? 'block' : 'none';?>; padding-top:10px">
						<p><b><?php _e('AWS Key')?>:</b></p>
						<input type="text" name="aws_key" value="<?php echo esc_html($data['system']->setting['aws_key']); ?>" size="50" />
						<div class="tips width90">
							<?php _e(sprintf('AWS Key from Amazon Console, See <a href="%s" target="_blank">AWS Security Credentials</a>.','http://aws.amazon.com/security-credentials '),'mgm'); ?>
						</div>
						
						<p><b><?php _e('AWS Secret Key')?>:</b></p>
						<input type="text" name="aws_secret_key" value="<?php echo esc_html($data['system']->setting['aws_secret_key']); ?>" size="80" />
						<div class="tips width90">
							<?php _e(sprintf('AWS Secret Key from Amazon Console, See <a href="%s" target="_blank">AWS Security Credentials</a>.','http://aws.amazon.com/security-credentials '),'mgm'); ?>
						</div>
					</div>
				</td>
			</tr>		
		</table>
	<?php mgm_box_bottom();?>
	
	<?php mgm_box_top('Email Configuration & Settings');?>
		<table width="100%" cellpadding="1" cellspacing="0" border="0">
			<tr>
				<td valign="top"><p><b><?php _e('Email From','mgm'); ?>:</b></p></td>
			</tr>
			<tr>
				<td valign="top">
					<input type="text" name="from_email" value="<?php echo esc_html($data['system']->setting['from_email']); ?>" size="50" />
					<p><div class="tips width90"><?php _e('Email of the sender ','mgm'); ?></div></p>
				</td>
			</tr>
			<tr>
				<td valign="top"><p><b><?php _e('Email From Name','mgm'); ?>:</b></p></td>
			</tr>
			<tr>
				<td valign="top">
					<input type="text" name="from_name" value="<?php echo esc_html($data['system']->setting['from_name']); ?>" size="50" />
					<p><div class="tips width90"><?php _e('Name of the sender (you or your site&rsquo;s name)','mgm'); ?></div></p>
				</td>
			</tr>
			
			<tr>
				<td valign="top"><p><b><?php _e('Email Content Type','mgm'); ?>:</b></p></td>
			</tr>
			<tr>
				<td valign="top">					
					<select name="email_content_type" style="width:150px;">
						<?php echo mgm_make_combo_options(array('text/html','text/plain'), $data['system']->setting['email_content_type'], MGM_VALUE_ONLY)?>	
					</select>					
					<p><div class="tips width90"><?php _e('Content Type of emails','mgm'); ?></div></p>
				</td>
			</tr>	
			<tr>
				<td valign="top"><p><b><?php _e('Email Charset','mgm'); ?>:</b></p></td>
			</tr>
			<tr>
				<td valign="top">					
					<select name="email_charset">
						<?php echo mgm_make_combo_options(array('UTF-8','ISO-8859-1','ISO-8859-9','windows-1254'), $data['system']->setting['email_charset'], MGM_VALUE_ONLY)?>	
					</select>					
					<p><div class="tips width90"><?php _e('Charset of emails','mgm'); ?></div></p>
				</td>
			</tr>		
		</table>
	<?php mgm_box_bottom();?>
	
	<?php mgm_box_top('Account Expiration Reminder Email Configuration & Settings');?>
		<table width="100%" cellpadding="1" cellspacing="0" border="0">
			<tr>
				<td valign="top"><p><b><?php _e('Days to Start','mgm'); ?>:</b></p></td>
			</tr>
			<tr>
				<td valign="top">
					<input type="text" name="reminder_days_to_start" value="<?php echo esc_html($data['system']->setting['reminder_days_to_start']); ?>" size="50" />
					<p><div class="tips width90"><?php _e('Days to start the email i.e. 5 Days','mgm'); ?></div></p>
				</td>
			</tr>
			<tr>
				<td valign="top"><p><b><?php _e('Incremental','mgm'); ?>:</b></p></td>
			</tr>
			<tr>
				<td valign="top">
					<input type="checkbox" name="reminder_days_incremental" value="Y" <?php echo ($data['system']->setting['reminder_days_incremental'] == 'Y') ? 'checked' : '';?>/>
					<input type="text" name="reminder_days_incremental_ranges" value="<?php echo esc_html($data['system']->setting['reminder_days_incremental_ranges']); ?>" <?php echo ($data['system']->setting['reminder_days_incremental'] == 'Y')?'':'disabled=true';?> />
					<p><div class="tips width90"><?php _e('Days range i.e. 5,3,1. With wrong value provided, default will be used','mgm'); ?></div></p>
				</td>
			</tr>	
		</table>
	<?php mgm_box_bottom();?>	
	
	<?php mgm_box_top('Payment/Subscription Settings');?>
		<table width="100%" cellpadding="1" cellspacing="0" border="0">
			<tr>
				<td valign="top"><p><b><?php _e('Select the Currency which will be used for the payments','mgm'); ?>:</b></p></td>
			</tr>
			<tr>
				<td valign="top">
					<select name="currency" id="currency" style="width:200px;">					
						<?php echo mgm_make_combo_options(mgm_get_currencies(), $data['system']->setting['currency'], MGM_KEY_VALUE)?>
					</select>		
					<p><div class="tips width90"><?php _e('All Payment transaction currency','mgm'); ?></div></p>
				</td>
			</tr>
			<tr>
				<td valign="top"><p><b><?php _e('Your Subscription Name','mgm'); ?>:</b></p></td>
			</tr>
			<tr>
				<td valign="top">
					<input type="text" name="subscription_name" value="<?php echo esc_html($data['system']->setting['subscription_name']); ?>" size="50" />
					<p><div class="tips width90"><?php _e('The name of the membership to display on the order form.<br> Use [blogname], 
					                               [membership] tags to set blogname and membership respectively.','mgm'); ?></div></p>
				</td>
			</tr>	
			<tr>
				<td valign="top"><p><b><?php _e('Register URL','mgm'); ?>:</b></p></td>
			</tr>
			<tr>
				<td valign="top">
					<input type="text" name="register_url" value="<?php echo esc_html($data['system']->setting['register_url']); ?>" size="120" />
					<p><div class="tips width90">
						<?php _e('Custom Register URL for regsiter and related actions. This URL is meant to be updated inside your site, 
						          you can create a Wordpress post/page and paste the page url here.<br>
								  <u><b>Tag</b></u>: <br>
								  <b>[user_register]</b> : Shows Register Form
								 ','mgm'); ?>
					</div></p>
				</td>
			</tr>	
			<tr>
				<td valign="top"><p><b><?php _e('Profile URL','mgm'); ?>:</b></p></td>
			</tr>
			<tr>
				<td valign="top">
					<input type="text" name="profile_url" value="<?php echo esc_html($data['system']->setting['profile_url']); ?>" size="120" />
					<p><div class="tips width90">
						<?php _e('Custom Profile URL for profile and related actions. This URL is meant to be updated inside your site, 
						          you can create a Wordpress post/page and paste the page url here.<br>
						          <u><b>Tag</b></u>: <br>
							      <b>[user_profile]</b> : Shows Profile
								 ','mgm'); ?>
					</div></p>
				</td>
			</tr>		
			<tr>
				<td valign="top"><p><b><?php _e('Transactions URL','mgm'); ?>:</b></p></td>
			</tr>
			<tr>
				<td valign="top">
					<input type="text" name="transactions_url" value="<?php echo esc_html($data['system']->setting['transactions_url']); ?>" size="120" />
					<p><div class="tips width90">
					<?php _e('Transactions URL for redirecting user to payment success/failed page. 
							This URL is meant to be updated inside your site, you can create a Wordpress post/page and paste the page url here.<br>
							<u><b>Tag</b></u>: <br>
							<b>[transactions]</b> : Shows Transaction Details<br>						
						','mgm'); ?>
					</div></p>
				</td>
			</tr>
			
			<tr>
				<td valign="top"><p><b><?php _e('Login URL','mgm'); ?>:</b></p></td>
			</tr>
			<tr>
				<td valign="top">
					<input type="text" name="login_url" value="<?php echo esc_html($data['system']->setting['login_url']); ?>" size="120" />
					<p><div class="tips width90">
					<?php _e('Login URL for custom login page. 							
							<br/><u><b>Tag</b></u>: <br>
							<b>[user_login]</b> : Shows Login Page<br>						
						','mgm'); ?>
					</div></p>
				</td>
			</tr>
			
			<tr>
				<td valign="top"><p><b><?php _e('Lost Password URL','mgm'); ?>:</b></p></td>
			</tr>
			<tr>
				<td valign="top">
					<input type="text" name="lostpassword_url" value="<?php echo esc_html($data['system']->setting['lostpassword_url']); ?>" size="120" />
					<p><div class="tips width90">
					<?php _e('Lost Password URL for custom Lost Password Page. 							
							<br/><u><b>Tag</b></u>: <br>
							<b>[user_lostpassword]</b> : Shows Lost Password Page<br>						
						','mgm'); ?>
					</div></p>
				</td>
			</tr>
			
			<tr>
				<td valign="top"><p><b><?php _e('Membership Details URL','mgm'); ?>:</b></p></td>
			</tr>
			<tr>
				<td valign="top">
					<input type="text" name="membership_details_url" value="<?php echo esc_html($data['system']->setting['membership_details_url']); ?>" size="120" />
					<p><div class="tips width90">
					<?php _e('Membership Details URL for custom Membership Details Page. 							
							<br/><u><b>Tag</b></u>: <br>
							<b>[membership_details]</b> : Shows Membership Details Page<br>						
						','mgm'); ?>
					</div></p>
				</td>
			</tr>
			
			<tr>
				<td valign="top"><p><b><?php _e('Membership Contents URL','mgm'); ?>:</b></p></td>
			</tr>
			<tr>
				<td valign="top">
					<input type="text" name="membership_contents_url" value="<?php echo esc_html($data['system']->setting['membership_contents_url']); ?>" size="120" />
					<p><div class="tips width90">
					<?php _e('Membership Contents URL for custom Membership Contents Page. 							
							<br/><u><b>Tag</b></u>: <br>
							<b>[membership_contents]</b> : Shows Membership Contents Page<br>						
						','mgm'); ?>
					</div></p>
				</td>
			</tr>
					
			<tr>
				<td valign="top"><p><b><?php _e('Use SSL for Payments','mgm'); ?>:</b></p></td>
			</tr>
			<tr>
				<td valign="top">
					<input type="checkbox" name="use_ssl_paymentpage" value="Y"  <?php echo ($data['system']->setting['use_ssl_paymentpage'] == 'Y') ? 'checked' : '';?>/> <?php _e('Yes, make payments secure','mgm'); ?>.
					<p><div class="tips width90"><?php _e('Do you want to make your payment page secure with SSL gateway? must install SSL before continuing.','mgm'); ?></div></p>
				</td>
			</tr>
			<tr>
				<td valign="top"><p><b><?php _e('Enable Autologin','mgm'); ?>:</b></p></td>
			</tr>
			<tr>
				<td valign="top">
					<input type="checkbox" name="enable_autologin" value="Y"  <?php echo ($data['system']->setting['enable_autologin'] == 'Y') ? 'checked' : '';?>/> Yes.
					<p><div class="tips width90"><?php _e('Do you want to take the user to profile page after registration is complete?.','mgm'); ?></div></p>
				</td>
			</tr>		
		</table>
	<?php mgm_box_bottom();?>
	
	<?php mgm_box_top('Affiliate Settings');?>
		<table width="100%" cellpadding="1" cellspacing="0" border="0">
			<tr>
				<td valign="top">
					<p><b>
					<?php _e('Make more money with Magic Members. You can earn 30% commission just like our other affiliates!
							  Please enter your Affiliate ID below. If you don\'t have an affiliate account 
							  <a href="http://www.magicmembers.com/affiliates/" target="_blank">click here</a> to create one now!','mgm'); ?>:
					</b></p>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<input type="checkbox" name="use_affiliate_link" id="use_affiliate_link" value="Y" <?php echo (get_option('mgm_affiliate_id')) ? 'checked' : '';?>/> <?php _e('Yes, use Affiliate Link','mgm'); ?>.<br />
					<?php _e('Affiliate ID','mgm'); ?>: <input type="text" name="affiliate_id" id="affiliate_id" value="<?php echo get_option('mgm_affiliate_id'); ?>" size="5" <?php echo (!get_option('mgm_affiliate_id')) ? 'disabled' : '';?>/>
					<p><div class="tips width90"><?php _e('Affiliate Link in footer.','mgm'); ?></div></p>
				</td>
			</tr>				
		</table>
	<?php mgm_box_bottom();?>			
	
	<?php mgm_box_top('Date Settings');?>
		<table width="100%" cellpadding="1" cellspacing="0" border="0">
			<tr>
				<td valign="top"><p><b><?php _e('Date Ranges','mgm'); ?>:</b></p></td>
			</tr>
			<tr>
				<td valign="top">
					<table width="100%" cellpadding="1" cellspacing="0" border="0">
						<tr>
							<td width="10%"><b><?php _e('Lower')?>:</b></td>
							<td>
								<input type="text" name="date_range_lower" value="<?php echo esc_html($data['system']->setting['date_range_lower']); ?>" size="6" maxlength="2"/> 
								<em>- <?php _e('current year','mgm')?> (<?php echo date('Y',strtotime('- '.(int)$data['system']->setting['date_range_lower'].' YEAR'))?>)</em>
							</td>
						</tr>
						<tr>
							<td width="10%"><b><?php _e('Upper')?>:</b></td>
							<td>
								<input type="text" name="date_range_upper" value="<?php echo esc_html($data['system']->setting['date_range_upper']); ?>" size="6" maxlength="2"/>
								<em> + <?php _e('current year','mgm')?> (<?php echo date('Y',strtotime('+ '.(int)$data['system']->setting['date_range_upper'].' YEAR'))?>)</em>
							</td>
						</tr>						
					</table>
					<p><div class="tips width90"><?php _e('Date lower and upper range in all calendar popup.','mgm'); ?></div></p>
				</td>
			</tr>
			<tr>
				<td valign="top"><p><b><?php _e('Date Formats','mgm'); ?>:</b></p></td>
			</tr>
			<tr>
				<td valign="top">
					<table width="100%" cellpadding="1" cellspacing="0" border="0">
						<tr>
							<td width="10%"><b><?php _e('Default')?>:</b></td>
							<td>
								<input type="text" name="date_farmat" value="<?php echo esc_html($data['system']->setting['date_farmat']); ?>" size="20" />
								<em>e.g.: <?php echo date($data['system']->setting['date_farmat'])?></em>
							</td>
						</tr>
						<tr>
							<td><b><?php _e('Long')?>:</b></td>
							<td>
								<input type="text" name="date_farmat_long" value="<?php echo esc_html($data['system']->setting['date_farmat_long']); ?>" size="20" />
								<em>e.g.: <?php echo date($data['system']->setting['date_farmat_long'])?></em>
							</td>
						</tr>
						<tr>
							<td><b><?php _e('Short')?>:</b></td>
							<td>
								<input type="text" name="date_farmat_short" value="<?php echo esc_html($data['system']->setting['date_farmat_short']); ?>" size="20" />
								<em>e.g.: <?php echo date($data['system']->setting['date_farmat_short'])?></em>
							</td>
						</tr>
					</table>	
					<p><div class="tips width90"><?php _e('Date formats, use php date settings.','mgm'); ?></div></p>
				</td>
			</tr>
		</table>
	<?php mgm_box_bottom();?>
	
	<?php mgm_box_top('Image Settings');?>
	<table width="100%" cellpadding="1" cellspacing="0" border="0">
		<tr>
			<td valign="top" width="15%">
				<p><b>
				Thumbnail width:
				</b></p>
			</td>
			<td valign="top" width="85%">
				<p>
				<input type="text" name="thumbnail_image_width" value="<?php echo esc_html($data['system']->setting['thumbnail_image_width']); ?>" size="20" />
				<p><div class="tips width90"><?php _e('Thumbnail size image width in pixels.','mgm'); ?></div></p>
				</p>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<p><b>
				Thumbnail height:
				</b></p>
			</td>
			<td valign="top">
				<p>
				<input type="text" name="thumbnail_image_height" value="<?php echo esc_html($data['system']->setting['thumbnail_image_height']); ?>" size="20" />
				<p><div class="tips width90"><?php _e('Thumbnail size image height in pixels.','mgm'); ?></div></p>
				</p>
			</td>
		</tr>	
		<tr>
			<td valign="top">
				<p><b>
				Medium width:
				</b></p>
			</td>
			<td valign="top">
				<p>
				<input type="text" name="medium_image_width" value="<?php echo esc_html($data['system']->setting['medium_image_width']); ?>" size="20" />
				<p><div class="tips width90"><?php _e('Medium size image width in pixels.','mgm'); ?></div></p>
				</p>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<p><b>
				Medium height:
				</b></p>
			</td>
			<td valign="top">
				<p>
				<input type="text" name="medium_image_height" value="<?php echo esc_html($data['system']->setting['medium_image_height']); ?>" size="20" />
				<p><div class="tips width90"><?php _e('Medium size image height in pixels.','mgm'); ?></div></p>
				</p>
			</td>
		</tr>		
		<tr>
			<td valign="top">
				<p><b>
				Image size:
				</b></p>
			</td>
			<td valign="top">
				<p>
				<input type="text" name="image_size_mb" value="<?php echo esc_html($data['system']->setting['image_size_mb']); ?>" size="20" />
				<p><div class="tips width90"><?php _e('Image size in MB.','mgm'); ?></div></p>
				</p>
			</td>
		</tr>				
	</table>
<?php mgm_box_bottom();?>

	<?php  mgm_box_top('Captcha Settings');
		$recaptcha = mgm_get_class('recaptcha');		
	?>
	<table width="100%" cellpadding="1" cellspacing="0" border="0">
		<tr>
			<td valign="top" width="25%">
				<p><b>
				reCaptcha Public Key:
				</b></p>
			</td>
			<td valign="top"  width="75%">
				<p>
				<input type="text" name="recaptcha_public_key" value="<?php echo esc_html($data['system']->setting['recaptcha_public_key']); ?>" size="60" />
				<p><div class="tips width90"><?php _e('reCAPTCHA Public Key. Generate your key at <br /><b>'.$recaptcha->recaptcha_get_signup_url().'</b>','mgm'); ?></div></p>
				</p>
			</td>
		</tr>		
		<tr>
			<td valign="top">
				<p><b>
				reCaptcha Private Key:
				</b></p>
			</td>
			<td valign="top">
				<p>
				<input type="text" name="recaptcha_private_key" value="<?php echo esc_html($data['system']->setting['recaptcha_private_key']); ?>" size="60" />
				<p><div class="tips width90"><?php _e('reCAPTCHA Private Key. Generate your key at <br /><b>'.$recaptcha->recaptcha_get_signup_url().'</b>','mgm'); ?></div></p>
				</p>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<p><b>
				reCAPTCHA API Server Url:
				</b></p>
			</td>
			<td valign="top">
				<p>
				<input type="text" name="recaptcha_api_server" value="<?php echo esc_html($data['system']->setting['recaptcha_api_server']); ?>" size="60" />
				<p><div class="tips width90"><?php _e('reCAPTCHA API Server Url.','mgm'); ?></div></p>
				</p>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<p><b>
				reCAPTCHA API Secure Server Url:
				</b></p>
			</td>
			<td valign="top">
				<p>
				<input type="text" name="recaptcha_api_secure_server" value="<?php echo esc_html($data['system']->setting['recaptcha_api_secure_server']); ?>" size="60" />
				<p><div class="tips width90"><?php _e('reCAPTCHA API Secure Server Url.','mgm'); ?></div></p>
				</p>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<p><b>
				reCAPTCHA Verify Server Url:
				</b></p>
			</td>
			<td valign="top">
				<p>
				<input type="text" name="recaptcha_verify_server" value="<?php echo esc_html($data['system']->setting['recaptcha_verify_server']); ?>" size="60" />
				<p><div class="tips width90"><?php _e('reCAPTCHA Verify Server Url','mgm'); ?></div></p>
				</p>
			</td>
		</tr>					
	</table>
	<?php mgm_box_top('Redirection Setting');?>
	<table width="100%" cellpadding="1" cellspacing="0" border="0">
		<tr>
			<td valign="top" width="30%">
				<p><b>
				Redirection Method:
				</b></p>
			</td>
			<td valign="top"  width="70%">
				<p>
				<select name="redirection_method" style="width:150px;">
					<?php echo mgm_make_combo_options(array('header'=>'header','javascript'=>'javascript','meta'=>'meta'), $data['system']->setting['redirection_method'], MGM_VALUE_ONLY)?>	
				</select>				
				<p><div class="tips width90"><?php _e('Redirection method.<br/>Default: Wordpress wp_redirect<br/>Javascript: Javascript redirection<br/>Meta: Html Meta tag redirection','mgm'); ?></div></p>
				</p>
			</td>
		</tr>			
	</table>
<?php mgm_box_bottom();?>
<?php mgm_box_bottom(); ?>

	<p class="submit" style="float:left">
		<input type="submit" name="settings_update" value="<?php _e('Save Settings','mgm') ?> &raquo;" />
	</p>
	<div class="clearfix"></div>	
</form>
<script language="javascript">
	<!--
	jQuery(document).ready(function(){
		// check bind
		jQuery("#frmsetgen :checkbox[name='reminder_days_incremental']").bind('click',function(){
			jQuery("#frmsetgen :input[name='reminder_days_incremental_ranges']").attr('disabled',!jQuery(this).attr('checked'));
		});		
		
		jQuery.validator.addMethod('checkSpecialChar', function(value, element) {
			return (value).match(/^[A-Za-z0-9_,]+$/);
		}, '<?php _e('Please remove space/special characters') ?>');
		// add : form validation
		jQuery("#frmsetgen").validate({
			submitHandler: function(form) {					    					
				jQuery("#frmsetgen").ajaxSubmit({type: "POST",										  
				  dataType: 'json',			
				  iframe: false,								 
				  beforeSubmit: function(){	
				  	// show message
					mgm_show_message('#frmsetgen', {status:'running', message:'<?php _e('Processing','mgm')?>...'}, true);							
				  },
				  success: function(data){	
				  	// show message
				  	mgm_show_message('#frmsetgen', data);																			
				  }}); // end   		
				return false;											
			},
			rules:{download_slug:{required:true,checkSpecialChar:true}},	
			messages: {	},		
			errorClass: 'invalid',
			errorPlacement:function(error, element) {										
				error.insertAfter(element);
			}
		});			
		
		// affiliate link
		jQuery('#use_affiliate_link').bind('click', function(){
			jQuery('#affiliate_id').attr('disabled', !jQuery(this).attr('checked'));
		});
		
		// aws_enable_s3
		jQuery('#aws_enable_s3').bind('click', function(){
			if(jQuery(this).attr('checked')){	
				jQuery("#aws_enable_s3_setting :input[type='text']").attr('disabled', false).val('');
				jQuery('#aws_enable_s3_setting').fadeIn();
			}else{
				jQuery("#aws_enable_s3_setting :input[type='text']").attr('disabled', true);
				jQuery('#aws_enable_s3_setting').fadeOut();
			}
		});
	});
	//-->
</script>