<div class="tabs">

	<?php// if ( is_user_logged_in() ) { ?>
	
		<div id="espresso-nav" class="side-navigation espresso-nav nav">
			 <?php
					$args = array(
						'child_of'     => $post->ID,
						'echo'         => 0,
						'post_type'    => 'page',
						'title_li'     => '', 
					);
				$children = wp_list_pages($args);
				if ($children) {
			?>
			 
				<ul><?php echo $children; ?></ul>
			
			<?php } ?>
				  
		</div><!-- /#nav -->
	
	<?php //} else {} ?>
</div>