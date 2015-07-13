<form name="frmcoupadd" id="frmcoupadd" method="POST" action="admin.php?page=mgm/admin/members&method=coupon_add" style="margin: 0px; pading: 0px;">
	<table width="100%"  cellpadding="1" cellspacing="0" border="0" class="widefat form-table">
		<thead>
			<tr>
				<th colspan="2"><?php _e('Add Coupon','mgm')?></th>
			</tr>
		</thead>
		<tbody>
			<tr>				
				<td valign="top" width="20%"><span class="required"><?php _e('Name','mgm')?></span>: </td>
				<td valign="top"><input type="text" name="name" size="100" maxlength="150" value=""/></td>
			</tr>
			<tr>	
				<td valign="top"><span class="required"><?php _e('Value','mgm')?></span>: </td>
				<td valign="top">
					<input type="text" name="value" size="100" maxlength="100" value=""/>
					<div class="tips">
						<b><?php _e('Example coupon values:','mgm');?></b><br />
						<b><?php _e('FLAT COST','mgm');?>         :</b> "5"<br />
						<b><?php _e('PERCENT','mgm');?>           :</b> "5%"<br />
						<b><?php _e('SUBSCRIPTION PACK','mgm');?> :</b> 
						"sub_pack#5_6_M_pro-membership" <?php _e('Where','mgm');?> <br />
						"5" <?php _e('is "Cost"','mgm');?><br>
						"6" <?php _e('is "Duration Unit" ','mgm');?><br />
						"M" <?php _e('is "Duration Type" ( M = Month, D = DAY, Y = Year)','mgm');?><br />
						"pro-membership" <?php _e('is "Membership Type", all lowercase, spaces replaced by hyphen "-" i.e. gold-member','mgm');?><br />
						<b><?php _e('SUBSCRIPTION PACK WITH BILLING CYCLE','mgm');?> :</b> 
						"sub_pack#5_6_M_pro-membership_12" <?php _e('Where','mgm');?> <br />
						"5" <?php _e('is "Cost"','mgm');?><br>
						"6" <?php _e('is "Duration Unit" ','mgm');?><br />
						"M" <?php _e('is "Duration Type" ( M = Month, D = DAY, Y = Year)','mgm');?><br />
						"pro-membership" <?php _e('is "Membership Type", all lowercase, spaces replaced by hyphen "-" i.e. gold-member','mgm');?><br />
						"12" <?php _e('is "Billing Cycle" ( 0 to 99 ) ','mgm');?><br />
						<b><?php _e('TRIAL SUBSCRIPTION PACK','mgm');?> :</b> 
						"sub_pack_trial#6_M_5_2" <?php _e('Where','mgm');?> <br />						
						"6" <?php _e('is "Trial Duration Unit" ','mgm');?><br />
						"M" <?php _e('is "Trial Duration Type" ( M = Month, D = DAY, Y = Year)','mgm');?><br />
						"5" <?php _e('is "Trial Cost"','mgm');?><br>
						"2" <?php _e('is "Trial Occurrences"','mgm');?>.
					</div>
				</td>
			</tr>
			<tr>	
				<td valign="top"><span class="required"><?php _e('Description','mgm')?></span>:</td> 
				<td valign="top"><textarea name="description" cols="50" rows="5"></textarea></td>
			</tr>	
			<tr>				
				<td valign="top"><?php _e('Usage Limit','mgm')?>: </td>
				<td valign="top"><input type="text" name="use_limit" disabled="disabled" size="5" maxlength="10" value=""/>&nbsp;<input type="checkbox" checked="checked" name="use_unlimited" /> <?php _e('Unlimited','mgm')?>?</td>
			</tr>		
			<tr>
				<td><?php _e('Expire Date','mgm') ?></td>
				<td><input name="expire_dt" id="expire_dt" type="text" size="12" value="" /></td>
			</tr>
		</tbody>	
		<tfoot>
			<tr>
				<td valign="middle" colspan="2">					
					<div style="float: left;">			
						<input class="button" type="submit" name="save_coupon" value="<?php _e('Save', 'mgm') ?> &raquo;" />		
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
		jQuery("#frmcoupadd").validate({
			submitHandler: function(form) {					    					
				jQuery("#frmcoupadd").ajaxSubmit({type: "POST",
				  url: 'admin.php?page=mgm/admin/members&method=coupon_add',
				  dataType: 'json',				
				  iframe: false,							 
				  beforeSubmit: function(){	
					// show message
					mgm_show_message('#coupon_manage', {status:'running', message:'<?php _e('Processing','mgm')?>...'});												
					// focus
					jQuery.scrollTo('#frmcoupadd',400);	
				  },
				  success: function(data){	
				  		// message																				
						mgm_show_message('#coupon_manage', data);
						// success	
						if(data.status=='success'){
							// clear fields
							jQuery("#frmcoupadd :input").not(":input[type='hidden']").not(":input[type='submit']").not(":input[type='checkbox']").val('');	
							// load new list														
							mgm_coupon_list();										
						}														
				  }}); // end   		
				return false;											
			},
			rules: {			
				name: "required",						
				value: "required",					
				description: "required",
				use_limit:{digits:true}	
			},
			messages: {			
				name: "<?php _e('Please enter name','mgm')?>",
				value: "<?php _e('Please enter value','mgm')?>",
				description: "<?php _e('Please enter description','mgm')?>",
				use_limit:{digits:"<?php _e('Please enter number','mgm')?>"}	
			},
			errorClass: 'invalid',
			errorPlacement:function(error, element) {										
				error.insertAfter(element);					
			}
		});	
		// use limit
		jQuery("#frmcoupadd :input[name='use_unlimited']").bind('click', function(){
			if(jQuery(this).attr('checked')){
				jQuery("#frmcoupadd :input[name='use_limit']").val('').attr('disabled', true);
			}else{
				jQuery("#frmcoupadd :input[name='use_limit']").attr('disabled', false);
			}
		});
		// date picker
		mgm_date_picker("#frmcoupadd :input[name='expire_dt']",'<?php echo MGM_ASSETS_URL?>');
	});	
	//-->	
</script>