<?php

$options = get_option( 'announcer_data' );

?>

<table class="widefat ancr-list-table">
	<thead>
		<tr>
			<th width="7%"><center><?php _e( "ID", 'announcer' ); ?></center></th>
			<th width="20%"><?php _e( "Name", 'announcer' ); ?></th>
			<th><?php _e("Announcement",'announcer'); ?></th>
			<th width="10%"><center><?php _e("Status",'announcer'); ?><center></th>
		</tr>
	</thead>
	
	<tbody>
	
	<?php
		$i = 0;
		
		foreach( $options as $key => $value ){
			
			if( intval ( $key ) > 0 ){
		
				$content =  strip_tags( $value['content'] );
				$name = '<a href="' . ANCR_ADMIN . '&action=edit&id=' . $key . '">' . strip_tags( $value['name'] ) . '</a>';
				$alternate = $i%2 == 0 ? 'class="alternate"' : '';
				$date = ancr_date_check( $value );
				
				if($value['status'] == 'yes' && $date ){
					$status = array( 'green', __( 'Active', 'announcer' ) );
				}else{
					$status = array( 'red', __( ' Inactive', 'announcer' ) );
				}
				
				if( empty( $value['fromdate'] ) ) $fdate = '(not set)';
				else $fdate = $value['fromdate'];
				
				if( empty( $value['todate'] ) ) $tdate = '(not set)';
				else $tdate = $value['todate'];
				
				echo '<tr ' . $alternate . '><td><span class="ancr-list-id">' . $key . '</span></td><td>' . $name . '</td><td>' . $content . '
				
				<div class="row-actions">
					<span class="delete"><a href="' . wp_nonce_url( ANCR_ADMIN . '&action=delete&id=' . $key, 'ancr-delete' ) .  '" class="delete-btn">' . __( 'Delete', 'announcer') . '</a></span>
					<span class="gray">' . $fdate. ' to ' . $tdate . '</span>
					<span><span class="cpreview" title="' . __( 'Border Color', 'announcer') . '" style="background:' . $value['borderclr'] . ';"></span>
					<span class="cpreview" title="' . __( 'Background Color', 'announcer') . '" style="background:' . $value['bgclr'] . ';"></span>
					<span class="cpreview" title="' . __( 'Text Color', 'announcer') . '" style="background:' . $value['textclr'] . ';"></span></span>
					
				</div>
				
				</td>
				
				<td><span class="ancr-list-status ' . $status[0] . '">' . $status[1] . '</span></td>
				
				</tr>';
				$i++;
			
			}
			
		}
		
	?>
	
	</tbody>
	
	<tfoot>
		<tr><td colspan="3"><a href="<?php echo ANCR_ADMIN . '&action=edit'; ?>" class="button-primary"> <?php _e("Create New Announcement",'announcer'); ?> </a> </td><td></td></tr>
	</tfoot>
	
</table>
