<form name="frmpostpackpadd" id="frmpostpackpadd" method="POST" action="admin.php?page=mgm/admin/payperpost&method=postpack_add" style="margin: 0px; pading: 0px;">
	<table width="100%"  cellpadding="1" cellspacing="0" border="0" class="widefat form-table">
		<thead>
			<tr>
				<th colspan="2"><b><?php _e('Create a New Pack','mgm')?></b></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td valign="top" width="20%"><span class="required-field"><?php _e('Name','mgm')?></span>: </td>
				<td valign="top"><input type="text" name="name" size="100" maxlength="150" value="" /></td>
			</tr>
			<tr>	
				<td valign="top"><span class="required-field"><?php _e('Cost','mgm')?></span>: </td>
				<td valign="top"><input type="text" name="cost" size="10" maxlength="20" value="" /> <em><?php echo $data['currency']?></em></td>
			</tr>
			<tr>	
				<td valign="top"><span class="required-field"><?php _e('Description','mgm')?></span>: </td>
				<td valign="top"><textarea name="description" cols="50" rows="5"></textarea></td>				
			</tr>
			<?php 
			// post product mapping
			$payment_modules = mgm_get_class('system')->get_active_modules('payment');
			// post purchase settings
			if($payment_modules):		
				foreach($payment_modules as $payment_module) :
					echo mgm_get_module($payment_module)->settings_postpack_purchase(false);
				endforeach;		
			endif;?>
		</tbody>
		<tfoot>
			<tr>
				<td valign="middle" colspan="2">					
					<div style="float: left;">
						<input class="button" type="submit" name="save_postpack" value="<?php _e('Save', 'mgm') ?> &raquo;" />					
					</div>					
				</td>
			</tr>
		</tfoot>	
	</table>	
</form>
<script language="javascript">
	<!--	
	// onready
	jQuery(document).ready(function(){   
		// add : form validation
		jQuery("#frmpostpackpadd").validate({
			submitHandler: function(form) {					    					
				jQuery("#frmpostpackpadd").ajaxSubmit({type: "POST",
				  url: 'admin.php?page=mgm/admin/payperpost&method=postpack_add',
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
							// clear fields
							jQuery("#frmpostpackpadd :input").not(":input[type='hidden']").not(":input[type='submit']").not(":input[type='checkbox']").val('');																		
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