<?php
// get option : wrapper for mgm objects stored in wp options table
// @loadtype  : base | saved | merged
function mgm_get_option_new($option_name, $loadtype='saved'){	
	// check class
	if(class_exists($option_name)){
		// return saved object, 	
		$saved_option_class_obj = get_option($option_name);
		// if saved
		if($saved_option_class_obj && $loadtype=='saved'){
			return $saved_option_class_obj;
		}	
	
		// return base		
		$base_option_class_obj = new $option_name;			
		// if base
		if($loadtype=='base' && $loadtype=='saved'){
			// save if only not saved
			if(get_option($option_name)==false){
				update_option($option_name, $base_option_class_obj);
			}
			// return
			return $base_option_class_obj;
		}				
		
		// if merged
		if($loadtype=='merged'){
			// merge
			$merged_option_class_obj = (object)array_merge((array)$base_option_class_obj, (array)$saved_option_class_obj);
			// save
			update_option($option_name, $merged_option_class_obj);
			// return
			return $merged_option_class_obj;
		}		
	}

	// return dummy
	return new stdClass;
}
// load base files before dependent 
function mgm_dependency($names){
	// get name
	$files = explode(',', $names);
	// load if set
	if(is_array($files)){
		foreach($files as $file){
			include_once($file.'.php');
		}
	}
} 
/* deprecated
function mgm_update_custom_field_partial() {
	$user_ID = mgm_get_user_id();

	$data_res = get_option('mgm_resources');
	$fld_obj = get_option('mgm_custom_fields');
	$user = get_user_option('mgm_user', $user_ID);
	$old_data = get_user_option('mgm_custom_fields', $user_ID);
	$entries = $fld_obj->entries;
	$order = $fld_obj->order;
	$return = false;

	if (strlen($order)) {
		if (strpos($order, ';') !== false) {
			$orders = explode(';', $order);
		} else {
			$orders = array($order);
		}

		$data = array();
		$skip = array(
		__('Terms and Conditions','mgm')
		, __('Subscription Introduction','mgm')
		, __('Subscription Options','mgm')
		);

		foreach ($orders as $order) {
			foreach ($entries as $entry) {
				if ($order == $entry['id']) {
					$old = $old_data[$entry['id']];

					if (in_array($entry['name'], $skip)) {
						continue;
					} else if ($entry['name'] == __('Birthdate','mgm') ) {
						if ((!empty($_POST['mgm_birthdate_month'])) && (!empty($_POST['mgm_birthdate_day'])) && (! empty($_POST['mgm_birthdate_year']))) {
							$data[$entry['id']] = $_POST['mgm_birthdate_month'] .'-'. $_POST['mgm_birthdate_day'] .'-'. $_POST['mgm_birthdate_year'];
						}
					} else if ($entry['name'] == __('Country','mgm') ) {
						$data[$entry['id']] = $_POST['mgm_country'];
					} else if (isset($_POST['mgm_field-'. $entry['id']])) {
						$data[$entry['id']] = $_POST['mgm_field-'. $entry['id']];
					}

					//sets the old data if you choose not to show this field.
					if (!isset($_POST['mgm_field-' . $entry['id']])) {
						$data[$entry['id']] = $old;
					}
				}
			}
		}

		update_user_option($user_ID, 'mgm_custom_fields', $data, true);
		$return = true;
	}

	return $return;
}
*/