<!--widgets-->
<?php mgm_box_top('Magic Members Dashboard');?>
	<div id="admin_dashboard">
		<div class="col1">
			<?php mgm_box_top('Plugin Messages', '', false, array('width'=>400));?>
			<?php mgm_get_messages();?>
			<?php mgm_box_bottom();?>	
			
			<?php mgm_box_top('Magic Members Subscription Status', '', false, array('width'=>400));?>
			<?php mgm_get_subscription_status();?>
			<?php mgm_box_bottom();?>	
			
			<?php mgm_box_top('Purchased Posts (last 5)', 'purchasedpostslast5', false, array('width'=>400));?>
			<?php mgm_render_posts_purchased(5);?>
			<?php mgm_box_bottom();?>	
			
			<?php mgm_box_top('Member Statistics', '', false, array('width'=>400));?>
			<?php mgm_member_statistics();?>
			<?php mgm_box_bottom();?>			
		</div>
		<div class="col2">
			<?php mgm_box_top('Version Check', '', false, array('width'=>400));?>
			<?php mgm_check_version();?>
			<?php mgm_box_bottom();?>	
			
			<?php mgm_box_top('Magic Members News', '', false, array('width'=>400));?>
			<?php mgm_site_rss_news();?>
			<?php mgm_box_bottom();?>	
			
			<?php mgm_box_top('Magic Members Blog', '', false, array('width'=>400));?>
			<?php mgm_rss_news();?>
			<?php mgm_box_bottom();?>	
		</div>		
	</div>
	<div class="clearfix"></div>
<?php mgm_box_bottom();?>