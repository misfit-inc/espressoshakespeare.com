<?php
/*
Copyright 2008 daiv Mowbray

This file is part of SuperSlider-Login

SuperSlider-Login is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

login is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Fancy Categories; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
	/**
   * Should you be doing this?
   */ 	
$login_domain = 'superslider-login';

	if ( !current_user_can('manage_options') ) {
		// Apparently not.
		die( __( 'ACCESS DENIED: Your don\'t have permission to do this.', $login_domain) );
		}
		if (isset($_POST['set_defaults']))  {
			check_admin_referer('login_options');
			$login_OldOptions = array(
				"load_moo" => "on",
				"css_load" => "default",
				"css_theme" => "default", 
				"opacity" => "0.7",
				"resize_dur" => "800",
				"mode"        => "horizontal",
				"trans_type"	=> "Sine",
				"trans_typeout" => "easeOut",
				"loginlink" => ".loginlink",
				"header_text"  =>  "Welcome",
				"message_text"  =>  "Remember the Prime Directive of Netiquette: Those are real people out there.",
				"guest_header_text"  =>  "Join",
				"guest_message_text"  =>  "Join us as we spread the word.",
				'delete_options' => ''
				);

			update_option('ssLogin_options', $login_OldOptions);
				
			echo '<div id="message" class="updated fade"><p><strong>' . __( 'login Default Options reloaded.', $login_domain) . '</strong></p></div>';
			
		}
		elseif ($_POST['action'] == 'update' ) {
			
			check_admin_referer('login_options'); // check the nonce
					// If we've updated settings, show a message
			echo '<div id="message" class="updated fade"><p><strong>' . __( 'login Options saved.', $login_domain) . '</strong></p></div>';
			
			$Login_newOptions = array(
				'load_moo'		=> $_POST['op_load_moo'],
				'css_load'		=> $_POST['op_css_load'],
				'css_theme'		=> $_POST["op_css_theme"],
				'opacity'		=> $_POST["op_overlayOpacity"],
				'resize_dur'	=> $_POST["op_resize_duration"],
				'mode'		    => $_POST["op_mode"],
				'trans_type'	=> $_POST["op_trans_type"],
				'trans_typeout'	=> $_POST["op_trans_typeout"],
				'loginlink'	=> $_POST["op_loginlink"],
				'header_text'	=> $_POST["op_header_text"],
				'message_text'	=> $_POST["op_message_text"],
				'guest_header_text'	=> $_POST["op_guest_header_text"],
				'guest_message_text'	=> $_POST["op_guest_message_text"],
				'delete_options'	=> $_POST["op_delete_options"]
			);	

		update_option('ssLogin_options', $Login_newOptions);

		}	

		$Login_newOptions = get_option('ssLogin_options');   

	/**
	*	Let's get some variables for multiple instances
	*/
    $checked = ' checked="checked"';
    $selected = ' selected="selected"';
	$site = get_option('siteurl'); 
?>

<div class="wrap">
<form name="login_options" method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
<?php if ( function_exists('wp_nonce_field') )
		wp_nonce_field('login_options'); echo "\n"; ?>
		
<div style="">
<a href="http://wp-superslider.com/">
<img src="<?php echo $site ?>/wp-content/plugins/superslider-login/admin/img/logo_superslider.png" style="margin-bottom: -15px;padding: 20px 20px 0px 20px;" alt="SuperSlider Logo" width="52" height="52" border="0" /></a>
  <h2 style="display:inline; position: relative;">SuperSlider-Login Options</h2>
 </div><br style="clear:both;" />
 <script type="text/javascript">
// <![CDATA[

function create_ui_tabs() {


    jQuery(function() {
        var selector = '#ssslider';
            if ( typeof jQuery.prototype.selector === 'undefined' ) {
            // We have jQuery 1.2.x, tabs work better on UL
            selector += ' > ul';
        }
        jQuery( selector ).tabs({ fxFade: true, fxSpeed: 'slow' });

    });
}

jQuery(document).ready(function(){
        create_ui_tabs();
});

// ]]>
</script>
 

<div id="ssslider" class="ui-tabs">
    <ul id="ssnav" class="ui-tabs-nav">
        <li <?php if ($this->base_over_ride != "on") { 
  		 echo '';
  		} else {
  		echo 'style="display:none;"';
  		}?>	class="ui-state-default" ><a href="#fragment-1"><span>Appearance</span></a></li>
        <li class="ui-tabs-selected"><a href="#fragment-2"><span>Transition Options</span></a></li>
        <li class="ui-state-default"><a href="#fragment-3"><span>Messages</span></a></li>
        <li <?php if ($this->base_over_ride != "on") { 
  		 echo '';
  		} else {
  		echo 'style="display:none;"';
  		}?>	class="ss-state-default" ><a href="#fragment-4"><span>File storage</span></a></li>
    </ul>
    <div id="fragment-1" class="ss-tabs-panel">
 	<div <?php if ($this->base_over_ride != "on") { 
  		 echo '';
  		} else {
  		echo 'style="display:none;"';
  		}?>	
	>
	<h3>Login Appearance</h3>
	
		<fieldset style="border:1px solid grey;margin:10px;padding:10px 10px 10px 30px;"><!-- Theme options start -->  	
		<legend><b><?php _e(' Themes',$login_domain); ?>:</b></legend>
	<table width="100%" cellpadding="10" align="center">
	<tr>
		<td width="25%" align="center" valign="top"><img src="<?php echo $site ?>/wp-content/plugins/superslider-login/admin/img/default.png" alt="default" border="0" width="110" height="25" /></td>
		<td width="25%" align="center" valign="top"><img src="<?php echo $site ?>/wp-content/plugins/superslider-login/admin/img/blue.png" alt="blue" border="0" width="110" height="25" /></td>
		<td width="25%" align="center" valign="top"><img src="<?php echo $site ?>/wp-content/plugins/superslider-login/admin/img/black.png" alt="black" border="0" width="110" height="25" /></td>
		<td width="25%" align="center" valign="top"><img src="<?php echo $site ?>/wp-content/plugins/superslider-login/admin/img/custom.png" alt="custom" border="0" width="110" height="25" /></td>
	</tr>
	<tr>
		<td><label for="op_css_theme1">
			 <input type="radio"  name="op_css_theme" id="op_css_theme1"
			 <?php if($Login_newOptions['css_theme'] == "default") echo $checked; ?> value="default" />
			</label>
		</td>
		<td> <label for="op_css_theme2">
			 <input type="radio"  name="op_css_theme" id="op_css_theme2"
			 <?php if($Login_newOptions['css_theme'] == "blue") echo $checked; ?> value="blue" />
			 </label>
  		</td>
		<td><label for="op_css_theme3">
			 <input type="radio"  name="op_css_theme" id="op_css_theme3"
			 <?php if($Login_newOptions['css_theme'] == "black") echo $checked; ?> value="black" />
			 </label>
  		</td>
		<td> <label for="op_css_theme4">
			 <input type="radio"  name="op_css_theme" id="op_css_theme4"
			 <?php if($Login_newOptions['css_theme'] == "custom") echo $checked; ?> value="custom" />
			</label>
     </td>
	</tr>
	</table>

  </fieldset>
  </div>
</div><!--  close frag 1-->
 
 

	<div id="fragment-2" class="ss-tabs-panel">
	<h3 class="title">Login Options</h3>
	
		<fieldset style="border:1px solid grey;margin:10px;padding:10px 10px 10px 30px;"><!-- options start -->
   <legend><b><?php _e(' Personalize Transitions',$login_domain); ?>:</b></legend>

   <ul style="list-style-type: none;">
     
     <li style="border-bottom:1px solid #cdcdcd; padding: 6px 0px 8px 0px;">
     <label for="op_trans_type"><?php _e(' Transition type',$login_domain); ?>:   </label>  
		 <select name="op_trans_type" id="op_trans_type">
			 <option <?php if($Login_newOptions['trans_type'] == "Sine") echo $selected; ?> id="Sine" value='Sine'> Sine</option>
			 <option <?php if($Login_newOptions['trans_type'] == "Elastic") echo $selected; ?> id="Elastic" value='Elastic'> Elastic</option>
			 <option <?php if($Login_newOptions['trans_type'] == "Bounce") echo $selected; ?> id="Bounce" value='Bounce'> Bounce</option>
			 <option <?php if($Login_newOptions['trans_type'] == "Back") echo $selected; ?> id="Back" value='Back'> Back</option>
			 <option <?php if($Login_newOptions['trans_type'] == "Expo") echo $selected; ?> id="Expo" value='Expo'> Expo</option>
			 <option <?php if($Login_newOptions['trans_type'] == "Circ") echo $selected; ?> id="Circ" value='Circ'> Circ</option>
			 <option <?php if($Login_newOptions['trans_type'] == "Quad") echo $selected; ?> id="Quad" value='Quad'> Quad</option>
			 <option <?php if($Login_newOptions['trans_type'] == "Cubic") echo $selected; ?> id="Cubic" value='Cubic'> Cubic</option>
			 <option <?php if($Login_newOptions['trans_type'] == "Linear") echo $selected; ?> id="Linear" value='Linear'> Linear</option>
			 <option <?php if($Login_newOptions['trans_type'] == "Quart") echo $selected; ?> id="Quart" value='Quart'> Quart</option>
			 <option <?php if($Login_newOptions['trans_type'] == "Quint") echo $selected; ?> id="Quint" value='Quint'> Quint</option>
			</select>
		<label for="op_trans_typeout"><?php _e(' Transition action.',$login_domain); ?></label>
		<select name="op_trans_typeout" id="op_trans_typeout">
			 <option <?php if($Login_newOptions['trans_typeout'] == "easeIn") echo $selected; ?> id="easeIn" value='easeIn'> ease in</option>
			 <option <?php if($Login_newOptions['trans_typeout'] == "easeOut") echo $selected; ?> id="easeOut" value='easeOut'> ease out</option>
			 <option <?php if($Login_newOptions['trans_typeout'] == "easeInOut") echo $selected; ?> id="easeInOut" value='easeInOut'> ease in out</option>     
		</select><br />
		<span class="setting-description"><?php _e(' IN is the begginning of transition. OUT is the end of transition.',$login_domain); ?></span>
     </li>   
      <li style="border-bottom:1px solid #cdcdcd; padding: 6px 0px 8px 0px;">
      
      <label for="op_mode"><?php _e(' Transition Direction.',$login_domain); ?></label>
		<select name="op_mode" id="op_mode">
			 <option <?php if($Login_newOptions['mode'] == "vertical") echo $selected; ?> id="vertical" value='vertical'> vertical</option>
			 <option <?php if($Login_newOptions['mode'] == "horizontal") echo $selected; ?> id="horizontal" value='horizontal'> horizontal</option>
			    
		</select><br />
		<span class="setting-description"><?php _e(' Panel enters from above (vertical) or from the side (horizontal)?',$login_domain); ?></span>
      </li>
	 <li style="border-bottom:1px solid #cdcdcd; padding: 6px 0px 8px 0px;">
     <label for="op_overlayOpacity"><?php _e(' Overlay opacity '); ?>:
		 <input type="text" class="span-text" name="op_overlayOpacity" id="op_overlayOpacity" size="3" maxlength="3"
		 value="<?php echo ($Login_newOptions['opacity']); ?>" /></label> 
		 <span class="setting-description"><?php _e('   (default 0.7)',$login_domain); ?></span>
	 </li>
      <li style="border-bottom:1px solid #cdcdcd; padding: 6px 0px 8px 0px;">
		 <label for="op_resize_duration"><?php _e(' Transition time '); ?>:
		 <input type="text" class="span-text" name="op_resize_duration" id="op_resize_duration" size="3" maxlength="6"
		 value="<?php echo ($Login_newOptions['resize_dur']); ?>" /></label> 
		 <span class="setting-description"><?php _e('  In milliseconds, ie: 1000 = 1 second, (default 500)',$login_domain); ?></span>
	</li>
	<li>
		 <label for="op_loginlink"><?php _e('Login link class'); ?>:
		 <input type="text" class="span-text" name="op_loginlink" id="op_loginlink" size="30" maxlength="300"
		 value="<?php echo ($Login_newOptions['loginlink']); ?>" /></label> 
		 <br /><span class="setting-description"><?php _e(' Add a class for the links which you want to activate the login slider. Search for comment_registration in your themes comments.php file and add class="loginlink" to the login link.',$login_domain); ?></span>
		 
	</li>
      	 

     </ul>
  </fieldset>
  </div><!--  close frag 2-->
		
	<div id="fragment-3" class="ss-tabs-panel">
	<h3 class="title">Messages</h3>
		  
		  <fieldset style="border:1px solid grey;margin:10px;padding:10px 10px 10px 30px;"><!-- options start -->
   <legend><b><?php _e(' Personalize Messages',$login_domain); ?>:</b></legend>
  <ul style="list-style-type: none;">       	 
	  <li>
		 <label for="op_header_text"><?php _e(' Logged in Headline'); ?>:
		 <input type="text" class="span-text" name="op_header_text" id="op_header_text" size="30" maxlength="300"
		 value="<?php echo ($Login_newOptions['header_text']); ?>" /></label> 
		 <span class="setting-description"><?php _e('  ',$login_domain); ?></span>
	</li>
	
	<li style="border-bottom:1px solid #cdcdcd; padding: 6px 0px 8px 0px;">
		 <label for="op_message_text"><?php _e('Message'); ?></label>
		 <textarea name="op_message_text" id="op_message_text" rows="4" cols="40"><?php echo ($Login_newOptions['message_text']); ?></textarea>
		 
		 
	</li>
     <li>
		 <label for="op_guest_header_text"><?php _e(' Guest Headline'); ?>:
		 <input type="text" class="span-text" name="op_guest_header_text" id="op_guest_header_text" size="30" maxlength="300"
		 value="<?php echo ($Login_newOptions['guest_header_text']); ?>" /></label> 
		 <span class="setting-description"><?php _e('  ',$login_domain); ?></span>
	</li>
	
	<li style="border-bottom:1px solid #cdcdcd; padding: 6px 0px 8px 0px;">
		 <label for="op_guest_message_text"><?php _e('Guest Message'); ?></label>
		 <textarea name="op_guest_message_text" id="op_guest_message_text" rows="4" cols="40"><?php echo ($Login_newOptions['guest_message_text']); ?></textarea>
		 
		 
	</li>
     </ul>
   </fieldset>
</div><!-- close frag3 -->

<div id="fragment-4" class="ss-tabs-panel">
	
	<div
<?php if ($this->base_over_ride != "on") { 
  		 echo '';
  		} else {
  		echo 'style="display:none;"';
  		}?> 
	>
	<h3 class="title">File Storage</h3>
<fieldset style="border:1px solid grey;margin:10px;padding:10px 10px 10px 30px;"><!-- Header files options start -->
   			<legend><b><?php _e(' Loading Options'); ?>:</b></legend>
  		 <ul style="list-style-type: none;">  		 
  		<li style="border-bottom:1px solid #cdcdcd; padding: 6px 0px 8px 0px;">
    	<label for="op_load_moo">
    	<input type="checkbox" 
    	<?php if($Login_newOptions['load_moo'] == "on") echo $checked; ?> name="op_load_moo" id="op_load_moo" />
    	<?php _e(' Load Mootools 1.2 into your theme header.',$login_domain); ?></label>
    	
	</li>
	
    <li style="border-bottom:1px solid #cdcdcd; padding: 6px 0px 8px 0px;">
  
    	<label for="op_css_load1">
			<input type="radio" name="op_css_load" id="op_css_load1"
			<?php if($Login_newOptions['css_load'] == "default") echo $checked; ?> value="default" />
			<?php _e(' Load css from default location. login plugin folder.',$login_domain); ?></label><br />
    	<label for="op_css_load2">
			<input type="radio" name="op_css_load"  id="op_css_load2"
			<?php if($Login_newOptions['css_load'] == "pluginData") echo $checked; ?> value="pluginData" />
			<?php _e(' Load css from plugin-data folder, see side note. (Recommended)',$login_domain); ?></label><br />
    	<label for="op_css_load3">
			<input type="radio" name="op_css_load"  id="op_css_load3"
			<?php if($Login_newOptions['css_load'] == "off") echo $checked; ?> value="off" />
			<?php _e(' Don\'t load css, manually add to your theme css file.',$login_domain); ?></label>

    </li>
    </ul>
     </fieldset>
    
		<p>
		<?php _e(' If your theme or any other plugin loads the mootools 1.2 javascript framework into your file header, you can de-activate it here.',$login_domain); ?></p><p><?php _e(' Via ftp, move the folder named plugin-data from this plugin folder into your wp-content folder. This is recomended to avoid over writing any changes you make to the css files when you update this plugin.',$login_domain); ?></p></td>
	</div><!-- close frag 8 -->
</div><!--  close tabs -->
<p>
<label for="op_delete_options">
		      <input type="checkbox" <?php if($Login_newOptions['delete_options'] == "on") echo $checked; ?> name="op_delete_options" id="op_delete_options" />
		      <?php _e('Remove options. '); ?></label>	
		 <br /><span class="setting-description"><?php _e('Select to have the plugin options removed from the data base upon deactivation.'); ?></span>
		 <br />
</p>
<p class="submit">
		<input type="submit" name="set_defaults" value="<?php _e(' Reload Default Options',$login_domain); ?> &raquo;" />
		<input type="submit" id="update2" class="button-primary" value="<?php _e(' Update options',$login_domain); ?> &raquo;" />
		<input type="hidden" name="action" id="action" value="update" />
 	</p>
 </form>
</div>
<?php
	echo "";
?>