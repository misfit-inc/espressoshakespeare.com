<?php
# select drop down generator
function mgk_make_combo_options($ComboOpts,$ComboSel="",$Type=1){
	  /**
	   * Combo Generator 
	   * @param  Options Array  List
	   * @param  Scalar/Array Default/Selected Value/Values
	   * @param  String Type of Select Array [VALUE_ONLY : 1 / KEY_VALUE : 2]   
	   */  
	
	$combo="";   	    
    switch(intval($Type)){
	  # VALUE_ONLY
	  case 1:	   
	   // array only has values
	   foreach($ComboOpts as $OptVal){	   
	     $combo.="<option value='$OptVal' ";
		 if(isset($ComboSel)){
			 if(is_array($ComboSel) && in_array($OptVal,$ComboSel)){
			   # array
			   $combo.=" Selected ";		   
			 }else if(trim($OptVal)==trim($ComboSel)){
			   # scalar
			   $combo.=" Selected ";		  
			 }else{
			   # default		   
			 }
		 } 
	     $combo.=">$OptVal</option>";
	   }
	   break;
	  # KEY_VALUE
	  case 2:
  	   //array  has key and values
	   foreach($ComboOpts as $OptKey=>$OptVal){
	    $combo.="<option value='$OptKey' ";		
		if(isset($ComboSel)){
			if(is_array($ComboSel) && in_array($OptKey,$ComboSel)){
			   $combo.=" Selected ";		   
			}else if(trim($OptKey)==trim($ComboSel)){
			   $combo.=" Selected ";		  
			}
		}	
	    $combo.=">$OptVal</option>";
	   }
	   break; 
	} 
	
	return $combo;   
}
# radio generator 
function mgk_make_radio_group($RadioGName,$RadioGOpts,$RadioGSel,$Type=1,$Event=""){    
    $radio=""; 	
    switch(intval($Type)){
	  case 1:	   
	   // array only has values only
	   foreach($RadioGOpts as $OptVal){
	     $radio.="<input type='radio' class='radio' name='{$RadioGName}' value='{$OptVal}'";
		 if($RadioGSel==$OptVal){
		   $radio.=" Checked ";		  
		 }
		 $radio.=" valign='absmiddle' {$Event}>&nbsp;{$OptVal}&nbsp;";
	   }
	   break; 
	  case 2:
	   //array  has key and values
	   foreach($RadioGOpts as $OptKey=>$OptVal){
	     $radio.="<input type='radio' class='radio' name='{$RadioGName}' value='{$OptKey}'";
		 if($RadioGSel==$OptKey){
		   $radio.=" Checked ";
		 }
		 $radio.=" valign='absmiddle' {$Event}>&nbsp;{$OptVal}&nbsp;";
	   }
	   break;	  
	} 	
	return $radio;
  }
  
# Checkbox generator 
# args : @name,@options array,@selected value/array,@type-key/value  
function mgk_make_checkbox_group($CheckGName,$CheckGOpts,$CheckGSel,$Type=1,$attr="",$style="ul"){    
    $checkbox="";   	
	
    switch(intval($Type)){
	  case 1:	   
	   // array only has values
	   foreach($CheckGOpts as $OptVal){
	     $checkbox.="%style_begin%<input type='checkbox' class='radio' name='{$CheckGName}' value='{$OptVal}' {$attr}";
		 if(is_array($CheckGSel) && in_array($OptVal,$CheckGSel)){
		   $checkbox.=" Checked ";		   
		 }else if(trim($CheckGSel)==trim($OptVal)){
		   $checkbox.=" Checked ";
	     }		 
		 $checkbox.=" valign='absmiddle'>&nbsp;$OptVal&nbsp;%style_end%";
	   }
	   break; 
	  case 2:
	   //array  has key and values
	   foreach($CheckGOpts as $OptKey=>$OptVal){
	     $checkbox.="%style_begin%<input type='checkbox' class='radio' name='{$CheckGName}' value='{$OptKey}' {$attr}";
		 if(is_array($CheckGSel) && in_array($OptKey,$CheckGSel)){
		   $checkbox.=" Checked ";		   
		 }else if(trim($CheckGSel)==trim($OptKey)){
		   $checkbox.=" Checked ";
	     }			 
		 $checkbox.=" valign='absmiddle'>&nbsp;$OptVal&nbsp;%style_end%";
	   }
	   break;	  
	}
	
	switch($style){
		case "ul":
		case "ol":
			$checkbox=str_replace(array("%style_begin%","%style_end%"),array("<li><div>","</div></li>"),$checkbox);
			$checkbox="<{$style} class='list-table'>{$checkbox}</{$style}>";
		break;
		case "table":
			$checkbox=str_replace(array("%style_begin%","%style_end%"),array("<td>","</td>"),$checkbox);
			$checkbox="<{$style}><tr>{$checkbox}</tr></{$style}>";
		break;
	}
	return $checkbox;
}
// select list of time 
function mgk_time_select($name="date",$properties=""){  
  $default=array("hour"=>date('g'),"minute"=>date('i'),"second"=>date('s'),
                 "meridian"=>date('A'),"readonly"=>false);  
  $properties =is_array($properties)? $properties : $default;
  # make vars
  extract($properties);
  
  $readonly =($readonly)?"disabled class='readonly'":"";

  echo "<select name='hour[$name]' {$readonly}>";
  echo mgk_make_combo_options(array_merge(array("00"),range(1,12)),$hour,1);
  echo "</select>";
  
  echo "<select name='minute[$name]' {$readonly}>";
  echo mgk_make_combo_options(array_merge(array("00"),range(1,60)),$minute,1);
  echo "</select>";
  
  /*
  echo "<select name='second' {$readonly_class}>";
  echo mgk_make_combo_options(range(1,60),$second,VALUE_ONLY);
  echo "</select>"; 
  */
  
  echo "<select name='meridian[$name]' {$readonly}>";
  echo mgk_make_combo_options(array('AM','PM'),$meridian,1);
  echo "</select>"; 
}
# check box selected
function mgk_check_if_match($self_value,$set_value,$default_value=""){
	if(is_string($set_value) && empty($set_value) && ($self_value==$default_value)){
	  	echo "checked";
	}else if(is_array($set_value) && in_array($self_value, $set_value)){
		echo "checked";		
	}else if($self_value==$set_value){
	  	echo "checked";
	}
}
# selct selected
function mgk_select_if_match($self_value,$set_value,$default_value=""){
	if(is_string($set_value) && empty($set_value) && ($self_value==$default_value)){
		echo "selected";
	}else if(is_array($set_value) && in_array($self_value, $set_value)){
		echo "checked";	  
	}else if($self_value==$set_value){
		echo "selected";
	}
}
?>