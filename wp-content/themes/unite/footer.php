</div><!-- /#wrapper -->

<div id="footer-outer">

	<div id="footer-widgets" class="col-full">

		<div class="block">
        	<?php woo_sidebar('footer-1'); ?>    
		</div>
		<div class="block center">
        	<?php woo_sidebar('footer-2'); ?>    
		</div>
		<div class="block last">
        	<?php woo_sidebar('footer-3'); ?>    
		</div>
		<div class="fix"></div>

	</div><!-- /#footer-widgets  -->


	<div id="footer" class="col-full" >
	
		<div id="copyright" class="col-left">
			<p>Easy web publishing with a WordPress Tumblog Theme.</p>
		</div>
		
		<div id="credit" class="col-right">
			<p>Exclusively by <a href="http://www.woothemes.com"><img src="<?php bloginfo('template_directory'); ?>/images/woothemes.png" width="74" height="19" alt="Woo Themes" /></a></p>
		</div>
		
	</div><!-- /#footer  -->
</div><!-- /#footer-outer  -->

<?php wp_footer(); ?>
<?php woo_foot(); ?>

</body>
</html>