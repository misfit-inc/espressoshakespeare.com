<?php
/*
Template Name: Register
*/
?>

<?php get_header('espresso'); ?>
       
    <div id="content" class="page col-full">
		<div id="main" class="fullwidth">
            
			<?php if ( function_exists('yoast_breadcrumb') ) { yoast_breadcrumb('<div id="breadcrumb"><p>','</p></div>'); } ?>
            <?php if (have_posts()) : $count = 0; ?>
            <?php while (have_posts()) : the_post(); $count++; ?>
                                                                        
                <div class="post">
				
                    <div class="entry">
						                	
						<h3>To receive access to the guide, you can either login via your facebook account</h3>
						
						<p style="text-align: center;"><?php jfb_output_facebook_btn();?></p>
						
						<h3 style="font-size: 32px; line-height: 30px; margin-top: 80px;">or create an account</h3>
						
						<?php echo do_shortcode('[user_register package=subscriber#4]'); ?>
												
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