<?php mgm_box_top(sprintf("View Users Registered with Coupon: %s",$data['coupon']->name));?>
<table width="100%" cellpadding="1" cellspacing="0" border="0" class="widefat form-table">
	<thead>
		 <tr>
			<th scope="col"><?php _e('ID','mgm')?></th>
			<th scope="col"><?php _e('Username','mgm')?></th>
			<th scope="col"><?php _e('Email','mgm')?></th>
			<th scope="col"><?php _e('Membership Type','mgm')?></th>
		</tr>
	</thead>
	<tbody>
	<?php 
	if($data['users']):
		foreach ($data['users'] as $user) :
			$show = false;
			if ($user->ID):				
				// user
				$user = get_userdata($user->ID)	;
				// member
				$mgm_member = mgm_get_member($user->ID);
				
				$mgm_member->coupon 			= (array) $mgm_member->coupon;
				if(isset($mgm_member->upgrade['coupon']))
					$mgm_member->upgrade['coupon'] 	= (array) $mgm_member->upgrade['coupon'];
				if($mgm_member->extend['coupon'])
					$mgm_member->extend['coupon'] 	= (array) $mgm_member->extend['coupon'];
				// check
				if(isset($mgm_member->coupon['id'])){
					if($data['coupon']->id == $mgm_member->coupon['id']){
						$show = true;
					}
				}else if(isset($mgm_member->upgrade['coupon']['id'])){
					if($data['coupon']->id == $mgm_member->upgrade['coupon']['id']){
						$show = true;
					}
				}else if(isset($mgm_member->extend['coupon']['id'])){
					if($data['coupon']->id == $mgm_member->extend['coupon']['id']){
						$show = true;
					}
				}
			// do not show
			if($show == false) continue;
			?>
		<tr class="<?php echo ($alt = ($alt=='') ? 'alternate': '');?>">
			<td><?php echo $user->ID ?></td>
			<td><?php echo $user->user_login?></td>
			<td><?php echo $user->user_email ?></td>
			<td><?php echo ucwords(str_replace('_', ' ', $mgm_member->membership_type)) ?></td>
		</tr>
		<?php
			endif;
		endforeach;			
	else:?>
		<tr>
			<td colspan="4"><?php _e('There are currently no user registered with this coupon.','mgm')?></td>
		</tr>
	<?php 
	endif;?>
	</tbody>
	<tfoot>
		<tr>
			<td valign="middle" colspan="4">
				<div style="float: left;">	
					<input type="button" class="button" onclick="mgm_coupon_users(false)" value="&laquo; <?php _e('Back to Coupons', 'mgm') ?>" />
				</div>		
			</td>
		</tr>
	</tfoot>			
</table>
<?php mgm_box_bottom();?>