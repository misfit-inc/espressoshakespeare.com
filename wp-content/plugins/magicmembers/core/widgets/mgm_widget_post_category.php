<?php
// post and category widgets
// todo , check access
// post meta boxes
add_action('admin_menu', 'mgm_post_setup_meta_box');
add_action('save_post' , 'mgm_post_setup_save');
// categoty boxes
add_filter('add_category_form' , 'mgm_category_form');
add_filter('edit_category_form', 'mgm_category_form');
add_action('create_category'   , 'mgm_category_save');
add_action('edit_category'     , 'mgm_category_save');
add_action('delete_category'   , 'mgm_category_delete');

// the meta box for post/page purchase
function mgm_post_setup_meta_box(){
	// 2.7+
	if( function_exists( 'add_meta_box' )) {		
		// update for custom post type
		if ( function_exists( 'get_post_types' ) ) {
			// get custom post types
			$custom_post_types = get_post_types( array(), 'objects' );
			// add to array
			foreach ( $custom_post_types as $post_type ) {
				// set
				if ( $post_type->show_ui ) {// check if enabled
					$post_types[] = $post_type->name;
				}
			}
		} else{
			// default post types
			$post_types = array('post','page');
		}				
		// mgm_array_dump($post_types);
		// assign
		foreach($post_types as $post_type){			
			add_meta_box('magicmemberdiv', __('Magic Members'), 'mgm_post_setup_meta_box_form', $post_type, 'side', 'high');
		}				
	}else{
		// just for test: deprecated
		add_action('dbx_post_advanced', 'mgm_post_setup_meta_box_form' );
    	add_action('dbx_page_advanced', 'mgm_post_setup_meta_box_form' );
	}	
}

// mgm_post_setup_meta_box_form 
function mgm_post_setup_meta_box_form($post){
	// get object
	$mgm_post = mgm_get_post($post->ID);
	
	// set default price
	if($mgm_post->purchase_cost == 0){
		if (mgm_get_module('mgm_paypal','payment')->setting['purchase_price']) {
			$mgm_post->purchase_cost = mgm_get_module('mgm_paypal','payment')->setting['purchase_price'];
		} else {
			$mgm_post->purchase_cost = mgm_get_class('system')->setting['post_purchase_price'];
		}
	}
	//issue#: 414(changed id submitpost => submitpost_member for the below div )
	?>
	<div class="submitbox" id="submitpost_member">	
		<div class="misc-pub-section">
			<p id="howto">
				<?php _e('Select which membership types will have access to read this post/page.','mgm') ?>
				<?php _e('Note: The private parts of the post should be inside the following tags: <strong>[private]</strong> <em>your text</em> <strong>[/private]</strong>','mgm') ?>
			</p>
			<p>
				<div style="border-bottom:1px solid #D7E5EE; height:25px; ">
					<input type="checkbox" name="check_all" value="mgm_post[access_membership_types][]" /> <span><?php _e('Select all','mgm'); ?></span>
				</div>
			</p>
			<p>
				<?php echo mgm_make_checkbox_group('mgm_post[access_membership_types][]', mgm_get_class('membership_types')->membership_types, $mgm_post->access_membership_types, MGM_KEY_VALUE);?>				
			</p>
			<?php $protect_content = mgm_protect_content()?>
			<?php if($protect_content == false):?>
			<div class="information" style="width:230px"><?php echo sprintf(__('<a href="%s">Content Protection</a> is <b>%s</b>. Make sure its enabled to Protect Post/Page.','mgm'), 'admin.php?page=mgm/admin', ($protect_content ? 'enabled' :'disabled'))?></div>			
			<?php endif;?>
		</div>	
		
		<div class="misc-pub-section">
			<b><?php _e( 'Pay Per Post', 'mgm' ); ?>:</b>
			<a href="#payperpost" class="mgm-toggle"><?php _e('Edit') ?></a>
			<div id="payperpostdiv" class="hide-if-js">
				<div style="padding:5px">				
					<p id="howto"><?php _e('If the user doesn\'t have access, is this post/page available to buy?','mgm') ?> </p>
					<ul style="list-style: none; margin: 0; padding: 3px;">
						<li>
							<label>
								<input type="radio" class="radio" name="mgm_post[purchasable]" value='N' <?php mgm_check_if_match('N',$mgm_post->purchasable);?>/> <?php _e('No') ?> 
							</label>
							<label>
								<input type="radio" class="radio" name="mgm_post[purchasable]" value='Y' <?php mgm_check_if_match('Y',$mgm_post->purchasable); ?>/> <?php _e('Yes') ?>
							</label>
						</li>
						<li>
							<label>
								<?php _e('Cost of Post?') ?> 
								<input type="text" name="mgm_post[purchase_cost]" style="width:55px;" value="<?php echo $mgm_post->purchase_cost; ?>"/> <?php echo mgm_get_class('system')->setting['currency']?>
							</label>
						</li>
						<li>
							<label><?php _e('The date that the ability to buy this page/post expires (Leave blank for indefinate).','mgm') ?>
								<br />
								<input type="text" name="mgm_post[purchase_expiry]" class="date_input" style="width:100px;" value="<?php echo (intval($mgm_post->purchase_expiry)>0) ? date(MGM_DATE_FORMAT_INPUT, strtotime($mgm_post->purchase_expiry)) : ''; ?>"/>
								<span style="font-size: 8px;">(MM/DD/YYYY)</span>
							</label>
						</li>
						<li>
							<label><?php _e('The number of days that the buyer will have access for (0 for indefinate).','mgm') ?>
								<br />
								<input type="text" name="mgm_post[access_duration]" style="width:50px;" value="<?php echo $mgm_post->get_access_duration(); ?>"/>
							</label>
						</li>						
						<?php 
						// post product mapping
						$payment_modules = mgm_get_class('system')->get_active_modules('payment');
						// post purchase settings
						if($payment_modules):		
							// loop
							foreach($payment_modules as $payment_module) :
								// print
								echo mgm_get_module($payment_module)->settings_post_purchase($mgm_post);
							endforeach;		
						endif;?>						
					</ul>			
				</div>	
			</div>
		</div>			
		<div class="misc-pub-section misc-pub-section-last">
			<b><?php _e( 'Post Delay (sequential posts)', 'mgm' ); ?>:</b>
			<a href="#postdelay" class="mgm-toggle"><?php _e('Edit') ?></a>
			<div id="postdelaydiv" class="hide-if-js">
				<div style="padding:5px">				
					<p id="howto"><?php _e('How long should the user have been a member to see this content?','mgm') ?></p>
					<table style="width:100%;">
					<?php
					foreach (mgm_get_class('membership_types')->membership_types as $type_code=>$type_name) :					
						$val = (int)$mgm_post->access_delay[$type_code];?>
						<tr>
							<td style="font-size:11px;"><?php echo $type_name; ?></td>
							<td style="font-size:11px;">
								<input type="text" name="mgm_post[access_delay][<?php echo $type_code; ?>]" value="<?php echo $val ?>" style="width: 50px;"/> Day(s)
							</td>
						</tr>
					<?php endforeach;?>
					</table>		
				</div>	
			</div>
		</div>				
	</div>	
	
	<script language="javascript">
		jQuery(document).ready(function(){			
			jQuery('.mgm-toggle').bind('click', function(){
				if(jQuery(this).html() == '<?php _e('Edit') ?>'){
					jQuery(jQuery(this).attr('href')+'div').slideDown();
					jQuery(this).html('<?php _e('Close') ?>')
				}else{
					jQuery(jQuery(this).attr('href')+'div').slideUp();
					jQuery(this).html('<?php _e('Edit') ?>')
				}
			});
			// check bind
			jQuery("#submitpost_member :checkbox[name='check_all']").bind('click',function(){
				jQuery("#submitpost_member :checkbox[name='"+jQuery(this).val()+"']").attr('checked', jQuery(this).attr('checked'));
				if(jQuery(this).attr('checked')){
					jQuery(this).next().html('<?php _e('Deselect all') ?>');
				}else{
					jQuery(this).next().html('<?php _e('Select all') ?>');
				}
			});	
			// date
			mgm_date_picker('.date_input');
		});
	</script>
	<?php
}

// mgm_post_setup_save
function mgm_post_setup_save($post_id){
	// update
	if(isset($_POST['mgm_post'])){
		// check revision
		if ( $the_post = wp_is_post_revision($post_id) )
			$post_id = $the_post;
			
		// get object
		$mgm_post_object = mgm_get_post($post_id);
		
		// check object
		if(is_object($mgm_post_object)){			
			// access_membership_types
			if(!isset($_POST['mgm_post']['access_membership_types'])){
				$_POST['mgm_post']['access_membership_types'] = array();
			}
			
			// access_delay
			if(!isset($_POST['mgm_post']['access_delay'])){
				$_POST['mgm_post']['access_delay'] = array();
			}
			
			// set new fields
			$mgm_post_object->set_fields($_POST['mgm_post']);
			
			// save meta
			// update_post_meta($post_id, '_mgm_post', $mgm_post_object);
			$mgm_post_object->save();
		}
	}	
	// return
	return true;
}

// mgm_category_form
function mgm_category_form($category){			
	// member types
	$access_membership_types = mgm_get_class('post_category')->get_access_membership_types();
	// init
	$membership_types = array();
	// check
	if(isset($category->term_id) && $category->term_id>0){
		// check
		if(isset($access_membership_types[$category->term_id])){
			$membership_types = $access_membership_types[$category->term_id];
		}
	}
	$mgm_category_access = mgm_make_checkbox_group('mgm_category_access[]', mgm_get_class('membership_types')->membership_types, $membership_types, MGM_KEY_VALUE);		
	?>
	<script language="javascript">
		<!--
		jQuery(document).ready(function(){	
			<?php if(isset($category->term_id) && intval($category->term_id) > 0):?> 							
			var html='<tr class="form-field form-required">' +
					 ' 	<th scope="row" valign="top"><label for="cat_name"><?php _e('Category Access','mgm')?></label></th>' +
					 '	<td><div>'+"<?php echo $mgm_category_access; ?>"+'</div>'+
					 '  <p><?php _e('(Leave all unchecked for public access to this category.)','mgm') ?></p></td>' +
					 '</tr>';
			jQuery("#edittag .form-table").append(html);
			<?php else:?>			
			var html ='<div class="form-field">'+
							'<label for="mgm_category_access"><?php _e('Category Access','mgm')?></label>'+
							"<?php echo $mgm_category_access; ?>"+
							'<p><?php _e('(Leave all unchecked for public access to this category.)','mgm') ?>.</p>'+
					   '</div>';								
			jQuery("#addtag p.submit").before(html);
			<?php endif;?>
		});
		//-->
	</script>
	<?php		
}

// category save
function mgm_category_save($category_id){	
	$mgm_post_category = mgm_get_class('post_category');	
	$mgm_post_category->access_membership_types[$category_id] = $_POST['mgm_category_access'];
	// update_option('mgm_post_category',$mgm_post_category);
	$mgm_post_category->save();
}

// category delete 
function mgm_category_delete($category_id){
	$mgm_post_category = mgm_get_class('post_category');	
	$mgm_post_category->access_membership_types[$category_id] = $_POST['mgm_category_access'];
	// update_option('mgm_post_category',$mgm_post_category);
	$mgm_post_category->save();
}
// end file