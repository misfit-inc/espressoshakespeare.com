<!--download add-->
<?php mgm_box_top('Add Download');?>
	<form name="frmdwnadd" id="frmdwnadd" action="admin.php?page=mgm/admin/contents&method=download_add" method="post" enctype="multipart/form-data">
		<table width="100%" cellpadding="1" cellspacing="0" border="0" class="form-table widefat">
			<tbody>
				<tr valign="middle">
					<td><?php _e('Title (required)','mgm') ?></td>
					<td><input name="title" id="title" type="text" size="100" value="" /></td>
				</tr>
				<tr valign="top">
					<td>
						<?php _e('Upload a file','mgm') ?>
					</td>
					<td>
						<input name="download_file" id="download_file" type="file" size="100"/>							
						<br /> 
						<?php _e('Direct Url','mgm') ?>: <input name="direct_url" id="direct_url" type="text" value="" size="100" maxlength="255"/>				
					</td>
				</tr>
				<tr valign="top">
					<td><?php _e('Restrict Access?','mgm') ?></td>
					<td>
						<input type="checkbox" name="members_only" <?php echo($download->members_only =='Y' ? "checked='checked'":'') ?> />
						<span style="color: gray; font-size: 10px; font-weight: normal;"><?php _e('If checked, only users of the appropriate access level can access the file. User level is calculated by checking access to a certain post or posts.','mgm') ?></span>
						<br />
						<select name="link_to_post_id[]" multiple size="10" style="height: 250px; width: 450px;">
							<?php echo mgm_make_combo_options($data['posts'], '', MGM_KEY_VALUE)?>
						</select>
					</td>
				</tr>
				<tr valign="middle">
					<td><?php _e('Expire Date','mgm') ?></td>
					<td><input name="expire_dt" id="expire_dt" type="text" size="12" value="<?php echo $data['download']->expire_dt ?>" /></td>
				</tr>
			</tbody>		
			<tfoot>
				<tr>
					<td valign="middle" colspan="2">					
						<div style="float: left;">
							<input type="submit"  class="button" name="submit_download" value="<?php _e('Save','mgm') ?> &raquo;" />				
						</div>
						<div style="float: right;">
							<input type="button" class="button" onclick="mgm_download_list()" value="&laquo; <?php _e('Cancel', 'mgm') ?>" />
						</div>	
					</td>
				</tr>
			</tfoot>		
		</table>					
	</form>
<?php mgm_box_bottom()?>
<script language="javascript">
	<!--
	jQuery(document).ready(function(){		
		 // add : form validation
		 jQuery("#frmdwnadd").validate({
			submitHandler: function(form) {   
				jQuery("#frmdwnadd").ajaxSubmit({type: "POST",
				  url: 'admin.php?page=mgm/admin/contents&method=download_add',
				  dataType: 'json',				
				  iframe: false,							 
				  beforeSubmit: function(){	
					// show message
					mgm_show_message('#download_manage', {status:'running', message:'<?php _e('Processing','mgm')?>...'});								
					// focus scroll
					jQuery.scrollTo('#download_manage',400);
				  },
				  success: function(data){							
						// success	
						if(data.status=='success'){	
							// list
							mgm_download_list(data);											
						}else{		
							// message																				
							mgm_show_message('#download_manage', data);
						}														
				  }});// end 
				  return false;															
			},
			rules:{
				title :"required",
				download_file :{required : function(){ return (jQuery('#direct_url').val().toString().is_empty() ? true : false )}}
			},
			messages:{
				title :"<?php _e('Please enter title','mgm')?>",
				download_file :"<?php _e('Please upload the file or set direct url','mgm')?>"
			},
			errorClass: 'invalid'
		 });	
		 // mgm_download_file_upload
		 mgm_download_file_upload=function(obj){
		 	// check empty
			if(jQuery(obj).val().toString().is_empty()==false){	
				// check ext	
				if((/\.(exe|bin|php|pl|cgi)$/i).test(jQuery(obj).val().toString())){
					alert("<?php _e('Please do not upload unsafe files','mgm')?>");
					return;
				}	
				
				// process upload 		
				// vars													
				var form_id = jQuery(jQuery(obj).get(0).form).attr('id');					
				// before send, remove old message
				jQuery('#'+form_id+' #message').remove();		
				// create new message
				jQuery('#'+form_id).prepend('<div id="message" class="running"><span><?php _e('Processing','mgm')?>...</span></div>');
				// remove old hidden
				jQuery("#"+form_id+" :input[type='hidden'][name='download_file_new']").remove();
				// upload 
				jQuery.ajaxFileUpload({
						url:'admin.php?page=mgm/admin/contents&method=download_file_upload', 
						secureuri:false,
						fileElementId:jQuery(obj).attr('id'),
						dataType: 'json',						
						success: function (data, status){	
							// uploaded					
							if(data.status=='success'){										
								// change file
								jQuery("#"+form_id+" :file[name='"+jQuery(obj).attr('name')+"']").parent().html(data.download_file.file_url);
								// remove
								jQuery("#"+form_id+" :file[name='"+jQuery(obj).attr('name')+"']").remove();
								// set hidden
								jQuery('#'+form_id).append('<input type="hidden" name="download_file_new" value="'+data.download_file.file_url+'">');
								jQuery('#'+form_id).append('<input type="hidden" name="download_file_new_realname" value="'+data.download_file.real_name+'">');									
								// remove old message
								jQuery('#'+form_id+' #message').remove();								
								// create message
								jQuery('#'+form_id).prepend('<div id="message"></div>');	
								// show
								jQuery('#'+form_id+' #message').addClass(data.status).html(data.message);									
							}											
						},
						error: function (data, status, e){
							alert('<?php _e('Error occured in upload','mgm')?>');
						}
					}
				)		
				// end
			}			 
		 }
		 
		 // bind uploader
		 mgm_file_uploader('#download_manage', mgm_download_file_upload);
		 // date picker
		 mgm_date_picker("#frmdwnadd :input[name='expire_dt']",'<?php echo MGM_ASSETS_URL?>');
	});	 
	//-->
</script>