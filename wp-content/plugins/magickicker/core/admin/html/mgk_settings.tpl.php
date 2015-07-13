<?php
// get flag
$action_flag=$_REQUEST['mode'];
// call process
mgk_call_process($action_flag,'f_general');

// define actions /////////////

// general
function f_general(){
	global $wpdb;
	// save
	if(isset($_POST['process']) && $_POST['process']=='true'){		
		extract($_POST);
		if(isset($settings)){
			// check email unique
			$old_settings=get_option('mgk_settings');
			if(is_array($old_settings)){
				$new_settings=array_merge($old_settings,$_POST['settings']);
			}else{
				$new_settings=$_POST['settings'];
			}
			update_option('mgk_settings', $new_settings);
			// message				
			$status  ='success';
			$message =__('General Settings updated successfully','mgk');		
		}else{
			$status  ='error';
			$message =__('Setting not provided','mgk');
		}		
		// the response
		echo json_encode(array('status'=>$status,'message'=>$message));
		exit();
	}
	// old settings
	$mgk_settings = get_option('mgk_settings');
	$settings     = $mgk_settings['general'];	
	
	// $membershipmods = mgk_get_setting('membership','recurringmodules');
	
	// show form
	?>
	<?php mgk_box_top('General Settings')?>	
	<form name="frmgenset" id="frmgenset" method="post" action="admin.php?page=mgk/admin&load=settings&mode=general">
		<table width="100%" cellpadding="1" cellspacing="0" border="0" class="form-table">		
			<tbody>
				<tr>
					<td width="30%" valign="top"><span class="required-field"><?php _e('Force logout URL','mgk')?></span></td>
					<td valign="top">
						<input type="text" name="settings[general][redirect_url]" size="80" value="<?php echo $settings['redirect_url']?>"/>
						<div class="tips"><?php _e('This is where the user is sent when their ip address is not the same as the one they previously logged in from.','mgk')?></div>						
					</td>
				</tr>
				<tr>
					<td valign="top"><span class="required-field"><?php _e('Multiple Login Lockout Condition','mgk')?></span></td>
					<td valign="top">
						<input type="text" name="settings[general][timeout_logins]" size="10" value="<?php echo $settings['timeout_logins']?>"/> <?php _e('Logins over', 'mgk')?>
						<input type="text" name="settings[general][timeout_minutes]" size="10" value="<?php echo $settings['timeout_logins']?>"/> <?php _e('Minutes', 'mgk')?>
						<div class="tips"><?php _e('The number of minutes between logins from multiple ip addresses that have to pass without both accounts being locked out.','mgk')?></div>						
					</td>
				</tr>
				<tr>
					<td valign="top"><span class="required-field"><?php _e('Lockout or Logout','mgk')?></span></td>
					<td valign="top">
						<input type="radio" name="settings[general][lockout_option]" value="lockout" <?php echo mgk_check_if_match('lockout', $settings['lockout_option'],'lockout')?> onclick="mgk_toggle_lo_options('lockout');"/> <?php _e('Lockout', 'mgk');?>
						<input type="radio" name="settings[general][lockout_option]" value="logout" <?php echo mgk_check_if_match('logout',$settings['lockout_option'])?> onclick="mgk_toggle_lo_options('logout');"/> <?php _e('Logout', 'mgk');?>		
						<div class="tips"><?php _e('This gives you the option to log the user out on a breach or lock them out completely.','mgk')?></div>						
					</td>
				</tr>	
				<tr>
					<td colspan="2">
						<div id="lockout_options_div" style="display:<?php echo ($settings['lockout_option'] == 'lockout' ? 'block':'none') ?>;">
							<table width="100%" cellpadding="1" cellspacing="0" border="0">
								<tr>
									<td valign="top" width="30%"><span class="required-field"><?php _e('Locked Out Login Error', 'mgk')?></span></td>
									<td valign="top">
										<input type="text" name="settings[general][login_error]" value="<?php echo $settings['login_error']?>" style="width: 450px;" />									
										<div class="tips"><?php _e('The message that is shown to the user on login when they have been locked out.', 'mgk')?></div>
									</td>
								</tr>								
								<tr>
									<td valign="top"><span class="required-field"><?php _e('Email Activation or Timed lockout', 'mgk')?></span></td>
									<td valign="top">										
										<input type="radio" name="settings[general][email_offender]" value="email" <?php echo mgk_check_if_match('email', $settings['email_offender'],'email')?> onclick="mgk_toggle_eo_options('email'); "/> <?php _e('Email', 'mgk');?>									
										<input type="radio" name="settings[general][email_offender]" value="timed" <?php echo mgk_check_if_match('timed', $settings['email_offender'])?> onclick="mgk_toggle_eo_options('timed');"/> <?php _e('Timed', 'mgk');?>								
										<div class="tips"><?php _e('Would you like to notify the owner of the account via email in the event of a breach or lock them out for a number of minutes?', 'mgk')?></div>
									</td>
								</tr>
							</table>	
						</div>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<div id="lockout_mins_div" style="display:<?php echo ($settings['lockout_option'] == 'lockout' && $settings['email_offender'] == 'timed' ? 'block':'none') ?>;">
							<table width="100%" cellpadding="1" cellspacing="0" border="0">
								<tr>
									<td valign="top" width="30%" ><span class="required-field"><?php _e('Lockout Minutes', 'mgk');?></span></td>
									<td valign="top">
										<input type="text" name="settings[general][lockout_minutes]" value="<?php echo $settings['lockout_minutes']?>" style="width: 50px;" /> <?php _e('Minutes', 'mgk');?>
										<br/>
										<div class="tips"><?php _e('If you selected the option to lock the user out for a period of time then how long should that be?', 'mgk');?></div>
									</td>
								</tr>
							</table>	
						</div>
						<div id="lockout_email_div" style="display:<?php echo ($settings['lockout_option'] == 'lockout' && $settings['email_offender'] == 'email' ? 'block':'none') ?>;">
							<table width="100%" cellpadding="1" cellspacing="0" border="0">
								<tr>
									<td valign="top" width="30%" ><span class="required-field"><?php _e('Email Subject', 'mgk');?></span></td>
									<td valign="top">
										<input type="text" name="settings[general][locked_mail_subject]" value="<?php echo $settings['locked_mail_subject']?>" style="width: 350px;" />
									</td>
								</tr>		
								<tr>
									<td valign="top"><span class="required-field"><?php _e('Email Message', 'mgk');?></span></td>
									<td valign="top">
										<textarea name="settings[general][locked_mail_message]" cols="40" rows="10" id="locked_mail_message" style="height:200px; width:500px"><?php echo $settings['locked_mail_message']?></textarea>
										<div class="tips"><?php _e('The email that is sent to the user of the account with the following hook in it [activation_link].', 'mgk');?></div>
									</td>
								</tr>		
								<tr>
									<td valign="top"><span class="required-field"><?php _e('Activation Redirect', 'mgk');?></span></td>
									<td valign="top">
										<input type="text" name="settings[general][activate_redirect]" style="width: 350px;" value="<?php echo $settings['activate_redirect']?>" />
										<div class="tips"><?php _e('This is an optional redirect for the user once an accepted activation link has been processed. If this box is left empty then then it will not redirect.', 'mgk');?></div>
									</td>
								</tr>
							</table>	
						</div>
					</td>
				</tr>					
				<tr>
					<td valign="top"><span class="not-required"><?php _e('Long date Format','mgk')?></span></td>
					<td valign="top">
						<select name="settings[general][long_date_format]" >
							<option value="m-d-Y h:i:s" <?php mgk_select_if_match('m-d-Y h:i:s',$settings['long_date_format'],'m-d-Y h:i:s')?>><?php echo date('m-d-Y h:i:s')?></option>
							<option value="m-d-Y H:i:s" <?php mgk_select_if_match('m-d-Y H:i:s',$settings['long_date_format'])?>><?php echo date('m-d-Y H:i:s')?></option>
							
							<option value="jS F, Y h:i:s" <?php mgk_select_if_match('jS F, Y h:i:s',$settings['long_date_format'])?>><?php echo date('jS F, Y h:i:s')?></option>
							<option value="jS F, Y H:i:s" <?php mgk_select_if_match('jS F, Y H:i:s',$settings['long_date_format'])?>><?php echo date('jS F, Y H:i:s')?></option>
							
							<option value="jS M, Y h:i:s" <?php mgk_select_if_match('jS M, Y h:i:s',$settings['long_date_format'])?>><?php echo date('jS M, Y h:i:s')?></option>
							<option value="jS M, Y H:i:s" <?php mgk_select_if_match('jS M, Y H:i:s',$settings['long_date_format'])?>><?php echo date('jS M, Y H:i:s')?></option>							
						</select>			
						<div class="tips"><?php _e('Long Date Format for Display.','mgk')?></div>						
					</td>
				</tr>	
				<tr>
					<td valign="top"><span class="required-field"><?php _e('Pagination Limit','mgk')?></span></td>
					<td valign="top">
						<input type="text" name="settings[general][pagination]" size="10" value="<?php echo $settings['pagination']?>"/>						
						<div class="tips"><?php _e('Pagination Limit (Admin) .','mgk')?></div>
					</td>
				</tr>		
				<tr>
					<td valign="top"></td>
					<td valign="top">
						<input type="submit" name="btn_save" value="<?php _e('Save','mgk')?>" class="button"/>						
					</td>
				</tr>
			</tbody>	
		</table>	
		<input type="hidden" name="process" value="true" />
	</form>											
	<br />	
	<?php mgk_box_bottom()?>
	<br />
	<?php mgk_box_top('Remove Magic Kickers')?>	
	<form name="frmgenuni" id="frmgenuni" method="post" action="admin.php?page=mgk/admin&load=settings&mode=remove_plugin">
		<table width="100%" cellpadding="1" cellspacing="0" border="0" class="form-table">		
			<tbody>
				<tr>
					<td>
						<p><?php _e('<strong>Warning!</strong> This will remove all Magic Kickers data including sales records.','mgk') ?></p>
						<p><strong><?php _e('Back up before removal! Once done this can not be undone!','mgk') ?></strong></p>
					</td>
				</tr>
				<tr>					
					<td valign="top">
						<input type="button" name="removebtn" value="<?php _e('Remove All Data','mgk') ?>" class="button" onclick="remove_mgk(this)"/>						
					</td>
				</tr>
				<tr>
					<td>
						<p><?php _e('Only use this tool if you really mean to permanently remove Magic Kickers, otherwise deactivate/activate using the normal Plugin screen','mgk') ?></p>
					</td>
				</tr>
			</tbody>
		</table>
		<?php wp_nonce_field( 'deactivate_plugin_magickicker', 'deactivate_plugin_magickicker', false );?>	
	</form>		
	<br />	
	<?php mgk_box_bottom()?>
	<br />
	<?php mgk_box_top('Setup Environment for Magic Kickers')?>	
	<form name="frmgenenv" id="frmgenenv" method="post" action="admin.php?page=mgk/admin&load=settings&mode=env_setup">
		<table width="100%" cellpadding="1" cellspacing="0" border="0" class="form-table">		
			<tbody>
				<tr>
					<td><b><?php _e('Please Select a jQueryUI version :','mgk')?></b></td>
				</tr>
				<tr>
					<td>						
						<select name="jqueryui_version" style="width:150px">
						<?php echo mgk_make_combo_options(mgk_get_jquery_ui_versions(), get_option('mgk_jqueryui_version'), MGK_VALUE_ONLY)?>
						</select>
						<div class="information"><?php _e('jQuery UI version to use, for best performance, version 1.8.2 is recommended if that works with your WP environment.','mgk')?></div>
					</td>
				</tr>
				<tr>
					<td><b><?php echo __('Disable Core jQuery On Site:','mgk')?></b></td>
				</tr>
				<tr>
					<td>
						<input type="checkbox" name="disable_core_jquery" value="Y"  <?php echo (get_option('mgk_disable_core_jquery') == 'Y') ? 'checked' : '';?>/> <?php _e('Yes','mgk')?>
						<div class="information"><?php _e('Easy way to solve jQuery clash problem i.e. with Thesis Theme. Only stop jQuery on Theme/Site.','mgk'); ?></div>						
					</td>
				</tr>
			</tbody>
			<tfoot>	
				<tr>
					<td height="10px">
						<p>	
							<input type="button" class="button" onclick="mgk_env_setup()" value="<?php _e('SETUP &raquo;','mgk') ?>"/>
						</p>
					</td>
				</tr>	
			</tfoot>
		</table>		
		<input type="hidden" name="process" value="true" />
	</form>		
	<br />	
	<?php mgk_box_bottom()?>	
	<script language="javascript">
	<!--
		// onready
		jQuery(document).ready(function(){   						
			// first field focus 	
			jQuery("#frmgenset :input:first").focus();		
			// blur
			jQuery("#frmgenset :input[name='settings[general][affiliate_login_url]']").blur(function(e){				
				jQuery('#affiliate_login_url').html(jQuery(this).val());
			});		
			// editor	
			jQuery("#frmgenset textarea[id]").each(function(){
				new nicEditor({fullPanel : true, iconsPath: '<?php echo MGK_ASSETS_URL?>js/nicedit/nicEditorIcons.gif'}).panelInstance(jQuery(this).attr('id')); 					
			});				
			// add login form validation
			jQuery("#frmgenset").validate({					
				submitHandler: function(form) {					    					
					jQuery("#frmgenset").ajaxSubmit({type: "POST",
					  url: 'admin.php?page=mgk/admin&load=settings&mode=general',
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
						jQuery('#wrap-admin-settings #message').remove();		
						jQuery('#wrap-admin-settings').prepend('<div id="message" class="running"> <?php _e('Saving','mgk');?>...</div>');									  	
					  },
					  success: function(data){	
							// remove 										   														
							jQuery('#wrap-admin-settings #message').remove();														
							// show message
							jQuery('#wrap-admin-settings').prepend('<div id="message"></div>');	
							// show
							jQuery('#wrap-admin-settings #message').addClass(data.status).html(data.message);	
							// focus
							jQuery.scrollTo('#admin_settings',400);								   	
					  }});    		
					return false;											
				},
				rules: {			
					'settings[general][affliate_site_title]': "required",
					'settings[general][affliate_site_copyright]': "required",
					'settings[general][cookie_life]': {required:true,digits:true},
					'settings[general][commission_level]':{required:function(){ return !jQuery("#frmgenset :checkbox[name='settings[general][tier_commission]']").attr('checked');}},
					'settings[general][recurring_commission_level]':{required:function(){ return jQuery("#frmgenset :checkbox[name='settings[general][recurring_commission_enabled]']").attr('checked');}},					
					'settings[general][signup_bonus]': {required:false,number:true},
					'settings[general][currency_symbol]': "required",
					'settings[general][currency_code]': "required",
					'settings[general][auto_commission_reversal]': "required",
					'settings[general][contact_email]': {required:true,email:true},	
					'settings[general][affiliate_login_url]': "required",
					'settings[general][pagination]': {required:true,digits:true}	
				},
				messages: {			
					'settings[general][affliate_site_title]': "<?php _e('Please enter site title','mgk')?>",
					'settings[general][affliate_site_copyright]': "<?php _e('Please enter site copyright','mgk')?>",					
					'settings[general][cookie_life]': "<?php _e('Please enter cookie life in days','mgk')?>",
					'settings[general][commission_level]': "<?php _e('Please enter commission level','mgk')?>",
					'settings[general][recurring_commission_level]': "<?php _e('Please enter recurring commission level','mgk')?>",
					'settings[general][signup_bonus]': {number:"<?php _e('Please enter signup bonus in amount','mgk')?>"},
					'settings[general][currency_symbol]': "<?php _e('Please enter currency symbol','mgk')?>",
					'settings[general][currency_code]': "<?php _e('Please enter currency code','mgk')?>",
					'settings[general][auto_commission_reversal]': "<?php _e('Please select auto commission reversal','mgk')?>",
					'settings[general][contact_email]': {required:"<?php _e('Please enter contact email','mgk')?>",email:"<?php _e('Email should be in valid format','mgk')?>"},	
					'settings[general][affiliate_login_url]': "<?php _e('Please enter Affiliate Center URL name','mgk')?>",
					'settings[general][pagination]': {required:"<?php _e('Please enter pagination limit','mgk')?>",digits:"<?php _e('Please enter pagination in digit','mgk')?>"}		
				},
				errorClass: 'validation-error',
				errorPlacement: function(error, element) {
					if(element.is(":input[name^='settings[general][recurring_commission_level]']"))		
						element.parent().parent().append( error );	
					else if(element.is(":input[name^='settings[general]']"))						
						element.parent().append( error );										
					else							
						error.insertAfter(element);
				}
			});		
			// check bind
			jQuery("#frmgenset :checkbox[name='settings[general][tier_commission]']").bind('click',function(){
				if(jQuery(this).attr('checked')){
					jQuery('#bl_tier_rates').show();
				}else{
					jQuery('#bl_tier_rates').hide();
				}
			});
			// check bind
			jQuery("#frmgenset :checkbox[name='settings[general][recurring_commission_enabled]']").bind('click',function(){
				if(jQuery(this).attr('checked')){
					jQuery('#bl_recurring_commission').show();
				}else{
					jQuery('#bl_recurring_commission').hide();
				}
			});
			// select bind					
			jQuery("#frmgenset select[name='settings[general][tier_levels]']").bind('change',function(){
				var pre_tier_commissions=null;
				try{	
					pre_tier_commissions=<?php echo json_encode($settings['tier_commissions'])?>;
				}catch(x){}					
				// remove old
				jQuery('#bl_tier_values').children().remove();
				// loop
				
				for(var index=1;index<=jQuery(this).val();index++){
					// tier val
					try{
						tier_val=(pre_tier_commissions[index])?pre_tier_commissions[index]:0;
					}catch(x){tier_val=0;}
					// create
					jQuery('#bl_tier_values').append('<li>Tier '+index+': <input type="text" name="settings[general][tier_commissions]['+index+']" value="'+tier_val+'" size="5">%</li>');
				}
			}).change();
			// remove
			remove_mgk=function(e){
				if(!confirm('<?php _e('Are you sure you want to remove all Magic Kicker data?','mgk')?>')) 
					return;					
				
				// process
				jQuery("#frmgenuni").ajaxSubmit({type: "POST",
					  url: 'admin.php?page=mgk/admin&load=settings&mode=remove_plugin',
					  dataType: 'json',											 
					  beforeSubmit: function(){	
						jQuery('#wrap-admin-settings #message').remove();		
						jQuery('#wrap-admin-settings').prepend('<div id="message" class="running"> <?php _e('Processing','mgk')?>...</div>');									  	
					  },
					  success: function(data){	
							// success
							if(data.status=='success'){														
								window.location.href=data.redirect;
							}
							
							// remove 										   																											
							jQuery('#wrap-admin-settings #message').remove();														
							// show message
							jQuery('#wrap-admin-settings').prepend('<div id="message"></div>');	
							// show
							jQuery('#wrap-admin-settings #message').addClass(data.status).html(data.message);	
							// focus
							jQuery.scrollTo('#admin_settings',400);								   	
					  }}); 
			}		
				
			// mgk_env_setup	
			mgk_env_setup = function(){				
				jQuery('#frmgenenv').ajaxSubmit({
					 dataType: 'json',											 
					  beforeSubmit: function(){	
						// show message
						mgk_show_message('#frmgenenv', {status:'running', message:'<?php _e('Processing','mgk')?>...'},true);						
					  },
					  success: function(data){	
							// message																				
							mgk_show_message('#frmgenenv', data);		
							
							// success	
							if(data.status=='success'){																													
								// redirect
								if(data.redirect != ''){
									window.location.href = data.redirect;
								}										
							}	
					  }
				});				
			}
						
			// toggle
			mgk_toggle_lo_options =function(s){
				if(s=='lockout'){
					jQuery('#lockout_options_div').slideDown();
				}else{
					jQuery('#lockout_options_div').slideUp();	
				}	
			}
			
			// toggle
			mgk_toggle_eo_options=function(s){
				if(s=='timed'){
					jQuery('#lockout_mins_div').slideDown();
					jQuery('#lockout_email_div').slideUp();
				}else{
					jQuery('#lockout_email_div').slideDown();
					jQuery('#lockout_mins_div').slideUp();	
				}	
			}
			
		});	
	//-->		
	</script>
	<?php
}

// email templates
function f_emailtemplates(){
	global $wpdb;
	// save
	if(isset($_POST['process']) && $_POST['process']=='true'){		
		extract($_POST);
		if(isset($settings)){
			// merge old settings
			$old_settings = get_option('mgk_settings');
			// set
			$new_settings['emailtemplates']['sale_notification_subject']   = addslashes(mgk_stripslashes_deep($_POST['settings']['emailtemplates']['sale_notification_subject']));
			$new_settings['emailtemplates']['sale_notification_body']      = addslashes(mgk_stripslashes_deep($_POST['settings']['emailtemplates']['sale_notification_body']));
			$new_settings['emailtemplates']['signup_notification_subject'] = addslashes(mgk_stripslashes_deep($_POST['settings']['emailtemplates']['signup_notification_subject']));
			$new_settings['emailtemplates']['signup_notification_body']    = addslashes(mgk_stripslashes_deep($_POST['settings']['emailtemplates']['signup_notification_body']));
			// log
			// mgk_log(print_r($new_settings,true));
			// array merge
			if(is_array($old_settings)){
				// merge email template
				$new_settings['emailtemplates'] = array_merge($old_settings['emailtemplates'], $new_settings['emailtemplates']);
				// log
				// mgk_log(print_r($new_settings,true));
				// merge with main
				$new_settings = array_merge($old_settings, $new_settings);
			}
			// save
			update_option('mgk_settings', $new_settings);
			// log	
			// mgk_log(print_r($new_settings,true));				
			// message
			$status  = 'success';
			$message = __('Email templates updated successfully.','mgk');		
		}else{
			$status  = 'error';
			$message = __('Email templates not provided.','mgk');
		}		
		// the response
		echo json_encode(array('status'=>$status,'message'=>$message));
		exit();
	}
	// old settings
	$mgk_settings = get_option('mgk_settings');
	$settings     = $mgk_settings['emailtemplates'];
	// mgk_array_dump($settings);		
	// show form
	?>
	<?php mgk_box_top('Email Templates')?>	
	<form name="frmemtset" id="frmemtset" method="post" action="admin.php?page=mgk/admin&load=settings&mode=emailtemplates">
		<table width="100%" cellpadding="1" cellspacing="0" border="0" class="form-table">		
			<tbody>				
				<tr>
					<td valign="top" height="30"><span class="required-field"><b><?php _e('Sale Notification','mgk') ?></b></span></td>
				</tr>
				<tr>	
					<td valign="top">
						<table>
							<tr>
								<td><b><?php _e('Subject','mgk')?></b></td>
							</tr>
							<tr>
								<td>
									<input type="text" name="settings[emailtemplates][sale_notification_subject]" size="80" value="<?php echo $settings['sale_notification_subject']?>"/>								
									<div></div>							
									<label></label>
								</td>
							</tr>
							<tr>
								<td><b><?php _e('Body','mgk')?></b></td>
							</tr>
							<tr>
								<td>
									<textarea cols="80" rows="10" name="settings[emailtemplates][sale_notification_body]" id="settings_emailtemplates_sale_notification_body" style="height:200px; width:750px"><?php echo mgk_stripslashes_deep(esc_html($settings['sale_notification_body']))?></textarea>						
									<div class="tips"><?php _e('Email template body for after sale notification to affiliates.','mgk') ?></div>
									<label></label>
								</td>
							</tr>
						</table>								
					</td>
				</tr>	
				<tr>
					<td valign="top" height="30"><span class="required-field"><b><?php _e('Signup Notification','mgk') ?></b></span></td>
				</tr>
				<tr>	
					<td valign="top">
						<table>
							<tr>
								<td><b><?php _e('Subject','mgk')?></b></td>
							</tr>
							<tr>
								<td>
									<input type="text" name="settings[emailtemplates][signup_notification_subject]" size="80" value="<?php echo $settings['signup_notification_subject']?>"/>								
									<div></div>							
									<label></label>
								</td>
							</tr>
							<tr>
								<td><b><?php _e('Body','mgk')?></b></td>
							</tr>
							<tr>
								<td>
									<textarea cols="80" rows="10" name="settings[emailtemplates][signup_notification_body]" id="settings_emailtemplates_signup_notification_body" style="height:200px; width:750px"><?php echo mgk_stripslashes_deep(esc_html($settings['signup_notification_body']))?></textarea>						
									<div class="tips"><?php _e('Email template body for after signup notification to affiliates.','mgk') ?></div>
									<label></label>
								</td>
							</tr>
						</table>	
					</td>
				</tr>							
				<tr>					
					<td valign="top">
						<input type="submit" name="btn_save" value="<?php _e('Save','mgk')?>" class="button"/>						
					</td>
				</tr>
			</tbody>	
		</table>	
		<input type="hidden" name="process" value="true" />
	</form>											
	<br />	
	<?php mgk_box_bottom()?>
	<script language="javascript">
		<!--
		// onready
		jQuery(document).ready(function(){   		
			// editor instance first	
			jQuery("#frmemtset textarea[id]").each(function(){
				new nicEditor({fullPanel : true, iconsPath: '<?php echo MGK_ASSETS_URL?>js/nicedit/nicEditorIcons.gif'}).panelInstance(jQuery(this).attr('id')); 					
			});					
			// first field focus 	
			jQuery("#frmemtset :input:first").focus();							
			// add login form validation
			jQuery("#frmemtset").validate({					
				submitHandler: function(form) {					    					
					jQuery("#frmemtset").ajaxSubmit({type: "POST",
					  url: 'admin.php?page=mgk/admin&load=settings&mode=emailtemplates',
					  dataType: 'json',					  										 
					  beforeSubmit: function(){	
						jQuery('#wrap-admin-settings #message').remove();		
						jQuery('#wrap-admin-settings').prepend('<div id="message" class="running"> <?php _e('Saving','mgk');?>...</div>');									  	
					  },
					  success: function(data){	
							// remove 										   														
							jQuery('#wrap-admin-settings #message').remove();														
							// show message
							jQuery('#wrap-admin-settings').prepend('<div id="message"></div>');	
							// show
							jQuery('#wrap-admin-settings #message').addClass(data.status).html(data.message);	
							// focus
							jQuery.scrollTo('#admin_settings',400);								   	
					  }});    		
					return false;											
				},
				rules: {								
					'settings[emailtemplates][sale_notification_subject]': "required",
					'settings[emailtemplates][sale_notification_body]': "required"				
				},
				messages: {			
					'settings[emailtemplates][sale_notification_subject]': "<?php _e('Please enter sale notification subject','mgk');?>",
					'settings[emailtemplates][sale_notification_body]': "<?php _e('Please enter sale notification template body','mgk');?>"
				},
				errorClass: 'validation-error',
				errorPlacement: function(error, element) {
					if(element.is(":input[name^='settings[emailtemplates]']"))
						error.appendTo( element.next().next() );											
					else							
						error.insertAfter(element);
				}
			});						
		});	
		//-->		
	</script>
	<?php
}

// remove plugin
function f_remove_plugin(){
	$plugin = trim('magickicker/magickicker.php');
	if (is_plugin_active($plugin)) {
		// deactivate first
		deactivate_plugins($plugin, true);
		// remove all
		mgk_deactivate(true);
		// redirect
		$status   ='success';
		$message  =__('Plugin deactivated','mgk');
		$redirect ='plugins.php?deactivate=true&plugin_status=active&paged=1'; 
	}else{
		$status  ='error';
		$message =__('Plugin already deactivated','mgk');
	}	
	// response
	echo json_encode(array('status'=>$status,'message'=>$message,'redirect'=>$redirect));
}

// mgk_env_setup
function f_env_setup(){
	// save
	if(isset($_POST['process']) && $_POST['process']=='true'){		
		extract($_POST);		
		// message				
		$status   ='success';
		$message  =__('Environment setup completed successfully.','mgk');	
		$redirect = 'admin.php?page=mgk/admin'; 	
		// update		
		update_option('mgk_jqueryui_version', $_POST['jqueryui_version']);
		update_option('mgk_disable_core_jquery', $_POST['disable_core_jquery']);		
		// the response
		echo json_encode(array('status'=>$status,'message'=>$message,'redirect'=>$redirect));
		exit();
	}		
	// echo json_encode(array('status'=>$status,'message'=>$message));
}
// end of file