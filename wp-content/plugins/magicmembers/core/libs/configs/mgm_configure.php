<?php
// config
global $mgm_config,$mgm_classes,$mgm_modules,$mgm_plugins,$mgm_widgets;
// default profile fields, mixed, track as mgm name => wp field
$mgm_config['default_register_fields'] = array('username'=>array('name'=>'user_login','label'=>__('Username','mgm'),'type'=>'text','attributes'=>array('required'=>true)),
											   'email'=>array('name'=>'user_email','label'=>__('E-mail','mgm'),'type'=>'text','attributes'=>array('required'=>true)));
// default profile fields, mixed, track as mgm name => wp name
$mgm_config['default_profile_fields'] = array('username'=>array('name'=>'user_login','id'=>'user_login','label'=>__('Username','mgm'),'type'=>'text',
                                             				   'attributes'=>array('required'=>true,'readonly'=>true)),  
                                              'password'=>array('name'=>'user_password','id'=>'user_password','label'=>__('Password','mgm'),'type'=>'password',
											 				    'attributes'=>array('required'=>true)),	
											  'password_conf'=>array('name'=>'user_password_conf','id'=>'user_password_conf','label'=>__('Confirm Password','mgm'),'type'=>'password',
											 				    'attributes'=>array('required'=>true)),				    		                                               
                                              'email'=>array('name'=>'user_email','id'=>'user_email','label'=>__('E-mail','mgm'),'type'=>'text',
											 				 'attributes'=>array('required'=>true)),
											  'url'=>array('name'=>'user_url','id'=>'user_url','label'=>__('Website','mgm'),'type'=>'text'),
                                              'display_name'=>array('name'=>'display_name','id'=>'display_name','label'=>__('Display name publicly as','mgm'),'type'=>'select'),
											  'first_name'=>array('name'=>'first_name','id'=>'first_name','label'=>__('First Name','mgm'),'type'=>'text'),
											  'last_name'=>array('name'=>'last_name','id'=>'last_name','label'=>__('Last Name','mgm'),'type'=>'text'),
											  'description'=>array('name'=>'description','id'=>'description','label'=>__('Biographical Info','mgm'),'type'=>'textarea'),
											  'nickname'=>array('name'=>'nickname','id'=>'nickname','label'=>__('Nickname','mgm'),'type'=>'text',
											  					'attributes'=>array('required'=>true)));
// profile_field_groups
$mgm_config['profile_field_groups'] = array('Photo' => array('photo'),'Name'=>array('username','first_name','last_name','nickname','display_name'),
                                            'Contact Info'=>array('email','url'),'About Yourself'=>array('description','password','password_conf'));	
											
																				  

// end of file