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

define( 'TYPOGRAPHY_WP_URL', trailingslashit( plugins_url('', __FILE__) ) );
define( 'TYPOGRAPHY_WP_PATH', trailingslashit( plugin_dir_path(__FILE__) ) );

add_theme_support( 'typography_wp',
    array(
        'id' => 'body_typo',
        'label' => 'Body Typography',
        'selector'       => 'body #page',
        'priority'      => 100,
        'fields' => array(
            'family'         => '',
            'category'       => '',
            'fontId'         => '',
            'fontType'       => '',
            'subsets'        => '',
            'variant'        => '',
            'textColor'      => '',
            'fontStyle'      => '',
            'fontWeight'     => '',
            'fontSize'       => '',
            'lineHeight'     => '',
            'letterSpacing'  => '',
            'textTransform'  => '',
            'textDecoration' => '',
        )
    ),

    array(
        'id' => 'heading_typo',
        'label' => 'Heading Typography',
        'selector'       => 'h1,h2,h3,h4,h5,h6',
        'priority'      => 15,
        'fields' => array(
            'family'         => '',
            'category'       => '',
            'fontId'         => '',
            'fontType'       => '',
            'subsets'        => '',
            'variant'        => '',
            'textColor'      => '',
            'fontStyle'      => '',
            'fontWeight'     => '',
            'fontSize'       => '',
            'lineHeight'     => '',
            'letterSpacing'  => '',
            'textTransform'  => '',
            'textDecoration' => '',
        )
    ),

    array(
        'id' => 'test__heading_typo',
        'label' => 'Test Default Typography',
        'selector'       => 'body .div.test',
        'priority'      => 15,
        'description'      => 'Here is the description.',
        'fields' => array(
            'family'         => 'Open Sans', // remove key if don't want to use
            'category'       => '',
            'fontId'         => '',
            'fontType'       => '',
            'subsets'        => array(
                 'greek' => 'greek',
                 'vietnamese' => 'vietnamese',
            ),
            'variant'        => '700italic',
            //'textColor'      => '#888888',
            'fontStyle'      => '',
            //'fontWeight'     => 'bold',
            //'fontSize'       => '17',
            //'lineHeight'     => '26',
            //'letterSpacing'  => '',
            //'textTransform'  => 'uppercase',
            //'textDecoration' => '',
        )
    )

);


/**
 * Init functions
 */
function typography_wp_init(){
    require_once TYPOGRAPHY_WP_PATH.'inc/functions.php';
    require_once TYPOGRAPHY_WP_PATH.'inc/class-render.php';
    require_once TYPOGRAPHY_WP_PATH.'inc/customize.php';
}
add_action( 'plugins_loaded', 'typography_wp_init' );



