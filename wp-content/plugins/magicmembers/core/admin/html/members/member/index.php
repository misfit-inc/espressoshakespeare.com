<!--members-->
<div id="members">
	<form id="mgmmembersfrm" name="mgmmembersfrm" action="admin.php?page=mgm/admin/members&method=members" method="post">
		<div id="member_list"></div>			
		<div id="member_update"></div>
	</form>
	<div id="member_export"></div>
</div>
<script language="javascript">
	<!--	
	//issue #: 219
	//var update_lb_members = 0;
	// onready
	jQuery(document).ready(function(){   
		// load list
		mgm_member_list=function(){
			jQuery('#member_list').load('admin.php?page=mgm/admin/members&method=member_list'); 
		}
		// load update
		mgm_member_update=function(){
			jQuery('#member_update').load('admin.php?page=mgm/admin/members&method=member_update'); 
		}	
		// load export
		mgm_member_export=function(){
			jQuery('#member_export').load('admin.php?page=mgm/admin/members&method=member_export'); 
		}		
		// list 
		mgm_member_list();
		// update
		mgm_member_update();
		// export
		mgm_member_export();
	});
	//-->
</script>