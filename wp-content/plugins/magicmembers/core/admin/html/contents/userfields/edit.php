<form name="frmuserfldedit" id="frmuserfldedit" method="POST" action="admin.php?page=mgm/admin/contents&method=userfields_edit" style="margin: 0px; pading: 0px;">	
	<table width="100%"  cellpadding="1" cellspacing="0" border="0" class="widefat form-table">
		<thead>
			<tr>
				<th colspan="2"><?php _e('Edit Custom Field','mgm')?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td valign="top" width="20%"><span class="required-field"><?php _e('Label/Name','mgm')?></span>:</td> 
				<td valign="top">
					<input type="text" name="label" id="label" value="<?php echo stripslashes($data['custom_field']['label'])?>" size="50" maxlength="150"/>
				</td>
			</tr>	
			<tr>
				<td valign="top"><span class="required-field"><?php _e('Name','mgm')?></span>:</td> 
				<td valign="top">
					<input type="text" name="name" id="name" value="<?php echo stripslashes($data['custom_field']['name'])?>"size="50" maxlength="150" />
					<div class="tips"><?php _e('default lowercase label value, spaces replaced by underscore','mgm')?>.</div>
				</td>
			</tr>
			<tr>
				<td valign="top"><span class="required-field"><?php _e('Input Type','mgm')?></span>:</td> 
				<td valign="top">
					<select name="type" id="type">
						<?php echo mgm_make_combo_options($data['input_types'], $data['custom_field']['type'], MGM_KEY_VALUE)?>
					</select>
				</td>
			</tr>	
			<tr style="display:none">
				<td valign="top"><span class="required-field"><?php _e('Value','mgm')?></span>:</td> 
				<td valign="top">	
					<div id="value_element"></div>					
					<div class="tips" style="margin-top:5px">						
						<?php 
							// counry
							$countrynote = '';
							// check
							if($data['custom_field']['name'] == 'country'):
								 $countrynote = sprintf('For country, use 2 character <a href="%s" target="_blank">ISO Country code</a>.', 'http://www.iso.org/iso/english_country_names_and_code_elements');
							endif;?>
						<?php _e(sprintf('The default value for the field. %s',$countrynote),'mgm')?>.
					</div>
					<input type="hidden" name="old_value" id="old_value" value="<?php echo htmlentities(mgm_stripslashes_deep($data['custom_field']['value'])) ?>"/>
				</td>
			</tr>		
			<tr style="display:none">
				<td valign="top"><span class="required-field"><?php _e('Options','mgm')?></span>:</td> 
				<td valign="top">
					 <textarea name="options" id="options" rows="4" cols="40"><?php echo stripslashes($data['custom_field']['options'])?></textarea>
					 <div class="tips" style="width:600px">
					 	<?php _e('Options for multiple value fields. Applicable to field type Select, Checkbox and Radio.<br />
						Comma or semicolon separated values, eg: value1;value2;value3 OR value1,value2,value3.<br />
						Leave blank for Country, will be populated from database','mgm')?>.
					 </div>
				</td>
			</tr>			
			<tr>
				<td valign="top"><span class="not-required-field"><?php _e('Display/Attributes','mgm')?></span>:</td> 
				<td valign="top">
					<ul>
						<li><input type="checkbox" class="checkbox" name="required" value="1" <?php mgm_check_if_match(1, $data['custom_field']['attributes']['required'])?>/> <?php _e('Required!','mgm') ?></li>
						<li><input type="checkbox" class="checkbox" name="readonly" value="1" <?php mgm_check_if_match(1, $data['custom_field']['attributes']['readonly'])?>/> <?php _e('Readonly','mgm') ?></li>
						<li><input type="checkbox" class="checkbox" name="hide_label" value="1" <?php mgm_check_if_match(1, $data['custom_field']['attributes']['hide_label'])?>/> <?php _e('Hide Label','mgm') ?></li>
						<li><input type="checkbox" class="checkbox" name="on_register" value="1" <?php mgm_check_if_match(1, $data['custom_field']['display']['on_register'])?>/> <?php _e('Show On Register Page','mgm') ?></li>
						<li><input type="checkbox" class="checkbox" name="on_profile" value="1" <?php mgm_check_if_match(1, $data['custom_field']['display']['on_profile'])?>/> <?php _e('Show On Profile Page','mgm') ?></li>
						<li><input type="checkbox" class="checkbox" name="on_payment" value="1" <?php mgm_check_if_match(1, $data['custom_field']['display']['on_payment'])?>/> <?php _e('Show On Payment Page','mgm') ?></li>
						<li><input type="checkbox" class="checkbox" name="on_public_profile" value="1"  <?php mgm_check_if_match(1, $data['custom_field']['display']['on_public_profile'])?>/> <?php _e('Show On Public Profile Page','mgm') ?></li>
						<?php if($data['custom_field']['name']=='coupon'):?>
						<li><input type="checkbox" class="checkbox" name="on_upgrade" value="1" <?php mgm_check_if_match(1, $data['custom_field']['display']['on_upgrade'])?>/> <?php _e('Show On Upgrade Page','mgm') ?></li>
						<li><input type="checkbox" class="checkbox" name="on_extend" value="1" <?php mgm_check_if_match(1, $data['custom_field']['display']['on_extend'])?>/> <?php _e('Show On Extend Page','mgm') ?></li>
						<li><input type="checkbox" class="checkbox" name="on_postpurchase" value="1" <?php mgm_check_if_match(1, $data['custom_field']['display']['on_postpurchase'])?>/> <?php _e('Show On Post Purchase Page','mgm') ?></li>
						<?php endif;?>						
					</ul>					
					<div class="tips"><?php _e('Display/Usage settings for the fields','mgm')?>.</div>
				</td>
			</tr>			
		</tbody>
		<tfoot>
			<tr>
				<td valign="middle" colspan="2">					
					<div style="float: left;">
						<input class="button" type="submit" id="save_fields" name="save_fields" value="<?php _e('Save', 'mgm') ?> &raquo;" />				
					</div>
					<div style="float: right;">
						<input class="button" type="button" name="btn_cancel" value="&laquo; <?php _e('Cancel', 'mgm') ?>" onclick="mgm_userfield_add()"/>
					</div>				
				</td>
			</tr>
		</tfoot>
	</table>		
	<input type="hidden" name="id" value="<?php echo $data['custom_field']['id']?>" />
	<input type="hidden" name="system" value="<?php echo $data['custom_field']['system']?>" />	
</form>
<script language="javascript">
	<!--	
	// onready
	jQuery(document).ready(function(){  
		// enable/disable options
		mgm_switch_options = function(options){
			// all options
			var opts = ['required','readonly','hide_label','on_register','on_payment','on_profile','on_public_profile','on_upgrade','on_extend'];
			// hide all
			jQuery.each(opts, function(){ jQuery("#frmuserfldedit :checkbox[name='"+this+"']").parent().hide();});		
			// show selected
			jQuery.each(options, function(){ jQuery("#frmuserfldedit :checkbox[name='"+this+"']").parent().show();});
		}	
		// switch elements
		mgm_switch_elements = function(type){
			// capture data
			var value = jQuery('#frmuserfldedit #old_value').val();
			// by type
			switch(type){				
				case 'text':
				case 'textarea':
				case 'password':
				case 'image':
					jQuery('#frmuserfldedit #value_element').html('');
					jQuery('#frmuserfldedit #value_element').parent().parent().fadeOut();
					
					jQuery("#frmuserfldedit textarea[name='options']").attr('disabled',true);
					jQuery('#frmuserfldedit #options').parent().parent().fadeOut();
					
					// show fields
					if(type=='password'){
						sf = ['required','hide_label','on_register','on_profile'];
					}else{
						if(jQuery('#frmuserfldedit #name').val() == 'coupon'){
							sf = ['required','readonly','hide_label','on_register','on_upgrade','on_extend','on_postpurchase'];
						}else{
							sf = ['required','readonly','hide_label','on_register','on_profile','on_public_profile','on_payment'];
						}	
					}	
					// show
					mgm_switch_options(sf);
				break;
				case 'html':
					jQuery('#frmuserfldedit #value_element').html('<textarea name="value" id="value" style="height:200px; width:650px">'+value+'</textarea>');					
					jQuery('#frmuserfldedit #value_element').parent().parent().fadeIn();
					
					mgm_toggle_editor(true);
					
					jQuery("#frmuserfldedit textarea[name='options']").attr('disabled',true);
					jQuery('#frmuserfldedit #options').parent().parent().fadeOut();
					
					mgm_switch_options(['hide_label','on_register']);
				break;	
				case 'select':					
				case 'checkbox':						
				case 'radio':
						
					jQuery('#frmuserfldedit #value_element').html('<input type="text" name="value" id="value" value="'+value+'" size="100"/>');	
					jQuery('#frmuserfldedit #value_element').parent().parent().fadeIn();
					
					// dont show options if country				
					if(jQuery('#frmuserfldedit #name').val() != 'country'){	
						jQuery("#frmuserfldedit textarea[name='options']").attr('disabled',false);
						jQuery('#frmuserfldedit #options').parent().parent().fadeIn();						
					}	
					
					mgm_switch_options(['required','readonly','hide_label','on_register','on_profile','on_public_profile','on_payment']);
				break;					
				case 'hidden':
					jQuery('#frmuserfldedit #value_element').html('<input type="text" name="value" id="value" value="'+value+'" size="100"/>');
					jQuery('#frmuserfldedit #value_element').parent().parent().fadeIn();
					
					jQuery("#frmuserfldedit textarea[name='options']").attr('disabled',true);
					jQuery('#frmuserfldedit #options').parent().parent().fadeOut();
					
					mgm_switch_options(['required','on_register','on_profile','on_public_profile','on_payment']);
				break;	
				case 'label':	
					
					if(jQuery('#frmuserfldedit #name').val() == 'subscription_options' || jQuery('#frmuserfldedit #name').val() == 'payment_gateways'){				
						jQuery('#frmuserfldedit #value_element').html('');
						jQuery('#frmuserfldedit #value_element').parent().parent().fadeOut();
					}else{
						jQuery('#frmuserfldedit #value_element').html('<input type="text" name="value" id="value" value="'+value+'" size="100"/>');
						jQuery('#frmuserfldedit #value_element').parent().parent().fadeIn();
					}	
					
					jQuery("#frmuserfldedit textarea[name='options']").attr('disabled',true);
					jQuery('#frmuserfldedit #options').parent().parent().fadeOut();
					
					if(jQuery('#frmuserfldedit #name').val() == 'subscription_options' || jQuery('#frmuserfldedit #name').val() == 'payment_gateways'){
						mgm_switch_options(['required','hide_label','on_register']);
					}else{
						mgm_switch_options(['required','hide_label','on_register','readonly','on_profile','on_public_profile','on_payment']);
					}	
				break;	
				/*
				case 'image':
					jQuery('#frmuserfldedit #value_element').html('<input type="file" name="value" id="value" value="'+value+'" size="100"/>');
					jQuery('#frmuserfldedit #value_element').parent().parent().fadeIn();
					
					jQuery("#frmuserfldedit textarea[name='options']").attr('disabled',true);
					jQuery('#frmuserfldedit #options').parent().parent().fadeOut();
					
					mgm_switch_options(['required','readonly','on_payment','on_profile'], false);
				break;*/
				case 'captcha':
					jQuery('#frmuserfldedit #value_element').html('');
					jQuery('#frmuserfldedit #value_element').parent().parent().fadeOut();
					
					jQuery("#frmuserfldedit textarea[name='options']").attr('disabled',true);
					jQuery('#frmuserfldedit #options').parent().parent().fadeOut();
					
					mgm_switch_options(['on_register','hide_label']);
					
					jQuery("#frmuserfldedit input[name='required']").attr('checked',true);
					
					jQuery("#frmuserfldedit input[name='readonly']").attr('checked',false);
					jQuery("#frmuserfldedit input[name='on_profile']").attr('checked',false);
					jQuery("#frmuserfldedit input[name='on_payment']").attr('checked',false);
					jQuery("#frmuserfldedit input[name='on_public_profile']").attr('checked',false);
					
				break;
			}
		}
		// bind
		jQuery("#frmuserfldedit select[name='type']").bind('change', function(){			
			mgm_switch_elements(jQuery(this).val());
		});
		// edit : form validation
		jQuery("#frmuserfldedit").validate({
			submitHandler: function(form) {					    					
				jQuery("#frmuserfldedit").ajaxSubmit({type: "POST",
				  url: 'admin.php?page=mgm/admin/contents&method=userfields_edit',
				  dataType: 'json',				
				  iframe: false,							 
				  beforeSubmit: function(){	
				  	// save
					if(jQuery("#frmuserfldedit select[name='type']").val() == 'html'){
						mgm_save_editor();
					}
				  	// show message
					mgm_show_message('#userfields_manage', {status:'running', message:'<?php _e('Processing','mgm')?>...'},true);						
				  },
				  success: function(data){																	
						// success	
						if(data.status=='success'){																										
							// message																				
							mgm_show_message('#userfields_manage', data);
							// list																			
							mgm_userfield_list();														
						}else{															
							// message																				
							mgm_show_message('#userfields_manage', data);
						}														
				  }}); // end   		
				  return false;											
			},
			rules: {			
				label: "required",			
				name: "required",				
				type: "required",					
				'options': {required: function(){ 
						return ( 
							(jQuery.inArray(jQuery("#frmuserfldedit select[name='type']").val(), ['select','checkbox','radio']) !=-1) 
							 && (jQuery('#frmuserfldedit #name').val() != 'country')
						); 
					}
				 },
				'value': {required: function(){ return ( (jQuery.inArray(jQuery("#frmuserfldedit select[name='type']").val(), ['select','checkbox','radio','hidden','label','image']) !=-1) ); }}			
			},
			messages: {			
				label: "<?php _e('Please enter label','mgm')?>",
				name: "<?php _e('Please enter name','mgm')?>",
				type: "<?php _e('Please select a type ','mgm')?>",
				'options': "<?php _e('Please enter options','mgm')?>",
				'value': "<?php _e('Please enter value/default','mgm')?>"
			},
			errorClass: 'invalid',
			errorPlacement:function(error, element) {	
				if(element.is("[name='name']"))
					error.insertAfter(element.next());	
				else
					error.insertAfter(element);					
			}
		});	
		
		// toggle editor
		var v_editor = null;// instance
		// toggle
		mgm_toggle_editor=function(op) {
			// add
			if(op) {
				v_editor = new nicEditor({fullPanel : true, iconsPath: '<?php echo MGM_ASSETS_URL?>js/nicedit/nicEditorIcons.gif'}).panelInstance('value');
			} else {
				// check
				if(v_editor){
					v_editor.removeInstance('value');			
					v_editor = null;
				}
			}
		}
		//issue#: 353
		mgm_save_editor = function() {			
			if(v_editor && typeof(v_editor) == 'object')				  	
				v_editor.nicInstances[0].saveContent();
		}
		// save: important: the below line is required(issue#: 353 ) 
		 jQuery("#save_fields").bind('click', mgm_save_editor);
		// onload switch
		mgm_switch_elements('<?php echo $data['custom_field']['type']?>');		
	});		
	//-->	
</script>