<!--core setup: based on best performance, core swicth, necessary for some server i.e. godaddy-->
<?php mgm_box_top('Switch Core version of Magic Members');?>
	<form name="frmcoreswtch" id="frmcoreswtch" action="admin.php?page=mgm/admin/tools&method=core_setup" method="post">
		<table width="100%" cellpadding="1" cellspacing="0" border="0" class="form-table">				
			<tbody>
				<tr>
					<td><b><?php _e('Please Select a core version :','mgm')?></b></td>
				</tr>
				<tr>
					<td>
						<?php
						if(get_option('mgm_core_version')){
							$core_version = get_option('mgm_core_version');
						}else{
							$core_version = "core";
						}
						
						// init
						$core_versions = array();
						// scan 						
						if($core_folders = glob( MGM_BASE_DIR . 'core*', GLOB_ONLYDIR)){
							// loop
							foreach($core_folders as $core_folder){
								$core_versions[] = basename($core_folder);
							}	
						}	
						// default
						if(count($core_versions) == 0){
							$core_versions[] = $core_version;
						}				
						?>
						<select name="core_version" style="width:150px">
						<?php echo mgm_make_combo_options($core_versions, $core_version, MGM_VALUE_ONLY)?>
						</select>
						<?php if(count($core_versions)<=1):?>								
						<div class="information"><?php _e('This option will be operational once there are multiple core folders available.','mgm')?></div>
						<?php endif;?>
					</td>
				</tr>
			</tbody>
			<tfoot>	
				<tr>
					<td height="10px">
						<p>			
							<input type="button" class="button" onclick="core_switch()" value="<?php _e('SWITCH &raquo;','mgm') ?>" <?php if(count($core_versions)<=1):?>disabled="disabled"<?php endif;?>/>
						</p>
					</td>
				</tr>	
			</tfoot>
		</table>
		<input type="hidden" name="core_setup_execute" value="core_switch" />
	</form>
<?php mgm_box_bottom();?>

<?php mgm_box_top('Setup Environment for Magic Members');?>
	<form name="frmcoreenv" id="frmcoreenv" action="admin.php?page=mgm/admin/tools&method=core_setup" method="post">
		<table width="100%" cellpadding="1" cellspacing="0" border="0" class="form-table">				
			<tbody>
				<tr>
					<td><b><?php echo __('Please Select a jQueryUI version :','mgm')?></b></td>
				</tr>
				<tr>
					<td>						
						<select name="jqueryui_version" style="width:150px">
						<?php echo mgm_make_combo_options(mgm_get_jquery_ui_versions(), get_option('mgm_jqueryui_version'), MGM_VALUE_ONLY)?>
						</select>
						<div class="information" style="width:85%"><?php _e('jQuery UI version to use, for best performance, version 1.8.2 is recommended if that works with your WP environment.','mgm')?></div>
					</td>
				</tr>
				<tr>
					<td><b><?php echo __('Disable Core jQuery On Site:','mgm')?></b></td>
				</tr>
				<tr>
					<td>
						<input type="checkbox" name="disable_core_jquery" value="Y"  <?php echo (get_option('mgm_disable_core_jquery') == 'Y') ? 'checked' : '';?>/> <?php _e('Yes','mgm')?>
						<div class="information"><?php _e('Easy way to solve jQuery clash problem i.e. with Thesis Theme. Only stop jQuery on Theme/Site.','mgm'); ?></div>						
					</td>
				</tr>
			</tbody>
			<tfoot>	
				<tr>
					<td height="10px">
						<p>	
							<input type="button" class="button" onclick="core_env_setup()" value="<?php _e('SETUP &raquo;','mgm') ?>"/>
						</p>
					</td>
				</tr>	
			</tfoot>
		</table>
		<input type="hidden" name="core_setup_execute" value="core_env" />
	</form>
<?php mgm_box_bottom();?>
<script language="javascript">
<!--
	jQuery(document).ready(function(){		
		// core_switch		
		core_switch = function(){
			if(confirm('<?php _e('Are sure you want to switch core version of Magic Members?','mgm') ?>')){
				jQuery('#frmcoreswtch').ajaxSubmit({
					 dataType: 'json',			
					 iframe: false,								 
					 beforeSubmit: function(){	
					  	// show message
						mgm_show_message('#frmcoreswtch', {status:'running', message:'<?php _e('Processing','mgm')?>...'},true);						
					 },
					 success: function(data){	
						// message																				
						mgm_show_message('#frmcoreswtch', data);			
					 }
				});
			}
		}
		
		// core_env_setup	
		core_env_setup = function(){
			//if(confirm('<?php _e('Are sure you want to switch core version of Magic Members?','mgm') ?>')){
				jQuery('#frmcoreenv').ajaxSubmit({
					 dataType: 'json',	
					 iframe: false,										 
					 beforeSubmit: function(){	
					  	// show message
						mgm_show_message('#frmcoreenv', {status:'running', message:'<?php _e('Processing','mgm')?>...'},true);						
					 },
					 success: function(data){	
						// message																				
						mgm_show_message('#frmcoreenv', data);		
						
						// success	
						if(data.status=='success'){																													
							// redirect
							if(data.redirect != ''){
								window.location.href = data.redirect;
							}										
						}	
					 }
				});
			//}
		}
	});
//-->
</script>