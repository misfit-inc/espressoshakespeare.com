<?php
// first time install, option will not be present, 
// both version and upgdare id used to attend auth data bug that may arise due to level-1 architecture upgrade
if(!get_option('mgm_version') && !get_option('mgm_upgrade_id')){	
	// is version merge?
	if(get_option('mgm_license_key')){
		// install
		require_once('install/mgm_version_merge.php');		
	}
	// install
	require_once('install/mgm_first_run.php');
}else{   
	// upgrade
	// get last upgrade version 
	$mgm_upgrade_id = get_option('mgm_upgrade_id');	
	// get list of upgrades
	$upgrades = glob(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'upgrades' . DIRECTORY_SEPARATOR . 'upgrade_id_*', GLOB_ONLYDIR);
	// we have some in the list
	if(count($upgrades)>0){
		// loop
		foreach($upgrades as $upgrade){		
			// get id from folder
			$upgrade_id = str_replace('upgrade_id_', '', pathinfo($upgrade, PATHINFO_BASENAME));
			// when new folder, not executed before
			if($mgm_upgrade_id < $upgrade_id){	
				// init
				$upgraded = false;
				// run upgrade
				foreach(array('mgm_schema','mgm_options','mgm_object_merge','mgm_patch','mgm_batch_upgrade') as $upgrade_file){
					// file name
					$upgrade_file_path = $upgrade . DIRECTORY_SEPARATOR . $upgrade_file . '.php';
					// file exists
					if(file_exists($upgrade_file_path)){
						// include upgrade
						include_once($upgrade_file_path);	
						// upgraded
						$upgraded = true;				
					}					
				}
				// upgraded
				if($upgraded) update_option('mgm_upgrade_id', $upgrade_id);
			}
		}
	}		
}

// end of file