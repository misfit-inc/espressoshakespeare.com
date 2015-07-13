// pack is valid
	if($pack !== false){				
		// check if extend allowed on the current pack of subscriber/user
		if(!mgm_pack_extend_allowed($pack)){
			return __('Renewal of the subscription is not allowed.','mgm'); exit;
		}
		// coupon
		// get coupon field ( and other active on extend page)
		$extend_html = mgm_get_partial_fields(array('on_extend'=>true),'mgm_extend_field');
		
		// found some
		if($extend_html){			
			// will have two step process
			// step 1: show packs that are on extend
			if (!isset($_POST['submit'])) {
				// init
				$html = '';
				//check erros if any:
				$error_field = mgm_request_var('error_field'); 
				// check
				if(!empty($error_field)) {
					// obj
					$errors = new WP_Error();
					// type
					switch (mgm_request_var('error_type')) {
						case 'empty':
							$error_string = 'You must provide a '.$error_field;
							break;
						case 'invalid':
							$error_string = 'Invalid '.$error_field;
							break;	
					}	
					// set			
					$errors->add( $error_field, __( '<strong>ERROR</strong>: '.$error_string, 'mgm' ));	
					$html .= mgm_set_errors($errors, true);					
				}	
				// query_arg
				$form_action = mgm_get_custom_url('transactions', false, array('action'=>'extend', 'pack_id'=>$_GET['pack_id'],'username'=> $username));			
				// active modules				
				$html .= '<p class="message register">'. __('Fill in the fields','mgm') .'</p>';
				$html .= '<form action="'.$form_action .'" method="post" class="mgm-form"><div style="clear: both; overflow: hidden; padding-bottom: 5px;">';
				$html .= '<input type="hidden" name="form_action" value="'. $form_action .'" />';
				$html .= $extend_html;
				$html .= '<a href="'.admin_url('profile.php?page=mgm/profile').'" class="button-primary">'.__('Cancel','mgm').'</a><p><input type="submit" name="submit" value="Next &raquo;" class="button-primary" /></p>';
				$html .= '</div></form>';
			}else{	
				// save
				$mgm_member = mgm_save_partial_fields(array('on_extend'=>true),'mgm_extend_field', $pack['cost']);				
				// is using a coupon ? reset prices
				if(isset($mgm_member->extend->coupon->id)){		
					// main				
					if($pack && $mgm_member->extend->coupon->cost){
						// original
						$pack['original_cost'] = $pack['cost'];
						// payable
						$pack['cost'] = $mgm_member->extend->coupon->cost;
					}
					// set pack on coupon	
					if($pack && $mgm_member->extend->coupon->duration)
						$pack['duration'] = $mgm_member->extend->coupon->duration;
					if($pack && $mgm_member->extend->coupon->duration_type)
						$pack['duration_type'] = $mgm_member->extend->coupon->duration_type;
					if($pack && $mgm_member->extend->coupon->membership_type)
						$pack['membership_type'] = $mgm_member->extend->coupon->membership_type;
					//issue#: 478/ add billing cycles.	
					if($pack && isset($mgm_member->extend->coupon->num_cycles))
						$pack['num_cycles'] = $mgm_member->extend->coupon->num_cycles;							
						
					// trial	
					if($pack && $mgm_member->extend->coupon->trial_on)
						$pack['trial_on'] = $mgm_member->extend->coupon->trial_on;
					if($pack && $mgm_member->extend->coupon->trial_cost)
						$pack['trial_cost'] = $mgm_member->extend->coupon->trial_cost;
					if($pack && $mgm_member->extend->coupon->trial_duration_type)
						$pack['trial_duration_type'] = $mgm_member->extend->coupon->trial_duration_type;
					if($pack && $mgm_member->extend->coupon->trial_duration)
						$pack['trial_duration'] = $mgm_member->extend->coupon->trial_duration;	
					if($pack && $mgm_member->extend->coupon->trial_num_cycles)
						$pack['trial_num_cycles'] = $mgm_member->extend->coupon->trial_num_cycles;		
						
					// mark pack as coupon applied
					$pack['coupon_id'] = $mgm_member->coupon->id;				
				}
				// get active modules	
				$a_payment_modules = $system->get_active_modules('payment');	
				
				// to handle free/cost=0 packages 		
				
				// init 
				$payment_modules = array();			
				// when active
				if($a_payment_modules){
					// loop
					foreach($a_payment_modules as $payment_module){
						// not trial
						if(in_array($payment_module, array('mgm_free','mgm_trial'))) continue;	
						//consider only the modules assigned to pack:issue# 430
						if(isset($pack['modules']) && !in_array($payment_module, (array)$pack['modules'])) continue;			
						// store
						$payment_modules[] = $payment_module;					
					}
				}
				// some modules active
				if (count($payment_modules)) {
					$html .= '<div style="height:30px; font-weight:bold" align="center">' . $packs_obj->get_pack_desc($pack) . '</div>';
					// coupon
					if(isset($mgm_member->extend->coupon->id)){	
						$html .= '<div style="height:30px; font-weight:bold" align="center">' . sprintf(__('Using Coupon "%s" - %s','mgm'),$mgm_member->extend->coupon->name, $mgm_member->extend->coupon->description) . '</div>';
					}
					$html .= '<div style="height:30px; font-weight:bold" align="center">' . __('Select Payment Gateway','mgm') . '</div>';
					// transaction 
					$tran_id = 0;
					// loop
					foreach ($payment_modules as $module) {	
						// module
						$mod_obj = mgm_get_module($module,'payment');	
						// create transaction
						if($tran_id==0){
							$tran_id = $mod_obj->_create_transaction($pack, array('user_id' => $user->ID));
						}								
						$html .= '<div>'.$mod_obj->get_button_subscribe(array('pack'=>$pack, 'tran_id'=>$tran_id)).'</div>' ;
					}
				} else {
					$html .= '<div>'.__('There are no gateways available at this time.','mgm').'</div>';
				}	
			}	
		}else{
			$html = '';	
			// get active modules	
			$a_payment_modules = $system->get_active_modules('payment');
			// init 
			$payment_modules = array();			
			// when active
			if($a_payment_modules){
				// loop
				foreach($a_payment_modules as $payment_module){
					// not trial
					if(in_array($payment_module, array('mgm_free','mgm_trial'))) continue;	
					//consider only the modules assigned to pack: issue# 430
					if(isset($pack['modules']) && !in_array($payment_module, (array)$pack['modules'])) continue;			
					// store
					$payment_modules[] = $payment_module;					
				}
			}
			// some active
			if (count($payment_modules)) {
				// transaction 
				$tran_id = 0;
				// loop
				foreach ($payment_modules as $module) {
					// module
					$mod_obj = mgm_get_module($module,'payment');	
					// create transaction
					if($tran_id==0){
						$tran_id = $mod_obj->_create_transaction($pack, array('user_id' => $user->ID));
					}				
					$html .= '<div>'.$mod_obj->get_button_subscribe(array('pack'=>$pack, 'tran_id'=>$tran_id)).'</div>' ;
				}
			} else {
				$html .= '<div>'.__('There are no gateways available at this time.','mgm').'</div>';
			}	
		}			
	}else{
		return __('Error in Pack','mgm');
	}