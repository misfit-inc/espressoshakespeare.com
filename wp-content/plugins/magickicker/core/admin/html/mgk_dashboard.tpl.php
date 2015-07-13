<?php
// get flag
$action_flag=$_REQUEST['mode'];
// call process
mgk_call_process($action_flag);
// define actions /////////////
// default list
function f_index(){
	if(!mgk_get_auth()){
		mgk_show_activation();
	}else{	
		mgk_show_dashboard();
	}	
}

// show_activation
function mgk_show_activation(){
	global $wpdb;		
	// show form
	?>
	<?php mgk_box_top('Activate Magic Kicker')?>	
	<div class="tab-error fade">
		<form name="frmactivate" id="frmactivate" method="post" action="admin.php?page=mgk/admin&load=dashboard&mode=activate">
			<table width="100%" cellpadding="1" cellspacing="0" border="0" class="form-table">		
				<tbody>				
					<tr>
						<td valign="top" colspan="2">
							<p><?php _e('Magic Kicker will not function until a valid License Key has been entered. Please enter your email address in the box below to activate the plugin. Please contact Magic Kickers if you need help with this.','mgk');?></p>
							<p><?php echo sprintf(__("If you don't have a key then please visit %s to purchase one.","mgk"),"<a href='http://www.magicmembers.com'>http://www.magicmembers.com</a>");?></p>
						</td>
					</tr>
					<tr>
						<td valign="top" colspan="2">
							<?php _e('Registration Email','mgk')?>: <input type="text" name="email" size="40"/> 
							<input type="submit" name="btn_activate" value="<?php _e('Activate','mgk')?>" class="button" />
							<label id="email-error"></label>
						</td>
					</tr>								
				</tbody>	
			</table>	
			<input type="hidden" name="process" value="true" />
		</form>	
		<br />
	</div>		
	<?php mgk_box_bottom()?>
	<script language="javascript">
		<!--
		// onready
		jQuery(document).ready(function(){   					
			// first field focus 	
			jQuery("#frmactivate :input:first").focus();							
			// add login form validation
			jQuery("#frmactivate").validate({					
				submitHandler: function(form) {					    					
					jQuery("#frmactivate").ajaxSubmit({type: "POST",
									          url: 'admin.php?page=mgk/admin&load=dashboard&mode=activate',
			                                  dataType: 'json',											 
											  beforeSubmit: function(){	
											  	jQuery('#wrap-admin-home #message').remove();		
												jQuery('#wrap-admin-home').prepend('<div id="message" class="running"> Requesting...</div>');									  	
											  },
											  success: function(data){	
											  		// remove 										   														
													jQuery('#wrap-admin-home #message').remove();	
													// cancel to list
													if(data.status=='success'){														
														window.location.href='<?php echo $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING']?>';													
													}
													// show message
													jQuery('#wrap-admin-home').prepend('<div id="message"></div>');	
													// show
													jQuery('#wrap-admin-home #message').addClass(data.status).html(data.message);	
													// focus
													jQuery.scrollTo('#admin_home',400);								   	
											  }});    		
					return false;											
				},
				rules: {			
					email:{
						required:true,
						email:true
					}				
				},
				messages: {	
					email: "<?php _e('Please enter a valid email address','mgk')?>"									
				},
				errorClass: 'validation-error',
				errorPlacement: function(error, element){
					error.appendTo(jQuery("#email-error"));
				}
			});				
		});	
		//-->		
	</script>
	<?php
}

// show_dashboard
function mgk_show_dashboard(){
	global $wpdb;
	?>
	<div>		
		<form name="frmstatsoverview" id="frmstatsoverview" method="post" action="admin.php?page=mgk/admin&load=dashboard">
		<table width="100%" cellpadding="1" cellspacing="0" border="0" class="form-table">
			<tr>
				<td colspan="2">					
					<?php 					
					mgk_box_top('Magic Kicker', '',false,array('width'=>830));
					_e('<p>Welcome to Magic Kicker. This plugin offers protection for your blog in that only one IP address per login can ever be used.</p><p>Use the settings page to set up your preferences. You can configure Magic Kicker allow a variable number of logins from the same IP over a variable number of minutes.</p><p>You have the option to lock out the account for a number of minutes or just log them out. If a lockout is chosen then you can set a time period for the lockout or require an email activation.</p><p>Using the admin links within Magic Kicker you can block IP addresses manually if need be and view any existing lockouts on the "Members" page.</p>', 'mgk');
					mgk_box_bottom(false);		
					?>															
				</td>				
			</tr>					
			<tr>
				<td valign="top">
					<?php
					mgk_box_top('Messages','messages',false,array('width'=>400));
					mgk_get_messages();
					mgk_box_bottom(false);	
					?>
				</td>
				<td valign="top">
					<?php
					mgk_box_top('Subscription Status','subscriptionstatus',false,array('width'=>400));
					mgk_get_subscription_status();
					mgk_box_bottom(false);	
					?>
				</td>
			</tr>
			<tr>	
				<td valign="top">
					<?php
					mgk_box_top('Version Check', '',false,array('width'=>400));
					mgk_check_version();
					mgk_box_bottom(false);	
					?>
				</td>
				<td valign="top"></td>				
			</tr>	
		</table>
		</form>
	</div>
	<script language="javascript">
		<!--
		// onready
		jQuery(document).ready(function(){   							
			// date
			jQuery("#frmstatsoverview :input[name^='sale_dt[']").each(function(i){
						jQuery(this)
						 .attr("size","11")
						 .attr("maxlength","10")						
						 .datepicker({showOn:'focus',buttonImage:'<?php echo MGK_ASSETS_URL?>/images/icons/calendar.png',buttonImageOnly:true});
			});
			// wrap scope ??? what a solution
			jQuery("#ui-datepicker-div").wrap("<div class='mgk'></div>");	
			
			// filter
			filter_sales=function(){				
				// add form validation
				jQuery("#frmstatsoverview").validate({					
					submitHandler: function(form) {					    					
						jQuery("#frmstatsoverview").ajaxSubmit({type: "POST",
												  url: 'admin.php?page=mgk/admin&load=dashboard&mode=stats_overview',
												  dataType: 'html',		
												  data:{filter_mode:'date'},										  										 
												  beforeSubmit: function(){	
													jQuery('#wrap-admin-home #message').remove();		
													jQuery('#wrap-admin-home').prepend('<div id="message" class="running"> <?php _e('Loading','mgk')?>...</div>');									  	
												  },
												  success: function(data){	
														// remove 										   														
														jQuery('#wrap-admin-home #message').remove();
														jQuery("#statsoverview").html(data);																																			   	
												  }});    		
						return false;											
					},
					rules: {							
						'sale_dt[start]': {required:false,date:true},
						'sale_dt[end]': {required:false,date:true}					
					},
					messages: {									
						'sale_dt[start]': {date:"<?php _e('Please enter valid date','mgk')?>"},					
						'sale_dt[end]': {date:"<?php _e('Please enter valid date','mgk')?>"}
					},
					errorClass: 'validation-error',
					errorPlacement: function(error, element) {						
						error.appendTo(jQuery("#error_all"));
					}
				});	
				// execute
				jQuery("#frmstatsoverview").submit();
			}
			// upgrade
			create_upgrade=function(v){				
				jQuery('#admin_home .content-div').tabs('remove',1);
				jQuery('#admin_home .content-div').tabs('add','admin.php?page=mgk/admin&load=dashboard&mode=upgrade&new_version='+v,'Upgrade',1);
				jQuery('#admin_home .content-div').tabs('select',1);
			}
		});
	//-->	
	</script>		
	<?php
}

// stats
function f_stats_overview(){	
	global $wpdb;
	extract($_POST);
	// default filter
	$filter="AND month(`[date_column]`)=month(CURRENT_DATE)";
	$message_header="for this Month";
	// date filter
	if(isset($sale_dt['start']) && !empty($sale_dt['start'])){
		if(isset($sale_dt['end']) && !empty($sale_dt['end'])){
			$filter=" AND (DATE_FORMAT(`[date_column]`,'%m/%d/%Y')>='{$sale_dt['start']}' AND DATE_FORMAT(`[date_column]`,'%m/%d/%Y')<='{$sale_dt['end']}')";
			$message_header=" between \"{$sale_dt['start']}\" and \"{$sale_dt['end']}\"";
		}else{
			$filter=" AND DATE_FORMAT(`[date_column]`,'%m/%d/%Y')>='{$sale_dt['start']}'";
			$message_header=" from \"{$sale_dt['start']}\" till date";
		}
	}
	
	$total_clicks      = $wpdb->get_var("SELECT COUNT(id) FROM ".TBL_CLICKS." WHERE 1 ".str_replace('[date_column]','hit_dt',$filter));
	$number_sales      = $wpdb->get_var("SELECT COUNT(id) FROM ".TBL_SALES." WHERE 1 ".str_replace('[date_column]','sale_dt',$filter));	
	$sales_amount      = $wpdb->get_var("SELECT SUM(totalprice) FROM `".TBL_SALES."` WHERE 1 ".str_replace('[date_column]','sale_dt',$filter));		
	$sales_commission  = $wpdb->get_var("SELECT SUM(commission_amount) FROM ".TBL_SALES." WHERE item_type='sales' ".str_replace('[date_column]','sale_dt',$filter));
	$signup_commission = $wpdb->get_var("SELECT SUM(commission_amount) FROM ".TBL_SALES." WHERE item_type='signup' ".str_replace('[date_column]','sale_dt',$filter));
	?>
	<div class="updated"><b><?php _e('Affiliate Stats Overview','mgk');?> <?php echo $message_header?></b></div>
	<ul>
		<li><?php _e('Total Clicks','mgk');?>: <?php echo $total_clicks?></li>
		<li><?php _e('Number of Sales','mgk');?>: <?php echo $number_sales?></li>
		<li><?php _e('Total Sales Amount','mgk');?>: <?php echo mgk_get_setting('currency_symbol').number_format($sales_amount,2,'.','.')?></li>
		<li><?php _e('Total Sales Commission','mgk');?>: <?php echo mgk_get_setting('currency_symbol').number_format($sales_commission,2,'.','.')?></li>
		<li><?php _e('Total New Affiliate Signup Commission','mgk');?>: <?php echo mgk_get_setting('currency_symbol').number_format($signup_commission,2,'.','.')?></li>							
	</ul>
	<?php
}
// activate
function f_activate(){
	// save
	if(isset($_POST['process']) && $_POST['process']=='true'){		
		extract($_POST);
		$status = 'error';
		if(!empty($email)){			
			$message=mgk_validate_subscription($email);			
			if($message===true){
				$status ='success';
				$message  =__('Your Acount has been activated.','mgk');
			}
		}else{			
			$message =__('Email is not provided.','mgk');
		}		
		// the response
		echo json_encode(array('status'=>$status,'message'=>$message));
		exit();
	}		
}
// 
function f_upgrade(){
	// upgrade
	if(isset($_GET['update'])){
		mgk_execute_upgrade();		
	}
	?>
	<form method="POST" class="form-table" action="admin.php?page=mgk/admin&load=dashboard&mode=upgrade&update=true">
	<ul>
		<li>
			<div id="update_data">
				<?php 
				$upgrade_url=MGK_PLUGIN_SERVICE_SITE.'upgrade_screen'.MGK_PLUGIN_INFORMATION.'&new_version='.$_REQUEST['new_version'];				
				echo mgk_remote_request($upgrade_url,false)?>
			</div>	
		</li>
	</ul>		
	<?php wp_nonce_field( 'mgk_upgrade', 'mgk_upgrade', false );?>	
	<input type="hidden" name="new_version" value="<?php echo $_GET['new_version']?>" />
	</form>
	<?php
}

// execute
function mgk_execute_upgrade(){	
	// get upgrader
	include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
    // download file
  	$download_file  = mgk_download_url(MGK_PLUGIN_SERVICE_SITE.'download&hash='.$_POST['hash'].'&filename='.$_POST['filename']);
	$downloadedfile = basename($download_file);
	$plugin         = trim('magickicker/magickicker.php');	
	
	// $plugin=trim('test_plugin/test_plugin.php');
	// $downloadedfile='test_plugin-v2.0.zip';
	// if active
	if (is_plugin_active($plugin)) {
		// deactivate first
		deactivate_plugins($plugin, true);
		// upgrader
		$plugin_upgrader=new Plugin_Upgrader();
		// upgrade from local
		$plugin_upgrader->run(array(
					'package' => WP_CONTENT_DIR.DIRECTORY_SEPARATOR.$downloadedfile,
					'destination' => WP_PLUGIN_DIR,
					'clear_destination' => true,
					'clear_working' => true,
					'hook_extra' => array('plugin' => $plugin)
				));	
		// acivate
		// activate_plugin($plugin);
		// activate_plugin($plugin,get_option('siteurl').'/wp-admin/admin.php?page=mgk/admin&tabs=admin_home&upgrade=success');
		// wp_redirect( 'update.php?action=activate-plugin&plugin=' . $plugin . '&_wpnonce=' . $_POST['_wpnonce'] );
		wp_redirect('plugins.php?plugin_status=inactive');		
		exit();
	}	
}
// end of file