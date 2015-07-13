<!--restapi-->
<?php mgm_box_top('Server Settings');?>
	<form name="frmrestserversett" id="frmrestserversett" method="post" action="admin.php?page=mgm/admin/settings&method=restapi">
		<table width="100%" cellpadding="1" cellspacing="0" border="0">			
			<tr>
				<td valign="top"><p><b><?php _e('Enable REST Server','mgm'); ?>:</b></p></td>
			</tr>
			<tr>
				<td valign="top">
					<input type="radio" name="rest_server_enabled" value="Y" <?php echo ($data['system']->setting['rest_server_enabled']=='Y') ? 'checked="checked"': ''; ?>/> <?php _e('Yes','mgm')?>
					<input type="radio" name="rest_server_enabled" value="N" <?php echo ($data['system']->setting['rest_server_enabled']=='N') ? 'checked="checked"': ''; ?> /> <?php _e('No','mgm')?>
					<p><div class="tips width90"><?php _e('Enable REST Server.','mgm'); ?></div></p>
				</td>
			</tr>
			<tr>
				<td valign="top"><p><b><?php _e('Allow REST Output Formats','mgm'); ?>:</b></p></td>
			</tr>
			<tr>
				<td valign="top">
					<input type="checkbox" name="rest_output_formats[]" value="xml" <?php echo (in_array('xml',$data['system']->setting['rest_output_formats'])) ? 'checked="checked"': ''; ?>/> <?php _e('XML','mgm')?><br />
					<input type="checkbox" name="rest_output_formats[]" value="json" <?php echo (in_array('json',$data['system']->setting['rest_output_formats'])) ? 'checked="checked"': ''; ?> /> <?php _e('JSON','mgm')?><br />
					<input type="checkbox" name="rest_output_formats[]" value="phps" <?php echo (in_array('phps',$data['system']->setting['rest_output_formats'])) ? 'checked="checked"': ''; ?> /> <?php _e('SERIALIZED PHP STRING','mgm')?><br />
					<input type="checkbox" name="rest_output_formats[]" value="php" <?php echo (in_array('php',$data['system']->setting['rest_output_formats'])) ? 'checked="checked"': ''; ?> /> <?php _e('PHP ARRAY','mgm')?><br />										
					<p><div class="tips width90"><?php _e('Allowed output formats.','mgm'); ?></div></p>
				</td>
			</tr>
			<tr>
				<td valign="top"><p><b><?php _e('Allow REST Input Methods','mgm'); ?>:</b></p></td>
			</tr>
			<tr>
				<td valign="top">
					<input type="checkbox" name="rest_input_methods[]" value="get" <?php echo (in_array('get',$data['system']->setting['rest_input_methods'])) ? 'checked="checked"': ''; ?>/> <?php _e('GET','mgm')?><br />
					<input type="checkbox" name="rest_input_methods[]" value="post" <?php echo (in_array('post',$data['system']->setting['rest_input_methods'])) ? 'checked="checked"': ''; ?> /> <?php _e('POST','mgm')?><br />
					<input type="checkbox" name="rest_input_methods[]" value="put" <?php echo (in_array('put',$data['system']->setting['rest_input_methods'])) ? 'checked="checked"': ''; ?> /> <?php _e('PUT','mgm')?><br />
					<input type="checkbox" name="rest_input_methods[]" value="delete" <?php echo (in_array('delete',$data['system']->setting['rest_input_methods'])) ? 'checked="checked"': ''; ?> /> <?php _e('DELETE','mgm')?><br />										
					<p><div class="tips width90"><?php _e('Allowed input methods.','mgm'); ?></div></p>
				</td>
			</tr>
			<tr>
				<td valign="top"><p><b><?php _e('Default Consumption Limit','mgm'); ?>:</b></p></td>
			</tr>
			<tr>
				<td valign="top">
					<input type="text" name="rest_consumption_limit" value="<?php echo $data['system']->setting['rest_consumption_limit']?>" /> <?php _e('per hour','mgm')?>
					<p><div class="tips width90"><?php _e('Default request consumption limit.','mgm'); ?></div></p>
				</td>
			</tr>
		</table>
		<p class="submit" style="float:left">
			<input type="submit" name="settings_update" value="<?php _e('Save Settings','mgm') ?> &raquo;" />
		</p>
		<div class="clearfix"></div>	
	</form>
<?php mgm_box_bottom();?>

<?php mgm_box_top('API Access Levels');?>
	<div id="restapi_access_levels_list"></div>
	<div>
		<p class="submit" style="float:left">
			<input type="button" name="add_level_btn" value="<?php _e('Add Level','mgm') ?> &raquo;" onclick="mgm_api_level_add()" />
		</p>
	</div>
	<div class="clearfix"></div>
<?php mgm_box_bottom();?>

<?php mgm_box_top('API Access Keys');?>
	<div id="restapi_access_keys_list"></div>
	<div>
		<p class="submit" style="float:left">
			<input type="button" name="add_key_btn" value="<?php _e('Add Key','mgm') ?> &raquo;" onclick="mgm_api_key_add()"/>
		</p>
	</div>
	<div class="clearfix"></div>
<?php mgm_box_bottom();?>
		
<script language="javascript">
	<!--
	jQuery(document).ready(function(){		
		// add : form validation
		jQuery("#frmrestserversett").validate({
			submitHandler: function(form) {					    					
				jQuery("#frmrestserversett").ajaxSubmit({type: "POST",										  
				  dataType: 'json',			
				  iframe: false,								 
				  beforeSubmit: function(){	
				  	// show message
					mgm_show_message('#frmrestserversett', {status:'running', message:'<?php _e('Processing','mgm')?>...'},true);
				  },
				  success: function(data){	
					// show message
				  	mgm_show_message('#frmrestserversett', data);														
				  }}); // end   		
				return false;											
			},
			rules:{
				rest_server_enabled:{
					required:true
				},
				'rest_output_formats[]':{
					required:true,
					minlength: 1
				},
				'rest_input_methods[]':{
					required:true,
					minlength: 1
				},
				rest_consumption_limit:{
					required:true,
					digits: true
				}
			},	
			messages: {	
				rest_server_enabled: "<?php _e('Please select server status','mgm')?>",
				'rest_output_formats[]': "<?php _e('Please select one output format','mgm')?>",
				'rest_input_methods[]': "<?php _e('Please select one input method','mgm')?>",
				rest_consumption_limit: "<?php _e('Please enter valid limit, digits only','mgm')?>"
			},		
			errorClass: 'invalid',
			errorPlacement:function(error, element) {	
				if(element.is(":input[name='rest_output_formats[]']"))
					error.insertAfter(jQuery(":input[name='rest_output_formats[]']:last").next());
				else if(element.is(":input[name='rest_input_methods[]']"))
					error.insertAfter(jQuery(":input[name='rest_input_methods[]']:last").next());
				else if(element.is(":input[name='rest_consumption_limit']"))
					error.insertAfter(element.next());						
				else									
					error.insertAfter(element);
			}
		});		
		
		// load levels
		mgm_load_api_levels=function(){
			jQuery('#restapi_access_levels_list').load('admin.php?page=mgm/admin/settings&method=restapi_levels');
		}
		// level edit
		mgm_api_level_edit=function(id){
			
		}
		// level add
		mgm_api_level_add=function(id){
			
		}
		
		// load keys
		mgm_load_api_keys=function(){
			jQuery('#restapi_access_keys_list').load('admin.php?page=mgm/admin/settings&method=restapi_keys');
		}			
		// key edit
		mgm_api_key_edit=function(id){
			
		}		
		// key add
		mgm_api_key_add=function(id){
			
		}
		// load 
		mgm_load_api_levels();
		mgm_load_api_keys();
	});
	//-->
</script>			
	