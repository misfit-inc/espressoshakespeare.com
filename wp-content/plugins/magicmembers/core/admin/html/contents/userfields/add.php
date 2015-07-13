<form name="frmuserfldadd" id="frmuserfldadd" method="POST" action="admin.php?page=mgm/admin/contents&method=userfields_add" style="margin: 0px; pading: 0px;">
	<table width="100%" cellpadding="1" cellspacing="0" border="0" class="widefat form-table">
		<thead>
			<tr>
				<th colspan="2"><?php _e('Add Custom Field','mgm')?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td valign="top" width="20%"><span class="required-field"><?php _e('Label','mgm')?></span>:</td> 
				<td valign="top"><input type="text" name="label" id="label" size="50" maxlength="150"/></td>
			</tr>	
			<tr>
				<td valign="top"><span class="required-field"><?php _e('Name','mgm')?></span>:</td> 
				<td valign="top">
					<input type="text" name="name" id="name" size="50" maxlength="150" /><br />
					<div class="tips"><?php _e('default lowercase label value, spaces replaced by underscore','mgm')?>.</div>
				</td>
			</tr>	
			<tr>
				<td valign="top"><span class="required-field"><?php _e('Input Type','mgm')?></span>:</td> 
				<td valign="top">
					<select name="type" id="type">
						<?php echo mgm_make_combo_options($data['input_types'], 'text', MGM_KEY_VALUE)?>
					</select>
				</td>
			</tr>	
			<tr style="display:none">
				<td valign="top"><span class="required-field"><?php _e('Value','mgm')?></span>:</td> 
				<td valign="top">					
					<div id="value_element"></div>	
					<div class="tips" style="margin-top:5px"><?php _e('The default value for the field','mgm')?>.</div>
				</td>
			</tr>
			<tr style="display:none">
				<td valign="top"><span class="required-field"><?php _e('Options','mgm')?></span>:</td> 
				<td valign="top">
					 <textarea name="options" id="options" style="height:200px; width:650px" disabled="disabled"></textarea>
					 <div class="tips" style="width:600px">
					 	<?php _e('Options for multiple value fields. Applicable to field type Select, Checkbox and Radio.<br />
						Comma or semicolon separated values, eg: value1;value2;value3 OR value1,value2,value3','mgm')?>.
					 </div>
				</td>
			</tr>
			<tr>
				<td valign="top"><span class="not-required-field"><?php _e('Display/Attributes','mgm')?></span>:</td> 
				<td valign="top">
					<ul>
						<li><input type="checkbox" class="checkbox" name="required" value="1" /> <?php _e('Required!','mgm') ?></li>
						<li><input type="checkbox" class="checkbox" name="readonly" value="1" /> <?php _e('Readonly','mgm') ?></li>
						<li><input type="checkbox" class="checkbox" name="hide_label" value="1" /> <?php _e('Hide Label','mgm') ?></li>
						<li><input type="checkbox" class="checkbox" name="on_register" value="1" /> <?php _e('Show On Register Page','mgm') ?></li>
						<li><input type="checkbox" class="checkbox" name="on_profile" value="1" /> <?php _e('Show On Profile Page','mgm') ?></li>
						<li><input type="checkbox" class="checkbox" name="on_payment" value="1" /> <?php _e('Show On Payment Page','mgm') ?></li>
						<li><input type="checkbox" class="checkbox" name="on_public_profile" value="1" /> <?php _e('Show On Public Profile Page','mgm') ?></li>
					</ul>					
					<div class="tips"><?php _e('Display/Usage settings for the fields','mgm')?>.</div>
				</td>
			</tr>			
		</tbody>
		<tfoot>
			<tr>
				<td valign="middle" colspan="2">					
					<div style="float: left;">
						<input class="button" id="save_fields" type="submit" name="save_fields" value="<?php _e('Save', 'mgm') ?> &raquo;" />					
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
		// enabld/disable options
		mgm_switch_options = function(options){
			// all options
			var opts = ['required','readonly','hide_label','on_register','on_payment','on_profile','on_public_profile','on_upgrade','on_extend'];
			// hide all
			jQuery.each(opts, function(){ jQuery("#frmuserfldadd :checkbox[name='"+this+"']").parent().hide();});		
			// show selected
			jQuery.each(options, function(){ jQuery("#frmuserfldadd :checkbox[name='"+this+"']").parent().show();});		
		}		
		// switch elements
		mgm_switch_elements = function(type){
			// by type
			switch(type){				
				case 'text':
				case 'textarea':
				case 'password':
				case 'image':
					jQuery('#frmuserfldadd #value_element').html('');
					jQuery('#frmuserfldadd #value_element').parent().parent().fadeOut();
					
					jQuery("#frmuserfldadd textarea[name='options']").attr('disabled',true);
					jQuery('#frmuserfldadd #options').parent().parent().fadeOut();
					
					if(type=='password'){
						mgm_switch_options(['required','hide_label','on_register','on_profile']);
					}else{
						mgm_switch_options(['required','hide_label','readonly','on_register','on_profile','on_public_profile','on_payment']);
					}	
				break;
				case 'html':
					jQuery('#frmuserfldadd #value_element').html('<textarea name="value" id="value" style="height:200px; width:650px"></textarea>');
					mgm_toggle_editor(true);
					jQuery('#frmuserfldadd #value_element').parent().parent().fadeIn();
					
					jQuery("#frmuserfldadd textarea[name='options']").attr('disabled',true);
					jQuery('#frmuserfldadd #options').parent().parent().fadeOut();
					
					mgm_switch_options(['hide_label','on_register']);
				break;	
				case 'select':					
				case 'checkbox':						
				case 'radio':
					jQuery('#frmuserfldadd #value_element').html('<input type="text" name="value" id="value" value="" size="100"/>');	
					jQuery('#frmuserfldadd #value_element').parent().parent().fadeIn();
					
					jQuery("#frmuserfldadd textarea[name='options']").attr('disabled',false);
					jQuery('#frmuserfldadd #options').parent().parent().fadeIn();
					
					mgm_switch_options(['required','hide_label','readonly','on_register','on_profile','on_public_profile','on_payment']);
				break;					
				case 'hidden':
					jQuery('#frmuserfldadd #value_element').html('<input type="text" name="value" id="value" value="" size="100"/>');
					jQuery('#frmuserfldadd #value_element').parent().parent().fadeIn();
					
					jQuery("#frmuserfldadd textarea[name='options']").attr('disabled',true);
					jQuery('#frmuserfldadd #options').parent().parent().fadeOut();
					
					mgm_switch_options(['required','on_register','on_profile','on_public_profile','on_payment']);
				break;	
				case 'label':					
					jQuery('#frmuserfldadd #value_element').html('<input type="text" name="value" id="value" value="" size="100"/>');
					jQuery('#frmuserfldadd #value_element').parent().parent().fadeIn();
					
					jQuery("#frmuserfldadd textarea[name='options']").attr('disabled',true);
					jQuery('#frmuserfldadd #options').parent().parent().fadeOut();
					
					mgm_switch_options(['required','hide_label','readonly','on_register','on_profile','on_public_profile','on_payment']);
				break;	
				/*
				case 'image':
					jQuery('#frmuserfldadd #value_element').html('<input type="file" name="value" id="value" value="" size="50"/>');
					jQuery('#frmuserfldadd #value_element').parent().parent().fadeIn();
					
					jQuery("#frmuserfldadd textarea[name='options']").attr('disabled',true);
					jQuery('#frmuserfldadd #options').parent().parent().fadeOut();
					
					mgm_switch_options(['required','readonly','on_payment','on_profile'], false);
				break;*/
				case 'captcha':
					jQuery('#frmuserfldadd #value_element').html('');
					jQuery('#frmuserfldadd #value_element').parent().parent().fadeOut();
					
					jQuery("#frmuserfldadd textarea[name='options']").attr('disabled',true);
					jQuery('#frmuserfldadd #options').parent().parent().fadeOut();
					
					mgm_switch_options(['on_register','hide_label']);
					
					jQuery("#frmuserfldadd input[name='required']").attr('checked',true);
					jQuery("#frmuserfldadd input[name='readonly']").attr('checked',false);
					jQuery("#frmuserfldadd input[name='on_profile']").attr('checked',false);
					jQuery("#frmuserfldadd input[name='on_payment']").attr('checked',false);
					jQuery("#frmuserfldadd input[name='on_public_profile']").attr('checked',false);
				break;
			}	
		}		
		// bind
		jQuery("#frmuserfldadd select[name='type']").bind('change', function(){		
			mgm_switch_elements(jQuery(this).val());					
		});
		// add : form validation
		jQuery("#frmuserfldadd").validate({
			submitHandler: function(form) {					    					
				jQuery("#frmuserfldadd").ajaxSubmit({type: "POST",
				  url: 'admin.php?page=mgm/admin/contents&method=userfields_add',
				  dataType: 'json',			
				  iframe: false,								 
				  beforeSubmit: function(){	
				  	// save
					if(jQuery("#frmuserfldadd select[name='type']").val() == 'html'){
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
							// clear fields
							jQuery("#frmuserfldadd :input").not(":input[type='hidden']").not(":input[type='submit']").not(":input[type='checkbox']").val('');
							// checkboxes
							jQuery("#frmuserfldadd :input[type='checkbox']").attr('checked',false);		
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
				'name': "required",						
				type: "required",					
				'options': {required: function(){ return ( (jQuery.inArray(jQuery("#frmuserfldadd select[name='type']").val(), ['select','checkbox','radio']) !=-1) ); }},
				'value': {required: function(){ return ( (jQuery.inArray(jQuery("#frmuserfldadd select[name='type']").val(), ['select','checkbox','radio','hidden','label','image']) !=-1) ); }}		
			},
			messages: {			
				label: "<?php _e('Please enter label','mgm')?>",
				'name': "<?php _e('Please enter name','mgm')?>",
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
		
		// bind keyup
		jQuery('#label').bind('keyup', function(){
			jQuery('#name').val(jQuery('#label').val().toString().keyslug())
		});
		// bind blur
		jQuery('#label').bind('blur', function(){
			jQuery('#name').val(jQuery('#label').val().toString().keyslug())
		});		
	});	
	//-->	
</script>