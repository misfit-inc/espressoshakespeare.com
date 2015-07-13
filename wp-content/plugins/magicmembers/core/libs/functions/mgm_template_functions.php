<?php
// Template/Theme Functions
// check membership
function mgm_member_check($membership_types = array()) {
	$user_ac = mgm_get_user_membership_type();
	return in_array($user_ac, $membership_types);
}

// deprecated / only on tag
function mgm_membership_content_page() {
	get_currentuserinfo();
	global $current_user, $wpdb;

	$snippet_length = 200;
	$max_loops = 30;
	$html = '';

	$membership_level = mgm_get_user_membership_type($current_user->ID);
	$mgm_member = mgm_get_member($current_user->ID);
	$arr_memberships = mgm_get_subscribed_membershiptypes($current_user->ID, $mgm_member );
	$posts = false;
	$blog_home = home_url();

	$sql = 'SELECT DISTINCT(ID), post_title, post_date, post_content
			FROM
				' . $wpdb->posts . ' p
				JOIN ' . $wpdb->postmeta . ' pm ON (
					p.ID = pm.post_id
					AND p.post_status = "publish"
					AND pm.meta_key LIKE "_mgm_post%"					
					AND post_type = "post"
				)
			ORDER BY post_date DESC';
		
	// get posts	
	$results = $wpdb->get_results($sql);
	
	// capture only purchasable
	$purchasable_posts = array();
	
	if (count($results) >0) {
		foreach ($results as $id=>$obj) {
			// get post
			$mgm_post = mgm_get_post($obj->ID);
			// membership types
			$membership_types = $mgm_post->get_access_membership_types();
			// is post is purchable and member is not in accesslist
			//multiple membership level purchase(issue#: 400) modification
			//if($mgm_post->purchasable == 'Y' && !in_array($mgm_member->membership_type, $membership_types)){
			if($mgm_post->purchasable == 'Y' && array_diff($membership_types, $arr_memberships) == $membership_types ){
				$purchasable_posts[] = $obj;
			}
			unset($mgm_post);
		}
	}
	
	// mgm_array_dump($purchasable_posts) ;
		
	if ($members_pages = count($purchasable_posts)) {
		$loops = 0;
		foreach ($purchasable_posts as $id=>$obj) {
			// check purchaseble			
			$published    = date('jS F Y', strtotime($obj->post_date));
			$title        = $obj->post_title;
			$full_content = $obj->post_content;
			if (function_exists('qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage')) {
				$title = qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage($title);
				$full_content = qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage($full_content);
			}
			$content = substr(strip_tags($full_content), 0, $snippet_length);
			$content = preg_replace("'\[/?\s?private\s?\]'i",'', $content);
			$ending = (strlen($full_content) > strip_tags($snippet_length) ? '...':'');

			$row = '<tr>
						<td style="border-top: 1px solid silver;">							
							<div style="font-weight: bold; font-size: 14px; margin-bottom: 5px;"><a href="' . get_permalink($obj->ID) . '">' . $title . '</a></div>
							<div style="font-size: 10px; margin-bottom: 5px;">' . $content . '</div>
						</td>
						<td style="vertical-align: top; border-top: 1px solid silver;">' . $published . '</td>
					</tr>';

			$posts .= $row;

			$loops++;

			if ($loops >= $max_loops) {
				break;
			}
		}
	}

	$table_intro = __('Showing the most recent ','mgm') . $loops . __(' posts of a total ','mgm') . $members_pages . __(' available to you','mgm').'.';

	$html .= '	<div style="margin-bottom: 5px; font-weight: bold;">' . __('Your membership level is:',"mgm") . ' ' . $membership_level . '.</div>
						<div style="margin-bottom: 10px; font-weight: bold;">
					' . __('You have access to a total of','mgm') . ' ' . $members_pages . ' ' . __('premium', 'mgm') . ' ' .  ($members_pages == 1 ? __('Post', 'mgm'):__('Posts', 'mgm')) . ' 
				</div>';

	if ($members_pages > 0) {
		$html .= $table_intro;

		$html .= '<div style="padding-top: 10px; margin-bottom: 10px;">
					<table style="width: 100%" cellspacing="0" cellpadding="2">
						<tr>
							<th style="text-align: left;">'.__('Post Title','mgm').'</th>
							<th style="width: 160px; text-align: left;">'.__('Published','mgm').'</th>
						</tr>
					' . $posts . '
				</table></div>';
		
		if ($pp = mgm_render_my_purchased_posts($current_user->ID, false, true)) {
			$html .= '<h4>'.__('My Purchased Posts','mgm').'</h4>
			' . $pp;
		}	
	}
	return $html;
}

// user profile
function mgm_user_subscription($user_id=NULL) {	
	// by user id
	if($user_id){
		$user = get_userdata($user_id);
	}
	
	// get current user
	if(!$user->ID){
		$user = wp_get_current_user();
	}
		
	// return when no user
	if(!$user->ID) return "";	
	//settings
	$settings = mgm_get_class('system')->setting;
	// packs
	$subscription_packs = mgm_get_class('subscription_packs');
	$duration_str = $subscription_packs->duration_str;
	// member
	$mgm_member  = mgm_get_member($user->ID);
	
	// mgm_pr($mgm_member);
	// pack
	$pack_id     = $mgm_member->pack_id;
	$pack        = $subscription_packs->get_pack($pack_id); 
	$extend_link = '';
	$subs_package = 'N/A';
	// allow renewal	
	if($pack){			
		// dsc
		$subs_package = $pack['description'];
		//issue#: 478
		$num_cycles = (isset($mgm_member->active_num_cycles) && !empty($mgm_member->active_num_cycles)) ? $mgm_member->active_num_cycles : $pack['num_cycles'] ;
		// check cycles	
		// WORK TEST
		if($num_cycles > 0 && mgm_pack_extend_allowed($pack)){
			$extend_link = ' (<a href="'. mgm_get_custom_url('transactions',false,array('action' => 'extend', 'pack_id'=>$pack_id, 'username' => $user->user_login)) . '">' . __('Extend','mgm') . '</a>)';
		}			
	}
	// set others
	$durstr   = ($mgm_member->duration == 1) ? rtrim($duration_str[$mgm_member->duration_type], 's') : $duration_str[$mgm_member->duration_type];
	$amount   = (is_numeric($mgm_member->amount)) ? sprintf(__('%1$s %2$s','mgm'),number_format($mgm_member->amount,2,'.',null),$user->currency):'N/A';
	$last_pay = $mgm_member->last_pay_date ? date(MGM_DATE_FORMAT_SHORT, strtotime($mgm_member->last_pay_date)) :'N/A';
	$expiry   = $mgm_member->expire_date ? date(MGM_DATE_FORMAT_SHORT, strtotime($mgm_member->expire_date)) :'N/A';
	$duration = $mgm_member->duration ? (($mgm_member->duration_type == 'l') ? $durstr : $mgm_member->duration . ' ' . $durstr .($mgm_member->duration > 1 ? 's' :'')): 'N/A';
	
	$membership_type = $mgm_member->membership_type;
	
	$html .= '<table width="100%" cellpadding="0" cellspacing="0" border="0" class="form-table widefat">
				<tr class="alternate">
					<td width="25%" align="left" height="30"><strong>' . __('Access Duration','mgm') . '</strong></td>
					<td width="2%" align="center" valign="top"><strong>:</strong></td>
					<td align="left">' . esc_html($duration) . '</td>
				</tr>
				<tr>
					<td align="left" height="30" valign="top"><strong>' . __('Last Payment Date','mgm') . '</strong></td>
					<td align="center" valign="top" ><strong>:</strong></td>
					<td align="left" valign="top">' . esc_html($last_pay) . '</td>
				</tr>';
	if($mgm_member->duration_type != 'l') {
		$html .= '<tr class="alternate">
						<td align="left" height="30" valign="top"><strong>' . __('Expiry Date','mgm') . '</strong></td>
						<td align="center" valign="top"><strong>:</strong></td>
						<td align="left" valign="top">' . esc_html($expiry) . $extend_link . '</td>
					</tr>';
	}
	$html .= '<tr>
					<td align="left" height="30" valign="top"><strong>' . __('Membership Cost','mgm') . '</strong></td>
					<td align="center" valign="top"><strong>:</strong></td>
					<td align="left" valign="top">' . ((is_super_admin() ? 'N/A' : esc_html($amount)) . ' ' . mgm_get_class('system')->setting['currency']) .'</td>
				</tr>
				<tr class="alternate">
					<td align="left" height="30" valign="top"><strong>' . __('Membership Type','mgm') . '</strong></td>
					<td align="center" valign="top"><strong>:</strong></td>
					<td align="left" valign="top">' .((is_super_admin() ? 'N/A' : mgm_stripslashes_deep(esc_html(mgm_get_user_membership_type($user->ID)))) .' (<a href="'. mgm_get_custom_url('transactions',false,array('action' => 'upgrade', 'username' => $user->user_login)) . '">' . __('Upgrade','mgm') . '</a>)').'</td>
				</tr>
				<tr class="alternate">
					<td align="left" height="30" valign="top"><strong>' . __('Subscribed Package','mgm') . '</strong></td>
					<td align="center" valign="top"><strong>:</strong></td>
					<td align="left" valign="top">' .mgm_stripslashes_deep(esc_html($subs_package)). '</td>
				</tr>';
		
		if(isset($settings['enable_multiple_level_purchase']) && $settings['enable_multiple_level_purchase'] == 'Y' && mgm_check_purchasable_level_exists($user->ID, $mgm_member)) {	
			$html .='<tr class="alternate">
						<td align="left" height="30" valign="top"><strong>' . __('Purchase Another Membership Level','mgm') . '</strong></td>
						<td align="center" valign="top"><strong>:</strong></td>
						<td align="left" valign="top">' .((is_super_admin() ? 'N/A' : ' (<a href="'. mgm_get_custom_url('transactions',false,array('action' => 'purchase_another', 'username' => $user->user_login))) . '">' . __('Purchase','mgm') . '</a>)').'</td>
					</tr>';
		}
		
		$html .='</table>';
	
	// filter
	$html = apply_filters('mgm_user_subscription_html',$html, $user->ID);	
	// return
	return $html;
}
//if multiple subscriptions exist
function mgm_other_subscriptions($user_id=NULL) {	
	// by user id
	if($user_id){
		$user = get_userdata($user_id);
	}
	
	// get current user
	if(!$user->ID){
		$user = wp_get_current_user();
	}
		
	// return when no user
	if(!$user->ID) return "";	
	
	// member
	$other_members = mgm_get_member($user->ID);
	
	if(isset($other_members->other_membership_types) && is_array($other_members->other_membership_types) && !empty($other_members->other_membership_types) > 0) { 
		// packs
		$subscription_packs = mgm_get_class('subscription_packs');
		$duration_str = $subscription_packs->duration_str;
		$mgm_membership_types = mgm_get_class('membership_types');	
		$subs_count = 0;
		
		foreach ($other_members->other_membership_types as $key => $mgm_member) {
			$mgm_member = mgm_convert_array_to_memberobj($mgm_member, $user->ID);			
			//skip default and expired memberships
			if(in_array($mgm_member->status, array(MGM_STATUS_NULL,MGM_STATUS_EXPIRED,MGM_STATUS_PENDING)) || strtolower($mgm_member->membership_type) == 'guest' ) continue;
			$pack_id     = $mgm_member->pack_id;
			$pack        = $subscription_packs->get_pack($pack_id); 
			$extend_link = '';
			$subs_package = 'N/A';
			// allow renewal	
			if($pack) {			
				// dsc
				$subs_package = $pack['description'];
				//issue#: 478
				$num_cycles = (isset($mgm_member->active_num_cycles) && !empty($mgm_member->active_num_cycles)) ? $mgm_member->active_num_cycles : $subs_pack['num_cycles'] ;
				// check cycles	
				if($num_cycles > 0 && mgm_pack_extend_allowed($pack)){
					$extend_link = ' (<a href="'. mgm_get_custom_url('transactions',false,array('action' => 'extend', 'pack_id'=>$pack_id, 'username' => $user->user_login)) . '">' . __('Extend','mgm') . '</a>)';
				}			
			}
			// set others
			$durstr   = ($mgm_member->duration == 1) ? rtrim($duration_str[$mgm_member->duration_type], 's') : $duration_str[$mgm_member->duration_type];
			$amount   = (is_numeric($mgm_member->amount)) ? sprintf(__('%1$s %2$s','mgm'),number_format($mgm_member->amount,2,'.',null),$user->currency):'N/A';
			$last_pay = $mgm_member->last_pay_date ? date(MGM_DATE_FORMAT_SHORT, strtotime($mgm_member->last_pay_date)) :'N/A';
			$expiry   = $mgm_member->expire_date ? date(MGM_DATE_FORMAT_SHORT, strtotime($mgm_member->expire_date)) :'N/A';
			$duration = $mgm_member->duration ? (($mgm_member->duration_type == 'l') ? $durstr : $mgm_member->duration . ' ' . $durstr): 'N/A';
			
			$membership_type = $mgm_member->membership_type;
			
			$html .= '<p><table width="100%" cellpadding="0" cellspacing="0" border="0" class="form-table widefat">
						<tr class="alternate">
							<td width="25%" align="left" height="30"><strong>' . __('Access Duration','mgm') . '</strong></td>
							<td width="2%" align="center" valign="top"><strong>:</strong></td>
							<td align="left">' . esc_html($duration) . '</td>
						</tr>
						<tr>
							<td align="left" height="30" valign="top"><strong>' . __('Last Payment Date','mgm') . '</strong></td>
							<td align="center" valign="top" ><strong>:</strong></td>
							<td align="left" valign="top">' . esc_html($last_pay) . '</td>
						</tr>';
			
			if($mgm_member->duration_type != 'l') {
				$html .= '<tr class="alternate">
								<td align="left" height="30" valign="top"><strong>' . __('Expiry Date','mgm') . '</strong></td>
								<td align="center" valign="top"><strong>:</strong></td>
								<td align="left" valign="top">' . esc_html($expiry). '</td>
							</tr>';
			}
			
			$html .= '<tr>
							<td align="left" height="30" valign="top"><strong>' . __('Membership Cost','mgm') . '</strong></td>
							<td align="center" valign="top"><strong>:</strong></td>
							<td align="left" valign="top">' . (is_super_admin() ? 'N/A' : esc_html($amount) . ' ' . mgm_get_class('system')->setting['currency']) .'</td>
						</tr>
						<tr class="alternate">
							<td align="left" height="30" valign="top"><strong>' . __('Membership Type','mgm') . '</strong></td>
							<td align="center" valign="top"><strong>:</strong></td>
							<td align="left" valign="top">' .(is_super_admin() ? 'N/A' : mgm_stripslashes_deep(esc_html($mgm_membership_types->membership_types[$membership_type]))) .'</td>
						</tr>
						<tr class="alternate">
							<td align="left" height="30" valign="top"><strong>' . __('Subscribed Package','mgm') . '</strong></td>
							<td align="center" valign="top"><strong>:</strong></td>
							<td align="left" valign="top">' .mgm_stripslashes_deep(esc_html($subs_package)). '</td>
						</tr>						
					</table></p>';
				// cancelled
				if($mgm_member->status == MGM_STATUS_CANCELLED) {
					$html .= '<div style="margin-bottom: 10px;">'.
								'<h4>'. __('Unsubscribed','mgm').'</h4>'.
								'<div style="margin-bottom: 10px; color:#FF0000">'.
									 __('You have unsubscribed.','mgm'). 
								'</div>'.
								'</div>';
				}elseif(isset($mgm_member->status_reset_on) && isset($mgm_member->status_reset_as)) {
					$html .= '<div style="margin-bottom: 10px;">'.
							'<h4>'. __('Unsubscribed','mgm').'</h4>'.
							'<div style="margin-bottom: 10px; color:#FF0000">'.						
								 __(sprintf('You have unsubscribed. Your account is marked for Cancellation on <b>%s</b>.', date(MGM_DATE_FORMAT_LONG, strtotime($mgm_member->status_reset_on))),'mgm'). 
							'</div>'.
							'</div>';
				}else {		
					// show unsucscribe button			
					if(!is_super_admin() && isset($mgm_member->payment_info->module)) {
						$module = $mgm_member->payment_info->module;
						if(method_exists(mgm_get_module($module,'payment'), 'get_button_unsubscribe')) {
							// output button
							$html .= mgm_get_module($module,'payment')->get_button_unsubscribe(array('user_id'=>$user->ID, 'membership_type' => $mgm_member->membership_type));							
							$subs_count++;							
						}
					}	
				}				
			}
			//heading
			if($subs_count > 0) {
				$html = '<h3>'.__('Other Subscriptions','mgm').'</h3>'.
						'<p>' . $html;
			}
			
			if($subs_count > 0 &&  ( in_array($other_members->membership_type, array('free','trial')) || $other_members->status == MGM_STATUS_CANCELLED || (isset($other_members->status_reset_on) && isset($other_members->status_reset_as)))) {
				
				$html .= '<script language="javascript">'.
									'confirm_unsubscribe=function(element){'.										
										'if(confirm("' .__('Your are about to unsubscribe, are you sure?','mgm') . '")){'.																					
											'jQuery(element).closest("form").submit();'.
										'}'.								
									'}'.
								'</script>';
			}
			
			if($subs_count > 0) {
				$html .='</p>';	
			}
	}	
	// filter
	$html = apply_filters('mgm_other_subscriptions_html',$html, $user->ID);	
	// return
	return $html;
}
//rss token/membesrship cancellation form
function mgm_membership_cancellation($user_id = NULL) {
	if($user_id){
		$user = get_userdata($user_id);
	}
	// get current user
	if(!$user->ID){
		$user = wp_get_current_user();
	}
	// return when no user
	if(!$user->ID) return "";
	
	$token      = mgm_get_rss_token();
	$url        = home_url();
	$rss_url    = add_query_arg(array('feed'=>'rss2','token'=>$token), $url) ;
	$mgm_member = mgm_get_member($user->ID);
	
	if (mgm_use_rss_token()) {
		$html .= '<div style="margin-bottom: 10px;">'.
				'<h4>'. __('RSS Tokens','mgm'). '</h4>'.
				'<div style="margin-bottom: 10px;">'. __('Your RSS Token is','mgm').': <strong>' .$token. '</strong></div>'.
				'<div style="margin-bottom: 10px;">'.
					 __('Use the following link to access your RSS feed with access to private parts of the site.','mgm').'<br /><br />'.
					'<a href="'. $rss_url .'">'. $rss_url.' </a>'.
				'</div>'.
				'</div>';	 
	}
	// cancelled
	if($mgm_member->status == MGM_STATUS_CANCELLED) {
		$html .= '<div style="margin-bottom: 10px;">'.
					'<h4>'. __('Unsubscribed','mgm').'</h4>'.
					'<div style="margin-bottom: 10px; color:#FF0000">'.
						 __('You have unsubscribed.','mgm'). 
					'</div>'.
					'</div>';
	}elseif(isset($mgm_member->status_reset_on) && isset($mgm_member->status_reset_as)) {
		$html .= '<div style="margin-bottom: 10px;">'.
				'<h4>'. __('Unsubscribed','mgm').'</h4>'.
				'<div style="margin-bottom: 10px; color:#FF0000">'.						
					 __(sprintf('You have unsubscribed. Your account is marked for Cancellation on <b>%s</b>.', date(MGM_DATE_FORMAT_LONG, strtotime($mgm_member->status_reset_on))),'mgm'). 
				'</div>'.
				'</div>';
	}else {		
		// show unsucscribe button			
		if(!is_super_admin()) {
			$module = $mgm_member->payment_info->module;
			if($module && method_exists(mgm_get_module($module,'payment'), 'get_button_unsubscribe')) {
				// output button
				$html .= mgm_get_module($module,'payment')->get_button_unsubscribe(array('user_id'=>$user->ID, 'membership_type' => $mgm_member->membership_type));
				$html .= '<script language="javascript">'.
						'confirm_unsubscribe=function(element){'.
							'if(confirm("' .__('Your are about to unsubscribe, are you sure?','mgm') . '")){'.																
								'jQuery(element).closest("form").submit();'.
							'}'.								
						'}'.
					'</script>';
			}
		}	
	}
	
	$html = apply_filters('mgm_membership_details_html',$html, $user->ID);
		
	return $html;
}
//user membership details
function mgm_membership_details($user_id = NULL) {	
	if($user_id){
		$user = get_userdata($user_id);
	}
	// get current user
	if(!$user->ID){
		$user = wp_get_current_user();
	}
	// return when no user
	if(!$user->ID) return "";
	
	$html ='';
	if(isset($_GET['unsubscribe_errors']) && !empty($_GET['unsubscribe_errors'])) {
		$errors = new WP_Error();		
		$errors->add('unsubscribe_errors', urldecode($_GET['unsubscribe_errors']), (isset($_GET['unsubscribed'])?'message':'error'));
		$html .= mgm_set_errors($errors, true);		
		unset($errors);		
	}
	//$html = '<div>'.			
	$html .=	'<h3>'.__('Subscription Information','mgm').'</h3>'.
			'<p>';
	$html .= mgm_user_subscription($user_id);
	$html .='</p>'.									
			'<h3>'.__('Membership Information','mgm').'</h3>'.
			'<p>';
	$html.= mgm_membership_cancellation($user_id);	
	$html.= '</p>';		
	//other subscriptions
	$html .= mgm_other_subscriptions();
			
	return $html;		
			
}
// mgm_user_profile
function mgm_user_profile($user_id=NULL){
	// get user
	if(!$user_id){
		$user = wp_get_current_user();
	}elseif(isset($_GET['username'])){// get from url
		$user = get_userdatabylogin($_GET['username']);
	}elseif(isset($_GET['email'])){// get from url
		$user = get_user_by_email($_GET['email']);	
	}elseif(isset($_GET['user_id'])){// get from url
		$user = get_userdata($_GET['user_id']);		
	}else{
		$user = get_userdata($user_id);
	}
	
	// check
	if(!$user){
		die(__('No user','mgm'));		
	}
	
	// do your code
	do_action('show_user_profile');
}
// accessible contents
function mgm_member_accessible_contents($pagetype = 'admin'){
	get_currentuserinfo();
	global $current_user, $wpdb;	
	// snippet
	$snippet_length = 200;
	// get member
	$mgm_member = mgm_get_member($current_user->ID);
	//get all subscribed membership types
	$arr_memberships = mgm_get_subscribed_membershiptypes($current_user->ID, $mgm_member);	
	// accessible posts
	$accessible_posts = mgm_get_membership_posts($arr_memberships,'accessible');
	// posts
	$posts = $accessible_posts['posts'];
	// total
	//$total_posts = $accessible_posts['total_posts'];
	$total_posts = $accessible_posts['total_posts'];
	// total post rows , unfiltered
	$total_post_rows = $accessible_posts['total_post_rows'];
	// pager
	$pager = $accessible_posts['pager'];
	// init output
	$html = '';
	// table
	$html .= '<table width="100%" cellpadding="0" cellspacing="0" border="0" class="form-table widefat">'.
		'<thead>'.
			'<tr>'.
				'<th scope="col" height="30"><b> '.__('Post Title','mgm') . '</b></th>'.
				'<th scope="col"><b>'.__('Post Content','mgm') .'</b></th>'.
				'<th scope="col"><b>'.__('Published','mgm') .'</b></th>'.				
			'</tr>'.
		'</thead>';		
		if($total_posts>0) { 
			$pattern = get_shortcode_regex();
			foreach ($posts as $id=>$obj) {
				// set			
				$published = date('jS F Y', strtotime($obj->post_date));
				$title     = $obj->post_title;
				$content   = $obj->post_content;
				// content convert
				if (function_exists('qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage')) {
					$title   = qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage($title);
					$content = qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage($content);
				}				
				//issue#: 443								
				$content = preg_replace('/'.$pattern.'/s', '', $content);
				$content  = substr(strip_tags($content), 0, $snippet_length);				
				$content .= (strlen($content) > $snippet_length ? '...':'');				
				$html .='<tr class="'.($alt = ($alt=='') ? 'alternate': '').'">'.
						'<td valign="top" width="30%" height="30"><a href="'.get_permalink($obj->ID).'">'.$title.'</a></td>'.
						'<td valign="top" width="55%">'.$content.'</td>'.
						'<td valign="top" width="35%">'.$published .'</td>'.
						'</tr>';
			}
		}else{
			$html .= '<tr class="'.($alt = ($alt=='') ? 'alternate': '').'">'.
					'<td colspan="3" align="center" height="30">'.__('No premium contents','mgm').'</td>'.
					'</tr>';
		}	
		$html .= '</tbody>';	
	$html .='</table>';	
	// footer
	if($total_posts > 0) {
		$html .= '<div style="margin: 10px;">';
		if(isset($_GET['section']) && $_GET['section'] == 'accessible') {
			$html .= '<div style="font-weight: bold; float:left; margin-left:10px;">'.
					'<a href="'.(($pagetype=='admin')? admin_url('profile.php?page=mgm/membership/content') : mgm_get_custom_url('membership_contents')) .'" class="button">&laquo;'.__('Back','mgm') .'</a>'.
					'</div>';
		}
		$html .= '<div style="font-weight: bold; float:left; margin-left:10px">'.
				__(sprintf('You have access to a total of %d premium %s.', $total_posts, ($total_posts == 1 ? __('Post', 'mgm'):__('Posts', 'mgm'))),'mgm').
				'</div>';
		$html .='<div style="font-weight: bold; float:right; margin-right:10px">';
		if(isset($_GET['section']) && $_GET['section'] == 'accessible') {
			$html .= '<span class="pager">'.$accessible_posts['pager'].'</span>';
		//}elseif($total_post_rows > $total_posts) {
		//Do not show See All if number of records are <= $total_posts
		}elseif($total_posts > count($posts)) {
			$html .= '<a href="'.(($pagetype=='admin') ? admin_url('profile.php?page=mgm/membership/content&section=accessible') : mgm_get_custom_url('membership_contents', false, array('section' => 'accessible'))).'" class="button">'.__('See All','mgm').'&raquo;</a>';
		}
		$html .='</div>';	
		$html .='<br/><div class="clearfix"></div>';
		$html .='</div>';	
	}
	return $html;
}

// purchased contents
function mgm_member_purchased_contents($pagetype = 'admin'){
	get_currentuserinfo();
	global $current_user, $wpdb;
	// snippet
	$snippet_length = 200;	
	// purchased
	$purchased_posts = mgm_get_purchased_posts($current_user->ID);	
	// posts
	$posts = $purchased_posts['posts'];
	// total_posts
	$total_posts = $purchased_posts['total_posts'];
	// total post rows , unfiltered
	//$total_post_rows = $purchased_posts['total_post_rows'];
	// start output
	$html .= '<table width="100%" cellpadding="0" cellspacing="0" border="0" class="form-table widefat">'.
			'<thead>'.
				'<tr>'.
					'<th scope="col" height="30"><b>'.__('Post Title','mgm').'</b></th>'.
					'<th scope="col"><b>'.__('Post Content','mgm').'</b></th>'.
					'<th scope="col"><b>'.__('Published','mgm').'</b></th>'.
					'<th scope="col"><b>'.__('Purchased','mgm').'</b></th>'.
					'<th scope="col"><b>'.__('Expiry','mgm').'</b></th>'.
				'</tr>'.
			'</thead>'.
			'<tbody>';
	// check		
	if($total_posts>0) { 
		// loop
		foreach($posts as $id=>$obj){
			// set			
			$published = date('jS F Y', strtotime($obj->post_date));
			$purchased = date('jS F Y', strtotime($obj->purchase_dt));
			$title     = $obj->post_title;
			$content   = $obj->post_content;
			if (function_exists('qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage')) {
				$title   = qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage($title);
				$content = qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage($content);
			}
			$content  = preg_replace("'\[/?\s?private\s?\]'i",'', $content);
			//$content  = preg_replace("'\[\[(.*)\]\]'i",'', $content);
			//issue: 314
			$content  = preg_replace("/\[.*?\]/",'', $content);
			$content  = substr(strip_tags($content), 0, $snippet_length);
			$content .= (strlen($content) > $snippet_length ? '...':'');
			//expiry date:
			$expiry = mgm_get_post($obj->ID)->get_access_duration();			
			$expiry = (!$expiry) ? __('Indefinite', 'mgm') : (date('jS F Y',(86400*$expiry) + strtotime($obj->purchase_dt)) . " (" . $expiry . (__(' Day', 'mgm').($expiry > 1?__('s', 'mgm'):'')).")");			
					
		$html .='<tr class="'.($alt = ($alt=='') ? 'alternate': '').'">'.
				'<td valign="top" height="30" ><a href="'.get_permalink($obj->ID).'">'.$title.'</a></td>'.
				'<td valign="top">'.$content.'</td>'.
				'<td valign="top">'.$published.'</td>'.
				'<td valign="top">'.$purchased.'</td>'.								
				'<td valign="top">'.$expiry.'</td>'.								
			'</tr>';			
		}
	}else {
		$html .='<tr class="'.($alt = ($alt=='') ? 'alternate': '').'">'.
			'<td colspan="5" align="center" height="30">'.__('No purchased contents','mgm').'</td>'.
			'</tr>';
	}			
	$html .='</tbody>'.
			'</table>';	
	//return $html;			
	if($total_posts > 0 ) {
		$html .= '<div style="margin: 10px;">';		
		if(isset($_GET['section']) && $_GET['section'] == 'purchased') {
			$html .='<div style="font-weight: bold; float:left; margin-left:10px;">'.
				'<a href="'.(($pagetype=='admin') ? admin_url('profile.php?page=mgm/membership/content') : mgm_get_custom_url('membership_contents')).'" class="button">&laquo;'.__('Back','mgm').'</a>'.
				'</div>';
		}
		$html .= '<div style="font-weight: bold; float:left; margin-left:10px">'.
			__(sprintf('You have purchased a total of %d %s.', $total_posts, ($total_posts == 1 ? __('Post', 'mgm'):__('Posts', 'mgm'))),'mgm').
			'</div>';
		$html .='<div style="font-weight: bold; float:right; margin-right:10px">';
		if(isset($_GET['section']) && $_GET['section'] == 'purchased') {
			$html .='<span class="pager">'.$purchased_posts['pager'].'</span>';
		//}elseif($total_post_rows > $total_posts) {
		//Do not show See All if number of records are <= $total_posts
		}elseif($total_posts > count($posts)) {
			$html .='<a href="'.(($pagetype=='admin') ? admin_url('profile.php?page=mgm/membership/content&section=purchased') : mgm_get_custom_url('membership_contents', false, array('section' => 'purchased'))) .'" class="button">'.__('See All','mgm').'&raquo;</a>';
		}	
		$html .= '</div>';
		$html .='<br/><div class="clearfix"></div>';
		$html .='</div>';	
	}
	return $html;	
}

// purchasable contents
function mgm_member_purchasable_contents($pagetype = 'admin'){
	get_currentuserinfo();
	global $current_user, $wpdb;
	$setting = mgm_get_class('system')->setting;
	// snippet
	$snippet_length = 200;
	//  member
	$mgm_member     = mgm_get_member($current_user->ID);
	$arr_memberships = mgm_get_subscribed_membershiptypes($current_user->ID, $mgm_member);	
	// purchasable
	$purchasable_posts = mgm_get_membership_posts($arr_memberships, 'purchasable', $current_user->ID);	
	// posts
	$posts = $purchasable_posts['posts'];
	
	// total posts
	$total_posts = $purchasable_posts['total_posts'];
	// total_post_rows
	$total_post_rows = $purchasable_posts['total_post_rows'];
	// start output
	$html .= '<table width="100%" cellpadding="0" cellspacing="0" border="0" class="form-table widefat">'.
		'<thead>'.
			'<tr>'.
				'<th scope="col"><b>'.__('Post Title','mgm').'</b></th>'.
				'<th scope="col"><b>'.__('Post Content','mgm').'</b></th>'.
				'<th scope="col"><b>'.__('Published','mgm').'</b></th>'.
				'<th scope="col"><b>'.__('Price','mgm').'('.$setting['currency'].')</b></th>'.
				'<th scope="col"></th>'.				
			'</tr>'.
		'</thead>';	
		// check	
		if($total_posts) {	
			$pattern = get_shortcode_regex();
			// loop
			foreach ($posts as $id=>$obj) {
				// check purchasable			
				$published = date('jS F Y', strtotime($obj->post_date));
				$title     = $obj->post_title;
				$content   = $obj->post_content;
				if (function_exists('qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage')) {
					$title   = qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage($title);
					$content = qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage($content);
				}
				// strip_shortcodes
				$content = preg_replace('/'.$pattern.'/s', '', $content);
				$content  = substr(strip_tags($content), 0, $snippet_length);				
				$content .= (strlen($content) > $snippet_length ? '...':'');				
				$html .='<tr class="'.($alt = ($alt=='') ? 'alternate': '').'">'.
						'<td valign="top" height="30" ><a href="'.get_permalink($obj->ID).'">'.$title.'</a></td>'.
						'<td valign="top">'.$content.'</td>'.
						'<td valign="top">'.$published.'</td>'.
						'<td valign="top">'.$obj->purchase_cost.'</td>'.
						'<td><a href="'.get_permalink($obj->ID).'" class="button">'.__('Buy','mgm').'</a></td>'.
						'</tr>';
			}
		}else{
			$html .= '<tr class="'.($alt = ($alt=='') ? 'alternate': '').'">'.
					'<td colspan="4" align="center" height="30">'.__('No purchasable contents','mgm').'</td>'.
					'</tr>';
		}	
		$html .='</tbody>';	
		$html .='</table>';	
		
	if($total_posts > 0 ) {
		$html .='<div style="margin: 10px;">';	
		if(isset($_GET['section']) && $_GET['section'] == 'purchasable') {
			$html .='<div style="font-weight: bold; float:left; margin-left:10px;">'.
				  '<a href="'.(($pagetype=='admin') ? admin_url('profile.php?page=mgm/membership/content'): mgm_get_custom_url('membership_contents')).'" class="button">&laquo;'. __('Back','mgm').'</a>'.
				  '</div>';
		}
		$html .='<div style="font-weight: bold; float:left; margin-left:10px">'.
				__(sprintf('You have a total of %d premium %s you can purchase and access.', $total_posts, ($total_posts == 1 ? __('Post', 'mgm'):__('Posts', 'mgm'))),'mgm').
				'</div>';
		$html .='<div style="font-weight: bold; float:right; margin-right:10px">';		
		if(isset($_GET['section']) && $_GET['section'] == 'purchasable') {
			$html .='<span class="pager">'.$purchasable_posts['pager'].'</span>';
		//}elseif($total_post_rows > $total_posts) { 
		//Do not show See All if number of records are <= $total_posts
		}elseif($total_posts > count($posts)) { 
			$html .='<a href="'.(($pagetype=='admin') ? admin_url('profile.php?page=mgm/membership/content&section=purchasable') : mgm_get_custom_url('membership_contents', false, array('section' => 'purchasable') )).'" class="button">'.__('See All','mgm').'&raquo;</a>';
		}
		$html .='</div>';	
		$html .='<br/><div class="clearfix"></div>';
		$html .='</div>';	
	}	
	return $html;	
}

// membership accessible/purchable posts
function mgm_get_membership_posts($membership_types, $type='accessible', $user_id=NULL){
	global $wpdb;	
	
	// membership types
	if(!is_array($membership_types)) $membership_types = array($membership_types);
	// sql per page
	$limit_per_page = 50;
	// limit
	if(isset($_GET['section']) && $_GET['section'] == $type){
		$limit = '';
	}else{
		$limit = 'LIMIT ' . $limit_per_page;
	}	
	// get types
	$post_types_in = mgm_get_post_types(true);	
	// from
	$sql_from = " FROM " . $wpdb->posts . " A JOIN " . $wpdb->postmeta . " B ON (A.ID = B.post_id ) 
			      WHERE post_status = 'publish' AND B.meta_key LIKE '_mgm_post%' 
				  AND post_type IN ({$post_types_in}) ";
				  
	// get count first
	$total_post_rows = $wpdb->get_var("SELECT COUNT(* ) AS total_post_rows {$sql_from}");
	
	// update limit
	if(!empty($limit) && $total_post_rows > $limit_per_page){
		$limit = 'LIMIT ' . $total_post_rows;
	}
	
	// get posts	
	$results = $wpdb->get_results("SELECT DISTINCT(ID), post_title, post_date, post_content {$sql_from} ORDER BY post_date DESC {$limit}");		
	
	// for purchable only, get purchased posts
	if($type == 'purchasable'){
		// sql
		$sql = "SELECT `post_id` FROM `" . TBL_MGM_POSTS_PURCHASED . "` WHERE `user_id` = '{$user_id}'";		
		$purchased = $wpdb->get_results($sql);
		// init
		$purchased_posts = array();
		// check
		if (count($purchased) >0) {
			foreach ($purchased as $id=>$obj) {			
				$purchased_posts[] = $obj->post_id;				
			}
		}	
	}
	
	// init 
	$posts = array();
	
	// store
	if (count($results) >0) {
		// set counter		
		$total_posts 	= 0;
		// per page
		$posts_per_page = 5;
		// loop
		foreach ($results as $id=>$obj) {
			// post
			$mgm_post = mgm_get_post($obj->ID);			
			$access_types = $mgm_post->get_access_membership_types();
			// branch
			switch($type){
				case 'accessible':
					//if(in_array($membership_type, $mgm_post->get_access_membership_types())){
					//multiple membership level purchase(issue#: 400) modification
					if(array_diff($access_types, $membership_types) != $access_types){ //if any match found
						$total_posts++;
						if( ($limit!='' && $total_posts <= $posts_per_page) || $limit == ''  )
							$posts[] = $obj;
					}
				break;
				case 'purchasable':
					//if($mgm_post->purchasable == 'Y' && !in_array($membership_type, $mgm_post->get_access_membership_types())){
					//multiple membership level purchase(issue#: 400) modification
					if($mgm_post->purchasable == 'Y' && array_diff($access_types, $membership_types) == $access_types){//if no match
						// not purchased
						if(!in_array($obj->ID, $purchased_posts)){
							$total_posts++;
							if( ($limit!='' && $total_posts <= $posts_per_page) || $limit == ''  ) {
								//fetch post price								
								$obj->purchase_cost     = mgm_convert_to_currency($mgm_post->purchase_cost);
								$posts[] = $obj;
							}
						}
					}
				break;
			}
			// unset			
			unset($mgm_post);
			// counter
			//if($limit!='' && $counter == 20) break;
		}
	}
	// reset total
	if(empty($posts)) $total_posts = 0;
	// pager 
	$pager = '';
	/*if($total_post_rows > $limit_per_page){	
		$pager 	= sprintf('<a href="%s">%s</a>', mgm_get_custom_url('membership_contents', false, array('page'=>2)), __('next &raquo;','mgm'));
	}*/
	// return 		
	//return array('posts'=>$posts, 'total'=>$total, 'pager'=>$pager); ? not as per the expected format
	return array('posts'=>$posts, 'total_posts'=> $total_posts, 'total_post_rows' => $total_post_rows, 'pager'=>$pager);
}

// member purchased posts
function mgm_get_purchased_posts($user_id){
	global $wpdb;	
	$total_limit = 20;
	$per_page 	 = 5;
	// limit
	if(isset($_GET['section']) && $_GET['section'] == 'purchased'){
		$limit = 'LIMIT '.$total_limit;
	}else{
		$limit = 'LIMIT '.$per_page;
	}
	
	
	// sql
	$sql = "SELECT SQL_CALC_FOUND_ROWS A.ID,post_title,post_date,post_content,purchase_dt FROM `{$wpdb->posts}` A 
	        JOIN `" . TBL_MGM_POSTS_PURCHASED . "` B ON(A.ID=B.post_id) WHERE user_id = '{$user_id}' ORDER BY purchase_dt DESC ".$limit;
	// echo $sql;		
	$results = $wpdb->get_results($sql);
	
	// total
	//$total = $wpdb->get_var("SELECT FOUND_ROWS() AS total_rows");
	$total = $wpdb->get_var("SELECT count(B.id) as count FROM `{$wpdb->posts}` A 
	        				JOIN `" . TBL_MGM_POSTS_PURCHASED . "` B ON(A.ID=B.post_id) WHERE user_id = '{$user_id}' ORDER BY purchase_dt DESC LIMIT ".$total_limit);
	
	// init 
	$posts = array();
	
	// store
	if (count($results) >0) {
		foreach ($results as $id=>$obj) {			
			$posts[$obj->ID] = $obj;				
		}
	}		
	// return 	
	return array('posts'=>$posts,'total_posts'=>$total, 'pager'=>'');
}

// get next drip feed
function mgm_get_next_drip_feed(){
	// get current user
	$current_user = wo_get_current_user();
	// mgm member object
	if($current_user->ID){
		$mgm_member = mgm_get_member($current_user->ID);
	}	
}
//membership contents
function mgm_membership_contents() {
	global $user_ID,$current_user;		
	$html = '';
	if($user_ID) {
		$section = isset($_GET['section']) ? $_GET['section'] : 'all';
		$html .= '<div>';
		//accessible contents
		if(in_array($section, array('all','accessible'))) {
			$arr_mtlabel = mgm_get_subscribed_membershiptypes_with_label($user_ID);					
			$html .= '<div class="postbox" style="margin:10px 0px;">'.
					'<h3>'.sprintf(__('Your Membership Level "%s" Accessible Contents','mgm'), mgm_stripslashes_deep(implode(', ',$arr_mtlabel))).'</h3>'.
					'<div class="inside">'.
					mgm_member_accessible_contents('user').
					'</div>'.
					'</div>';
		}
		//already purchased contents
		if(in_array($section, array('all','purchased'))) {
			$html .= '<div class="postbox" style="margin:10px 0px;">'.
					 '<h3>'.__('Purchased Contents','mgm') . '</h3>'.
					 '<div class="inside">'.mgm_member_purchased_contents('user') .'</div></div>';
		}
		//purchasable contents
		if(in_array($section, array('all','purchasable'))) {
			$html .= '<div class="postbox" style="margin:10px 0px;">'.
					'<h3>'. __('Purchasable Contents','mgm') . '</h3>'.
					'<div class="inside">'.
					mgm_member_purchasable_contents('user') .
					'</div>' .
					'</div>' ;
		}
		$html .= '</div>';
		
	}else {
		$template = mgm_get_template('private_text_template', array(), 'templates');		
		$html = 'You need to be logged in to access this page.';
		$html .= sprintf(__(' Please <a href="%s"><b>login</b> here.</a>','mgm'), mgm_get_custom_url('login', false, array('redirect_to' => get_permalink($post->ID) )));
		$html = str_replace('[message]', $html, $template);
	}
	$html = apply_filters('mgm_membership_contents_html',$html);
	
	return $html;
}
//fetch posts for membership level
function mgm_get_posts_for_level($membership_type = '', $show_all = true) {
	global $wpdb, $post;	
	if(!empty($membership_type)) {
		if(!is_array($membership_type))
			$membership_type = array(0 => $membership_type);
		// get post types
		$post_types_in = mgm_get_post_types(true);
		// id
		$post_id_notin = (is_numeric($post->ID)) ? $post->ID : 0 ; 
		// sql 	
		$limit = 50;
		$per_page = 10;			
		$sql = "SELECT DISTINCT(ID), post_title, post_date, post_content
				FROM " . $wpdb->posts . " A JOIN " . $wpdb->postmeta . " B ON (A.ID = B.post_id ) 
				WHERE post_status = 'publish' AND B.meta_key LIKE '_mgm_post%' 
				AND post_type IN ({$post_types_in}) AND A.id NOT IN($post_id_notin) 
				ORDER BY post_date DESC LIMIT 0,".$limit;					
		// get posts	
		$results = $wpdb->get_results($sql);	
		// chk
		if ( count($results) > 0 ) {
			// set counter		
			$total 		= 0;			
			// loop
			foreach ($results as $id=>$obj) {
				// post
				$mgm_post = mgm_get_post($obj->ID);
				$access_types = $mgm_post->get_access_membership_types();
				$found = false;
				if(!empty($access_types)) {
					foreach ($access_types as $type) {
						if(in_array($type, $membership_type)){
							$mgm_membership = mgm_get_class('membership_types');
							$obj->access_membership_type = $mgm_membership->get_type_name($type);							
							$found = true;
							$total++;
							break;
						}
					}
					if($found && ( (isset($_GET['show']) && $_GET['show'] == 'all' || $show_all) || $total <= $per_page))
						$posts[] = $obj;
				}
				// branch				
								
			}						
			return array('posts' => $posts, 'total' => $total);
		}
	}
	return array();
}
//display posts for membership level
function mgm_posts_for_membership($membership_type = '') {
	$posts = mgm_get_posts_for_level($membership_type, false);		
	
	$membership_type = (is_array($membership_type)) ? $membership_type : array(0 => $membership_type);
	$levels = '';
	if(!empty($membership_type)) {
		$i = 0;
		$cnt = count($membership_type);
		foreach ($membership_type as $key => $lvl) {
			$sep = '';
			if($i > 0 && $i == $cnt -1)
				$sep = ' and ';
			elseif ($i > 0 )
				$sep = ', ';	
			$mgm_membership = mgm_get_class('membership_types');
			$levels .= $sep . '"'.mgm_stripslashes_deep($mgm_membership->get_type_name($lvl)).'"';
			$i++;
		}
	}	
	
	$html .= '<div class="postbox" style="margin:10px 0px;">'.
				'<h3>'.sprintf(__('Accessible Contents For %s','mgm'), $levels).'</h3>'.
				'<div class="inside">';					
	$html .= '<table width="100%" cellpadding="0" cellspacing="0" border="0" class="form-table widefat">'.
			'<thead>'.
				'<tr>'.
					'<th scope="col"><b> '.__('Post Title','mgm') . '</b></th>'.
					'<th scope="col"><b>'.__('Post Content','mgm') .'</b></th>'.
					'<th scope="col"><b>'.__('Published','mgm') .'</b></th>'.
					'<th scope="col"><b>'.__('Membership Type','mgm') .'</b></th>'.				
				'</tr>'.
			'</thead>';
		
	if(isset($posts['total']) && $posts['total'] > 0) {		
		$pattern = get_shortcode_regex();
		$snippet_length = 200;
		foreach ($posts['posts'] as $id=>$obj) {
			// check purchaseble			
			$published = date('jS F Y', strtotime($obj->post_date));
			$title     = $obj->post_title;
			$content   = $obj->post_content;
			if (function_exists('qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage')) {
				$title   = qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage($title);
				$content = qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage($content);
			}				
			$content = preg_replace('/'.$pattern.'/s', '', $content);
			$content  = substr(strip_tags($content), 0, $snippet_length);				
			$content .= (strlen($content) > $snippet_length ? '...':'');				
			$html .='<tr class="'.($alt = ($alt=='') ? 'alternate': '').'">'.
					'<td valign="top" height="30" ><a href="'.get_permalink($obj->ID).'">'.$title.'</a></td>'.
					'<td valign="top">'.$content.'</td>'.
					'<td valign="top">'.$published.'</td>'.					
					'<td valign="top">'.mgm_stripslashes_deep($obj->access_membership_type).'</td>'.					
					'</tr>';
		}
				
	}else{
		$html .= '<tr class="'.($alt = ($alt=='') ? 'alternate': '').'">'.
				'<td colspan="3" align="center" height="30">'.__('No posts available','mgm').'</td>'.
				'</tr>';
	}
	$html .='</tbody>';	
	$html .='</table>';	
	
	if(isset($posts['total']) && $posts['total'] > 0) {	
		$html .= '<div style="margin: 10px;">';
		if(isset($_GET['show']) && $_GET['show'] == 'all') {
			$html .= '<div style="font-weight: bold; float:left; margin-left:10px;">'.
					'<a href="'. (add_query_arg(array('show' => 'paged'),mgm_current_url())). '" class="button">&laquo;'.__('Back','mgm') .'</a>'.
					'</div>';
		}
		$html .= '<div style="font-weight: bold; float:left; margin-left:10px">'.
				__(sprintf('Total Accessible Contents: %d', $posts['total']),'mgm').
				'</div>';
		$html .='<div style="font-weight: bold; float:right; margin-right:10px">';		
		if((!isset($_GET['show']) || (isset($_GET['show']) && $_GET['show'] == 'paged')) && $posts['total'] > count($posts['posts']) )
			$html .= '<a href="'.(add_query_arg(array('show' => 'all'),mgm_current_url())).'" class="button">'.__('See All','mgm').'&raquo;</a>';
		
		$html .='</div>';	
		$html .='<br/><div class="clearfix"></div>';
		$html .='</div>';	
	}
				
	$html .= '</div>'.
			 '</div>';
			 
	$html = apply_filters('mgm_posts_for_membership_html', $html);		 
			 
	return $html;
}
//generate scripts for custom photo upload
function mgm_upload_script_js($form_id, $images) {
	if(is_super_admin())
		$upload_url = mgm_get_custom_url('profile');
	else {
		//the below url doesn't exist but will capture file_upload query
		$upload_url = trailingslashit(site_url()) .'upload';
	}
	$upload_url = add_query_arg(array('file_upload'=>'image'), $upload_url); 
	$user = wp_get_current_user();	
	$field_name = $images[0];
	$field_type = ($user->ID > 0) ? 'profile' : 'register';

	$js = 'jQuery(document).ready(function(){';
	
	$js .= 'jQuery( "#'.$form_id.'").attr( "enctype", "multipart/form-data" );';
 	$js .= 'jQuery( "#'.$form_id.'").attr( "encoding", "multipart/form-data" );';	 	
	$js .= 'jQuery("#uploader_loading").hide();';
	 // mgm_profile_photo_upload
	$js .= 'mgm_profile_photo_upload = function(obj) {'."\n".		 	
			//'if(jQuery(obj).val().toString().is_empty()==false){'."\n".					
				'if(!(/\.(png|jpe?g|gif)$/i).test(jQuery(obj).val().toString())){'."\n".
					'alert(\''.__('Please upload only gif,jpg and png files', 'mgm').'\');'."\n".
					'return;'."\n".
				'}'."\n".				
				// process upload 		
				// vars													
				//'var form_id = jQuery(jQuery(obj).get(0).form).attr(\'id\');'."\n".					
				'jQuery("#uploader_loading").show();'."\n".	
				// upload 				
				'jQuery.ajaxFileUpload({'."\n".						
						'url:\''. ($upload_url) .'\','."\n". 
						'secureuri:false,'."\n".
						'fileElementId:jQuery(obj).attr(\'id\'),'."\n".
						'dataType: \'json\','."\n".						
						'success: function (data, status){'."\n".	
							// uploaded					
							'if(data.status==\'success\'){'."\n".
								'jQuery("#uploader_loading").hide();'."\n".																	
								// change file								
								'var cont_obj = jQuery("#'.($form_id).' :file[name=\'"+jQuery(obj).attr(\'name\')+"\']").parent();'."\n".								
								'cont_obj.fadeOut();'."\n".
								'setTimeout(function(){'."\n".
									'cont_obj.html(\'<img style="width:\'+data.upload_file.width+\'px;" src="\'+data.upload_file.file_url+\'"><input type="hidden" id="mgm_'.$field_type.'_field_'.$field_name.'_hidden" name="mgm_'.$field_type.'_field['.$field_name.']" value="\'+data.upload_file.file_url+\'">&nbsp;<span onclick="delete_upload(this,\\\'\'+data.upload_file.hidden_field_name+\'\\\')"><img style="cursor:pointer;" src="'.MGM_ASSETS_URL . '/images/icons/cross.png" alt="'.__('Delete').'" title="'.__('Delete').'"></span>\');'."\n".																
									'//alert(data.message);'."\n".
									'cont_obj.fadeIn();'."\n".
								'},\'300\');'."\n".	
								//temp message:
																								
							'}else{'."\n".
								'jQuery("#uploader_loading").hide();'."\n".
								'mgm_file_uploader("#'.($form_id).'", mgm_profile_photo_upload);'."\n".
								'alert(data.message);'."\n".
							'}'."\n".						
						'},'."\n".
						'error: function (data, status, e){'."\n".
							'jQuery("#uploader_loading").hide();'."\n".
							'mgm_file_uploader("#'.($form_id).'", mgm_profile_photo_upload);'."\n".
							'alert(\''.__('Error occured in upload','mgm').'\');'."\n".							
						'}'."\n".
					'}'."\n".
				')'."\n".		
				// end
		//	'}'."\n".			 
		 '}'."\n";		
	 // bind uploader	 
	$js .= 'mgm_file_uploader("#'.($form_id).'", mgm_profile_photo_upload);'."\n";
		 
	$js .= 'delete_upload = function(container, hidden_field_name){'."\n".
		   'var obj_parent = jQuery(container).parent();'."\n".						
		   'obj_parent.fadeOut();'."\n".
		   'setTimeout(function(){'."\n".
		   'obj_parent.html(\'<input type="file" id="mgm_'.$field_type.'_field_'.$field_name.'" name="mgm_'.$field_type.'_field['.$field_name.']"><input type="hidden" id="mgm_'.$field_type.'_field_'.$field_name.'_hidden" name="mgm_'.$field_type.'_field['.$field_name.']" value="">&nbsp;<img id="uploader_loading" src="'.esc_url( admin_url( 'images/wpspin_light.gif' ) ).'" alt="'.__('Loading','mgm').'" title="'.__('Loading','mgm').'">\');'."\n".				
		   'mgm_file_uploader("#'.($form_id).'", mgm_profile_photo_upload);'."\n".
		   'jQuery("#uploader_loading").hide();'."\n".				
		   'obj_parent.fadeIn();'."\n".
		   '},\'300\');'."\n".				
		   '}'."\n";		
	$js .= '});';	
	return "\n".'<script type="text/javascript">'."\n".$js."\n".'</script>';
}
//custom logout link
function mgm_logout_link($label, $return = true) {
	//logged in user:	
	$user = wp_get_current_user();
	//if no login
	if(!isset($user->ID) || (isset($user->ID) && $user->ID == 0 ) )
		return "";
		
	if(empty($label))
		 $label = __('Logout', 'mgm');
	$logout_url = wp_logout_url();
	$logout_url = mgm_logout_url($logout_url,'');
	
	if($return)
		return '<a href='.$logout_url.'>'. $label .'</a>';
	else 
		echo '<a href='.$logout_url.'>'. $label .'</a>';	
}
/**
 * Membership extend link
 *
 * @param string $label : link lable
 * @param boolean $return: whether return the link or echo
 * @return the link
 */
function mgm_membership_extend_link($label, $return = true) {
	//default label
	if(empty($label))
		$label = __('Extend', 'mgm');
	
	$extend_link = "";
	//logged in user:	
	$user = wp_get_current_user();
	
	if(!isset($user->ID) || (isset($user->ID) && $user->ID == 0 ) || is_super_admin())
		return "";
		
	$subscription_packs = mgm_get_class('subscription_packs');	
	$mgm_member  = mgm_get_member($user->ID);	
	$pack_id     = $mgm_member->pack_id;
	if($pack_id) {
		$pack        = $subscription_packs->get_pack($mgm_member->pack_id); 
		$num_cycles = (isset($mgm_member->active_num_cycles) && !empty($mgm_member->active_num_cycles)) ? $mgm_member->active_num_cycles : $pack['num_cycles'] ;
		// check cycles	
		if($num_cycles > 0 && mgm_pack_extend_allowed($pack)) {
			$extend_link = '<a href="'. mgm_get_custom_url('transactions',false,array('action' => 'extend', 'pack_id'=>$pack_id, 'username' => $user->user_login)) . '">' . $label . '</a>';
		}
	}
	//if return
	if($return)
		return $extend_link;
	else 
		echo $extend_link;	 			 
}
// end of file