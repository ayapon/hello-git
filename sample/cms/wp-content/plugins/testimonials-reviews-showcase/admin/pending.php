<?php include_once 'setting.php'; ?>
<?php
if (isset($_GET['approve'])) :

    $id = sanitize_text_field(trim($_GET['approve']));
    wp_publish_post($id);

endif;

$plugin = new SmartcatTestimonialsPlugin();
$posts = $plugin->get_pending_posts();

if ($posts) : ?>

    <div class="width70 left">
        <table class="widefat">
            <thead>
                <tr>
                    <th>From</th>
                    <th>Testimonial</th>            
                    <th>Status</th>
                    <th>Rating</th>            
                    <th>Edit</th>
                    <th>Approve</th>
                </tr>
            </thead>
            <tbody>

                <?php foreach ($posts as $post) : ?>

                    <tr>
                        <td><?php echo $post->post_title; ?></td>
                        <td><?php echo wp_trim_words($post->post_content, 25); ?></td>
                        <td><?php echo $post->post_status ?></td>
                        <td>
                            <?php $rating = (int) get_post_meta($post->ID, 'testimonial_rating', TRUE); ?>
                            <?php echo $rating; ?> / 5
                        </td>            
                        <td><a href="<?php echo get_edit_post_link($post->ID) ?>">Edit</a></td>
                        <td><a href="<?php echo admin_url('edit.php?post_type=testimonial&page=smartcat_testimonials_pending'); ?>&approve=<?php echo $post->ID ?>">Approve</a></td>

                    </tr>

                <?php endforeach; ?>


            </tbody>
        </table>

    </div>

<?php else : 
    
    echo 'No Pending Testimonials/Reviews';

endif; ?>
