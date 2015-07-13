<!--coupon_list-->
<table width="100%" cellpadding="1" cellspacing="0" border="0" class="widefat form-table">
	<thead>
		<tr>
			<th scope="col"><?php _e('ID','mgm')?></th>
			<th scope="col"><?php _e('Coupon Code','mgm')?></th>
			<th scope="col"><?php _e('Value','mgm')?></th>
			<th scope="col"><?php _e('Desc.','mgm')?></th>
			<th scope="col"><?php _e('Use Limit','mgm')?></th>
			<th scope="col"><?php _e('Expire Dt.','mgm')?></th>
			<th scope="col"><?php _e('Create Dt.','mgm')?></th>
			<th scope="col" style="width: 300px;"><?php _e('Action','mgm')?></th>
		</tr>
	</thead>
	<tbody>
	<?php if($data['coupons']):?>	
		<?php foreach ($data['coupons'] as $i=>$coupon) :?>
		<tr class="<?php echo ($alt = ($alt=='') ? 'alternate': '');?>" id="coupon_row_<?php echo $coupon->id ?>">
			<td><?php echo $coupon->id ?></td>
			<td><?php echo $coupon->name ?></td>
			<td><?php echo mgm_format_currency($coupon->value)?></td>
			<td><?php echo $coupon->description ?></td>
			<td><?php echo is_null($coupon->use_limit) ? __('Unlimited','mgm') : __(sprintf('%d of %d used', $coupon->used_count,$coupon->use_limit), 'mgm') ?></td>
			<td><?php echo strtotime($coupon->expire_dt)>0 ? date(MGM_DATE_FORMAT_SHORT, strtotime($coupon->expire_dt)) : __('Never','mgm') ?></td>
			<td><?php echo date(MGM_DATE_FORMAT_SHORT, strtotime($coupon->create_dt)) ?></td>
			<td>
				<input class="button" name="edit" type="button" value="<?php _e('Edit', 'mgm') ?>" onclick="mgm_coupon_edit('<?php echo $coupon->id ?>');"/>
				<input class="button" name="delete" type="button" value="<?php _e('Delete', 'mgm') ?>" onclick="mgm_coupon_delete('<?php echo $coupon->id ?>');"/>
				<input class="button" name="view_users" type="button" value="<?php _e('Users', 'mgm') ?>" onclick="mgm_coupon_users('<?php echo $coupon->id ?>');"/>	 
			</td>
		</tr>
		<?php	
			  endforeach;
		else:?>
		<tr>
			<td colspan="6"><?php _e('You haven\'t created any coupon yet.','mgm')?></td>
		</tr>
		<?php endif;?>
	</tbody>
</table>	