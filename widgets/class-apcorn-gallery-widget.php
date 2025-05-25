<?php
use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class APCORN_Gallery_Widget extends Widget_Base {

    public function get_name() {
        return 'apcorn_gallery';
    }

    public function get_title() {
        return __( 'APCORN Gallery', 'apcorn_gallery' );
    }

    public function get_icon() {
        return 'eicon-gallery-grid';
    }

    public function get_categories() {
        return [ 'general' ];
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_content',
            [
                'label' => __( 'Gallery Settings', 'apcorn_gallery' ),
            ]
        );

        $this->add_control(
            'gallery_source',
            [
                'label' => __( 'Post Source', 'apcorn_gallery' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'current' => __( 'Current Post', 'apcorn_gallery' ),
                    'custom'  => __( 'Custom Post ID', 'apcorn_gallery' ),
                ],
                'default' => 'current',
            ]
        );

        $this->add_control(
            'custom_post_id',
            [
                'label' => __( 'Custom Post ID', 'apcorn_gallery' ),
                'type' => Controls_Manager::NUMBER,
                'condition' => [
                    'gallery_source' => 'custom',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $post_id = ( $settings['gallery_source'] === 'custom' && !empty($settings['custom_post_id']) )
            ? $settings['custom_post_id']
            : get_the_ID();

        $gallery = get_post_meta( $post_id, '_apcorn_gallery', true );

        if ( ! $gallery ) {
            echo '<p>' . __( 'No gallery found.', 'apcorn_gallery' ) . '</p>';
            return;
        }

        $ids = explode(',', $gallery);

        echo '<div class="apcorn-gallery-widget glightbox-gallery">';
        foreach ( $ids as $id ) {
            $url = wp_get_attachment_url($id);
            $thumb = wp_get_attachment_image($id, 'medium', false, ['class' => 'gallery-img']);
            echo '<a href="'.esc_url($url).'" class="glightbox" data-gallery="apcorn-gallery">'.$thumb.'</a>';
        }
        echo '</div>';
    }


    public function get_style_depends() {
        return [ 'apcorn-gallery-css', 'glightbox-css' ];
    }

    public function get_script_depends() {
        return [ 'glightbox-js', 'apcorn-gallery-init-js' ];
    }


}
