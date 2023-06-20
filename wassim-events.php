<?php

/**
*Plugin Name: Wassim Events
*Plugin URI: https://wordpress.org/mv-events
*Description: My plugin's description
*Version: 1.0
*Requires at least: 5.6
*Author: Wassim Jelleli
*Author URI: https://www.linkedin.com/in/wassim-jelleli/
*Text Domain: wassim-events
*Domain Path: /languages
*/

if ( ! defined ( 'ABSPATH' ) ) {
    exit;
}

if( ! class_exists( 'Wassim_Events' ) ) {

    class Wassim_Events {

        public function __construct() {

            $this->define_constants();
            require_once( W_EVENTS_PATH . 'cpt/class.wassim-events-cpt.php' );
            $wassim_events_cpt = new Wassim_Events_Post_Type();
            add_filter( 'theme_page_templates', array( $this, 'my_template_register' ), 10, 3 );
            add_filter( 'template_include', array( $this, 'load_template' ), 999 );
            add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
            add_action( 'wp_ajax_filter_post', array( $this, 'filter_posts' ) );
            add_action( 'wp_ajax_nopriv_filter_post', array( $this, 'filter_posts' ) );
        }

        public function define_constants() {

            define( 'W_EVENTS_PATH', plugin_dir_path( __FILE__ ) );
            define( 'W_EVENTS_URL', plugin_dir_url( __FILE__ ) );
            define( 'W_EVENTS_VERSION', '1.0.0' );
        }

        public function my_templates_array() {

            $temps = [];
            $temps['events.php'] = 'Events Template';
            return $temps;
        }

        public function my_template_register( $page_templates, $theme, $post ) {

            $templates = $this->my_templates_array();
            foreach( $templates as $tk => $tv ) {
                $page_templates[$tk] = $tv;
            }
            return $page_templates; 
        }

        public function load_template( $template ) {

            global $post, $wp_query, $wpdb;
            $page_temp_slug = get_page_template_slug( $post->ID );
            $templates = $this->my_templates_array();
            if( isset( $templates[$page_temp_slug] ) ) {
                $template = W_EVENTS_PATH . 'templates/' .  $page_temp_slug;
            }
            return $template;
        }

        public function enqueue_scripts() {

            wp_enqueue_script( 'events-ajax-script', W_EVENTS_URL . 'assets/js/script.js', array( 'jquery' ), W_EVENTS_VERSION, true );
            wp_localize_script( 'events-ajax-script', 'VARS', array(
                'ajax_url' => admin_url( 'admin-ajax.php' )
            ) );
        }

        public function filter_posts() {
            $today = date("Y-m-d");
            $args = array(
                'post_type' => 'events',
                'post_status' => 'publish',
                'meta_key' => 'wassim_events_sdate',
                'orderby' => 'meta_value',
                'order' => 'ASC',
                'meta_query' => array(
                    array(
                        'key' => 'wassim_events_sdate',
                        'compare' => '>=',
                        'value' => $today,
                        'type' => 'DATE'
                    )
                )
            );

            $type = $_POST['cat'];
            if( ! empty( $type ) ) {
                $args['tax_query'][] = array(
                    'taxonomy' => 'event_cat',
                    'field' => 'slug',
                    'terms' => $type
                );
            }
            $events = new WP_Query( $args);
            ?>

                <?php if ( $events->have_posts() ) :
                    while ( $events->have_posts() ) : $events->the_post();
                    $start_date = get_post_meta( get_the_ID(), 'wassim_events_sdate', true );
                    $end_date = get_post_meta( get_the_ID(), 'wassim_events_edate', true );
                ?>
                <article class="event_block">
                    <?php if( has_post_thumbnail() ) {
                        the_post_thumbnail( array( 230, 230 ) );
                    }
                    ?>
                    <h3><?php the_title(); ?></h3>
                    <?php
                        $terms_list = get_the_terms( get_the_ID(), 'event_cat' );
                        echo join( ', ', wp_list_pluck( $terms_list, 'name' ) );
                    ?>
                    <ul class="dates">
                        <li><b>Start date:</b> <?php echo $start_date; ?></li>
                        <li><b>End date:</b> <?php echo $end_date; ?></li>
                    </ul>
                    <?php the_content(); ?>
                </article>
                <?php
                endwhile;
                wp_reset_postdata();
            endif; wp_die();

        }

        public static function activate() {
            update_option( 'rewrite_rules', '' );
        }

        public static function deactivate() {
            flush_rewrite_rules();
            unregister_post_type( 'events' );
        }

        public static function uninstall() {
            
        }
    }
}

if( class_exists( 'Wassim_Events' ) ) {

    register_activation_hook( __FILE__, array( 'Wassim_Events', 'activate' ) );
    register_deactivation_hook( __FILE__, array( 'Wassim_Events', 'deactivate' ) );
    register_uninstall_hook( __FILE__, array( 'Wassim_Events', 'uninstall' ) );

    $wassim_events = new Wassim_Events();
}

?>