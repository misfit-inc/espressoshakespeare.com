<?php
/*
Template Name: Popup Content Only
*/
?>

<?php if (have_posts()) : $count = 0; ?>
<?php while (have_posts()) : the_post(); $count++; ?>

<script type="text/javascript">

$(document).ready(function() {
	$('.popup-videos li:first-child').addClass('first-child');
	$('.popup-videos li:nth-child(2)').addClass('second-child');
	$('.popup-videos li:nth-child(3)').addClass('third-child');
	$('.popup-videos li:nth-child(4)').addClass('fourth-child');
	
	$('.popup-videos iframe').click().colorbox();
});

</script>
													
			<div id="the-post">
			
				<div class="content">
					<div class="ppwrap">
					<?php
						$popup_title = get_field('popup_title');
						$popup_title_hash = strtolower($popup_title);
								
						$popup_title_hash = preg_replace(
						'/[^a-zA-Z0-9\.\$\%\'\`\-\@\{\}\~\!\#\(\)\&\_\^]/'
						,'',str_replace(array(' ','%20'),array('_','_'),$popup_title_hash)); 
						
					?>
				
					<?php if (get_field('popup_title')) { ?>
					
						<h3 class="popup-title"><?php the_field('popup_title'); ?></h3>
						
						<div class="fix"></div>
					
					<?php } else {} ?>
					
					<div class="quoted-section">
					
						<?php if (get_field('popup_title')) { ?>
					
							<?php the_field('quoted_section'); ?>
							
						<?php } else {} ?>
												
						<a class="popup-transcript" href="<?php the_field('popup_link_to_transcript'); ?>" target="_blank">Read the full transcript.</a>
					
					</div>
					
						 
					
					
						<?php 
						
							$videos = get_field('videos');
							
							if($videos) { ?>
							
							<div class="popup-videos">
							
							<span>Click the icon <span class="vimeo-icon-enlarge">icon</span> / <span class="youtube-icon-enlarge">icon</span> on the video to enlarge</span>
							
							<ul>
							
						<?php
								
								foreach($videos as $video) { 
								
						?>
								
									<li><?php echo $video['popup_embed_video']; ?></li>
								
								<?php } ?>
								
								</ul>
							</div>
							<?php } ?>
						
						<?php 
						
							$rel_links = get_field('popup_links');
							
							if($rel_links) { ?>
							
							<div class="related-links">
					
								<h3>SEE ALSO</h3>
							
								<ul>
							
									<?php
											
											foreach($rel_links as $rel_link) { 
											
									?>
								
									<li><a href="<?php echo $rel_link['popup_link_url']; ?>#<?php echo $popup_title_hash ?>"><?php echo $rel_link['popup_link_name']; ?> - <?php the_field('popup_title'); ?></a></li>
									
								<?php } ?>
								
								</ul>	
						 
							</div>
							
							<?php } ?>
										 				
				</div>
				</div>
			</div>
			
			<div class="fix"></div>
			
		<!-- <?php edit_post_link( __('{ Edit }', 'woothemes'), '<span class="small">', '</span>' ); ?> -->
	
	<?php $comm = $woo_options['woo_comments']; if ( ($comm == "page" || $comm == "both") ) : ?>
		<?php comments_template(); ?>
	<?php endif; ?>
										
<?php endwhile; else: ?>

		<?php _e('Sorry, no posts matched your criteria.', 'woothemes') ?>

<?php endif; ?> 