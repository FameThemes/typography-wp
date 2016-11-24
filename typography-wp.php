<?php
/**
 * Plugin Name:       Typography WP
 * Plugin URI:        http://famethemes.com/
 * Description:       Easy to add Typography customize for your site.
 * Version:           1.0.0
 * Author:            famethemes,shrimp2t
 * Author URI:        http://famethemes.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       typography_wp
 * Domain Path:       /languages
 */

/**
 * Check maybe plugin loaded somewhere.
 */
if ( ! function_exists( 'typography_wp_init' ) ) {
    /**
     * Init functions
     */
    function typography_wp_init()
    {
        if ( ! defined( 'TYPOGRAPHY_WP_URL' ) ) {
            define('TYPOGRAPHY_WP_URL', trailingslashit(plugins_url('', __FILE__)));
        }

        if ( ! defined( 'TYPOGRAPHY_WP_URL' ) ) {
            define('TYPOGRAPHY_WP_PATH', trailingslashit(plugin_dir_path(__FILE__)));
        }

        require_once TYPOGRAPHY_WP_PATH . 'inc/functions.php';
        require_once TYPOGRAPHY_WP_PATH . 'inc/class-render.php';
        require_once TYPOGRAPHY_WP_PATH . 'inc/customize.php';
    }

    add_action( 'after_setup_theme', 'typography_wp_init');
}



