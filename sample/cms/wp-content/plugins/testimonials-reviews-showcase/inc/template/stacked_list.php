<?php
/*
 * This is the stacked list template
 * June 8th, 2015
 * @author bilal hassan <bilal@smartcat.ca>
 * 
 */
$args = $this->sc_get_args( $group, $limit );
$members = new WP_Query( $args );
?>
<div id="smartcat-testimonials" class="smartcat_<?php echo $template == '' ? $this->options[ 'template' ] : $template; ?>_template">

    <?php
    if ( $members->have_posts() ) {
        while ( $members->have_posts() ) {
            $members->the_post(); ?>
    
            <div class="smartcat-testimonial">

                <?php if( $images == 'yes' || ( empty( $images ) && $this->options['use_images'] == 'yes' ) ) : ?>
                    <div class="sc-col-sm-4 smartcat-testimonial-image <?php echo $this->options['image_style'] . ' ' . $this->options['image_size'] . ' ' . $this->options['image_greysacle'];?>" >
                        <?php if ( has_post_thumbnail() ) : ?>
                            <?php echo the_post_thumbnail( 'medium' ); ?>
                        <?php endif ?>
                    </div>
                <?php endif; ?>
                
                <div class="sc-col-sm-8">
                    
                    <div class="<?php echo !empty( $font ) ? $font : '' ?> smartcat_testimonials_content <?php echo $this->options['italic_content']; ?>">
                        <span class="icon-quote-left"></span><?php echo wp_trim_words( get_the_content(), $this->options['word_count'] ); ?><span class="icon-quote-right"></span>
                    </div>                

                    <?php if( $date == 'yes' || ( empty( $date ) && $this->options['show_date'] == 'yes' ) ) : ?>
                    <div class="smartat_testimonial_date">
                        <?php echo the_date(); ?>
                    </div>
                    <?php endif; ?>                
                    
                    <div itemprop="name" class="smartcat_testimonial_title">                          
                        <?php the_title() ?>
                    </div>

                    <div itemprop="title" class="smartcat_testimonial_subtitle">
                        <?php $subtitle = get_post_meta(get_the_ID(), 'testimonial_subtitle', TRUE ); ?>
                        <?php $subtitle_url = get_post_meta( get_the_ID(), 'testimonial_subtitle_url', TRUE ); ?> 

                        <?php if( !empty( $subtitle_url ) ) : ?>
                            <?php $this->smartcat_testimonials_href_this( $subtitle, $subtitle_url, get_post_meta(get_the_ID(), 'testimonial_subtitle_target', TRUE ) ); ?>
                        <?php else : ?>
                            <?php echo $subtitle; ?>
                        <?php endif; ?>


                    </div>

                    <div itemprop="rating" class="smartcat_testimonial_rating">
                        <?php $rating = (int)get_post_meta( get_the_ID(), 'testimonial_rating', TRUE ); ?>
                        <?php $unrating = 5 - $rating; ?>

                        <?php for( $i = 0; $i < $rating; $i++ ) : ?>
                            <span class="testimonial-rating icon-<?php echo $this->options['ratings_icon']; ?>"></span>
                        <?php endfor; ?>
                        <?php for( $i = 0; $i < $unrating; $i++ ) : ?>
                            <span class="testimonial-rating icon-<?php echo $this->options['ratings_icon']; ?>-o"></span>
                        <?php endfor; ?>
                    </div>   
                    
                </div>
                

                
                

               

            </div>
            <?php
        }
    } else {
        echo 'There are no reviews to display';
    }
    wp_reset_postdata();
    ?>
    
</div>
<div class="clear"></div>