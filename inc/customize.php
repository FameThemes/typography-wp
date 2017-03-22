<?php

function typography_wp_customize_register( $wp_customize ){

    $controls = typography_wp_get_customize_controls();
    if ( ! is_array( $controls ) ) {
        return false;
    }

    require_once TYPOGRAPHY_WP_PATH.'inc/customize-control.php';
    $wp_customize->register_control_type( 'WP_Customize_Typography_WP_Control' );

    $panel_settings = apply_filters( 'typography_wp_panel_settings', array(
        'priority'    => 35,
        'title'       => esc_html__( 'Typography', 'typography_wp' ),
        'description' => '',
    ) );

    $wp_customize->add_panel( 'typography_wp' , $panel_settings );

    $sections_flag = array();

    foreach ( $controls as $control ) {
        $control = wp_parse_args( $control, array(
            'id'        => '',
            'label'     => '',
            'description'     => '',
            'selector'   => '',
            'section'   => '',
            'priority'  => '',
            'fields'    => ''
        ) );
        if ( $control['id'] ) {

            $section_id = sanitize_title( $control['section'] );
            $section_label = $control['section'] ;
            if ( ! $section_id ) {
                $section_id = sanitize_title( $control['id'] );
                $section_label = $control['label'];
            }

            if ( $section_id && ! isset( $sections_flag[ $section_id ] ) ) {
                $wp_customize->add_section( $section_id,
                    array(
                        'title' => $section_label,
                        'description' => '',
                        'panel' => 'typography_wp',
                    )
                );

                $sections_flag[ $section_id ] = true;
            }

            if ( ! is_array( $control['fields'] ) ) {
                $control['fields'] = array();
            }

            if ( isset( $control['fields']['family'] ) && $control['fields']['family'] ) {
                if ( ! $control['fields']['fontId'] || ! $control['fields']['fontId'] ) {
                    $control['fields']['fontId'] = sanitize_title(  $control['fields']['family'] );
                }
            }

            $wp_customize->add_setting( $control['id'],
                array(
                    'sanitize_callback' => 'typography_wp_sanitize_typography',
                    'default' =>  $control['fields'],
                    'transport' => 'postMessage',
                )
            );

            $wp_customize->add_control(new WP_Customize_Typography_WP_Control(
                    $wp_customize,
                    $control['id'],
                    array(
                        'label' => $control['label'],
                        'section' => $section_id,
                        'priority' => $control['priority'],
                        'description' => $control['description'],
                        'fields' => $control['fields']
                    )
                )
            );
        }
    }

}
add_action( 'customize_register', 'typography_wp_customize_register' );



function typography_wp_customize_print_styles(){
    ?>
    <style type="text/css">
        .font-subsets, .typography-wp-settings option {
            text-transform: capitalize;
        }
    </style>
    <?php
}
add_action( 'customize_controls_print_styles', 'typography_wp_customize_print_styles' );

