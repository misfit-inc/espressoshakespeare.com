<?php
/*
Template Name: Introduction Page
*/
?>
<?php get_header('espresso'); ?>
<?php global $woo_options; ?>
       
    <div id="content" class="page col-full">
		
		<div id="main" class="col-left">
		           
            <?php if (have_posts()) : $count = 0; ?>
            <?php while (have_posts()) : the_post(); $count++; ?>
                                                                        
                <div class="post">

                    <div class="entry">
						
						<div id="the-post" class="introduction-content">
						
							<div>
							
								<!-- <h1><?php the_title(); ?></h1> -->
								
								<?php the_content(); ?>
								
							</div>
							
							<h2>Signup below to receive updates, first access to new videos and teaching tips.</h2>
						
							<div id="mc_embed_signup">
								<form action="http://espressoshakespeare.us2.list-manage.com/subscribe/post?u=6b31f41ebfc5c0c138fd41c57&amp;id=b3015fa4a3" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
									<div class="mc-field-group">
										<input type="email" name="EMAIL" class="required email" id="mce-EMAIL" onblur="if (this.value == '') {this.value = 'EMAIL';}" onfocus="if (this.value == 'EMAIL') {this.value = '';}" value="EMAIL">
									</div>
									
									<div class="mc-field-group">
										<input type="text" name="FNAME" class="required" id="mce-FNAME" onblur="if (this.value == '') {this.value = 'NAME';}" onfocus="if (this.value == 'NAME') {this.value = '';}" value="NAME">
									</div>
									
									<div id="mce-responses" class="clear">
										<div class="response" id="mce-error-response" style="display:none"></div>
										<div class="response" id="mce-success-response" style="display:none"></div>
									</div>
									
									<input type="submit" value="" name="subscribe" id="mc-embedded-subscribe" class="button">
									
									<div class="clear"></div>
								</form>
							</div>
						
						</div>

	               	</div><!-- /.entry -->
                    
                </div><!-- /.post -->
                                                                    
			<?php endwhile; else: ?>
				<div class="post">
                	<p><?php _e('Sorry, no posts matched your criteria.', 'woothemes') ?></p>
                </div><!-- /.post -->
            <?php endif; ?>  
        
		</div><!-- /#main -->

    </div><!-- /#content -->
		
<?php get_footer(); ?>