<div id="mgm-wrapper">
  <div id="mgm-panel-wrap">
		<div id="mgm-panel-wrapper">
			<div id="mgm-panel">
				<div id="mgm-panel-content-wrap">
					<div id="mgm-panel-content">						
						<a href="admin.php?page=mgm/admin"><img src="<?php echo MGM_ASSETS_URL ?>images/logo.png" alt="mgm-panel" class="pngfix" id="mgm-panel-logo" width="150" height="70"/></a>
						<ul id="mgm-panel-mainmenu">													
							<?php if (mgm_get_class('auth')->verify()) :?>
							<li><a href="admin.php?page=mgm/admin/home" title="admin home"><img src="<?php echo MGM_ASSETS_URL ?>images/icons/status_online.png" class="pngfix" alt="" /><?php echo __('Magic Members','mgm')?></a></li>
							
							<li><a href="#admin_members" title="admin members"><img src="<?php echo MGM_ASSETS_URL ?>images/icons/user.png" class="pngfix" alt="" /><?php echo __('Members','mgm')?></a></li>

							<li><a href="#admin_contents" title="admin contents"><img src="<?php echo MGM_ASSETS_URL ?>images/icons/page_white_key.png" class="pngfix" alt="" /><?php echo __('Content Control','mgm')?></a></li>
							
							<li><a href="#admin_payperpost" title="admin pay per post"><img src="<?php echo MGM_ASSETS_URL ?>images/icons/page_white_lightning.png" class="pngfix" alt="" /><?php echo __('Pay Per Post','mgm')?></a></li>

							<li><a href="#admin_payments" title="admin payments"><img src="<?php echo MGM_ASSETS_URL ?>images/icons/money.png" class="pngfix" alt="" /><?php echo __('Payment Settings','mgm')?></a></li>
							
							<?php /*?><li><a href="#admin_autoresponders" title="admin autoresponders"><img src="<?php echo MGM_ASSETS_URL ?>images/icons/email_go.png" class="pngfix" alt="" /><?php echo __('Autoresponders','mgm')?></a></li><?php */?>
							
							<?php /*?><li><a href="#admin_plugins" title="admin plugins"><img src="<?php echo MGM_ASSETS_URL ?>images/icons/connect.png" class="pngfix" alt="" /><?php echo __('Plugins','mgm')?></a></li><?php */?>
							
							<li><a href="#admin_settings" title="admin settings"><img src="<?php echo MGM_ASSETS_URL ?>images/icons/cog.png" class="pngfix" alt="" /><?php echo __('Misc. Settings','mgm')?></a></li>							
							
							<li><a href="#admin_tools" title="admin tools"><img src="<?php echo MGM_ASSETS_URL ?>images/icons/wrench.png" class="pngfix" alt="" /><?php echo __('Tools','mgm')?></a></li>

							<li class="last"><a href="#admin_support_docs" title="admin support docs"><img src="<?php echo MGM_ASSETS_URL ?>images/icons/report.png" class="pngfix" alt="" /><?php echo __('Support Docs','mgm')?></a></li>		

							<?php else:?>

							<li class="last"><a href="admin.php?page=mgm/admin/activation" title="admin activation"><img src="<?php echo MGM_ASSETS_URL ?>images/icons/status_online.png" class="pngfix" alt="" /><?php echo __('Magic Members','mgm')?></a></li>

							<?php endif;?>
						</ul><!-- end mgm-panel mainmenu -->
						
						<!--tabs contents-->
						<div id="admin_home"></div>
						<div id="admin_members"></div>
						<div id="admin_contents"></div>
						<div id="admin_payperpost"></div>
						<div id="admin_payments"></div>						 
						<!--<div id="admin_autoresponders"></div>-->
						<!--						
						<div id="admin_plugins"></div>
						-->
						<div id="admin_settings"></div>
						<div id="admin_tools"></div>
						<div id="admin_support_docs"></div>	
						<div id="admin_activation"></div>				
						<!--/tab contents-->

					</div> <!-- end mgm-panel-content div -->

				</div> <!-- end mgm-panel-content-wrap div -->

			</div> <!-- end mgm-panel div -->

		</div> <!-- end mgm-panel-wrapper div -->

		<div id="mgm-panel-bottom">   	
               
			<?php mgm_render_infobar()?>	
			
        </div><!-- end mgm-panel-bottom div -->

        <div style="clear: both;"></div>

	  </div> <!-- end panel-wrap div -->

	</div> <!-- end wrapper div -->

	<script language="javascript">
		//<![CDATA[
		jQuery(document).ready(function(){	
			// attach loading mask
			mgm_ajax_loader();		
			// create main inline tabs	
			$primary_urls = [];
			$primary = jQuery('#mgm-panel-content').tabs(
									{ fx: { opacity: 'toggle' }, idPrefix: 'ui-tabs-primary', 
									  load : function(event,ui){	
									  	// set next urls								  	
										jQuery('#mgm-panel-mainmenu li a[href][title]').each(function(index){		
											// home page already									
											if(index>0){													
												// get url
												$url = jQuery(this).attr('href').replace('#','').replace('_','/');			
												// add
												if(jQuery.inArray($url, $primary_urls) == -1){																					
													$primary.tabs('url', index, 'admin.php?page=mgm/'+$url);
													$primary_urls.push($url);
												}
											}
										});										
										// create secondary tabs										
										$secondary = jQuery('.content-div').tabs({ 
														    fx: { opacity: 'toggle' }, cache: false, idPrefix: 'ui-tabs-secondary',
														    spinner: '<?php _e('Loading...','mgm')?>',
														    load: function(event,ui){ mgm_attach_tips();}, 
														    select: function(event,ui){jQuery('#message').remove()}
																	  
										}); // end secondary tabs										
									  }
			}); // end primary tabs						
		});
		//]]>
	</script>	