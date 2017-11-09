<?php

$options = get_option( 'announcer_data' );
$att = array();
$ancr_id = '';

if( $params[ 'id' ] !== false ){
	if( isset( $options[ $params[ 'id' ] ] ) ){
		$ancr_id = intval( $params[ 'id' ] );
		$att = $options[ $ancr_id ];
		
		$date = ancr_date_check( $att );
				
		if($att['status'] == 'yes' && $date ){
			$status = array( 'green', __( 'Active', 'announcer' ) );
		}else{
			$status = array( 'red', __( ' Inactive', 'announcer' ) );
		}
		
		$title = __( 'Edit Announcement: ', 'announcer' ) . $ancr_id . '<div class="ancr-single-info"><span class="ancr-single-status ' . $status[0] . '">' . $status[1] . '</span> <a class="button delete-btn" href="' . wp_nonce_url( ANCR_ADMIN . '&action=delete&id=' . $ancr_id, 'ancr-delete' ) .  '">' . __( 'Delete', 'announcer' ) . '</a></div>';
		
	}else{
		echo '<div class="updated below-h2 error"><p>Announcement does not exist !!! </p></div>';
		$title = __( "Create an Announcement", 'announcer' );
	}
}else{
	
	$title = __( "Create an Announcement", 'announcer' );

}

$defaults = ancr_default_data();
$att = array_merge( $defaults, $att );

?>
<form method="post" id="ancr-single-form">

<table class="widefat ancr-table">
	<tbody>
	
		<tr>
			<td colspan="2"><a href="<?php echo ANCR_ADMIN; ?>" class="button single-back">&lt; <?php _e("Back",'announcer'); ?></a> <h3 class="single-edit-title"><?php echo $title; ?></h3></td>
		</tr>
	
		<tr>
			<td width="150px"><?php _e("Name",'announcer'); ?></td>
			<td><input class="name" type="text" name="atts[name]" required="required" value="<?php echo $att['name']; ?>" />
			<br/><small class="gray"><?php _e( 'Identification name. Will not be displayed in the announcement', 'announcer' ); ?></small>
			</td>
		</tr>
		
		<tr>
			<td><?php _e("Announcement",'announcer'); ?></td>
			<td><?php wp_editor( $att['content'], 'atts[content]', array( 'wpautop'=> false, 'textarea_rows'=> 5 )); ?></td>
		</tr>
		
		<tr>
			<td><?php _e("Show Announcement",'announcer'); ?></td>
			<td>
			<select name="atts[status]">
		  <option <?php echo $att['status'] == 'yes' ? ' selected="selected"' : ''; ?> value="yes"><?php _e('Yes', 'announcer'); ?></option>
		  <option <?php echo $att['status'] == 'no' ? ' selected="selected"' : ''; ?> value="no"><?php _e('No', 'announcer'); ?></option>
		</select>
			</td>
		</tr>
		
		<tr>
			<td><?php _e("Show this announcement in the time range",'announcer'); ?></td>
			<td>
			
			<table class="tt-table" width="100%"><tr><td width="30%"><label>From:<br/><input class="dpicker" type="text" name="atts[fromdate]" value="<?php echo $att['fromdate']; ?>" required="required" /></label></td><td><label>To:<br/><input class="dpicker" type="text" name="atts[todate]" required="required" value="<?php echo $att['todate']; ?>" /></label></td></table>
			
			<small class="gray"><?php _e( 'Set a longer "to" date to the keep announcement always on', 'announcer' ); ?></small></td>
			
			</td>
		</tr>
		
		<tr>
			<td><?php _e("Show close button",'announcer'); ?></td>
			<td>
			
			<select name="atts[closebt]">
		  <option <?php echo $att['closebt'] == 'yes' ? ' selected="selected"' : ''; ?> value="yes"><?php _e('Yes', 'announcer'); ?></option>
		  <option <?php echo $att['closebt'] == 'no' ? ' selected="selected"' : ''; ?> value="no"><?php _e('No', 'announcer'); ?></option>
		</select>
			
			</td>
		</tr>
		
		<tr>
			<td><?php _e("Announcement position",'announcer'); ?></td>
			<td>
			
			<select name="atts[position]" id="ancr_position">
			
			<optgroup label="Float and Scroll">
			<option <?php echo $att['position'] == 'top-float' ? ' selected="selected"' : ''; ?> value="top-float">Top Float</option>
			<option <?php echo $att['position'] == 'bottom-float' ? ' selected="selected"' : ''; ?> value="bottom-float">Bottom Float</option>
			</optgroup>
			
			<optgroup label="Static and No scroll">
			<option <?php echo $att['position'] == 'top-static' ? ' selected="selected"' : ''; ?> value="top-static">Top Static</option>
			<option <?php echo $att['position'] == 'bottom-static' ? ' selected="selected"' : ''; ?> value="bottom-static">Bottom Static</option>
			</optgroup>
			
			<optgroup label="Other">
			<option <?php echo $att['position'] == 'above-content' ? ' selected="selected"' : ''; ?> value="above-content">Above title</option>
			<option <?php echo $att['position'] == 'manual' ? ' selected="selected"' : ''; ?> value="manual">Manual placement</option>
			</optgroup>
			
		</select>
			
			<div class="ancr-manualcode"><br/>
			
			<?php if( !empty( $ancr_id ) ): ?>
			<small class="gray"><?php _e( 'Function to use in your theme:', 'announcer' ); ?></small> <code>&lt;?php echo announcer( <?php echo $ancr_id; ?> ); ?&gt;</code>
			<?php else: ?>
			<small class="gray"><?php _e( 'Please save to obtain announcement ID' ); ?></small>
			<?php endif;?>
			
			</div>
			
			</td>
		</tr>
		
	</tbody>
</table>

<h3><?php _e("Location rules",'announcer'); ?></h3>

<table class="widefat ancr-table">
	<tbody>
		<tr>
			<td width="150px"><?php _e("Select pages to display the announcement",'announcer'); ?></td>
			<td>
				<div class="loc-rule-box">
				
<?php 

echo '<p class="rule-info">' . __( 'No rules added. Announcement will be displayed site wide.', 'announcer' ) . '</p>';
echo '<p class="rule-head">' . __( 'Show this announcement to', 'announcer' ) . '</p>';

if( is_array( $att['location'] ) ){

	$i=0;
	
	foreach( $att['location'] as $grp ){
		$j=0;
		echo '<div class="group-wrap">';
		foreach( $grp as $rle ){
			$tval = array( $rle['page'], $rle['operator'], $rle['value'] );
			echo ancr_location_group_template( $tval, 0 );
			$j++;
		}
		echo '</div>';
		$i++;
	}
	
}

?>
					
				</div>
				
				<div class="loc-temp-box"></div>
				
				<a href="#" class="button-primary group-add" title="<?php _e( 'Add another page', 'announcer' ); ?>">  AND  </a>
			</td>
		</tr>
	</tbody>
</table>

<h3><?php _e("Themes and Effects",'announcer'); ?></h3>

<table class="widefat ancr-table">
	<tbody>
		<tr>
			<td width="150px"><?php _e("Box Style",'announcer'); ?></td>
			<td>
			
			<select id="ancr_style" name="atts[style]">
				<option <?php echo $att['style'] == 'style1' ? ' selected="selected"' : ''; ?> value="style1">Style 1</option>
				<option <?php echo $att['style'] == 'style2' ? ' selected="selected"' : ''; ?> value="style2">Style 2</option>
				<option <?php echo $att['style'] == 'style3' ? ' selected="selected"' : ''; ?> value="style3">Style 3</option>
				<option <?php echo $att['style'] == 'style4' ? ' selected="selected"' : ''; ?> value="style4">Style 4</option>
				<option <?php echo $att['style'] == 'style5' ? ' selected="selected"' : ''; ?> value="style5">Style 5</option>
				<option <?php echo $att['style'] == 'style6' ? ' selected="selected"' : ''; ?> value="style6">Style 6</option>
				<option <?php echo $att['style'] == 'style7' ? ' selected="selected"' : ''; ?> value="style7">Style 7</option>
				<option <?php echo $att['style'] == 'style8' ? ' selected="selected"' : ''; ?> value="style8">Style 8</option>
				<option <?php echo $att['style'] == 'style9' ? ' selected="selected"' : ''; ?> value="style9">Style 9</option>
				<option <?php echo $att['style'] == 'style10' ? ' selected="selected"' : ''; ?> value="style10">Style 10</option>
				<option <?php echo $att['style'] == 'style11' ? ' selected="selected"' : ''; ?> value="style11">Style 11</option>
				<option <?php echo $att['style'] == 'custom' ? ' selected="selected"' : ''; ?> value="custom">Custom class</option>
			</select>
			
			&nbsp; <small class="ancr-cpicker"><a href="<?php echo ANCR_URL . '/admin/styles-preview.php'; ?>" target="_blank">Preview styles</a></small>
			
			</td>
		</tr>
		
		<tr class="ancr-cclass">
			<td><?php _e("Custom Class",'announcer'); ?></td>
			<td><input class="name" type="text" name="atts[customclass]" value="<?php echo $att['customclass']; ?>" /></td>
		</tr>
		
		<tr class="ancr-cpicker">
			<td><?php _e("Border Color",'announcer'); ?></td>
			<td><input class="cpicker" type="text" name="atts[borderclr]" value="<?php echo $att['borderclr']; ?>" /></td>
		</tr>
		
		<tr class="ancr-cpicker">
			<td><?php _e("Background Color",'announcer'); ?></td>
			<td><input class="cpicker" type="text" name="atts[bgclr]" value="<?php echo $att['bgclr']; ?>" /></td>
		</tr>
		
		<tr class="ancr-cpicker">
			<td><?php _e("Text Color",'announcer'); ?></td>
			<td><input class="cpicker" type="text" name="atts[textclr]" value="<?php echo $att['textclr']; ?>" /></td>
		</tr>
		
		<tr>
			<td><?php _e("Additional Class names",'announcer'); ?></td>
			<td><input class="name" type="text" name="atts[addclass]" value="<?php echo $att['addclass']; ?>" />
			<br/><small class="gray"><?php _e( 'Additional class name for styling', 'announcer' ); ?></small>
			</td>
		
		<tr>
			<td><?php _e("Intro effect",'announcer'); ?></td>
			<td>
			
			<select name="atts[effect]">
				<option <?php echo $att['effect'] == 'fade' ? ' selected="selected"' : ''; ?> value="fade">Fade In</option>
				<option <?php echo $att['effect'] == 'slide' ? ' selected="selected"' : ''; ?> value="slide">Slide into page</option>
				<option <?php echo $att['effect'] == 'none' ? ' selected="selected"' : ''; ?> value="none"><?php _e("No effect",'announcer'); ?></option>
			</select>
			
			</td>
		</tr>
		
		<tr>
			<td><?php _e("Effect Duration",'announcer'); ?></td>
			<td><input type="number" name="atts[effectduration]" value="<?php echo $att['effectduration']; ?>" />
			<br/><small class="gray"><?php _e( 'in seconds', 'announcer' ); ?></small></td>
		</tr>
		
	</tbody>
	
</table>

<?php wp_nonce_field( 'ancr_submit_form' ); ?>

<input type="hidden" name="ancr_id" value="<?php echo $ancr_id; ?>" />

<footer class="ancr-edit-foot"><input id="ancr_submit" type="submit" name="ancr_submit" class="button-primary" value="<?php _e("Update",'announcer'); ?>"></footer>

</form>

<script>
var ancr_rule_group_temp = '<?php echo ancr_location_group_template( array( '', '', '' ), 1 ); ?>';
</script>

<?php

function ancr_location_group_template( $val, $grp ){

	$pages = array(
		array( 'single', 'Single post', 1), // value, text, need parameters or not
		array( 'page', 'Page', 1),
		array( 'home', 'Home page', 0),
		array( 'front-page', 'Front page', 0 ),
		array( 'sticky', 'Sticky posts', 0 ),
		array( 'post-type', 'Post type',  1),
		array( 'page-template', 'Page template', 1 ),
		array( 'category', 'Category page', 1 ),
		array( 'tag', 'Tags page', 1 ),
		array( '404', '404 page', 0 ),
		array( 'has-category', 'Categories of post', 1 ),
		array( 'has-tag', 'Tags of post', 1 ),
	);
	
	$operators = array(
		array( 'equal', 'is equal to' ),
		array( 'not-equal', 'is not equal to' )
	);
	
	$comb = array( $pages, $operators );
	$select = array();
	
	$i=0;
	foreach( $comb as $st ){
		$select[ $i ] = '';
		
		foreach( $st as $opts ){
			$v = $opts[0];
			$s =  ($val[$i] == $v) ? 'selected="selected"' : '';
			$n = $opts[1];
			$p = ( isset( $opts[2] ) && $opts[2] == 1 ) ? 'data-param="1"' : '';
			
			$select[$i] .= "<option value=\"$v\" $s $p>$n</option>";
		}
		$i++;
	}
	
	$rule = '<div class="rule-wrap"><select class="loc-page">' . $select[0] . '</select><select class="loc-operator">' . $select[1] . '</select><input type="text" class="loc-value" value="' . $val[2] . '" title="ID, title or slug" placeholder="ID/title/slug sep. by comma"/><a href="#" class="button-primary rule-add" title="Add another criteria to match">+</a><a href="#" class="button-primary rule-remove" title="Remove criteria">-</a></div>';
	
	if( $grp ) return '<div class="group-wrap">' . $rule . '</div>';
	else return $rule;
}

?>