<?php 
	// Don't display sidebar if full width
	global $woo_options;
	if ( $woo_options['woo_layout'] != "layout-full" ) :
?>	

	<?php if (is_page('introduction')) { ?>
		<?php if (woo_active_sidebar('espresso-1')) : ?>
		<div class="primary">
			<?php woo_sidebar('espresso-1'); ?>
		</div>
		<?php endif; ?>
	<?php } elseif (is_page('synopsis')) { ?>
		<?php if (woo_active_sidebar('espresso-2')) : ?>
		<div class="primary">
			<?php woo_sidebar('espresso-2'); ?>
		</div>
		<?php endif; ?>
	<?php } elseif (is_page('character-bios')) { ?>
		<?php if (woo_active_sidebar('espresso-3')) : ?>
		<div class="primary">
			<?php woo_sidebar('espresso-3'); ?>
		</div>
		<?php endif; ?>
	<?php } ?>
	
</div><!-- /#sidebar -->

<?php endif; ?>