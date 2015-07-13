<!--messages-->
<form name="frmmessages" id="frmmessages" method="post" action="admin.php?page=mgm/admin/settings&method=messages">
	<?php /* issue#: 353 (subscription introduction and Terms and conditions are reading from custom fields)*/?>
	<?php mgm_box_top('Main Messages');?>
		<table width="100%" cellpadding="1" cellspacing="0" border="0">
			<tr>
				<td valign="top"><p><b><?php _e('Subscription Introduction','mgm'); ?>:</b></p></td>
			</tr>
			<tr>
				<td valign="top">
					<textarea name="setting[subs_intro]" id="setting_subs_intro" style="height:200px; width:820px"><?php echo mgm_print_template_content('subs_intro'); ?></textarea>
					<p><div class="tips"><?php _e('This is the text which will appear before the subscription options. HTML format is allowed.','mgm'); ?></div></p>
				</td>
			</tr>
			<tr>
				<td valign="top"><p><b><?php _e('Terms &amp; Conditions','mgm'); ?>:</b></p></td>
			</tr>
			<tr>
				<td valign="top">
					<textarea name="setting[tos]" id="setting_tos" style="height:200px; width:820px"><?php echo mgm_print_template_content('tos'); ?></textarea>
					<p><div class="tips"><?php _e('This is your Terms &amp; Conditions text and it will appear on the registration page. Users have to agree your Terms &amp; Conditions in order to register.','mgm'); ?></div></p>
				</td>
			</tr>
		</table>
	<?php mgm_box_bottom();?><br />
	
	<?php mgm_box_top('Post Messages');?>
		<table width="100%" cellpadding="1" cellspacing="0" border="0">
			<tr>
				<td valign="top"><p><b><?php _e('Post Shortcodes','mgm'); ?>:</b></p></td>
			</tr>
			<tr>
				<td valign="top">					
					<div>
						<ul>
							<li><strong>[[purchase_cost]]</strong> = <?php _e('Displays the cost and currency of a purchasable post.','mgm'); ?></li>
							<li><strong>[[register]]</strong> = <?php _e('Displays the register form.','mgm'); ?></li>
							<li><strong>[[login_register]]</strong> = <?php _e('Displays the login or register form.','mgm'); ?></li>
							<li><strong>[[login_register_links]]</strong> = <?php _e('Displays the links for login and register.','mgm'); ?></li>
							<li><strong>[[login_link]]</strong> = <?php _e('Displays only the Login link.','mgm'); ?></li>
							<li><strong>[[register_link]]</strong> = <?php _e('Displays only the Register link.','mgm'); ?></li>
							<li><strong>[[membership_types]]</strong> = <?php _e('Displays a list of membership levels that can see the post/page.','mgm'); ?></li>
							<li><strong>[[duration]]</strong> = <?php _e('Displays the number of days the user will have access to the content for.','mgm'); ?></li>							
							<li><strong>[[name]]</strong> = <?php _e('Displays user name.','mgm'); ?></li>
							<li><strong>[[username]]</strong> = <?php _e('Displays user username.','mgm'); ?></li>
						</ul>
					</div>
					<p>
						<div class="tips" style="width:95%"><?php _e('In this section you can change how the messages will display inside the posts. You are free to use HTML coding and special tags.','mgm'); ?>:</div>
					</p>
				</td>
			</tr>
			<tr>
				<td valign="top"><p><b><?php _e('Private Text [before login]','mgm'); ?>:</b></p></td>
			</tr>
			<tr>
				<td valign="top">
					<textarea name="setting[private_text]" id="setting_private_text" style="height:200px; width:820px"><?php echo mgm_print_template_content('private_text'); ?></textarea>
					<p>
						<div class="tips" style="width:95%">
							<?php _e('The following message replaces the text inside the [private]...[/private] tags in your posts and pages when the viewer is not logged in or do not have the right account.','mgm'); ?>
						</div>
					</p>
				</td>
			</tr>
			<tr>
				<td valign="top"><p><b><?php _e('Private Text [after login, but no access for membership type]','mgm'); ?>:</b></p></td>
			</tr>
			<tr>
				<td valign="top">
					<textarea name="setting[private_text_no_access]" id="setting_private_text_no_access" style="height:200px; width:820px"><?php echo mgm_print_template_content('private_text_no_access'); ?></textarea>
					<p><div class="tips" style="width:95%"><?php _e('The following message replaces the text inside the [private]...[/private] tags in your posts and pages when the viewer is logged in but is not allowed to see the rest of the post.','mgm'); ?></div></p>
				</td>
			</tr>
			<tr>
				<td valign="top"><p><b><?php _e('Private Text [after login, purchasable post]','mgm'); ?>:</b></p></td>
			</tr>
			<tr>
				<td valign="top">
					<textarea name="setting[private_text_purchasable]" id="setting_private_text_purchasable" style="height:200px; width:820px"><?php echo mgm_print_template_content('private_text_purchasable'); ?></textarea>
					<p><div class="tips" style="width:95%"><?php _e('The following message replaces the text inside the [private]...[/private] tags for your purchasable posts when the viewer is logged out or has not purchased the post yet.','mgm'); ?></div></p>
				</td>
			</tr>
			
			<tr>
				<td valign="top"><p><b><?php _e('Private Text [before login, purchasable post]','mgm'); ?>:</b></p></td>
			</tr>
			<tr>
				<td valign="top">
					<textarea name="setting[private_text_purchasable_login]" id="setting_private_text_purchasable_login" style="height:200px; width:820px"><?php echo mgm_print_template_content('private_text_purchasable_login'); ?></textarea>
					<p><div class="tips" style="width:95%"><?php _e('The following message replaces the text inside the [private]...[/private] tags for your purchasable posts when the viewer is not logged in.','mgm'); ?></div></p>
				</td>
			</tr>
		</table>		
	<?php mgm_box_bottom();?>
	
	<?php mgm_box_top('Error Messages');?>
		<?php _e('You will be able to configure error messages in the following sections. If you want to output your user\'s username just use the tag <strong>[[USERNAME]]</strong> .','mgm') ?>
		<table width="100%" cellpadding="1" cellspacing="0" border="0">
			<tr>
				<td valign="top"><p><b><?php _e('Inactive Account','mgm'); ?>:</b></p></td>
			</tr>
			<tr>
				<td valign="top">
					<textarea name="setting[login_errmsg_null]" id="setting_login_errmsg_null" style="height:200px; width:820px"><?php echo mgm_print_template_content('login_errmsg_null'); ?></textarea>
					<p><div class="tips" style="width:95%"><?php _e('This error message is shown to the users if they are not subscribed yet or in case their account is inactive for other reasons.','mgm'); ?></div></p>
				</td>
			</tr>
			<tr>
				<td valign="top"><p><b><?php _e('Subscription Expired','mgm'); ?>:</b></p></td>
			</tr>
			<tr>
				<td valign="top">
					<textarea name="setting[login_errmsg_expired]" id="setting_login_errmsg_expired" style="height:200px; width:820px"><?php echo mgm_print_template_content('login_errmsg_expired'); ?></textarea>
					<p><div class="tips style="width:95%""><?php _e('When a user\'s subscription expires and the user attempt to login, the following message will appear.','mgm'); ?></div></p>
				</td>
			</tr>
			<tr>
				<td valign="top"><p><b><?php _e('Trial Expired','mgm'); ?>:</b></p></td>
			</tr>
			<tr>
				<td valign="top">
					<textarea name="setting[login_errmsg_trial_expired]" id="setting_login_errmsg_trial_expired" style="height:200px; width:820px"><?php echo mgm_print_template_content('login_errmsg_trial_expired'); ?></textarea>
					<p><div class="tips" style="width:95%"><?php _e('When a user\'s trial account expires and the user attempt to login, the following message will appear.','mgm'); ?></div></p>
				</td>
			</tr>
			<tr>
				<td valign="top"><p><b><?php _e('Subscription Payment Pending','mgm'); ?>:</b></p></td>
			</tr>
			<tr>
				<td valign="top">
					<textarea name="setting[login_errmsg_pending]" id="setting_login_errmsg_pending" style="height:200px; width:820px"><?php echo mgm_print_template_content('login_errmsg_pending'); ?></textarea>
					<p><div class="tips" style="width:95%"><?php _e('This error message is shown to the users only if their subscription payment is pending.','mgm'); ?></div></p>
				</td>
			</tr>
			<tr>
				<td valign="top"><p><b><?php _e('Unknown Error in login','mgm'); ?>:</b></p></td>
			</tr>
			<tr>
				<td valign="top">
					<textarea name="setting[login_errmsg_default]" id="setting_login_errmsg_default" style="height:200px; width:820px"><?php echo mgm_print_template_content('login_errmsg_default'); ?></textarea>
					<p><div class="tips" style="width:95%"><?php _e('This error message is shown when login fails for an unexpacted reason. This should not occur in case the system works properly.','mgm'); ?></div></p>
				</td>
			</tr>			
		</table>
	<?php mgm_box_bottom();?>
	
	<?php mgm_box_top('Misc. Message Templates');?>
	<table width="100%" cellpadding="1" cellspacing="0" border="0">
		<tr>
			<td valign="top"><p><b><?php _e('Membership Pack Description Template','mgm'); ?>:</b></p></td>
		</tr>
		<tr>
			<td valign="top">
				<textarea name="setting[pack_desc_template]" id="setting_pack_desc_template" style="height:200px; width:820px"><?php echo mgm_print_template_content('pack_desc_template','templates'); ?></textarea>
				<p><div class="tips" style="width:95%"><?php _e(' When the packs are shown to the user they are placed in a certain format (eg: Member - 5 USD per 3 Months), this allows you to change it using any or all of the following hooks: [membership_type], [cost], [currency], [duration], [duration_period]. If your membership packs are a recurring payment and you have limited the number then you can use [num_cycles] below to indicate the number of payments. If you would like to use a Paypal trial then indicate this in the string using [trial_cost], [trial_duration], [trial_duration_period] [description]. Encapsulate any trial specific parts of the string in [if_trial_on][/if_trial_on] and for those that arent using a trial it\'s contents will be removed.','mgm'); ?></div></p>
			</td>
		</tr>
		<tr>
			<td valign="top"><p><b><?php _e('Purchasable Post Pack Template','mgm'); ?>:</b></p></td>
		</tr>
		<tr>
			<td valign="top">
				<textarea name="setting[ppp_pack_template]" id="setting_ppp_pack_template" style="height:200px; width:820px"><?php echo mgm_print_template_content('ppp_pack_template','templates'); ?></textarea>
				<p><div class="tips" style="width:95%"><?php _e('When you use [payperpost_pack#num] within a post or page the following template will be called and populated. Use the following hooks and any html you like to create your design: [pack_name] [pack_cost] [pack_currency] [pack_description] [pack_posts].','mgm'); ?></div></p>
			</td>
		</tr>		
	</table>
	<?php mgm_box_bottom();?>	
	
	<?php mgm_box_top('Payment Messages');?>
		<table width="100%" cellpadding="1" cellspacing="0" border="0">
			<tr>
				<td valign="top"><p><b><?php _e('Payment Success Title','mgm'); ?>:</b></p></td>
			</tr>
			<tr>
				<td valign="top">
					<input type="text" size="100" name="setting[payment_success_title]" id="setting_payment_success_title" value="<?php echo strip_tags(mgm_print_template_content('payment_success_title')); ?>">
					<p><div class="tips"><?php _e('Payment success title, displayed after successful payments.','mgm'); ?></div></p>
				</td>
			</tr>
			<tr>
				<td valign="top"><p><b><?php _e('Payment Success Message','mgm'); ?>:</b></p></td>
			</tr>
			<tr>
				<td valign="top">
					<textarea name="setting[payment_success_message]" id="setting_payment_success_message" style="height:200px; width:820px"><?php echo mgm_print_template_content('payment_success_message'); ?></textarea>
					<p><div class="tips"><?php _e('Payment success message, displayed after successful payments. HTML format is allowed.','mgm'); ?></div></p>
				</td>
			</tr>
			<tr>
				<td valign="top"><p><b><?php _e('Payment Failed Title','mgm'); ?>:</b></p></td>
			</tr>
			<tr>
				<td valign="top">
					<input type="text" size="100" name="setting[payment_failed_title]" id="setting_payment_failed_title" value="<?php echo strip_tags(mgm_print_template_content('payment_failed_title')); ?>">
					<p><div class="tips"><?php _e('Payment failed title, displayed after failed payments.','mgm'); ?></div></p>
				</td>
			</tr>
			<tr>
				<td valign="top"><p><b><?php _e('Payment Failed Message','mgm'); ?>:</b></p></td>
			</tr>
			<tr>
				<td valign="top">
					<textarea name="setting[payment_failed_message]" id="setting_payment_failed_message" style="height:200px; width:820px"><?php echo mgm_print_template_content('payment_failed_message'); ?></textarea>
					<p><div class="tips"><?php _e('Payment failed message, displayed after failed payments. HTML format is allowed.','mgm'); ?></div></p>
				</td>
			</tr>
		</table>
		<div>
			<input type="checkbox" name="apply_update_to_modules" value="Y" /> <b><?php _e('After update, apply the changes to all payment modules.','mgm')?></b><br />
			<em style="color:#FF0000; margin-left:15px"><?php _e('WARNING!, there is no going back, all modules will be updated.','mgm')?></em>
		</div>
	<?php mgm_box_bottom();?>
	
	<?php mgm_box_top('Message Templates');?>
		<table width="100%" cellpadding="1" cellspacing="0" border="0">
			<tr>
				<td valign="top"><p><b><?php _e('Private Text Template','mgm'); ?>:</b></p></td>
			</tr>
			<tr>
				<td valign="top">
					<textarea name="setting[private_text_template]" id="setting_private_text_template" style="height:200px; width:820px"><?php echo mgm_print_template_content('private_text_template', 'templates'); ?></textarea>
					<p><div class="tips"><?php _e('Wrapper template for private text messages.','mgm'); ?></div></p>
				</td>
			</tr>			
			<tr>
				<td valign="top"><p><b><?php _e('Register Form Row Template','mgm'); ?>:</b></p></td>
			</tr>
			<tr>
				<td valign="top">
					<textarea name="setting[register_form_row_template]" id="setting_register_form_row_template" style="height:200px; width:820px"><?php echo mgm_print_template_content('register_form_row_template', 'templates'); ?></textarea>
					<p><div class="tips"><?php _e('Template for register form field row.','mgm'); ?></div></p>
				</td>
			</tr>
			<tr>
				<td valign="top"><p><b><?php _e('Register Form Autoresponder Row Template','mgm'); ?>:</b></p></td>
			</tr>
			<tr>
				<td valign="top">
					<textarea name="setting[register_form_row_autoresponder_template]" id="setting_register_form_row_autoresponder_template" style="height:200px; width:820px"><?php echo mgm_print_template_content('register_form_row_autoresponder_template', 'templates'); ?></textarea>
					<p><div class="tips"><?php _e('Template for register form autoresponder field row.','mgm'); ?></div></p>
				</td>
			</tr>
			<tr>
				<td valign="top"><p><b><?php _e('Profile Form Row Template','mgm'); ?>:</b></p></td>
			</tr>
			<tr>
				<td valign="top">
					<textarea name="setting[profile_form_row_template]" id="setting_profile_form_row_template" style="height:200px; width:820px"><?php echo mgm_print_template_content('profile_form_row_template', 'templates'); ?></textarea>
					<p><div class="tips"><?php _e('Template for user profile form field row.','mgm'); ?></div></p>
				</td>
			</tr>
			
		</table>
	<?php mgm_box_bottom(); ?>
	
	<p class="submit">
		<input type="submit" name="msgs_update" value="<?php _e('Save Messages','mgm'); ?> &raquo;"/>
	</p>
</form>
<script language="javascript">
<!--
	jQuery(document).ready(function(){
		var textfields_exclude = ['setting_private_text_template','setting_register_form_row_template','setting_register_form_row_autoresponder_template','setting_profile_form_row_template'];
		// editor
		jQuery("#frmmessages textarea[id]").each(function(){			
			if(-1 == (jQuery.inArray( jQuery(this).attr('id') , textfields_exclude ))) {	
				new nicEditor({fullPanel : true, iconsPath: '<?php echo MGM_ASSETS_URL?>js/nicedit/nicEditorIcons.gif'}).panelInstance(jQuery(this).attr('id')); 			
			}
		});
		// add : form validation
		jQuery("#frmmessages").validate({
			submitHandler: function(form) {					    					
				jQuery("#frmmessages").ajaxSubmit({type: "POST",										  
				  dataType: 'json',		
				  iframe: false,		
				  beforeSerialize: function($form) { 					
					// only on IE
					if(jQuery.browser.msie){
						jQuery($form).find("textarea[id]").each(function(){								
							jQuery(this).val(nicEditors.findEditor(jQuery(this).attr('id')).getContent()); 
						});										
					}
				  },							 
				  beforeSubmit: function(){	
				  	// show message
					mgm_show_message('#frmmessages', {status:'running', message:'<?php _e('Processing','mgm')?>...'});							
					// focus
					jQuery.scrollTo('#frmmessages',400);	
				  },
				  success: function(data){	
					// message																				
					mgm_show_message('#frmmessages', data);																					
				  }}); // end   		
				return false;											
			},			
			errorClass: 'invalid'
		});							  
	});	
//-->
</script>		