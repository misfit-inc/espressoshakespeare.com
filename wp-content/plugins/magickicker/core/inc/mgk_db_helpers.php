<?php
// enum to array
function mgk_enum_field_values($table,$column,$excludes=''){
   global $wpdb; 		
   $to_exclude   =explode(",",$excludes);
   $to_exclude[] ="enum";
   $to_exclude[] ="set";
  
   $fields =array();  
   $enum   =$wpdb->get_row("SHOW COLUMNS FROM `{$table}` LIKE '{$column}'");  
   $tok    =strtok($enum->Type, ")(',");
   while ($tok !== false) {      
	  if(!in_array($tok,$to_exclude)){
			$fields[] = $tok;
	  }
	  $tok = strtok(")(',");
   }
  
   return $fields;
}
// check duplicate
function mgk_is_duplicate($table,$fields,$extra_clause='',$source=''){			
	global $wpdb; 	
	/*
	 params 
	 @1 tablename  : string 
	 @2 fields     : array
	 @3 sql clause : string
	 @4 source     : array
	*/
	$source = is_array($source) ? $source : $_REQUEST;
	foreach($fields as $fld){      
		$fld_clause_arr[]=" `{$fld}`='{$source[$fld]}' ";
	}				
	$fld_clause=implode(" AND ",$fld_clause_arr);	
	$count=$wpdb->get_var("SELECT COUNT(*) AS CNT FROM {$table} WHERE {$fld_clause} {$extra_clause} ");	
	#echo "SELECT COUNT(*) AS CNT FROM {$table} WHERE {$fld_clause} {$extra_clause} ";	
	return ($count>0) ? true : false;	
} 
// single quote wrap helper
function mgk_single_quote($field_data){
	return "'{$field_data}'";
}
// map to quotes
function mgk_map_for_in($map){
	$mapped=array_map("mgk_single_quote",$map);
	return implode(",",$mapped); 
}
// end of file