<!--upgrade : time to time upgrader, internal system, modules, code, database etc.-->
<!--core_setup-->
<?php mgm_box_top('Upgrade Magic Members');?>
	<form name="frmmgmupgrade" id="frmmgmupgrade" action="admin.php?page=mgm/admin/tools&method=upgrade" method="post">
		<table width="100%" cellpadding="1" cellspacing="0" border="0" class="form-table">				
			<tbody>				
				<tr>
					<td>
						<?php // _e('No Upgrade available','mgm') ?>
						<div id="update_data">
							<?php 
							// load remote data
							$upgrade_url=MGM_SERVICE_SITE.'upgrade_screen'.MGM_INFORMATION;//.'&new_version='.$_REQUEST['new_version'];				
							echo mgm_remote_request($upgrade_url,false);
							?>
						</div>	
					</td>
				</tr>
			</tbody>
			<!--<tfoot>	
				<tr>
					<td height="10px">
						<p>					
							<input type="button" class="button" onclick="core_setup()" value="<?php _e('UPGRADE &raquo;','mgm') ?>" disabled="disabled" />
						</p>
					</td>
				</tr>	
			</tfoot>-->
		</table>
		<input type="hidden" name="upgrade_execute" value="true" />
	</form>
<?php mgm_box_bottom();?>
<script language="javascript">
<!--
	jQuery(document).ready(function(){		
		// core_setup		
		core_setup = function(){
			//if(confirm('<?php _e('Are sure you want to update core version of Magic Members?','mgm') ?>')){
				jQuery('#frmmgmupgrade').ajaxSubmit({
					 dataType: 'json',		
					 iframe: false,									 
					 beforeSubmit: function(){	
					  	// show message
						mgm_show_message('#frmmgmupgrade', {status:'running', message:'<?php _e('Processing','mgm')?>...'},true);						
					 },
					 success: function(data){	
						// message																				
						mgm_show_message('#frmmgmupgrade', data);			
					 }
				});
			//}
		}
	});
//-->
</script>

