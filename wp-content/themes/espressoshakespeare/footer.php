<?php global $woo_options; ?>
	
	<?php if(is_page_template('template-espresso.php')) { ?>

		<div class="newfooter">
			<p>Espresso Shakespeare Copyright &copy; <?php echo date('Y'); ?>. All Rights Reserved.</p>
		</div>

	<?php } else { ?> 
	
	<div class="newfooter dontpad">
		<p>Espresso Shakespeare Copyright &copy; <?php echo date('Y'); ?>. All Rights Reserved.</p>
	</div>
	
	<!--
	
	<div id="footer-out">
		
		<?php 
			$total = $woo_options['woo_footer_sidebars']; if (!isset($total)) $total = 4;				   
			if ( ( woo_active_sidebar('footer-1') ||
				   woo_active_sidebar('footer-2') || 
				   woo_active_sidebar('footer-3') || 
				   woo_active_sidebar('footer-4') ) && $total > 0 ) : 
			
	  	?>
		
		<div id="footer-widgets" class="col-full col-<?php echo $total; ?>">
	
			<?php $i = 0; while ( $i < $total ) : $i++; ?>			
				<?php if ( woo_active_sidebar('footer-'.$i) ) { ?>
	
			<div class="block footer-widget-<?php echo $i; ?>">
	        	<?php woo_sidebar('footer-'.$i); ?>    
			</div>
			        
		        <?php } ?>
			<?php endwhile; ?>
		
			<div class="fix"></div>
	
		</div>
		
	    <?php endif; ?>
		
		<div class="footer">
        
			<div class="col-full">

				<div id="copyright">
					<p>Espresso Shakespeare Copyright &copy; <?php echo date('Y'); ?>. All Rights Reserved.</p>
				</div>
				
				<div class="report-nav">
					<a alt="Having an issue? Report it here." href="https://podio.com/webforms/4040641/313544" target="_blank">
						<span class="dn">HAVING AN ISSUE? REPORT IT HERE.</span>
					</a>
				</div>
				
			</div>

		</div>
		
	</div>
	
	-->
	
	<?php } ?>

</div><!-- /#wrapper -->
<?php wp_footer(); ?>
<?php woo_foot(); ?>

</body>
</html>