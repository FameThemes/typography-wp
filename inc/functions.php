<?php

/**
 * Get typo default fields
 *
 * @return array
 */
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

    // Color
    $value['textColor'] = sanitize_hex_color( $value['textColor'] );

    // Unit
    if ( ! in_array( $value['unit'], array( 'px', '%', 'rem', 'em', 'pt' ) ) ) {
        $value['unit'] = 'px';
    }

    if ( ! is_array( $value['subsets'] ) ) {
        $value['subsets'] = array();
    }

    foreach( $value['subsets'] as $k => $v ) {
        $value['subsets'][ $k ] = sanitize_text_field( $v );
    }

    if ( ! $value['fontId'] ) {
        $value['fontId'] = sanitize_title( $value['family'] );
    }

    $value['variant'] = sanitize_text_field( $value['variant'] );
    $value['fontWeight'] = sanitize_text_field( $value['fontWeight'] );

    if ( floatval( $value['fontSize'] ) != $value[ 'fontSize' ] ) {
        $value['fontSize'] = '';
    }

    if ( floatval( $value['lineHeight'] ) != $value[ 'lineHeight' ] ) {
        $value['lineHeight'] = '';
    }

    if ( floatval( $value['letterSpacing'] ) != $value[ 'letterSpacing' ] ) {
        $value['letterSpacing'] = '';
    }

    return $value;
}

/**
 * Get cusstomizer control by theme support `typography_wp`
 *
 * @return mixed|void
 */
function typography_wp_get_customize_controls(){
    $controls = get_theme_support( 'typography_wp' );
    return apply_filters( 'typography_wp_get_customize_controls', $controls );
}