<!--reset-->
<?php mgm_box_top('Reset Magic Members');?>
	<form name="frmresetmgm" id="frmresetmgm" action="admin.php?page=mgm/admin/tools&method=system_reset" method="post">
		<table width="100%" cellpadding="1" cellspacing="0" border="0" class="form-table">				
			<tbody>
				<tr>
					<td><b><?php echo __('Please Select a reset type :','mgm')?></b></td>
				</tr>
				<tr>
					<td>
						<?php $opts = array('settonly'=>__('Reset Settings Only','mgm'),
						                    'settntable'=>__('Reset Settings and Tables','mgm'),
											'fullreset'=>__('Full Reset (Deactivate)','mgm'))?>
						<?php echo mgm_make_radio_group('reset_type', $opts, 'settonly', MGM_KEY_VALUE)?>
					</td>
				</tr>
			</tbody>
			<tfoot>	
				<tr>
					<td height="10px">
						<p>					
							<input type="button" class="button" onclick="system_reset()" value="<?php _e('RESET &raquo;','mgm') ?>" />
						</p>
					</td>
				</tr>	
			</tfoot>
		</table>
		<input type="hidden" name="reset_execute" value="true" />
	</form>
<?php mgm_box_bottom();?>
<script language="javascript">
<!--
	jQuery(document).ready(function(){		
		// reset		
		system_reset = function(){
			var reset_type = jQuery("#frmresetmgm :radio[name='reset_type']:checked").val();
			
			switch(reset_type){
				case 'settonly':
					var message = "<?php _e('This will erase all custom settings and revert to factory settings.','mgm') ?>";
				break;
				case 'settntable':
					var message = "<?php _e('This will erase all custom settings, post settings, table data (coupons etc.) and revert to factory settings.','mgm') ?>";
				break;
				case 'fullreset':
					var message = "<?php _e('This will erase all Magic Members data and deactivate the plugin. To deactivate without erasing data, please use Wordpress Plugin Management Interface.','mgm') ?>";
				break;
			}		
			// warn
			if(confirm('<?php _e('Are sure you want to reset Magic Members? ','mgm') ?>'+message)){
				jQuery('#frmresetmgm').ajaxSubmit({
					 dataType: 'json',		
					 iframe: false,									 
					 beforeSubmit: function(){	
					  	// show message
						mgm_show_message('#frmresetmgm', {status:'running', message:'<?php _e('Processing','mgm')?>...'}, true);						
					 },
					 success: function(data){	
						// message																				
						mgm_show_message('#frmresetmgm', data);																					
												
						// success	
						if(data.status=='success'){																													
							// redirect
							if(data.redirect && data.redirect!=''){
								window.location.href = data.redirect;
							}										
						}													
					 }
				});
			}
		}
	});
//-->
</script>
