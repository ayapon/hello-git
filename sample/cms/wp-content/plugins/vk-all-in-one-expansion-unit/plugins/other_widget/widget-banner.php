<?php
namespace Vektor\ExUnit\Package\Widgets;


add_action('widgets_init', 'Vektor\ExUnit\Package\Widgets\register_banner');
function register_banner(){
    return register_widget("Vektor\ExUnit\Package\Widgets\WidgetBanner");
}


class WidgetBanner extends \WP_Widget
{

    function __construct()
    {
        $widget_name = vkExUnit_get_short_name().'_'.__( 'Banner', 'vkExUnit' );

        parent::__construct(
            'vkExUnit_banner',
            $widget_name,
            array(
                'description' => sprintf( __( 'You can easily set up a banner simply by registering images and link destinations.', 'vkExUnit' ),vkExUnit_get_little_short_name() ),
                )
        );
    }


    public function widget( $args, $instance )
    {
        $instance = self::default_option( $instance );
        $image = null;
        if (is_numeric($instance['id'])) {
            $image = wp_get_attachment_image_src( $instance['id'], 'full' );
            $alt = ( $instance['alt'] ) ? esc_attr($instance['alt']):'';
        }
        if (!$image) return;
        echo $args['before_widget'];
        if ($instance['href']) echo '<a href="'. esc_url( $instance['href'] ) .'"'. (($instance['blank'])? ' target="_blank"' : '') .' >';
        echo '<img src="'.$image[0].'" alt="'.$alt.'" />';
        if ($instance['href']) echo '</a>';
        echo $args['after_widget'];

        return;
    }


    public function update( $new_instance, $old_instance )
    {
        $instance['id']    = $new_instance['id'];
        $instance['href']  = $new_instance['href'];
        $instance['alt']  = $new_instance['alt'];
        $instance['blank'] = (isset($new_instance['blank']) && $new_instance['blank'] == 'true');
        return $new_instance;
    }


    public static function default_option( $instance = array() )
    {
        $defaults = array(
            'id'    => Null,
            'href'  => '',
            'blank' => false,
            'alt' => ''
        );
        return wp_parse_args( $instance, $defaults );
    }


    public function form( $instance )
    {
        $instance = self::default_option( $instance );
        $image = null;
        if (is_numeric($instance['id'])) {
            $image = wp_get_attachment_image_src( $instance['id'], 'full' );
        }
        ?>
<div class="vkExUnit_banner_area" style="padding: 2em 0;">
<div class="_display" style="height:auto">
    <?php if ($image): ?>
        <img src="<?php echo $image[0]; ?>" style="width:100%;height:auto;" />
    <?php endif; ?>
</div>
<button class="button button-default button-block" style="display:block;width:100%;text-align: center; margin:4px 0;" onclick="javascript:vkEx_banner_addiditional(this);return false;"><?php _e('Set image', 'vkExUnit'); ?></button>
<div class="_form" style="line-height: 2em">
    <input type="hidden" class="__id" name="<?php echo $this->get_field_name( 'id' ); ?>" value="<?php echo esc_attr( $instance['id'] ); ?>" />
    <label>URL : <input type="text" name="<?php echo $this->get_field_name( 'href' ); ?>" style="width: 100%" value="<?php echo esc_attr( $instance['href'] ); ?>" /></label><br/>
    <label><input type="checkbox" name="<?php echo $this->get_field_name( 'blank' ); ?>" value="true" <?php if ($instance['blank']) echo 'checked'; ?> /> <?php _e('Open link new tab.', 'vkExUnit'); ?></label><br/>
    <label><?php _e('Alternative text', 'vkExUnit'); ?> : <input type="text" name="<?php echo $this->get_field_name( 'alt' ); ?>" style="width: 100%" value="<?php echo esc_attr( $instance['alt'] ); ?>" /></label><br/>
</div>
</div>
<script type="text/javascript">
if(vkEx_banner_addiditional == undefined){
var vkEx_banner_addiditional = function(e){
    var d=jQuery(e).parent().children("._display");
    var w=jQuery(e).parent().children("._form").children('.__id')[0];
    var u=wp.media({library:{type:'image'},multiple:false}).on('select', function(e){
        u.state().get('selection').each(function(f){ d.children().remove();d.append(jQuery('<img style="width:100%;mheight:auto">').attr('src',f.toJSON().url));jQuery(w).val(f.toJSON().id).change(); });
    });
    u.open();
};
}
</script>
<?php
        return $instance;
    }
}
