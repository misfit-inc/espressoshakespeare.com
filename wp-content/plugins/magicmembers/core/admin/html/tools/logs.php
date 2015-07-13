<!--logs-->
	<?php mgm_box_top('Transaction Logs');?>
	<table width="100%" cellpadding="1" cellspacing="0" border="0" class="widefat form-table">
		<thead>
			<tr>
				<th scope="col" style="text-align:center"><b><?php _e('ID#','mgm') ?></b></th>
				<th scope="col" style="text-align:center"><b><?php _e('Type','mgm') ?></b></th>
				<th scope="col" style="text-align:center"><b><?php _e('Module','mgm') ?></b></th>
				<th scope="col" style="text-align:center"><b><?php _e('Status','mgm') ?></b></th>
				<th scope="col" style="text-align:center"><b><?php _e('Status Text','mgm') ?></b></th>
				<th scope="col" style="text-align:center"><b><?php _e('Transaction Date','mgm') ?></b></th>
			</tr>
		</thead>
		<tbody>
		<?php if(count($data['transactions_logs'])>0): foreach($data['transactions_logs'] as $tran_log):?>
			<tr class="<?php echo ($alt = ($alt=='') ? 'alternate': '');?>">					
			   <td>
			   		<?php echo $tran_log->id ?>		   
			   </td>
			   <td>
			   		<?php echo ucwords(str_replace('_',' ',$tran_log->payment_type)) ?>		   
			   </td>
			   <td>
			   		<?php echo ucwords($tran_log->module) ?>		   
			   </td>
			   <td>
			   		<?php echo $tran_log->status ?>		   
			   </td>
			   <td>
			   		<?php echo ($tran_log->status_text)? $tran_log->status_text : __('N/A', 'mgm') ?>		   
			   </td>
			   <td>
			   		<?php echo date(MGM_DATE_FORMAT_LONG_TIME, strtotime($tran_log->transaction_dt))?>		   
			   </td>
			</tr>   
		<?php endforeach; else:?>
			<tr class="<?php echo ($alt = ($alt=='') ? 'alternate': '');?>">
				<td colspan="6" align="center">
				 <?php _e('No transaction log','mgm')?>					 					
				</td>
			</tr>
		 <?php endif;?>				
		 </tbody>
	</table>
	<?php mgm_box_bottom();?>
	
	<?php mgm_box_top('Rest API Access Logs');?>
	<table width="100%" cellpadding="1" cellspacing="0" border="0" class="widefat form-table">
		<thead>
			<tr>
				<th scope="col" style="text-align:center"><b><?php _e('API Key#','mgm') ?></b></th>
				<th scope="col" style="text-align:center"><b><?php _e('URI','mgm') ?></b></th>
				<th scope="col" style="text-align:center"><b><?php _e('Method','mgm') ?></b></th>
				<th scope="col" style="text-align:center"><b><?php _e('IP','mgm') ?></b></th>
				<th scope="col" style="text-align:center"><b><?php _e('Authorized?','mgm') ?></b></th>
				<th scope="col" style="text-align:center"><b><?php _e('Date','mgm') ?></b></th>
			</tr>
		</thead>
		<tbody>
		<?php if(count($data['api_logs'])>0): foreach($data['api_logs'] as $api_log):?>
			<tr class="<?php echo ($alt = ($alt=='') ? 'alternate': '');?>">					
			   <td>
			   		<?php echo $api_log->api_key ?>		   
			   </td>
			   <td>
			   		<?php echo $api_log->uri ?>		   
			   </td>
			   <td>
			   		<?php echo strtoupper($api_log->method) ?>		   
			   </td>
			   <td>
			   		<?php echo $api_log->ip_address ?>		   
			   </td>
			   <td>
			   		<?php echo ($api_log->is_authorized=='Y')? sprintf('<span style="color:green;font-weight:bold">%s</span>',__('Yes','mgm')) 
					                                         : sprintf('<span style="color:red;font-weight:bold">%s</span>',__('No', 'mgm'))  ?>		   
			   </td>
			   <td>
			   		<?php echo date(MGM_DATE_FORMAT_LONG_TIME, strtotime($api_log->create_dt))?>		   
			   </td>
			</tr>   
		<?php endforeach; else:?>
			<tr class="<?php echo ($alt = ($alt=='') ? 'alternate': '');?>">
				<td colspan="6" align="center">
				 <?php _e('No api log','mgm')?>					 					
				</td>
			</tr>
		 <?php endif;?>				
		 </tbody>
	</table>
	<?php mgm_box_bottom();?>
	
	<script language="javascript">
	<!--
	jQuery(document).ready(function(){
		// set pager
		// mgm_set_pager('#member_list');		
	});
	//-->	
	</script>