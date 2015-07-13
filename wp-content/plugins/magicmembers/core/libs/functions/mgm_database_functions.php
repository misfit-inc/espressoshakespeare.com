<?php
// enum to array
function mgm_enum_field_values($table,$column,$excludes=''){
	global $wpdb; 		
	// exclude
	$to_exclude   = explode(',', $excludes);
	$to_exclude[] = 'enum';
	$to_exclude[] = 'set';
	// init
	$fields = array();  
	$enum   = $wpdb->get_row("SHOW COLUMNS FROM `{$table}` LIKE '{$column}'");  
	$tok    = strtok($enum->Type, ")(',");
	while($tok !== false) {      
		// set
		if(!in_array($tok,$to_exclude)){
			$fields[] = $tok;
		}
		// tokonize 
		$tok = strtok(")(',");
	}
	// return
	return $fields;
}
// array of fields 
function mgm_field_values($table, $key, $value, $where='', $orderby='', $join=''){
	global $wpdb;	
	// order by
	$orderby = (!empty($orderby)) ? $orderby : "`{$value}` ASC"; 
	// sql
	$sql     = "SELECT {$key},{$value} FROM `{$table}` {$join} WHERE 1 {$where} ORDER BY {$orderby}";	
	$rows    = $wpdb->get_results($sql);
	// split alias
	$key     = mgm_alias_split($key);
	$value   = mgm_alias_split($value);
	// store
	$_array  = array();
	// captured
	if($rows){		
		// loop
		foreach($rows as $row){
			// set
			$_array[$row->$key] = $row->$value;
		}
	} 
	// return
	return $_array;
}
// check duplicate
function mgm_is_duplicate($table,$fields,$extra_clause='',$source=''){			
	global $wpdb; 	
	/*
	 params 
	 @1 tablename  : string 
	 @2 fields     : array
	 @3 sql clause : string
	 @4 source     : array
	*/
	// reset source
	$source = is_array($source) ? $source : $_REQUEST;
	// init 
	$fld_clauses = array();
	// loop
	foreach($fields as $fld){  
		// set    
		$fld_clauses[] = " `{$fld}` = '{$source[$fld]}' ";
	}		
	// join		
	$fld_clause = implode(' AND ', $fld_clauses);	
	// extra
	if(!empty($extra_clause)){
		// check
		if(!preg_match('/^AND/i',$extra_clause)){
			// extra_clause
			$extra_clause = 'AND ' . $extra_clause;
		}
	}
	// get var
	$count = $wpdb->get_var("SELECT COUNT(*) AS _CNT FROM `{$table}` WHERE {$fld_clause} {$extra_clause} ");	
	// log
	// mgm_log('last_query: '.$wpdb->last_query);
	// return 
	return ($count>0) ? true : false;	
} 
// single quote wrap helper
function mgm_single_quote($field_data){
	return "'{$field_data}'";
}
// map to quotes
function mgm_map_for_in($map){
	$mapped=array_map("mgm_single_quote",$map);
	return implode(",",$mapped); 
}
# field alias split helper
function mgm_alias_split($alias){
	// CONCAT(first_name,last_name) AS name => returns name
	if(preg_match("/(\s+)AS(\s+)/",$alias)){
		list($discard,$alias)=preg_split("/(\s+)AS(\s+)/",$alias);
	}
	
	// A.id => return id
	if(preg_match("/[a-zA-Z]\.(.*)/",$alias)){		
		list($discard,$alias)=explode('.',$alias);		
	}
	
	// return
	return $alias;
}
// end of file