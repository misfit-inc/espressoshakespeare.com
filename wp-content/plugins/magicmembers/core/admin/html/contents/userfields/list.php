<!--userfields_list-->
<table width="100%" cellpadding="1" cellspacing="0" border="0" class="widefat form-table">
	<thead>
		<tr>
			<th scope="col"><?php _e('Active?','mgm')?></th>			
			<th scope="col"><?php _e('Label','mgm')?></th>
			<th scope="col"><?php _e('Name/ID','mgm')?></th>
			<th scope="col"><?php _e('Type','mgm')?></th>
			<th scope="col"><?php _e('Others','mgm')?></th>				
			<th scope="col" align="center"><?php _e('Action','mgm')?></th>
		</tr>
	</thead>
	<?php if($data['custom_fields']->custom_fields):?>	
	<tbody id="sortable_userfields">
		<?php
			// list by order first 
			foreach (array_unique($data['custom_fields']->sort_orders) as $id) :
				foreach($data['custom_fields']->custom_fields as $field):
					if($field['id'] == $id):
						// active
						$active = true;
						// reapeat
						include('list_repeat.php');
					endif;
				endforeach;
			endforeach;
			// list rest of inactive fields  
			foreach($data['custom_fields']->custom_fields as $field):
				if(!in_array($field['id'], $data['custom_fields']->sort_orders)):
					// id
					$id = $field['id'];
					// active
					$active = false;
					// reapeat
					include('list_repeat.php');
				endif;
			endforeach;
		else:?>
		<tr>
			<td colspan="6"><?php _e('There are no custom fields yet.','mgm')?></td>
		</tr>
		<?php endif;?>
	</tbody>
</table>	
<script language="javascript">
	<!--
	jQuery(document).ready(function(){			
		// toggle
		mgm_toggle_uf= function(id){
			jQuery('#'+id).toggle() ;
		}		
		
		// jQuery("#demo img[title]").tooltip();
		jQuery("#sortable_userfields a[rel]").overlay({effect: 'apple'});
		
		jQuery("#sortable_userfields").sortable({update:function(event, ui){		
			// id not set, not active
			if(!ui.item.attr('id'))
				return;
			
			// id flag not active
			if(!(/^active_/.test(ui.item.attr('id'))))
				return;			
			
			// not checked, not active
			if(!ui.item.children('td').children('input').attr('checked'))
				return;				
			
			jQuery.ajax({url:'admin.php?page=mgm/admin/contents&method=userfields_sort',
						type:'POST',
						dataType:'json',
						data:{sort_order:jQuery('#sortable_userfields').sortable('serialize')},
						success:function(data){
							// remove old
							jQuery('#userfields_list #message').remove();	
							// success
							if(data.status == 'success'){
								// create message
								jQuery('#userfields_list').prepend('<div id="message"></div>');
								// show
								jQuery('#userfields_list #message').addClass(data.status).html(data.message);								
							}else{
								// show error								
								// create message
								jQuery('#userfields_list').prepend('<div id="message"></div>');
								// show
								jQuery('#userfields_list #message').addClass(data.status).html(data.message);	
							}				
						}});// ajax end
		}});// sortable end		
		
		// bind active/inactive
		jQuery(":checkbox[name='userfields[]']").bind('click',function(){		
			// vars
			var id	     = jQuery(this).val();	
			var active   = (jQuery(this).attr('checked'))?'Y':'N';
			var $element = jQuery(this);
			// send
			jQuery.ajax({url:'admin.php?page=mgm/admin/contents&method=userfields_status_change',
						type:'POST',
						dataType:'json',
						data:{id:id, active:active},
						success:function(data){
							// remove old
							jQuery('#userfields_list #message').remove();	
							// success
							if(data.status == 'success'){
								// create message
								jQuery('#userfields_list').prepend('<div id="message"></div>');
								// show
								jQuery('#userfields_list #message').addClass(data.status).html(data.message);
								// set id for sort
								if(active=='Y')
									$element.parent().parent().attr('id','active_userfield_row_'+id);
								else
									$element.parent().parent().attr('id','inactive_userfield_row_');
							}else{
								// show error								
								// create message
								jQuery('#userfields_list').prepend('<div id="message"></div>');
								// show
								jQuery('#userfields_list #message').addClass(data.status).html(data.message);	
							}				
						}});
		});
	});
	//-->
</script>	