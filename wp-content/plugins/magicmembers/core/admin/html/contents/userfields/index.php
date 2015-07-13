<!--userfields-->
<div id="userfields">
	<?php mgm_box_top('Custom Registration Fields');?>
		<div id="userfields_list"></div>
	<?php mgm_box_bottom()?>
		<p>&nbsp;</p>
	<?php mgm_box_top('Create/Edit Custom Field');?>	
		<div id="userfields_manage"></div>
	<?php mgm_box_bottom()?>
</div>
<script language="javascript">
	<!--	
	// onready
	jQuery(document).ready(function(){   
		// load list	
		mgm_userfield_list=function(){
			jQuery('#userfields_list').load('admin.php?page=mgm/admin/contents&method=userfields_list');	
		}	
		// load add
		mgm_userfield_add=function(){								
			jQuery('#userfields_manage').load('admin.php?page=mgm/admin/contents&method=userfields_add'); 
		}
		// edit
		mgm_userfield_edit=function(id) {
			// load add
			jQuery('#userfields_manage').load('admin.php?page=mgm/admin/contents&method=userfields_edit',{id:id}, function(){
				// focus
				jQuery.scrollTo('#frmuserfldedit',400);
			}); 
		}
		// delete	
		mgm_userfield_delete=function(id) {						
			if (confirm("<?php _e('Are you sure you want to delete this custom field?', 'mgm')?>")) {
				jQuery.ajax({url:'admin.php?page=mgm/admin/contents&method=userfields_delete', type: 'POST', dataType: 'json', data :{id: id}, cache: false,
							 beforeSend: function(){	
							 	// show message
								mgm_show_message('#userfields', {status:'running', message:'<?php _e('Processing','mgm')?>...'});						
								// focus
								jQuery.scrollTo('#userfields',400);	
							 },
							 success:function(data){							
								// success	
								if(data.status=='success'){																							
									// message																				
									mgm_show_message('#userfields', data);
									// delete row
									jQuery("#sortable_userfields tr[id$='userfield_row_"+id+"']").remove();											
								}else{															
									// message																				
									mgm_show_message('#userfields', data);
								}	
							 }});
			}
		}
		
		// list
		mgm_userfield_list();
		// add form
		mgm_userfield_add();			
	});		
	//-->
</script>
