<?php
/**
 * Plugin Name:       Typography WP
 * Plugin URI:        http://famethemes.com/
 * Description:
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

add_theme_support( 'typography_wp', array() );

function typography_wp_customize_register( $wp_customize ){

    require_once TYPOGRAPHY_WP_PATH.'inc/customize-control.php';
    $wp_customize->register_control_type( 'WP_Customize_Typography_WP_Control' );

    $wp_customize->add_section( 'test' ,
        array(
            //'priority'    => 3,
            'title'       => esc_html__( 'Section Test', 'onepress' ),
            'description' => '',
            //'panel'       => 'onepress_contact',
        )
    );

    // Show Content
    $wp_customize->add_setting( 'test',
        array(
            'sanitize_callback' => '',
            'default'           => '',
        )
    );
    $wp_customize->add_control( new WP_Customize_Typography_WP_Control(
            $wp_customize,
            'test',
            array(
                'label' 		=> esc_html__('Test', 'onepress-plus'),
                'section' 		=> 'test',
                'priority'      => 100,
            )
        )
    );
}
add_action( 'customize_register', 'typography_wp_customize_register' );
