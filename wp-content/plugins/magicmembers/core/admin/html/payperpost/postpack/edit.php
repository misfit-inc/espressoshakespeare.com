<form name="frmpostpackpedit" id="frmpostpackpedit" method="POST" action="admin.php?page=mgm/admin/payperpost&method=postpack_edit">
	<table width="100%"  cellpadding="1" cellspacing="0" border="0" class="widefat form-table">
		<thead>
			<tr>
				<th colspan="2"><b><?php _e('Edit Post Pack','mgm')?></b></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td valign="top" width="20%"><span class="required-field"><?php _e('Name','mgm')?></span>: </td>
				<td valign="top"><input type="text" name="name" size="100" maxlength="150" value="<?php echo $data['postpack']->name?>"/></td>
			</tr>
			<tr>	
				<td valign="top"><span class="required-field"><?php _e('Cost','mgm')?></span>: </td>
				<td valign="top"><input type="text" name="cost" size="10" maxlength="20" value="<?php echo $data['postpack']->cost?>"/> <em><?php echo $data['currency']?></em></td>
			</tr>
			<tr>	
				<td valign="top"><span class="required-field"><?php _e('Description','mgm')?></span>: </td>
				<td valign="top"><textarea name="description" cols="50" rows="5"><?php echo $data['postpack']->description?></textarea></td>
			</tr>	
			<?php 
			// post product mapping
			$payment_modules = mgm_get_class('system')->get_active_modules('payment');
			// post purchase settings
			if($payment_modules):		
				foreach($payment_modules as $payment_module) :
					echo mgm_get_module($payment_module)->settings_postpack_purchase(json_decode($data['postpack']->product, true));
				endforeach;		
			endif;?>
		</tbody>	
		<tfoot>
			<tr>
				<td valign="middle" colspan="2">					
					<div style="float: left;">			
						<input class="button" type="submit" name="save_postpack" value="<?php _e('Save', 'mgm') ?> &raquo;" />		
					</div>
					<div style="float: right;">
						<input class="button" type="button" name="btn_cancel" value="&laquo; <?php _e('Cancel', 'mgm') ?>" onclick="mgm_postpack_add()"/>
					</div>			
				</td>
			</tr>
		</tfoot>	
	</table>	
	<input type="hidden" name="id" value="<?php echo $data['postpack']->id?>" />
</form>
<script language="javascript">
	<!--	
	// onready
	jQuery(document).ready(function(){   
		// edit : form validation
		jQuery("#frmpostpackpedit").validate({
			submitHandler: function(form) {					    					
				jQuery("#frmpostpackpedit").ajaxSubmit({type: "POST",
				  url: 'admin.php?page=mgm/admin/payperpost&method=postpack_edit',
				  dataType: 'json',		
				  iframe: false,									 
				  beforeSubmit: function(){	
						// show message
						mgm_show_message('#postpack_manage', {status:'running', message:'<?php _e('Processing','mgm')?>...'}, true);												
				  },
				  success: function(data){	
						// message																				
						mgm_show_message('#postpack_manage', data);														
						// success	
						if(data.status=='success'){																				
							// load new list	
							mgm_postpack_list();
						}														
				  }}); // end   		
				return false;											
			},
			rules: {			
				name: "required",						
				cost: {required:true, number: true},
				description: "required"	
			},
			messages: {			
				name: "<?php _e('Please enter name','mgm')?>",
				cost: {required:"<?php _e('Please enter cost','mgm')?>",number:"<?php _e('Please enter number only','mgm')?>"},
				description: "<?php _e('Please enter description','mgm')?>"
			},
			errorClass: 'invalid',
			errorPlacement:function(error, element) {				
				if(element.is(":input[name='cost']"))
					error.insertAfter(element.next());										
				else		
					error.insertAfter(element);					
			}
		});	
	});	
	//-->	
</script>