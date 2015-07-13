<!--downloads-->
<div id="download_manage"></div>	
<script language="javascript">
	<!--
	jQuery(document).ready(function(){
		// list_download
		mgm_download_list= function(data){			
			jQuery('#download_manage').load('admin.php?page=mgm/admin/contents&method=download_list',function(){
				// message
				if(data){
					mgm_show_message('#download_manage', data);
				}
			});
		}
		// add_download
		mgm_download_add= function(){			
			jQuery('#download_manage').load('admin.php?page=mgm/admin/contents&method=download_add');
		}
		// edit_download
		mgm_download_edit= function(id){
			jQuery('#download_manage').load('admin.php?page=mgm/admin/contents&method=download_edit',{id:id});
		}
		// delete_download
		mgm_download_delete= function(id){			
			if(confirm('<?php _e('Are you sure you want to delete this download?','mgm')?>')){
				jQuery.ajax({url:'admin.php?page=mgm/admin/contents&method=download_delete', 
					type:'POST', 
					dataType: 'json',
					data:{id:id},
					 beforeSend: function(){	
						// show message
						mgm_show_message('#download_manage', {status:'running', message:'<?php _e('Processing','mgm')?>...'});								
						// focus scroll
						jQuery.scrollTo('#download_manage',400);
					},
					success:function(data){								
						// success	
						if(data.status=='success'){																				
							// message																				
							mgm_show_message('#download_manage', data);
							// remove row
							jQuery('#download_manage #row-'+id).remove();									
							// none
							if(jQuery("#download_manage #download_list tr[id^='row-']").size() == 0 ){
								jQuery('#download_manage #download_list').append('<tr><td colspan="7"><?php _e('No downloads','mgm')?></td></tr>');
							}											
						}else{															
							// message																				
							mgm_show_message('#download_manage', data);
						}	
					}
				});
			}
		}
		
		// list
		mgm_download_list();
	});
	//-->
</script>