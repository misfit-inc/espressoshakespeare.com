<?php
/**
 * Magic Members admin payperpost module
 *
 * @package MagicMembers
 * @since 2.0
 */
 class mgm_payperpost extends mgm_controller{
 	
	// construct
	function mgm_payperpost()
	{		
		// load parent
		parent::__construct();
	}
	
	// index
	function index(){
		// data
		$data = array();
		// load template view
		$this->load->template('payperpost/index', array('data'=>$data));	
	}
	
	// posts
	function posts(){		
		// data
		$data = array();
		// load template view
		$this->load->template('payperpost/post/index', array('data'=>$data));	
	}
	
	// post_purchase_statistics
	function post_purchase_statistics(){		
		global $wpdb;
		// data
		$data = array();
		// sql
		$sql = 'SELECT p.post_title AS title, COUNT(pp.id) AS count
				FROM `' . TBL_MGM_POSTS_PURCHASED.'` pp 
				JOIN ' . $wpdb->posts . ' p ON (p.id = pp.post_id)
				WHERE is_gift="N" 
				GROUP BY pp.post_id  ORDER BY pp.post_id DESC';
		// mgm_log( $sql );
		// store		
		$data['posts'] = $wpdb->get_results($sql);
		// load template view
		$this->load->template('payperpost/post/purchase_statistics', array('data'=>$data));	
	}
	
	// post_purchase_gifts
	function post_purchase_gifts(){		
		global $wpdb;
		// data
		$data = array();
		// sql
		$sql = 'SELECT p.ID AS post_id, p.post_title, pp.purchase_dt, u.user_login, pp.id,
		        pp.is_gift,pp.is_expire 
				FROM `' . TBL_MGM_POSTS_PURCHASED.'` pp 
				JOIN ' . $wpdb->posts . ' p ON (p.id = pp.post_id) 
				JOIN ' . $wpdb->users . ' u ON (u.ID = pp.user_id) 
				ORDER BY u.user_login, p.post_title';
		// store		
		$data['posts'] = $wpdb->get_results($sql);	
		// load template view
		$this->load->template('payperpost/post/purchase_gifts', array('data'=>$data));	
	}
	
	// post_purchase_record_delete
	function post_purchase_delete(){
		global $wpdb;	
		extract($_POST);		
		// sql
		$sql = "DELETE FROM `" . TBL_MGM_POSTS_PURCHASED . "` WHERE id = '{$id}'"; 		
		// delete
	    if ($wpdb->query($sql)) {    	    
			$message = __('Successfully deleted post purchase record.', 'mgm');
			$status  = 'success';
	    }else{
			$message = __('Error while deleting post purchase record.', 'mgm');
			$status  = 'error';
		}
		// return response
		
		echo json_encode(array('status'=>$status, 'message'=>$message));
		
	}
	
	// post_send_gift
	function post_send_gift(){		
		global $wpdb;	
		extract($_POST);
		
		// save
		if(isset($send_gift)){	
			// user data
			$user = get_userdata($user_id);	
			$post = get_post($post_id);	
			// expire
			if(!isset($is_expire) || empty($is_expire))
				$is_expire = 'Y';  
			
			// sql
			$sql = "REPLACE INTO `" . TBL_MGM_POSTS_PURCHASED . "` SET `user_id`='{$user_id}', `post_id`='{$post_id}', 
			       `is_gift`='Y',`purchase_dt`=NOW(), `is_expire`='{$is_expire}'";			
			// saved
			if ($wpdb->query($sql)) {
				$message = sprintf(__('Successfully gifted post - "%s" to member - "%s".', 'mgm'), $post->post_title, $user->display_name);
				$status  = 'success';
			}else{
				$message = sprintf(__('Error while gifting post - "%s" to member - "%s".', 'mgm'),$post->post_title, $user->display_name);
				$status  = 'error';
			}
			// return response
			
			echo json_encode(array('status'=>$status, 'message'=>$message));
				
			exit();
		}
		// data
		$data = array();
		// users
		$data['users'] = mgm_field_values( $wpdb->users, 'ID', 'user_login', "AND ID<>1", 'user_login');	
		// posts		
		$data['posts'] = mgm_get_purchasable_posts();
		// load template view
		$this->load->template('payperpost/post/send_gift', array('data'=>$data));	
	}
	
	// postpacks
	function postpacks(){		
		// data
		$data = array();
		// load template view
		$this->load->template('payperpost/postpack/index', array('data'=>$data));		
	}
	
	// postpack_list
	function postpack_list(){
		global $wpdb;	
		// data
		$data = array();	
		// postpacks		
	    $data['postpacks'] = $wpdb->get_results('SELECT id, name, description, create_dt, cost FROM `' . TBL_MGM_POST_PACK . '` ORDER BY name');
		// currency
		$data['currency'] = mgm_get_class('system')->setting['currency'];		 
		// load template view
		$this->load->template('payperpost/postpack/list', array('data'=>$data));		
	}
	
	// postpack_add
	function postpack_add(){	
		global $wpdb;	
		extract($_POST);
		
		// save
		if(isset($save_postpack)){			
			// product
			$product = json_encode($product);
			// sql
			$sql = "INSERT INTO `" . TBL_MGM_POST_PACK . "` SET name='{$name}', cost='{$cost}', description='{$description}', product='{$product}', create_dt=NOW() ";		
			// saved
			if ($wpdb->query($sql)) {
				$message = sprintf(__('Successfully created new postpack: %s', 'mgm'),  $name);
				$status  = 'success';
			}else{
				$message = sprintf(__('Error while creating new postpack: %s', 'mgm'),  $name);
				$status  = 'error';
			}
			// return response
			
			echo json_encode(array('status'=>$status, 'message'=>$message));
				
			exit();
		}	
		
		// data
		$data = array();
		// currency
		$data['currency'] = mgm_get_class('system')->setting['currency'];	
		// load template view
		$this->load->template('payperpost/postpack/add', array('data'=>$data));		
	}	
	
	// postpack_edit
	function postpack_edit(){	
		global $wpdb;	
		extract($_POST);
		
		// save
		if(isset($save_postpack)){	
			// product
			$product = json_encode($product);		
			// sql
			$sql = "UPDATE `" . TBL_MGM_POST_PACK . "` SET name='{$name}', cost='{$cost}', product='{$product}',description='{$description}' WHERE id='{$id}' ";		
			// saved
			if ($wpdb->query($sql)) {
				$message = sprintf(__('Successfully updated postpack: %s', 'mgm'),  $name);
				$status  = 'success';
			}else{
				$message = sprintf(__('Error while updating postpack: %s', 'mgm'),  $name);
				$status  = 'error';
			}
			// return response
			
			echo json_encode(array('status'=>$status, 'message'=>$message));
				
			exit();
		}	
		
		// data
		$data = array();
		// postpack
		$data['postpack'] = $wpdb->get_row("SELECT * FROM ".TBL_MGM_POST_PACK." WHERE id='{$id}'");
		// currency
		$data['currency'] = mgm_get_class('system')->setting['currency'];		 
		// load template view
		$this->load->template('payperpost/postpack/edit', array('data'=>$data));		
	}	
	
	// postpack_delete 
	function postpack_delete(){
		global $wpdb;	
		extract($_POST);		
		// sql
		$sql = "DELETE FROM `" . TBL_MGM_POST_PACK . "` WHERE id = '{$id}'"; 		
		// delete
	    if ($wpdb->query($sql)) {    	    
			$message = __('Successfully deleted postpack.', 'mgm');
			$status  = 'success';
	    }else{
			$message = __('Error while deleting postpack.', 'mgm');
			$status  = 'error';
		}
		// return response
		
		echo json_encode(array('status'=>$status, 'message'=>$message));
		
	}
	
	// postpack_posts
	function postpack_posts(){
		global $wpdb;	
		extract($_POST);		
		// postpack
		$postpack = $wpdb->get_row("SELECT * FROM ".TBL_MGM_POST_PACK." WHERE id='{$pack_id}'");
		// save
		if(isset($save_postpack_post)){		
			// marker
			$updated = 0;
			
			// save 				
			if ($posts) {				
				foreach ($posts as $post_id) {
					// clear old
					// $wpdb->query('DELETE FROM `' . TBL_MGM_POST_PACK_POST_ASSOC . '` WHERE pack_id = ' . $pack_id);
					// is added
					$count = $wpdb->get_var("SELECT COUNT(*) AS _C FROM ". TBL_MGM_POST_PACK_POST_ASSOC ." WHERE pack_id='$pack_id' AND post_id='$post_id'");
					if($count == 0 ){
						$sql = "INSERT INTO `" . TBL_MGM_POST_PACK_POST_ASSOC. "` SET pack_id='$pack_id', post_id='$post_id', create_dt=NOW() ";
						$wpdb->query($sql);
						$updated++;		
					}		
				}
			}			
					
			// saved
			if ($updated) {
				$message = sprintf(__('Successfully associated post to postpack: %s', 'mgm'),  $postpack->name);
				$status  = 'success';
			}else{
				$message = sprintf(__('Error while associated post to postpack: %s', 'mgm'),  $postpack->name);
				$status  = 'error';
			}
			// return response
			
			echo json_encode(array('status'=>$status, 'message'=>$message));
				
			exit();
		}	
		
		// data
		$data = array();
		// postpack
		$data['postpack'] = $postpack;
		// exclude 
		$data['exclude_posts'] = array();
		// fetch 
		$associated_posts = $wpdb->get_results('SELECT post_id FROM `' . TBL_MGM_POST_PACK_POST_ASSOC . '`  WHERE pack_id =  ' . $pack_id);
		if($associated_posts){
			foreach($associated_posts as $a_post){
				$data['exclude_posts'][] = $a_post->post_id;
			}
		}
		// load template view
		$this->load->template('payperpost/postpack/post/index', array('data'=>$data));		
	}
	
	// postpack_post_list
	function postpack_post_list(){
		global $wpdb;	
		extract($_POST);
		
		// data
		$data = array();
		// postpack posts				
        $data['postpack_posts'] = $wpdb->get_results('SELECT id, pack_id, post_id, create_dt FROM `' . TBL_MGM_POST_PACK_POST_ASSOC . '`  WHERE pack_id =  ' . $pack_id);
		// load template view
		$this->load->template('payperpost/postpack/post/list', array('data'=>$data));		
	}
	
	// postpack_post_delete 
	function postpack_post_delete(){
		global $wpdb;	
		extract($_POST);		
		// sql
		$sql = "DELETE FROM `" . TBL_MGM_POST_PACK_POST_ASSOC . "` WHERE id = '$id' "; 		
		// delete
	    if ($wpdb->query($sql)) {    	    
			$message = __('Successfully deleted postpack post association.', 'mgm');
			$status  = 'success';
	    }else{
			$message = __('Error while deleting postpack association.', 'mgm');
			$status  = 'error';
		}
		// return response		
		echo json_encode(array('status'=>$status, 'message'=>$message));	
	}
 }
// return name of class 
return basename(__FILE__,'.php');
// end file /core/admin/mgm_payperpost.php