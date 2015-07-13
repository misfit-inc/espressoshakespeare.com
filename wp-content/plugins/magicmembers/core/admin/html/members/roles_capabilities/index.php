<!--subscription_options-->
<?php mgm_box_top('Roles & Capabilities','Magic Members Roles')?>
		<div id="roles_capabilities_list_message_mgm"></div>
		<div id="roles_capabilities_list_mgm">			
		</div>
		
		<div id="roles_capabilities_list_message_default"></div>
		<div id="roles_capabilities_list_default">			
		</div>
		
		<div id="roles_capabilities_list_message_others"></div>
		<div id="roles_capabilities_list_others">			
		</div>
		
		<div id="roles_capabilities_add_message"></div>
		<div id="roles_capabilities_add"></div>
<?php mgm_box_bottom()?>
<script language="javascript">
		<!--
		jQuery(document).ready(function(){	
			load_roles_capabilities_mgm=function(){
				jQuery('#roles_capabilities_list_mgm').load('admin.php?page=mgm/admin/members&method=roles_capabilities_list', function(data){					
					// set up accordian
					jQuery("#roles_list_div_mgm").accordion({
						collapsible: true,
						autoHeight: true,
						fillSpace: false,
						clearStyle: true,
						active: false
					});					
				});	
			}
			
			load_roles_capabilities_default=function(){
				jQuery('#roles_capabilities_list_default').load('admin.php?page=mgm/admin/members&method=roles_capabilities_list_default', function(){
					// set up accordian
					jQuery("#roles_list_div_default").accordion({
						collapsible: true,
						autoHeight: true,
						fillSpace: false,
						clearStyle: true,
						active: false
					});					
				});	
			}
			
			load_roles_capabilities_others=function(){
				jQuery('#roles_capabilities_list_others').load('admin.php?page=mgm/admin/members&method=roles_capabilities_list_others', function(){
					// set up accordian
					jQuery("#roles_list_div_others").accordion({
						collapsible: true,
						autoHeight: true,
						fillSpace: false,
						clearStyle: true,
						active: false
					});					
				});	
			}
			
			load_roles_capabilities_add=function(){
				jQuery('#roles_capabilities_add').load('admin.php?page=mgm/admin/members&method=roles_capabilities_add', function(){					
				});	
			}
			
			load_roles_capabilities_mgm();
			load_roles_capabilities_default();		
			load_roles_capabilities_others();	
			load_roles_capabilities_add();
		});
		var clear_message_divs = function() {
			jQuery('#roles_capabilities_list_message_mgm').html('');
			jQuery('#roles_capabilities_list_message_default').html('');
			jQuery('#roles_capabilities_list_message_others').html('');
			jQuery('#roles_capabilities_add_message').html('');
		}
		//-->
</script>