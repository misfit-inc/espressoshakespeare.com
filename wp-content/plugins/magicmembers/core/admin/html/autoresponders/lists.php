<!--autoresponders lists-->
<?php mgm_box_top('Autoresponders Lists & Settings','autoresponder-lists-settings')?>
	<!--<form name="frmautoresponders" id="frmautoresponders" action="admin.php?page=mgm/admin/autoresponders&method=lists_update" method="post">-->
		<div id="autoresponders_list">
			<table width="100%" cellpadding="1" cellspacing="0" border="0" class="form-table">	
				<thead>
					<tr>
						<th><b><?php _e('Autoresponder Lists/Groups','mgm')?></b></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>				
							<div class='mgm'>
								<div id="autoresponders_lists_panel">
									<?php foreach($data['autoresponders'] as $autoresponder) :?>		
									<h3><a href="#"><b><?php echo ucwords($autoresponder)?></b></a></h3>
									<div>
										<p>							
											<!-- new ar-->
											<div id="ars_<?php echo $autoresponder?>">
												<?php echo $data['autoresponder'][$autoresponder]?>
											</div>
											<!--<p>
												<div style="padding-top:10px">	
													<a class="button" href="javascript:add_list('<?php echo $autoresponder; ?>')"><?php _e('Add New List &raquo;','mgm') ?></a>								
												</div>			
											</p>-->
											<div class="clearfix"></div>
										</p>
									</div>
									<?php endforeach?>		
								</div>			
							</div>
						</td>
					</tr>
				</tbody>	
				<!--<tfoot>
					<tr>
						<td valign="middle">				
							<div style="float:left">
								<input type="button" class="button" onclick="update_lists()" value="<?php _e('Update Settings','mgm') ?> &raquo;" />
							</div>	
						</td>
					</tr>
				</tfoot>-->
			</table>		
		</div>
	<!--</form>	-->
<?php mgm_box_bottom()?>	
	
<script language="javascript">
	<!--
	jQuery(document).ready(function(){	
		// setup autoresponders list
		setup_autoresponders_lists=function(){			
			// set up accordian
			jQuery("#autoresponders_lists_panel").accordion({
				collapsible: true,
				autoHeight: true,
				fillSpace: false,
				clearStyle: true,
				active: false
			});	
		}
		// add list
		add_list = function(autoresponder){		
			jQuery.ajax({ 
				url: 'admin.php?page=mgm/admin/autoresponders&method=lists_add',
				type: 'POST',
				cache: false,
				dataType: 'html',
				data: {module: autoresponder},
				success: function(data,textStatus){					 	
					jQuery('#ars_'+autoresponder+' .fields').append(data);
				}
			});
		}	
		// update list
		/*update_lists = function(){		
			// update
			jQuery("#frmautoresponders").ajaxSubmit({
				type: "POST",
				url: 'admin.php?page=mgm/admin/autoresponders&method=lists_update',
				dataType: 'json',			
				iframe: false,		
				cache: false,						 
				beforeSubmit: function(){
					// show message
					mgm_show_message('#frmautoresponders', {status:'running', message:'<?php _e('Processing','mgm')?>...'}, true);					
				},
				success: function(data){	
					// message																				
					mgm_show_message('#frmautoresponders', data);																		
				}}); 
		  // end 
		}*/			
		// setup autoresponders lists
		setup_autoresponders_lists();			
	});
	//-->
</script>
