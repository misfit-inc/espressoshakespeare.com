<!--autoresponders-->
<form name="frmaresp" id="frmaresp" method="post" action="admin.php?page=mgm/admin/settings&method=autoresponders">
	<?php mgm_box_top('Auto Responders');?>
	<?php
		foreach($data['module'] as $module_name):
			echo $module_name['html'];
		endforeach;
	?>
	<div class="clearfix"></div>
	<p class="submit"><input type="submit" name="update" value="<?php _e('Save','mgm'); ?> &raquo;" /></p>
	<?php mgm_box_bottom();?>
</form>
<script language="javascript">
	<!--
	jQuery(document).ready(function(){
		 jQuery('.module_box_wide').corner("5px");
		 
		 // add : form validation
		jQuery("#frmaresp").validate({
			submitHandler: function(form) {					    					
				jQuery("#frmaresp").ajaxSubmit({type: "POST",										  
				  dataType: 'json',		
				  iframe: false,									 
				  beforeSubmit: function(){	
					// show message
					mgm_show_message('#frmaresp', {status:'running', message:'<?php _e('Processing','mgm')?>...'});													
					// focus
					jQuery.scrollTo('#frmaresp',400);	
				  },
				  success: function(data){	
						// remove message										   														
						jQuery('#frmaresp #message').remove();													
						// success	
						if(data.status=='success'){													
							// create message
							jQuery('#frmaresp').prepend('<div id="message"></div>');
							// show
							jQuery('#frmaresp #message').addClass(data.status).html(data.message);																								
						}else{															
							// create message
							jQuery('#frmaresp').prepend('<div id="message"></div>');
							// show
							jQuery('#frmaresp #message').addClass(data.status).html(data.message);
						}														
				  }}); // end   		
				return false;											
			},
			/*rules: {			
				name: "required",						
				cost: "required",					
				description: "required"	
			},
			messages: {			
				name: "<?php _e('Please enter name','mgm')?>",
				cost: "<?php _e('Please enter cost','mgm')?>",
				description: "<?php _e('Please enter description','mgm')?>"
			},*/
			errorClass: 'invalid'
		});		
	});
	//-->
</script>		 