$j = jQuery.noConflict();

$j(document).ready(function(){
	$j('.cpicker').wpColorPicker();
	
	$j('#ancr_style').change(function(){
		ancr_customclassfld();
	});
	
	$j('#ancr_position').change(function(){
		ancr_manualcode();
	});
	
	$j( '.dpicker' ).datepicker({ dateFormat: "yy-mm-dd" });
	
	$j('.delete-btn').click(function(){
		var cfrm = confirm( 'Are you sure want to delete this Announcement ?' );
		if( cfrm == false ){
			return false;
		}
	});
	
	$j('.group-add').on( 'click', function(e){
		e.preventDefault();
		ancr_add_rule( true, false);
	});
	
	$j(document.body).on( 'click', '.rule-add', function(e){
		e.preventDefault();
		ancr_add_rule( false, $j(this) );
	});
	
	$j(document.body).on( 'click', '.rule-remove', function(e){
		e.preventDefault();
		ancr_remove_rule( $j(this) );
	});
	
	$j(document.body).on( 'change',  '.loc-page', function(){
		ancr_loc_page_change( $j(this) );
	})
	
	$j('.loc-page').each(function(){
		ancr_loc_page_change( $j(this) );
	});
	
	$j('#ancr-single-form').submit(function(){
		ancr_gen_loc_rule();
	});
	
	$j('#amtSelect').change(function(){
		if( $j( this ).val() == '' ) {
			var amt = parseInt( prompt( 'Enter custom amount: ' ) );
			if( !isNaN( amt ) ) $j(this).append( '<option value="' + amt + '" selected="selected" >' + amt + '</option>' );
		}
	});
	
	var atload = 0;
	$j('.shareBtn').hover(function(){
		if( atload == 0 ){
			ancr_async( '//s7.addthis.com/js/300/addthis_widget.js#pubid=vaakash' );
			atload = 1;
		}
	});
	
	ancr_gen_loc_rule();
	ancr_customclassfld();
	ancr_manualcode();
	
});

function ancr_add_rule( group, btn ){
	var grp_temp = ancr_rule_group_temp,
		rule_temp = $j( "<div>" ).append( grp_temp ).find('.rule-wrap')[0].outerHTML;
		
	if( group ){
		
		var grp_count = $j('.group-wrap').length;
		$j('.loc-rule-box').append( grp_temp );
		
	}else{
		
		var rule_group = btn.parent().parent(),
			rule_count = rule_group.find( '.rule-wrap' ).length;
			
		rule_group.append( rule_temp );
		
	}
	
	ancr_gen_loc_rule();
	
}

function ancr_remove_rule( btn ){
	var rule = btn.parent();
	var grp = rule.parent();
	
	rule.remove();
	
	if( grp.children().length == 0 ){
		grp.remove();
	}
	
	ancr_gen_loc_rule();
	
}

function ancr_gen_loc_rule(){
	var tbox = $j( '.loc-temp-box' ),
		tgrp = $j( '.loc-rule-box' ).find( '.group-wrap' ),
		tinfo = $j( '.rule-info' ),
		thead = $j( '.rule-head' ),
		gadd = $j( '.group-add' ),
		i = 0;
	
	tbox.empty();
	
	$j( tgrp ).each(function(){
		var trle = $j(this).find( '.rule-wrap' ),
			j = 0;
			
		$j( trle ).each(function(){
			var vals = [
				$j(this).find( '.loc-page' ).val(),
				$j(this).find( '.loc-operator' ).val(),
				$j(this).find( '.loc-value' ).val()
			];
			tbox.append( '<input name="atts[location]['+ i +']['+ j +'][page]" value="'+ vals[0] +'" /><input name="atts[location]['+ i +']['+ j +'][operator]" value="'+ vals[1] +'" /><input name="atts[location]['+ i +']['+ j +'][value]" value="'+ vals[2] +'" />');
			j++;
		});
		
		i++;
	});
	
	if( tbox.children().length == 0 ){
		tbox.append('<input name="atts[location]" />');
		tinfo.show();
		thead.hide();
		gadd.text( 'Add new rules' );
	}else{
		tinfo.hide();
		thead.show();
		gadd.text( ' AND ' );
	}
}

function ancr_customclassfld(){
	if($j('#ancr_style').val() == 'custom'){
		$j('.ancr-cclass').fadeIn();
		$j('.ancr-cpicker').hide();
	}else{
		$j('.ancr-cclass').hide();
		$j('.ancr-cpicker').fadeIn();
	}
}

function ancr_loc_page_change( obj ){
	var sibs = obj.siblings( '.loc-operator, .loc-value' );
		
	if( obj.children().filter(':selected').attr( 'data-param' ) == '1' ){
		sibs.show();
	}else{
		sibs.hide();
	}
}

function ancr_manualcode(){
	if($j('#ancr_position').val() == 'manual'){
		$j('.ancr-manualcode').fadeIn();
	}else{
		$j('.ancr-manualcode').hide();
	}
}

function ancr_async(u){
	var a=document.createElement("script");a.type="text/javascript";a.async=true;a.src=u;var s=document.getElementsByTagName("script")[0];s.parentNode.insertBefore(a,s);
}