<?php
/*
Template Name: Content Only
*/
?>
          
<?php if ( function_exists('yoast_breadcrumb') ) { yoast_breadcrumb('<div id="breadcrumb"><p>','</p></div>'); } ?>

<?php if (have_posts()) : $count = 0; ?>
<?php while (have_posts()) : the_post(); $count++; ?>
													
			<div id="the-post" class="full-content">
			
				<div class="content-full-width">
			
				<?php while(the_flexible_field("content")): ?>
				 
					<?php if(get_row_layout() == "video_content"): ?>
				 
						<div>
							<?php 
							
								$videos = get_sub_field('video');
								
								if($videos) {
									
									foreach($videos as $video) { 
									
							?>
									
										<?php echo $video['video_title']; ?>
										<?php echo $video['video_embed']; ?>
										<?php echo $video['video_description']; ?>
									
									<?php }
								
								} ?>
							 
								
							 
						</div>
						
					<?php elseif(get_row_layout() == "text_content"): ?>
				 
						<div>
							<?php 
							
								$texts = get_sub_field('text');
								
								if($texts) {
									
									foreach($texts as $text) { 
									
							?>
									
										<?php echo $text['text_title']; ?>
										<?php echo $text['text_description']; ?>
									
									<?php }
								
								} ?>
								
						</div>
						
					<?php elseif(get_row_layout() == "audio_content"): ?>
				 
						<div>
							<?php 
							
								$audios = get_sub_field('audio');
								
								if($audios) {
									
									foreach($audios as $audio) { 
									
							?>
									
										<?php echo $audio['audio_title']; ?>
										<?php echo $video['audio_embed']; ?>
										<?php echo $video['audio_description']; ?>
									
									<?php }
								
								} ?>
								
						</div>
						
					<?php // elseif(get_row_layout() == "slider_content"): ?>
					<!--
						
						<div>
							<?php 
							/*
								$slides = get_sub_field('slider');
								
								if($slides) {
									
									foreach($slides as $slide) { 
									*/
							?>
									
										<?php// echo $slide['slide']; ?>
									
									<?php //}
								
								//} ?>
								
						</div>
						
					-->
						
					<?php elseif(get_row_layout() == "character_bios"): ?>
				 
						<div>
							<?php 
							
								$characters = get_sub_field('character');
								
								if($characters) {
									
									foreach($characters as $character) { 
									
							?>
							
									<div class="character">
									
										<h3><?php echo $character['character_name']; ?></h3>
										
										<?php echo $character['character_video_embed']; ?>
										<?php echo $character['character_description']; ?>
										<?php echo $character['audio_embed']; ?>
										
									</div>
									
									<?php }
								
								} ?>
								
						</div>
						
					<?php endif; ?>
				 
				<?php endwhile; ?>
				
				<div style="display: none;">
				
					<div id="test">
						test
					</div>
				
				</div>
			
				</div>
			
			</div>
			
			<div class="fix"></div>
			
		<?php edit_post_link( __('{ Edit }', 'woothemes'), '<span class="small">', '</span>' ); ?>
	
	<?php $comm = $woo_options['woo_comments']; if ( ($comm == "page" || $comm == "both") ) : ?>
		<?php comments_template(); ?>
	<?php endif; ?>
										
<?php endwhile; else: ?>

		<?php _e('Sorry, no posts matched your criteria.', 'woothemes') ?>

<?php endif; ?> 