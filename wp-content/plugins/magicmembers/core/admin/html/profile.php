<!--profile-->
<?php /*if(isset($_GET['unsubscribed']) && $_GET['unsubscribed']=='true'):?>
<script language="Javascript">//var t = setTimeout ( "window.location='<?php echo wp_logout_url()?>'", 1000 ); </script>
<?php endif;*/?>
<div class="wrap" id="mgm-profile-page">
	<div id="icon-profile" class="icon32"><br /></div> 
	<h2><?php _e('Magic Members - Membership Information','mgm') ?></h2>
	<?php 
	// error notice
	if(isset($_GET['unsubscribe_errors'])):
		echo sprintf('<p><div class="error">%s</div></p>', urldecode($_GET['unsubscribe_errors']));
	endif;?>
	<div id="poststuff">
		<div style="min-height:400px; width:auto;">					
			<div class="postbox" style="margin:10px 0px 20px 0px; width: 450px; float: left;">
				<h3><?php _e('Subscription Information','mgm') ?></h3>
				<div class="inside">
					<?php echo mgm_user_subscription();?>
				</div>
			</div>			
			<div class="postbox" style="margin: 10px 0px 20px 10px; width: 530px; float: left;">
				<h3><?php _e('Membership Information','mgm')?></h3>
				<div class="inside">
				<?php echo mgm_membership_cancellation(); ?>	
				</div>
			</div>		
		</div>
	</div>	
</div>
<div class="clearfix"></div>
