<!--payments-->
<div id="admin_payments">
	<div id="wrap-admin-payments" class="content-div">
		<ul class="tabs">		
			<?php foreach($data['payment_modules'] as $payment_module):
					// get module
					$module = mgm_get_module($payment_module);
					// skip no tabs
					//if($module->settings_tab === false) continue;?>	
			<li><a href="admin.php?page=mgm/admin/payments&method=module_settings&module=<?php echo $module->code?>"><span class="pngfix"><?php echo sprintf(__('%s', 'mgm'), $module->name)?></span></a></li>
			<?php endforeach;?>			
			<li><a href="admin.php?page=mgm/admin/payments&method=payment_modules"><span class="pngfix"><?php _e('Payment Modules','mgm')?></span></a></li>			
		</ul>										
	</div>
</div>