<?php
/*
Template Name: Login
*/
?>

<?php get_header(); ?>
       
    <div id="content" class="page col-full">
		<div id="main" class="fullwidth">
            
			<?php if ( function_exists('yoast_breadcrumb') ) { yoast_breadcrumb('<div id="breadcrumb"><p>','</p></div>'); } ?>
            <?php if (have_posts()) : $count = 0; ?>
            <?php while (have_posts()) : the_post(); $count++; ?>
                                                                        
                <div class="post">

                    <h1 class="title"><?php the_title(); ?></h1>
                    
                    <div class="entry">
						
						<?php 

							global $user_identity, $user_ID;	
							// If user is logged in or registered, show dashboard links in panel
							if (is_user_logged_in()) { 
						?>
						
						<?php } else { ?>
							
							<div class="facebook-register">
							
								<h2><?php _e('Log in with facebook'); ?></h2>
							
								<p style="margin-bottom: 30px;"><fb:login-button v="2" scope="email,user_website" onlogin="window.location.reload();" /></p>
								
								<h2><?php _e('Register with facebook'); ?></h2>
							
								<?php sfc_register_form(); ?>
								
							</div>
						
							<div class="normal-login-register">
						
								<h2><?php _e('Log in normally'); ?></h2>
										<!-- Login Form -->
								<form class="clearfix login" action="<?php bloginfo('wpurl') ; ?>/wp-login.php" method="post">
									
									<fieldset><label class="login_label label-1" for="log"><?php _e('Username') ?></label>
									<input class="field ui-corner-all" type="text" name="log" id="log" value="<?php echo wp_specialchars(stripslashes($user_login), 1) ?>" size="23" />
									<label class="login_label label-1" for="pwd"><?php _e('Password') ?></label>
									<input class="field ui-corner-all" type="password" name="pwd" id="pwd" size="23" />
									<label style="display:none;"><input name="rememberme" id="rememberme" type="checkbox" checked="checked" value="forever" /><?php _e('Remember Me') ?></label>
									<div class="clear"></div>
									<input type="submit" name="submit" value="Login" class="bt_login ui-corner-all" />
									<input type="hidden" name="redirect_to" value="<?php echo $_SERVER['REQUEST_URI']; ?>" />
									<a class="lost-pwd" href="<?php bloginfo('wpurl') ; ?>/wp-login.php?action=lostpassword"><?php _e('Lost your password?') ?></a>
									</fieldset>
								</form>
								
								<h2 style="margin-top: 30px;"><?php _e('Register normally'); ?></h2>	
								<!-- Register Form -->
								
								<form name="registerform" id="registerform" action="<?php echo site_url('wp-login.php?action=register', 'login_post') ?>" method="post">					
									<fieldset><label class="login_label label-2" for="user_login"><?php _e('Username') ?></label>
									<input class="field input ui-corner-all" type="text" name="user_login" id="user_login" value="<?php echo attribute_escape(stripslashes($user_login)); ?>" size="23" tabindex="10" />
									<label class="login_label label-2" for="user_email"><?php _e('E-mail') ?></label>
									<input class="field input ui-corner-all" type="text" name="user_email" id="user_email" value="<?php echo attribute_escape(stripslashes($user_email)); ?>" size="23" tabindex="20" />
									<p style="margin-bottom: 10px;" id="reg_passmail"><?php _e('A password will be e-mailed to you.') ?></p>
									<input class="ui-corner-all" type="submit" name="wp-submit" id="wp-submit" value="<?php _e('Register'); ?>" />
								</fieldset>
								</form>
								
							</div>
						
						<?php } ?>
			
	               	</div><!-- /.entry -->

					<?php edit_post_link( __('{ Edit }', 'woothemes'), '<span class="small">', '</span>' ); ?>

                </div><!-- /.post -->
                                                    
			<?php endwhile; else: ?>
				<div class="post">
                	<p><?php _e('Sorry, no posts matched your criteria.', 'woothemes') ?></p>
                </div><!-- /.post -->
            <?php endif; ?>  
        
		</div><!-- /#main -->
		
    </div><!-- /#content -->
		
<?php get_footer(); ?>