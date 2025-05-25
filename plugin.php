<?php
/**
 * Plugin Name: APCORN Gallery
 * Plugin URI: https://apcorn.com
 * Author: Saber Hassen Rabbani
 * Author URI: https://apcorn.com
 * Text Domain: apcorn_gallery
 * Domain Path: /languages
 */

// load textdomain
function apcorn_gallery_init() {
    load_plugin_textdomain( 'apcorn_gallery', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'apcorn_gallery_init' );


// Enqueue media uploader and custom script
function apcorn_gallery_enqueue_scripts($hook) {
    global $post;
    if ( $hook == 'post-new.php' || $hook == 'post.php' ) {
        wp_enqueue_media();
        wp_enqueue_script( 'apcorn-gallery-js', plugin_dir_url(__FILE__) . 'assets/js/apcorn-gallery.js', array( 'jquery' ), '1.0', true );
        wp_enqueue_style( 'apcorn-gallery-css', plugin_dir_url(__FILE__) . 'assets/css/apcorn-gallery.css' );
    }
}
add_action( 'admin_enqueue_scripts', 'apcorn_gallery_enqueue_scripts' );

// Gallery Frontend Assets
function apcorn_gallery_enqueue_frontend_styles() {
    wp_enqueue_style( 'glightbox-css', plugin_dir_url(__FILE__) . 'assets/css/glightbox.min.css' );
    wp_enqueue_style( 'apcorn-gallery-css', plugin_dir_url(__FILE__) . 'assets/css/apcorn-gallery.css' );
}
add_action( 'wp_enqueue_scripts', 'apcorn_gallery_enqueue_frontend_styles' );

function apcorn_gallery_enqueue_frontend_scripts() {
    wp_enqueue_script( 'glightbox-js', plugin_dir_url(__FILE__) . 'assets/js/glightbox.min.js', [], '3.2.0', true );
    wp_enqueue_script( 'apcorn-gallery-init-js', plugin_dir_url(__FILE__) . 'assets/js/apcorn-gallery-init.js', [ 'glightbox-js' ], '1.0', true );
}
add_action( 'wp_enqueue_scripts', 'apcorn_gallery_enqueue_frontend_scripts' );



// Add meta-box
function apcorn_gallery_add_meta_box() {
    add_meta_box(
        'apcorn_gallery_meta',
        __( 'Gallery Images', 'apcorn_gallery' ),
        'apcorn_gallery_meta_box_callback',
        'boat', // Change 'post' to your custom post type if needed
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'apcorn_gallery_add_meta_box' );


// Meta-box callback
function apcorn_gallery_meta_box_callback( $post ) {
    wp_nonce_field( 'apcorn_gallery_save_meta_box', 'apcorn_gallery_meta_box_nonce' );
    $gallery = get_post_meta( $post->ID, '_apcorn_gallery', true );
    ?>
    <div id="apcorn-gallery-wrapper">
        <button type="button" class="button apcorn-add-gallery"><?php _e( 'Add/Edit Gallery', 'apcorn_gallery' ); ?></button>
        <ul class="apcorn-gallery-preview">
            <?php
            if ( $gallery ) {
                $ids = explode(',', $gallery);
                foreach ( $ids as $id ) {
                    $img = wp_get_attachment_image( $id, 'thumbnail' );
                    echo "<li data-id='$id'>$img<span class='remove'>×</span></li>";
                }
            }
            ?>
        </ul>
        <input type="hidden" name="apcorn_gallery" id="apcorn_gallery_input" value="<?php echo esc_attr( $gallery ); ?>">
    </div>
    <?php
}

// Save meta-box
function apcorn_gallery_save_meta_box( $post_id ) {
    if ( ! isset( $_POST['apcorn_gallery_meta_box_nonce'] ) ||
        ! wp_verify_nonce( $_POST['apcorn_gallery_meta_box_nonce'], 'apcorn_gallery_save_meta_box' ) ) {
        return;
    }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

    if ( isset( $_POST['apcorn_gallery'] ) ) {
        update_post_meta( $post_id, '_apcorn_gallery', sanitize_text_field( $_POST['apcorn_gallery'] ) );
    }
}
add_action( 'save_post', 'apcorn_gallery_save_meta_box' );

// Elementor widget init
function apcorn_gallery_elementor_widgets() {
    // Check if Elementor is active
    if ( did_action( 'elementor/loaded' ) ) {
        require_once plugin_dir_path( __FILE__ ) . 'widgets/class-apcorn-gallery-widget.php';
        \Elementor\Plugin::instance()->widgets_manager->register( new \APCORN_Gallery_Widget() );
    }
}
add_action( 'elementor/widgets/register', 'apcorn_gallery_elementor_widgets' );

