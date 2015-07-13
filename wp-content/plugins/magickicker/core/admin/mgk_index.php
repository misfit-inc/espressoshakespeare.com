<div class="wrap">
	<div id="mk-wrapper">
		<div id="mk-panel-wrap">
			<div id="mk-panel-wrapper">
				<div id="mk-panel">
					<div id="mk-panel-content-wrap">
						<div id="mk-panel-content">
							<a href="admin.php?page=mgk/admin"><img src="<?php echo MGK_ASSETS_URL ?>images/kicker-logo.jpg" alt="mk-panel" class="pngfix" id="mk-panel-logo" width="150" height="70"/></a>
							<ul id="mk-panel-mainmenu">
								<li <?php if(!mgk_get_auth()):?>class="last"<?php endif;?>><a href="#admin_home" title="Home"><img src="<?php echo MGK_ASSETS_URL ?>images/icons/anchor.png" class="pngfix" alt="" /><?php _e('Magic Kicker','mgk')?></a></li>
								<?php if(mgk_get_auth()):?>
								<li><a href="#admin_blockedlists" title="Blocked Lists"><img src="<?php echo MGK_ASSETS_URL ?>images/icons/lock.png" class="pngfix" alt="" /><?php _e('Blocked Lists','mgk')?></a></li>
								<li><a href="#admin_accesslogs" title="Access Logs"><img src="<?php echo MGK_ASSETS_URL ?>images/icons/database_key.png" class="pngfix" alt="" /><?php _e('Access Logs','mgk')?></a></li>
								<li class="last"><a href="#admin_settings" title="Settings"><img src="<?php echo MGK_ASSETS_URL ?>images/icons/page_white_gear.png" class="pngfix" alt="" /><?php _e('Settings','mgk')?></a></li>
								<?php endif;?>
							</ul><!-- end mk-panel mainmenu -->
							<!--tabs contents-->		
							<div id="admin_home">
								<div id="wrap-admin-home" class="content-div">
									<ul class="tabs">			
										<li><a href="admin.php?page=mgk/admin&load=dashboard"><span class="pngfix"><?php echo _e('Dashboard','mgk')?></span></a></li>				
									</ul>										
								</div>
							</div>
							<?php if(mgk_get_auth()):?>
								
							<div id="admin_blockedlists">
								<div id="wrap-admin-blockedlists" class="content-div">
									<ul class="tabs">			
										<li><a href="admin.php?page=mgk/admin&load=blockedlists&mode=members"><span class="pngfix"><?php _e('Members','mgk')?></span></a></li>
										<li><a href="admin.php?page=mgk/admin&load=blockedlists&mode=ips"><span class="pngfix"><?php _e('IPs','mgk')?></span></a></li>																												
									</ul>										
								</div>
							</div>	
							<div id="admin_accesslogs">
								<div id="wrap-admin-accesslogs" class="content-div">
									<ul class="tabs">			
										<li><a href="admin.php?page=mgk/admin&load=accesslogs&mode=members"><span class="pngfix"><?php _e('Members','mgk')?></span></a></li>										
									</ul>										
								</div>
							</div>							
							<div id="admin_settings">
								<div id="wrap-admin-settings" class="content-div">
									<ul class="tabs">			
										<li><a href="admin.php?page=mgk/admin&load=settings&mode=general"><span class="pngfix"><?php _e('General','mgk')?></span></a></li>																				
										<!--<li><a href="admin.php?page=mgk/admin&load=settings&mode=emailtemplates"><span class="pngfix"><?php _e('Email Templates','mgk')?></span></a></li>										-->
									</ul>										
								</div>								
							</div>		
							<?php endif;?>								
							<!--/tab contents-->
						</div> <!-- end mk-panel-content div -->
					</div> <!-- end mk-panel-content-wrap div -->
				</div> <!-- end mk-panel div -->
			</div> <!-- end mk-panel-wrapper div -->
			<div id="mk-panel-bottom">   	
				<?php mgk_render_infobar()?>	
			</div><!-- end mk-panel-bottom div -->
			<div style="clear: both;"></div>
		</div> <!-- end panel-wrap div -->
	</div> <!-- end wrapper div -->
</div>
<script language="javascript">
	//<![CDATA[
	jQuery(document).ready(function(){			
		// create main inline tabs	
		jQuery('#mk-panel-content').tabs({ fx: { opacity: 'toggle' }, idPrefix: 'ui-tabs-primary' });
		// create sub ajax tabs
		jQuery('.content-div').tabs({ fx: { opacity: 'toggle' }, cache: false, idPrefix: 'ui-tabs-secondary',
									  spinner: "<?php _e('Loading','mgk')?>",
		                              load: function(event,ui){mgk_attach_tips();}, 
									  select: function(event,ui){jQuery('#message').remove()}});
	});
	//]]>
</script>