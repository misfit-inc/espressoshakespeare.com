<?php
/*
Template Name: Espresso
*/

global $post;
$postid = $post->ID;
$title = get_the_title();
?>
<?php get_header('espresso'); ?>
<?php global $woo_options; ?>
       
	<?php 

		$args = array(
			'post_parent' => $postid,
			'post_type'=> 'page'
		);
		
		$the_query = new WP_Query( $args );
		
	?>
	
	<?php if ( $the_query->have_posts() ) : while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
	
	<?php while(the_flexible_field("content")): ?>
		<?php if(get_row_layout() == "slider_content"): ?>
			
			<div class="iosSlider">
			<div class="slider">
			
			<?php $slides = get_sub_field('slider');
				$r = 1;
				
				if($slides) {
					foreach($slides as $slide) {
						if($r == 1) {
							if($slide['slide_text'] == '') {
								echo '<div class="item current"><img src="' . $slide['slide'] . '" /></div>'; $r++; 
							} else {
								echo '<div class="item current"><img src="' . $slide['slide'] . '" /><div class="slidetarget"><p>' . $slide['slide_text'] . '</p></div></div>'; $r++;
							}
						} else {
							if($slide['slide_text'] == '') {
								echo '<div class="item"><img src="' . $slide['slide'] . '" /></div>'; $r++;
							} else {
								echo '<div class="item"><img src="' . $slide['slide'] . '" /><div class="slidetarget"><p>' . $slide['slide_text'] . '</p></div></div>'; $r++;
							}
						}
					}
				}
			?>
			
			</div>
				<div class="iosslider-prev fa fa-angle-left"> </div><div class="iosslider-next fa fa-angle-right"></div>
			</div>
			
		<?php endif; ?>
			
	<?php endwhile; ?>
	<?php endwhile; wp_reset_postdata(); endif; ?>
	
    <div id="content" class="page col-full">
	
		<!-- <link type="text/css" href="<?php bloginfo('template_url'); ?>/includes/css/ui-lightness/jquery-ui-1.8.16.custom.css" rel="stylesheet" /> -->
		<!-- <script type="text/javascript" src="<?php bloginfo('template_url'); ?>/includes/js/jquery-1.6.2.min.js"></script> -->
		<!-- <script type="text/javascript" src="<?php bloginfo('template_url'); ?>/includes/js/jquery-ui-1.8.16.custom.min.js"></script> -->
		<script type="text/javascript">
			// jQuery(document).ready(function($){

				// Tabs
				// $('.tabs').tabs({ 
					// fx: { opacity: 'toggle', duration:'fast'},
					// panelTemplate: '<div></div>',
				// });
				
				// $('#wait').ajaxStart(function() {
					// $(this).show();
				// }).ajaxComplete(function() {
					// $(this).hide();
				// });
				
			// });
			
		</script>
		
		<div id="main" class="col-left">
		           
			<?php if ( function_exists('yoast_breadcrumb') ) { yoast_breadcrumb('<div id="breadcrumb"><p>','</p></div>'); } ?>

            <?php if (have_posts()) : $count = 0; ?>
            <?php while (have_posts()) : the_post(); $count++; ?>
                                                                        
                <div class="post">

                    <div class="entry">
					
						<!-- <span id="wait"></span> -->
	                	
						<h1 class="title-page"><?php echo $title; ?></h1>
						
						<?php get_template_part('loop','main') ?>
						
						<div id="the-post" class="first">
						
						</div>

	               	</div><!-- /.entry -->
                 
				<?php

					// if ( is_user_logged_in() ) {
					// 	echo '';
					// } else

					// { 

				?>

					
					<!-- <div class="error-user">
						<h2>Error - User not logged in</h2>
						<p>Hello! You have to Sign in first before you can enter. The Sign in button is found in the top right corner of your screen.
						</br>Can't find the Sign in button? <a href="http://espressoshakespeare.com/wp-login.php">Click here</a>.
						</p>
					</div> -->

				<?php //}; ?>
				 
                </div><!-- /.post -->
				
                <?php $comm = $woo_options['woo_comments']; if ( ($comm == "page" || $comm == "both") ) : ?>
                    <?php comments_template(); ?>
                <?php endif; ?>
                                                    
			<?php endwhile; else: ?>
				<div class="post">
                	<p><?php _e('Sorry, no posts matched your criteria.', 'woothemes') ?></p>
                </div><!-- /.post -->
            <?php endif; ?>  
        
		</div><!-- /#main -->
		
		<div class="fix"></div>
		
		<div class="menu-report">
			<?php wp_nav_menu( array( 'depth' => 6, 'sort_column' => 'menu_order', 'container' => 'ul', 'menu_id' => 'topnavmenu', 'menu_class' => 'nav postnav', 'theme_location' => 'top-menu' ) ); ?>
			
			<div class="reportq">
				<a class="deskcboxpodio" target="_blank" href="https://podio.com/webforms/4040641/313544"><div><i class="fa fa-question"></i></div></a>
				<a class="tabcboxpodio" target="_blank" href="https://podio.com/webforms/4040641/313544"><div><i class="fa fa-question"></i></div></a>
			</div>
		</div>

    </div><!-- /#content -->
		
<?php get_footer(); ?>