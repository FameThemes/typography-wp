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

add_theme_support( 'typography_wp',
    array(
        'id' => 'body_typo',
        'label' => 'Body Typography',
        'selector'       => 'body',
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
            'unit'           => 'px',
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
            'unit'           => 'px',
        )
    )

);


require_once TYPOGRAPHY_WP_PATH.'inc/class-render.php';


function typography_wp_default_fields(){
    return array(
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
        'unit'           => 'px',
    );
}

/**
 * Sanitize typography fields
 *
 * @param $value
 * @return bool|mixed|string|void
 */
function typography_wp_sanitize_typography( $value ){

    if( is_string( $value ) ) {
        $value = json_decode( $value, true );
    }

    if ( ! is_array( $value ) ) {
        return false;
    }

    $value = wp_parse_args( $value, typography_wp_default_fields() );

   // foreach( $value as $k => $v ){
       // $value[ $k ] =  sanitize_text_field( $v );
   // }

    //$value = array_filter( $value );
    return $value;
}

function typography_wp_customize_register( $wp_customize ){

    require_once TYPOGRAPHY_WP_PATH.'inc/customize-control.php';
    $wp_customize->register_control_type( 'WP_Customize_Typography_WP_Control' );

    $wp_customize->add_panel( 'typography_wp' ,
        array(
            'priority'    => 35,
            'title'       => esc_html__( 'Typography', 'typography_wp' ),
            'description' => '',
            //'panel'       => 'onepress_contact',
        )
    );

    $controls = get_theme_support( 'typography_wp' );
    if ( ! is_array( $controls ) ) {
        return false;
    }

    foreach ( $controls as $control ) {
        $control = wp_parse_args( $control, array(
            'id'        => '',
            'label'     => '',
            'description'     => '',
            'element'   => '',
            'priority'  => '',
            'fields'    => ''
        ) );
        if ( $control['id'] ) {

            $wp_customize->add_section( $control['id'] ,
                array(
                    //'priority'    => 3,
                    'title'       => $control['label'],
                    'description' => '',
                    'panel'       => 'typography_wp',
                )
            );

            $wp_customize->add_setting( $control['id'],
                array(
                    'sanitize_callback' => 'typography_wp_sanitize_typography',
                    'default' => '',
                )
            );
            $wp_customize->add_control(new WP_Customize_Typography_WP_Control(
                    $wp_customize,
                    $control['id'],
                    array(
                        'label' => $control['label'],
                        'section' => $control['id'],
                        'priority' => $control['priority'],
                        'description' => $control['description'],
                        'element' => $control['element'],
                        'fields' => $control['fields']
                    )
                )
            );
        }
    }


}
add_action( 'customize_register', 'typography_wp_customize_register' );
