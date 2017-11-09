/**
 * 
 * @author Smartcat June 4, 2015
 */
jQuery( document ).ready(function($){
    
    
    // ---------- FORM
    $('.smartcat_testimonials_form .testimonial-rating-icon').hover( function() {
        
        
        
    });
    
    $("#smartcat-testimonials.smartcat_slider_template,#smartcat-testimonials.smartcat_slider2_template,#smartcat-testimonials.smartcat_slider3_template").owlCarousel({
        singleItem      : true,
        transitionStyle : "fade",
        autoPlay        : false
        
    });
    
    
    $("#smartcat-testimonials.smartcat_boxed_template").owlCarousel({
        singleItem      :   true,
        transitionStyle : "fade",
        autoPlay        : false
    });
    
    
    //
    
    var element = $('#smartcat-testimonials.smartcat_grid_template .smartcat-testimonial');
    var maxHeight = 0;
    
    element.each( function() {
        
        if( $(this).height() > maxHeight ) {
            maxHeight = $(this).height();
        }
        
    });
    element.height(maxHeight);
    
});
