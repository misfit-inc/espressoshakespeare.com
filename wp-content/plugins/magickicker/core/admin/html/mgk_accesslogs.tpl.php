<?php
// get flag
$action_flag=$_REQUEST['mode'];
// call process
mgk_call_process($action_flag,'f_members');

// define actions /////////////

// default: list of blocked members
function f_members(){
	global $wpdb;
	// pager
	$pager = new mgk_pager();
	// sql		  
	$sql = 'SELECT SQL_CALC_FOUND_ROWS B.id, A.user_login, A.user_email,B.ip_address,B.access_dt
			FROM ' . $wpdb->users . ' A	JOIN ' . TBL_MGK_USER_IPS . ' B ON (A.ID = B.user_id)
			ORDER BY B.access_dt DESC '.$pager->get_query_limit(mgk_get_setting('pagination'));	 
	// echo $sql;		
	// rows	
	$logged_users=$wpdb->get_results($sql);
	// get page links
	$page_links=$pager->get_pager_links('admin.php?page=mgk/admin&load=accesslogs');	
	?>
	<?php mgk_box_top('Recently Logged Members')?>
	<div align="right"><a href="javascript:mgk_reload_accesslogs()"><img src="<?php echo MGK_ASSETS_URL ?>images/icons/arrow_refresh.png" /></a></div> 	
	<div align="right"><?php if($page_links):?><div class="pager-wrap"><?php echo $page_links?></div><?php endif; ?></div>			
		<form name="frmblklist" id="frmblklist" method="post" action="admin.php?page=mgk/admin&load=accesslogs">																
			<table width="100%" cellpadding="1" cellspacing="0" border="0" class="form-table widefat">
				<thead>
					<tr>				
						<th scope="col"><b>#<?php _e('ID','mgk')?></b></th>							
						<th scope="col"><b><?php _e('Username','mgk');?></b></th>
						<th scope="col"><b><?php _e('IP','mgk');?></b></th>												
						<th scope="col"><b><?php _e('Access Time','mgk');?></b></th>											
						<th scope="col"></th>
					</tr>
				</thead>
				<tbody>
					<?php if(count($logged_users)==0):?>
					<tr class="<?php echo ($alt = ($alt=='') ? 'alternate': '');?>" id="row-0">		
						<td colspan="5" align="center"><?php _e('There are currently no logged users.','mgk')?></td>
					</tr>
					<?php endif;						
					// show
					foreach ($logged_users as $log) :?>
					<tr class="<?php echo ($alt = ($alt=='') ? 'alternate': '');?>" id="row-<?php echo $log->id?>">		
						<td valign="top"><?php echo $log->id?></td>									
						<td valign="top"><?php echo $log->user_login?></td>
						<td valign="top"><?php echo $log->ip_address?></td>	
						<td valign="top"><?php echo (strtotime($log->access_dt) > 1) ? date(mgk_get_setting('long_date_format'), strtotime($log->access_dt)) : __('N/A', 'mgk')?></td>							
						<td valign="top" nowrap="nowrap">
							<a href="javascript:mgk_access_view('<?php echo $log->id?>')"><img src="<?php echo MGK_ASSETS_URL?>images/icons/view_detail.png" /></a>							
						</td>
					</tr>
					<?php endforeach;?>					
				</tbody>	
			</table>
		</form>		
		<div align="right"><?php if($page_links):?><div class="pager-wrap"><?php echo $page_links?></div><div class="clearfix"></div><?php endif; ?></div>
	<?php mgk_box_bottom()?>
	<script language="javascript">
		<!--
		// onready
		jQuery(document).ready(function(){  
			// set pager
			mgk_set_pager('#admin_accesslogs');
			// reload
			mgk_reload_accesslogs=function(){
				jQuery('#admin_accesslogs .content-div').tabs('load',0);
			}	
			// edit
			mgk_access_view=function(id){			
				// create tab
				jQuery('#admin_accesslogs .content-div').mgkAutoTabs({index: 1, label: '<?php _e('Accessed URLs','mgk')?>', url: 'admin.php?page=mgk/admin&load=accesslogs&mode=view_accessed_urls&id='+id});						
			}	
		});		
		//-->
	</script>
	<?php								
}

// edit blocked ip 
function f_view_accessed_urls(){	
	global $wpdb;
	$id = (int)$_GET['id'];
	// get all	
	$sql = "SELECT * FROM `" . TBL_MGK_ACCESSED_URLS . "` WHERE `ip_id` = '{$id}'";
	$accessed_urls = $wpdb->get_results($sql);
	
	//mgk_array_dump($accessed_urls);
	// show form
	?>
	<?php mgk_box_top('Accessed URLs')?>	
	<form name="frmaccurlview" id="frmaccurlview" method="post" action="admin.php?page=mgk/admin&load=accesslogs">
		<table width="100%" cellpadding="1" cellspacing="0" border="0" class="form-table widefat">
			<thead>
				<tr>				
					<th scope="col"><b>#<?php _e('ID','mgk')?></b></th>							
					<th scope="col"><b><?php _e('URL','mgk');?></b></th>										
					<th scope="col"><b><?php _e('Access Time','mgk');?></b></th>											
				</tr>
			</thead>
			<tbody>
				<?php if(count($accessed_urls)==0):?>
				<tr class="<?php echo ($alt = ($alt=='') ? 'alternate': '');?>" id="row-0">		
					<td colspan="3" align="center"><?php _e('There are currently no logged urls.','mgk')?></td>
				</tr>
				<?php endif;						
				// show
				foreach ($accessed_urls as $acc) :?>
				<tr class="<?php echo ($alt = ($alt=='') ? 'alternate': '');?>" id="row-<?php echo $acc->id?>">		
					<td valign="top"><?php echo $acc->id?></td>									
					<td valign="top"><?php echo $acc->url?></td>
					<td valign="top"><?php echo (strtotime($acc->access_dt) > 1) ? date(mgk_get_setting('long_date_format'), strtotime($acc->access_dt)) : __('N/A', 'mgk')?></td>												
				</tr>
				<?php endforeach;?>	
											
			</tbody>				
		</table>
		<br />
		<div align="right">
			<a href="javascript:mgk_url_view_cancel()" class="button"><?php _e('Cancel','mgk')?></a>
		</div>		
		<div class="clearfix"></div>						
	</form>											
	<br />	
	<?php mgk_box_bottom()?>
	<script language="javascript">
		<!--
		// onready
		jQuery(document).ready(function(){   
			// cacel
			mgk_url_view_cancel=function(){
				jQuery('#admin_accesslogs .content-div').tabs('remove',1);
				jQuery('#admin_accesslogs .content-div').tabs('select',0);
			}						
		});	
		//-->		
	</script>
	<?php
}
// end of file