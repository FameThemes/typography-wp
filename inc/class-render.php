<?php

class Typography_WP_Render {
    public $settings = array();
    public $google_fonts = array();
    function __construct(){
        add_action( 'wp_head', array( $this, 'head' ) );
    }

    function head(){

    }

    function get_controls(){
        $controls = get_theme_support( 'typography_wp' );
        if ( ! is_array( $controls ) ) {
            return false;
        }

        foreach ( $controls as $control ) {
            $control = wp_parse_args( $control, array(
                'id'        => '',
                'label'     => '',
                'description'     => '',
                'selector'   => '',
                'priority'  => '',
                'fields'    => ''
            ) );
            if ( $control['id'] ) {
                $this->settings[ $control['id'] ] = array(
                    'selector' => $control['selector'],
                    'data' => get_theme_mod( $control['id'], $control['fields'] ),
                );
            }
        }
    }

    function standard_font_name( $font_name, $category = '', $google_font = false ){
        if ( ! $font_name ) {
            return false;
        }
        $names = explode( ',', $font_name );
        foreach ( $names as $k => $v ) {
            if ( ! $v ) {
                unset( $names[ $k ] );
            } else {
                $v = trim( $v );
                if ( sanitize_title( $v ) != $v ) {
                    $v = '"'.$v.'"';
                }
                $names[ $k ] = $v;
            }
        }

        if ( $category ) {
            return join( ', ', $names ).', '.$category;
        } else {
            return join( ', ', $names );;
        }

    }

    function add_google_font( $font_name ){
        if ( ! isset(  $this->google_fonts[ $font_name ] ) ) {
            $this->google_fonts[ $font_name ] = array(
                'name' => $font_name,
                'subsets' => array(),
                'variants' => array(),
            );
        }
    }


    function add_google_font_variants( $font_name, $variants ){
        $this->add_google_font( $font_name );
        if ( ! $variants || empty( $variants ) ) {
            return false;
        }
        if ( is_array( $variants ) ) {
            foreach ( $variants as $s ) {
                $this->google_fonts[ $font_name ]['variants'][ $s ] = $s;
            }
        } else {
            $this->google_fonts[ $font_name ]['subsets'][ $variants ] = $variants;
        }
        return true;
    }

    function add_google_font_subsets( $font_name, $subsets ){
        $this->add_google_font( $font_name );
        if ( ! $subsets || empty( $subsets ) ) {
            return false;
        }
        if ( is_array( $subsets ) ) {
            foreach ( $subsets as $s ) {
                $this->google_fonts[ $font_name ]['subsets'][ $s ] = $s;
            }
        } else {
            $this->google_fonts[ $font_name ]['subsets'][ $subsets ] = $subsets;
        }
        return true;
    }

    function css( $fields ) {
        $fields = wp_parse_args( $fields, typography_wp_default_fields() );
        $css = '';
        if ( $fields['fontType'] == 'google' ) {
            $font_name = $this->standard_font_name( $fields['family'], $fields['category'], true );
            if ( $font_name ){
                $css .= 'font-family: '.$font_name.';';
            }
            $this->add_google_font_subsets( $fields['family'], $fields['subsets'] );
            $this->add_google_font_variants( $fields['variant'], $fields['variant'] );
        }
        
    }

    function render(){

    }
}

$GLOBALS['Typography_WP_Render'] = new Typography_WP_Render();