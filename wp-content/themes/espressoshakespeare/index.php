<?php get_header('espresso'); ?>
<?php global $woo_options; ?>

<div id="content" class="col-full">

	<!-- COMING SOON! INDEX PAGE -->

	<div id="main" class="fullwidth soon" <?php if (get_field('startsoon_toggle' , 'option') == 'one') { ?>style="display: none;"<?php } ?>>
	
		<div class="post">
		
			<div class="entry">
			
				<h3 class="title"><?php the_field('coming_soon_header' , 'option'); ?></h3>
				<div class="soon-text">
					<?php the_field('coming_soon_text_area' , 'option'); ?>
					<!-- <div class="soon-podio"><?php // the_field('podio_embed_code' , 'option'); ?></div> -->
				</div>
			</div><!-- /.entry -->
		</div><!-- /.post -->
	</div><!-- /#main -->

	<!-- COMING SOON! INDEX PAGE END -->
	
	
	<!-- START COURSE INDEX PAGE -->
	
	<div id="main" class="fullwidth startcourse" <?php if (get_field('startsoon_toggle' , 'option') == 'two') { ?>style="display: none;"<?php } ?>>
	
		<div class="post">
		
			<div class="entry">
			
				<h3 class="title"><?php the_field('home_title' , 'option'); ?></h3>
				
				<div class="fr">
					
					<?php the_field('home_video_embed' , 'option'); ?>
					<?php the_field('home_video_text' , 'option'); ?>
					
				</div>
				
				<div style="width: 470px;" class="fl">
					
					<?php the_field('home_description' , 'option'); ?>
					
					<?php //if ( is_user_logged_in() ) { ?>
					
						<a class="start-the-course" href="/introduction"><span class="dn">Start The Course</span></a>
						
					<?php// } else {} ?>
				
				</div>
				
				<div class="text-area-box">
					<div class="home-box left">
						<div>
							<h1><?php the_field('left_bottom_header' , 'option'); ?></h1>
							<?php the_field('left_bottom_text_area' , 'option'); ?>
						</div>
					</div>
					
					<div class="home-box mid">
						<div>
							<h1><?php the_field('mid_bottom_header' , 'option'); ?></h1>
							<?php the_field('mid_bottom_text_area' , 'option'); ?>
						</div>
					</div>
					
					<div class="home-box right">
						<div>
							<h1><?php the_field('right_bottom_header' , 'option'); ?></h1>
							<?php the_field('right_bottom_text_area' , 'option'); ?>
						</div>
					</div>
				</div>
				
			</div><!-- /.entry -->
		</div><!-- /.post -->
	</div><!-- /#main -->
</div><!-- /#content -->
	
				<!-- <?php //if (is_user_logged_in()) { ?>

				<?php //} else { ?>
				
					<div class="fix"></div>
					<div class="divider"></div>
										
					<h3 style="margin-top: 40px; text-align: center;">To receive access to the guide, you can either login via your facebook account</h3>
					<p style="text-align: center;"><?php //jfb_output_facebook_btn();?></p>
					<h3 style="font-size: 32px; line-height: 30px; margin-top: 80px; text-align: center; margin-bottom: 0;">or create an account</h3>
					
					<div class="ico-head form-icons"></div>
					<div class="ico-mail form-icons"></div>
					<div class="ico-id form-icons"></div>
					<div class="ico-key form-icons"></div>
					
					<?php //echo do_shortcode('[user_register package=subscriber#4]'); ?>
					
				<?php //} ?> -->

<?php get_footer(); ?>