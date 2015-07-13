<?php
/**
 * Admin Functions
 */
// infobar
function mgm_render_infobar() {
	// get auth
	$auth = mgm_get_class('auth');
	// mgm url
	$mgm_url = 'http://www.magicmembers.com/';	
	$style = 'style="color:#161616; text-decoration:none;"';	
	echo '<div id="mgm-info" style="float:right; color:#161616; padding-top:10px; padding-right:15px;">
			<strong>
				<a href="' . $mgm_url . '" ' . $style . ' target="_blank">'.__('Powered by Magic Members','mgm').'</a> |
				<a href="' . $mgm_url . 'support" ' . $style . ' target="_blank">'.__('Support','mgm').'</a> |
				<a href="' . $mgm_url . $auth->get_product_info('product_url') .'" ' . $style . ' target="_blank">V. ' . $auth->get_product_info('product_version') . ' - '. $auth->get_product_info('product_name') .'</a>
			</strong>
		</div>';
}
// get tip
function mgm_get_tip($key){
	global 	$mgm_tips_info;
	return (isset($mgm_tips_info[$key]))?$mgm_tips_info[$key]:"[tip '$key' not available]";
}	
// get video
function mgm_get_video($key){	
	global 	$mgm_videos_info;
	return (isset($mgm_videos_info[$key]))?$mgm_videos_info[$key]:"[video '$key' not available]";
} 
// box top
function mgm_box_top($title, $helpkey='', $return=false, $attributes=false){
	// defaults
	$attributes_default=array('width'=>845, 'style'=>'margin:10px 0;');
	// attributes
	if(is_array($attributes)){
		$options = array_merge($attributes_default,$attributes);
	}else{
		$options = $attributes_default;
	}
	// local
	extract($options);	
	
	// help key
	if(empty($helpkey)){
		$helpkey = strtolower(preg_replace('/\s+/','',$title));
	}
	// html
	$html= '<div class="mgm-panel-box" style="'.$style.($width ? ';width: ' . ($width) . 'px;':'') .'">			
				<div class="box-title" style="' .($width ? 'width: ' . ($width-5) . 'px;':'') . '">			
					<h3>'.__($title, 'mgm').'</h3>				
						<div class="box-triggers">
							<img src="'.MGM_ASSETS_URL.'images/panel/help-image.png" alt="description" class="box-description" />							
							<img src="'.MGM_ASSETS_URL.'images/panel/television.png" alt="video" class="box-video" />
						</div>						
						<div class="box-description-content">				
							<p>'.mgm_get_tip($helpkey).'</p>				
						</div> <!-- end box-description-content div -->						
						<div class="box-video-content">				
							<p>'.mgm_get_video($helpkey).'</p>				
						</div> <!-- end box-video-content div -->				
					</div> <!-- end div box-title -->				
					<div class="box-content">';	
			  
	// return output
	if ($return) {
		return $html;
	} else {
		echo $html;
	}
}
// box bottom
function mgm_box_bottom($return=false){
	$html = '</div>
		   </div>';
	if ($return) {
		return $html;
	} else {
		echo $html;
	}
}
// get post types
function mgm_get_post_types($join=true){
	// get post types
	$post_types = get_post_types( array(), 'names' );
	// default
	if(!$post_types) $post_types = array('post','page');	
	// init
	$_post_types = array();
	// internal
	$internal_post_types = array('attachment','revision','nav_menu_item');
	// filter out un needed
	foreach($post_types as $post_type){
		// check
		if(in_array($post_type, $internal_post_types)) continue;
		// set
		$_post_types[] = $post_type;
	}
	// return
	return ($join) ? mgm_map_for_in($_post_types) : $_post_types;
}
// get_purchasable_posts
function mgm_get_purchasable_posts($exclude=false){
	global $wpdb;
	// exclude
	$exclude_sql='';
	if(is_array($exclude) && count($exclude)>0){
		$exclude_sql = "AND A.ID NOT IN (".implode(',', $exclude).")";
	}
	// get types
	$post_types_in = mgm_get_post_types(true);
	// update to include both _mgm_post_options and _mgm_post for old and new option name
	// sql
	$sql = "SELECT DISTINCT(A.ID) AS id, A.post_title AS post_title FROM " . $wpdb->posts . " A	JOIN " . $wpdb->postmeta . " B 
	        ON (A.ID = B.post_id AND B.meta_key LIKE '_mgm_post%' ) WHERE A.post_status = 'publish' 
			AND A.post_type IN ({$post_types_in}) {$exclude_sql} ORDER BY A.post_title";										
	// fetch
	$rows = $wpdb->get_results($sql);	
	// init
	$purchasable_posts = array();
	// captured
	if($rows){	
		// loop	
		foreach($rows as $row){
			// get post object
			$mgm_post = mgm_get_post($row->id);
			// in array
			if($mgm_post->purchasable =='Y'){
				$purchasable_posts[$row->id] = $row->post_title;
			}
			// unset
			unset($mgm_post);
		}
	} 	
	// return
	return $purchasable_posts;
}
// check version
function mgm_check_version() {
	echo mgm_get_class('auth')->check_version();
}
// get message
function mgm_get_messages() {	
    echo mgm_get_class('auth')->get_messages();
}
// get subscription status
function mgm_get_subscription_status(){ 
	echo mgm_get_class('auth')->get_subscription_status();;
}
// site rss news
function mgm_site_rss_news(){
	$items = get_option('mgm_rss_site');
	
    if (empty($items)) {
		mgm_get_rss(MGM_GET_NEWS_URL,'mgm_rss_site', 5);
		$items = get_option('mgm_rss_site');
	}
        
	if (is_array($items)) {
		foreach ($items as $item){
			$content = $item['description'];
			if (strlen($content > '500')) {
				$content = substr($content, 0, '500') . '...';
			}
			echo "<div style='margin-bottom: 5px;'>
				  	<div style='font-weight: bold;'>
						<a href='".$item['link']."'>".$item['title']."</a>
					</div>
					<div style='border-bottom: 1px solid #D5D5D5; font-size: 10px;'>".$content."</div>
				 </div>";
		}
	} else { 
		echo '<ul><li>'.$items.'</li></ul>';
	}
}
// rss news
function mgm_rss_news(){
	$items = get_option('mgm_rss_blog');
	
	if (empty($items)) {
		mgm_get_rss(MGM_GET_BLOG_RSS,'mgm_rss_blog','5');
		$items = get_option('mgm_rss_blog');
	}
	
	echo '<ul>';
	if(is_array($items)){		
		foreach ( $items as $item ){
			echo "<li><a href='".$item['link']."'>".$item['title']."</a></li>";
		}
	} else { 
		echo '<li>'.$items.'</li>';
	}	
	echo '</ul>';
}
// rss parsing
function mgm_get_rss($url,$rss_name,$maxitems){
	require_once (ABSPATH . WPINC . '/rss.php');
	$rss = fetch_rss($url);
	
	if($rss != null) {
		$items = array_slice($rss->items, 0, $maxitems);
	} else {
		$items = 'Error fetching the feed';
	}
	update_option($rss_name, $items);
}
// members on dashboard
function mgm_member_statistics() {
	global $wpdb;	
	// get membership_types
	$membership_types = mgm_get_class('membership_types');	
	// create table
	echo '<table style="width:100%;">';
	echo '  <tr>
				<td style="border-bottom: 1px solid #EFEFEF; font-weight:bold;">'.__('Membership Types','mgm').'</td>
				<td style="border-bottom: 1px solid #EFEFEF; font-weight:bold; width:20%;">'.__('Users','mgm').'</td>
			</tr>';			
	// check
	foreach ($membership_types->membership_types as $type_code=>$type_name) {
		// members
		$members = mgm_get_members_with('membership_type', $type_code);
		$count = isset($members) ? count($members) : 0;
		echo '<tr>
				<td style="border-bottom: 1px solid #EFEFEF;">' . mgm_stripslashes_deep($type_name) . '</td>
				<td style="border-bottom: 1px solid #EFEFEF;">' . $count . '</td>
			  </tr>';
	}
	echo '</table>';
}
// members on dashboard
/**
 * Shows total revenue for Subscription packages
 *
 * @param boolean $return
 * @return array/html string: if $return is true, an array of Subscription packages and total revenue will be returned
 * 								Eg:  [Gold[Package #4]] => 21.00
 */
function mgm_membership_statistics($return = false) {
	global $wpdb;
	
	$arr_total = array(); 	
	// get membership_types
	$membership_types 	= mgm_get_class('membership_types');	
	$subscription_packs = mgm_get_class('subscription_packs')->packs;	
	
	// create table
	if(!$return) {
		echo '<table style="width:100%;">';
		echo '  <tr>
					<td style="border-bottom: 1px solid #EFEFEF; font-weight:bold;">'.__('Subscription Package','mgm').'</td>
					<td style="border-bottom: 1px solid #EFEFEF; font-weight:bold; width:20%;">'.__('Total','mgm').'</td>
				</tr>';
	}			
	// check
	foreach ($membership_types->membership_types as $type_code=>$type_name) {
		foreach ($subscription_packs as $pack) {
			//check pack:
			if($pack['membership_type'] == $type_code) {
				// members
				//the below function will return user ids satisfying (membership_type AND pack_id )
				$members = mgm_get_members_with('membership_type', $type_code, array('pack_id' => $pack['id']));
				$count = isset($members) ? count($members) : 0;				
				$total = number_format($count * $pack['cost'],2,'.', null );
				
				$label = mgm_stripslashes_deep($type_name) . '[' .sprintf(__('Package #%d','mgm'),$pack['id']).']';
				if(!$return) {
					echo '<tr>
							<td style="border-bottom: 1px solid #EFEFEF;" align="left">' . $label . '</td>
							<td style="border-bottom: 1px solid #EFEFEF;" align="left" valign="top">' . $total . '</td>
						  </tr>';
				}else {
					//update array
					$arr_total[$label] = $total;					
				}
			}
		}
	}
	if(!$return) {
		echo '</table>';
	}else {
		//return array
		return $arr_total;
	}
}
// purchased posts on dashboard
function mgm_render_posts_purchased($limit=false) {
	global $wpdb;

	$prefix = $wpdb->prefix;
	$sql = "SELECT B.post_title AS title, COUNT(B.id) AS count
			FROM `" . TBL_MGM_POSTS_PURCHASED."` A
			JOIN " . $wpdb->posts . " B ON (B.id = A.post_id)
			GROUP BY A.post_id	ORDER BY A.post_id DESC";

	$results = $wpdb->get_results($sql,'ARRAY_A');

	echo '<table style="width:100%;">
		  	<tr>
				<td style="border-bottom: 1px solid #EFEFEF; font-weight:bold;">'.__('Post Title', 'mgm').'</td>
				<td style="border-bottom: 1px solid #EFEFEF; font-weight:bold; text-align: right; width:20%;">'.__('Purchased', 'mgm').'</td>
			</tr>';

	if (count($results[0])) {
		$loop = 1;
		foreach ($results as $result) {
			echo '<tr>
					<td style="border-bottom: 1px solid #EFEFEF;">' . $result['title'] . '</td>
					<td style="border-bottom: 1px solid #EFEFEF; text-align:right;">' . $result['count'] . '</td>';
			echo '</tr>';

			$loop++;

			if ($limit && $loop == $limit) {
				break;
			}
		}
	} else {
		echo '<tr>
					<td colspan=2>'.__('No posts have been sold yet', 'mgm').'</td>
			  </tr>';
	}

	echo '</table>';
	
	// show all link
	if($limit!==false){
		echo '<div style="text-align: right; margin-top: 5px;">
				<a href="javascript:mgm_set_tab_url(3, 1)">'.__('View All', 'mgm').' &#0187;</a>
			  </div>';
	}
}
/*// mgm_delete_file
function mgm_delete_file($file){
	// check file
	if(is_file($file) && !is_dir($file)){
		// delete
		unlink($file);
		// success
		return true;
	}
	// failed
	return false;
}*/
// find members with
function mgm_get_members_with($field, $value, $extra_params = array()){
	global $wpdb;	
	// get all users
	//$sql   = "SELECT ID FROM " . $wpdb->users . " WHERE ID != 1";		
	//$users = $wpdb->get_results($sql);	
	
	//read from cache:
	$users = wp_cache_get('all_user_ids', 'users');		
	//if empty read from db:
	if(empty($users)) {
		$users = mgm_get_all_userids(array('ID'), 'get_results');
		//update cache with user ids:
		wp_cache_set('all_user_ids', $users, 'users');		
	}
	
	// filter 
	$members = array();
	// check
	if($users){
		// loop
		foreach($users as $user){
			// vlid
			$valid = false;
			// member object
			$mgm_member= mgm_get_member($user->ID);			
			// log
			// mgm_log(print_r($mgm_member,true));
			// matrch field
			switch($field){
				case 'last_pay_date':
				case 'expire_date':
					// match
					if($mgm_member->{$field} == date('Y-m-d', strtotime($value))){
						$valid = true;
					}
					// check other
					if(!$valid && isset($mgm_member->other_membership_types) && is_array($mgm_member->other_membership_types) && count($mgm_member->other_membership_types) > 0) {
						// loop
						foreach ($mgm_member->other_membership_types as $key => $memtypes) {
							$memtypes = mgm_convert_array_to_memberobj($memtypes, $user->ID);
							//skip default values:
							if(strtolower($memtypes->membership_type) == 'guest' || $memtypes->status == MGM_STATUS_NULL ) continue;
							// match
							if($memtypes->{$field} == date('Y-m-d', strtotime($value)) ) {
								//reset if already saved
								$valid = true;
								break;
							}
						}
					}
				break;
				default:
					// match
					if($mgm_member->{$field} == $value){
						$valid = true;
						
						//check extra parameters: Loop through $extra_params ['field' => 'value'] to find a match with AND operator
						if(!empty($extra_params)) {
							foreach ($extra_params as $ext_field => $ext_value) {
								if($valid) {
									if($mgm_member->{$ext_field} != $ext_value)
										$valid = false;
										//as the operator is AND, no longer need to loop
										break;
								}
							}
						}
					}
					// check other
					if(!$valid && isset($mgm_member->other_membership_types) && is_array($mgm_member->other_membership_types) && count($mgm_member->other_membership_types) > 0) {
						// loop
						foreach ($mgm_member->other_membership_types as $key => $memtypes) {
							$memtypes = mgm_convert_array_to_memberobj($memtypes, $user->ID);
							//skip default values:
							if(strtolower($memtypes->membership_type) == 'guest' || $memtypes->status == MGM_STATUS_NULL ) continue;
							// match
							if($memtypes->{$field} == $value ) {
								//reset if already saved
								$valid = true;
								
								//check extra parameters: Loop through $extra_params ['field' => 'value'] to find a match with AND operator
								if(!empty($extra_params)) {
									foreach ($extra_params as $ext_field => $ext_value) {
										if($valid) {
											if($memtypes->{$ext_field} != $ext_value) {
												$valid = false;
												//as the operator is AND, no longer need to loop
												break;
											}
										}
									}
								}
								//exit as first condition is satisfied
								break;
							}
						}
					}
				break;		
			}
			
			// store
			if($valid){
				$members[] = $user->ID;
			}
			// unset object
			unset($mgm_member);
		}
	}
	
	// return
	return $members;
}

// create dir
function mgm_create_dir($dir){	
	// create if not created already
	if(!is_dir($dir)){
		// create
		mkdir( $dir );
		// mode
		chmod( $dir , 0777 );		
		// create a noindex
		$index    = "<html><head><title>403 Forbidden</title></head><body><p>Directory access is forbidden.</p></body></html>";
		$htaccess = "# deny web access\n\n order deny,allow\n\n deny from all\n\n allow from none\n\n";
		// save
		file_put_contents($dir. DIRECTORY_SEPARATOR . 'index.html', $index);			
		// save
		file_put_contents($dir. DIRECTORY_SEPARATOR . '.htaccess', $htaccess);			
	}	
}

// create upload dirs
function mgm_create_files_dir($dirs){
	// create	
	foreach($dirs as $dir){
		mgm_create_dir($dir);
	}
}

// create file
function mgm_create_xls_file($rowset){	
	// writer
	$xls_writer = new mgm_xls_writer();
	// init
	$xls_arr = array();
	$xls_writer->xls_bof();
	$row_ctr =0;
	$columns = array();
	// log	
	foreach($rowset as $row){	
		$col  = 0;	
		$data = mgm_object2array($row);			
		// header
		if(empty($columns)){
			$labels = array_keys($data);
			foreach($labels as $label){
				$xls_writer->xls_write_label($row_ctr, $col++, $label);
				$columns[] = $label;
			}
			// $header= true;	
			// increment row
			$row_ctr++;		
		}		
		// reset col
		$col  = 0;
		foreach($columns as $column){
			// value
			$value = isset($data[$column]) ? $data[$column]: 'N/A';
			//limit string length: issue#: 492
			if(strlen($value) > 1024)
				$value = substr($value, 0, 1020) .'...';
			// set
			$xls_writer->xls_write_label($row_ctr, $col++, $value);
		}			
		$row_ctr++;				
	}
	$xls_writer->xls_eof();
	// string	
	$xls_string = $xls_writer->xls_output();	
	// filename		
	$filename = 'XLS_download_'.date('mdYHis').'.xls';
	// file
	$fp       = fopen(MGM_FILES_EXPORT_DIR . $filename, "w+");
	// write
	fwrite($fp, $xls_string);
	// close
	fclose($fp);
	// return
	return $filename;
}
// mgm_get_jquery_ui_versions
function mgm_get_jquery_ui_versions(){
	// read
	$_versions = glob(MGM_ASSETS_DIR . implode(MGM_DS, array('js','jquery','jquery.ui')) . MGM_DS . 'jquery-ui-*.js');	
	// init
	$versions = array('1.7.2','1.7.3','1.8.2');
	// check
	if($_versions){
		// loop
		foreach($_versions as $_version){
			// trim
			$versions[] = str_replace(array('jquery-ui-','.min.js'), '', basename($_version));
		}
	}	
	// return 
	return array_unique($versions);	
}
// end file