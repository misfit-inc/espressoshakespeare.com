<?php
// first time install
if(!get_option('mgk_version')){
	// install
	require_once('install/mgk_first_run.php');				
}else{
	// fix for next
	// update_option('mgk_upgrade_id', 0.0);
	// upgrade
	// get last upgrade version 
	$mgk_upgrade_id = get_option('mgk_upgrade_id');	
	// get list of upgrades
	$upgrades = glob(dirname(__FILE__).DIRECTORY_SEPARATOR.'upgrades'.DIRECTORY_SEPARATOR.'upgrade_id_*', GLOB_BRACE);
	// we have some in the list
	if(count($upgrades)>0){
		// loop
		foreach($upgrades as $upgrade){		
			// get id form folder
			$upgrade_id = str_replace('upgrade_id_','',pathinfo($upgrade,PATHINFO_BASENAME));
			// when new folder, not executed before
			if($mgk_upgrade_id < $upgrade_id){	
				// run upgrade
				foreach(array('mgk_schema','mgk_options') as $upgrade_file){
					$upgrade_file = $upgrade . DIRECTORY_SEPARATOR . $upgrade_file . '.php';
					// file exists
					if(file_exists($upgrade_file)){
						// include upgrade
						include_once($upgrade_file);
						// upgrade id, set to 1
						update_option('mgk_upgrade_id', $upgrade_id);
					}
				}
			}
		}
	}		
}

// end of file