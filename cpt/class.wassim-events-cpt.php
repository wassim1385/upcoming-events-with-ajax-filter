<?php

if( ! class_exists( 'Wassim_Events_Post_Type' ) ) {

    class Wassim_Events_Post_Type {

        public function __construct() {

            add_action( 'init', array( $this, 'create_post_type' ) );
            add_action( 'init', array( $this, 'register_taxonomies' ) );
            add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
            add_action( 'save_post', array( $this, 'save_post' ) );
        }

        public function create_post_type() {

            register_post_type(
                'events',
                array(
                    'label' => 'Event',
                    'description'   => 'Events',
                    'labels' => array(
                        'name'  => 'Events',
                        'singular_name' => 'Event'
                    ),
                    'public'    => true,
                    'supports'  => array( 'title', 'editor', 'thumbnail' ),
                    'hierarchical'  => false,
                    'show_ui'   => true,
                    'show_in_menu'  => true,
                    'menu_position' => 5,
                    'show_in_admin_bar' => true,
                    'show_in_nav_menus' => true,
                    'can_export'    => true,
                    'has_archive'   => true,
                    'exclude_from_search'   => false,
                    'publicly_queryable'    => true,
                    'show_in_rest'  => false, //New Gutenberg Editor
                    'menu_icon' => 'dashicons-calendar-alt'
                )
            );
        }

        public function register_taxonomies() {

            register_taxonomy(
                'event_cat',
                'events',
                array(
                    'hierarchical' => true,
                    'labels' => array(
                        'name' => 'categories',
                        'singular_name' => 'Category',
                        'menu_name' => 'categories',
                    ),
                'show_ui' => true,
                'show_admin_column' => true,
                )
            );
        }

        public function add_meta_boxes() {

            add_meta_box(
            'wassim_events_meta_box',
            'Events details',
            array( $this, 'add_inner_meta_boxes' ),
            'events',
            'normal',
            'high'
            );
        }

        public function add_inner_meta_boxes( $post ) {

            require_once( W_EVENTS_PATH . 'views/wassim-events_metaboxes.php' );
        }

        public function save_post( $post_id ) {

            if( isset( $_POST['wassim_events_nonce'] ) ){
                if( ! wp_verify_nonce( $_POST['wassim_events_nonce'], 'wassim_events_nonce' ) ){
                    return;
                }
            }

            if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ){
                return;
            }

            if( isset( $_POST['post_type'] ) && $_POST['post_type'] === 'events' ){
                if( ! current_user_can( 'edit_page', $post_id ) ){
                    return;
                }elseif( ! current_user_can( 'edit_post', $post_id ) ){
                    return;
                }
            }

            if( isset( $_POST['action'] ) && $_POST['action'] == 'editpost' ) {

                $old_event_sdate = get_post_meta( $post_id, 'wassim_events_sdate', true );
                $new_event_sdate = sanitize_text_field( $_POST['wassim_events_sdate'] );
                $old_event_edate = get_post_meta( $post_id, 'wassim_events_edate', true );
                $new_event_edate = sanitize_text_field( $_POST['wassim_events_edate'] );

                if( empty( $new_event_sdate ) ) {
                    update_post_meta( $post_id, 'wassim_events_sdate', date("Y/m/d") );
                } else {
                    update_post_meta( $post_id, 'wassim_events_sdate', $new_event_sdate, $old_event_sdate );
                }
                if( empty( $new_event_edate ) ) {
                    update_post_meta( $post_id, 'wassim_events_edate', date("Y/m/d") );
                } else {
                    update_post_meta( $post_id, 'wassim_events_edate', $new_event_edate, $old_event_edate );
                }
            }
        }
    }

}

?>