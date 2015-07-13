<!--modules-->
<div class="payment_modules">
	<?php mgm_box_top('Payment Modules');?>		
	<?php
		foreach($data['modules'] as $module):
			echo '<div id="modform_cont_'.$module['code'].'">';
			echo $module['html'];
			echo '</div>';
		endforeach;
	?>		
	<div class="clearfix"></div>
	<?php mgm_box_bottom();?>
</div>
<script language="javascript">
	<!--
	jQuery(document).ready(function(){
		 // corner
		 jQuery('.module_box').corner("5px");			 	
		 // mgm_update_module_setting
		 mgm_update_module = function(form, act){
		 	// form id
		 	var form_id = jQuery(form).attr('id');		
			// act
			jQuery("#"+form_id+" :input[type='hidden'][name='act']").val(act);
			// post
		 	jQuery(form).ajaxSubmit({type: "POST",										  
				 dataType: 'json',		
				 iframe: false,				 										 
				 beforeSubmit: function(){	
					// show message
					mgm_show_message('#'+form_id, {status:'running', message:'<?php _e('Processing','mgm')?>...'});														
				 },
				 success: function(data){	
						// remove message										   														
						jQuery('#'+form_id+' #message').remove();													
						// success	
						if(data.status=='success'){													
							// create message
							jQuery('#'+form_id).prepend('<div id="message"></div>');
							// show
							jQuery('#'+form_id+' #message').addClass(data.status).html(data.message);	
							// create tab for act
							if(act == 'status_update'){
								// set new status										
								jQuery("#status_label_"+data.module.code).html((data.enable == 'Y') ? '<?php _e('Enabled','mgm');?>' : '<?php _e('Disabled','mgm')?>');
								// status update										
								mgm_update_payment_tabs(data);										
							}else if(act == 'logo_update'){
								jQuery('#modform_cont_'+data.module.code).load('admin.php?page=mgm/admin/payments&method=module_setting_box&module='+data.module.code, function(){
									// bind uploader
									mgm_file_uploader('.payment_modules', mgm_upload_logo);	// check
									// log
									// console.log('logo_update binder');
									// bind status modifier
									mgm_status_modifier();											
								});
							}	
						}else{															
							// create message
							jQuery('#'+form_id).prepend('<div id="message"></div>');
							// show
							jQuery('#'+form_id+' #message').addClass(data.status).html(data.message);
						}														
					 }
				}
			); // end  
		 }		
		 // mgm_update_payment_tabs
		 mgm_update_payment_tabs = function(data){	
			// undefined
		 	if(data.module == 'undefined')
				return;
			
			// remove disabled		
			index = 0 ; 		
			jQuery('#admin_payments .tabs li a[href]').each(function(){
				// remove
				if(data.enable == 'N'){
					if(jQuery(this).children('span').html() == data.module.name){					
						jQuery('#admin_payments .content-div').tabs('remove', index);
					}			
				}
				index++;				
			});
			// get length
			length = jQuery('#admin_payments .content-div').tabs( "length" );
			
			// add new tab
			if(data.enable == 'Y'){		
				// do not create for mgm_free,mgm_trial
				// if(jQuery.inArray(data.module.code, ['mgm_free','mgm_trial']) !=-1) return;
				// if(data.module.tab == false) return;
				
				// url
				var url = 'admin.php?page=mgm/admin/payments&method=module_settings&module='+data.module.code;
				
				// create
				jQuery('#admin_payments .content-div').tabs('add', '#'+data.module.code, data.module.name, (length-1));	// use hash code to properly create div	
				// reset
				jQuery('#admin_payments .content-div').tabs( 'url' , (length-1) , url );// set url now
				// select/load
				jQuery('#admin_payments .content-div').tabs('load', (length-1));				
			}
		 }
		 // update logo
		 mgm_update_logo=function(form_id){		
		 	// get form
		 	var form = jQuery('#'+form_id);	 	
			// send	logo_update		 		
			mgm_update_module(form, 'logo_update');
		 }		 
		 // bind enable/disable
		 mgm_status_modifier = function(){
		 	 // attach event
			 jQuery("#admin_payments :checkbox[name='payment[enable]']").bind('click', function(){
				// get form
				var form = jQuery(this).get(0).form;				
				// send	status_update		 		
				mgm_update_module(form, 'status_update');
			 });
		 }		 
		 // bind uploader for settings_box quick uploads
		 mgm_file_uploader('.payment_modules', mgm_upload_logo);		
		 // bind status modifier
		 mgm_status_modifier(); 
	});	 
	//-->
</script>

