<?php

class Typography_WP_Render {
    public $settings = array();
    private $google_fonts = array();
    private $subsets = array(); // google font subsets
    public $css;
    private $is_ajax = false;
    private $customized = array();
    private $theme = '';
    function __construct(){
        add_action( 'wp_head', array( $this, 'head' ) );

        add_action( 'wp_ajax_typography_wp_render_css', array( $this, 'ajax' ) );
        add_action( 'wp_ajax_nopriv_typography_wp_render_css', array( $this, 'ajax' ) );
    }

    function get_theme_mod( $name, $default = null ) {
        //$theme_slug = get_option( 'stylesheet' );
        if ( $this->theme ) {
            $mods = get_option( "theme_mods_$this->theme " );

            if ( isset( $mods[$name] ) ) {
                /**
                 * Filters the theme modification, or 'theme_mod', value.
                 *
                 * The dynamic portion of the hook name, `$name`, refers to
                 * the key name of the modification array. For example,
                 * 'header_textcolor', 'header_image', and so on depending
                 * on the theme options.
                 *
                 * @since 2.2.0
                 *
                 * @param string $current_mod The value of the current theme modification.
                 */
                return apply_filters( "theme_mod_{$name}", $mods[$name] );
            }

            if ( is_string( $default ) )
                $default = sprintf( $default, get_template_directory_uri(), get_stylesheet_directory_uri() );

            /** This filter is documented in wp-includes/theme.php */
            return apply_filters( "theme_mod_{$name}", $default );

        } else {
            return get_theme_mod( $name, $default );
        }

    }

    function ajax(){
        if ( ! wp_verify_nonce( $_POST['nonce'], 'typography_wp' ) ) {
            die( 'security_check' );
        }
        $this->is_ajax = true;
        $this->customized = json_decode( wp_unslash( $_POST['customized'] ), true );
        $this->theme =  wp_unslash( $_POST['theme'] );
        $this->setup();
        wp_send_json_success( array( 'font'=> $this->get_google_font_url(), 'css' => $this->css ) );
    }

    function head(){
        $this->setup();
        $url = $this->get_google_font_url();
        if ( $url ) {
            echo '<link id="typography-wp-google-font" href="'.esc_url( $url ).'" rel="stylesheet"> ';
        }
        if ( $this->css ) {
            echo "\n".'<style id="typography-wp-style-inline-css" type="text/css">'."\n";
            echo $this->css;
            echo "\n</style>\n";
        }
    }

    function get_data( $mod, $default = null ) {
        if ( $this->is_ajax && isset( $this->customized[ $mod ] ) ) {
            return json_decode( $this->customized[ $mod ], true );
        } else {
            $data = $this->get_theme_mod( $mod, $default );
        }
        return $data;
    }

    function setup(){
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
                if ( $control['selector'] ) {
                    $data = $this->get_data( $control['id'], $control['fields'] );
                    $css = $this->css( $data );
                    if ( $css ) {
                        $this->css .= $control['selector']."\n{\n" .$css."\n}\n";
                    }
                }
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
            return join( ', ', $names );
        }

    }

    function add_google_font( $font_name ){
        if ( ! isset(  $this->google_fonts[ $font_name ] ) ) {
            $this->google_fonts[ $font_name ] = array(
                'name' => $font_name,
                'variants' => array(),
            );
        }
    }

    function sanitize_variant( $v ){
        if ( intval( $v ) > 0 ) {
            if ( strlen( $v ) > 4 ) {
                return substr( $v, 0, 4 );
            } else {
                return $v;
            }
        } else {
            return $v;
        }

    }

    function add_google_font_variants( $font_name, $variants ){
        $this->add_google_font( $font_name );
        if ( ! $variants || empty( $variants ) ) {
            return false;
        }
        if ( is_array( $variants ) ) {
            foreach ( $variants as $s ) {
                $this->google_fonts[ $font_name ]['variants'][ $s ] = $this->sanitize_variant( $s );
            }
        } else {
            $this->google_fonts[ $font_name ]['variants'][ $variants ] = $this->sanitize_variant( $variants );
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
                $this->subsets[ $s ] = $s;
            }
        } else {
            $this->google_fonts[ $font_name ]['subsets'][ $subsets ] = $subsets;
            $this->subsets[ $subsets ] = $subsets;
        }

        return true;
    }

    function get_google_font_url(){
        $url = false;
        if ( ! empty( $this->google_fonts ) ) {
            $scheme = is_ssl() ? 'https' : 'http';
            $url = $scheme.'://fonts.googleapis.com/css?family=';
            $fonts = array();
            foreach ( $this->google_fonts as $k => $f ) {
                $s = $f['name'];
                if ( ! empty( $f['variants'] ) ) {
                    $s .= ':'.join(',', $f['variants'] );
                }
                $fonts[] = $s;
            }
            $url .=  join( '|',$fonts  );
            if ( $this->subsets ) {
                $url.= '&subset='.join( ',', $this->subsets );
            }
        }
        return $url;
    }

    function css( $fields ) {

        $fields = wp_parse_args( $fields, typography_wp_default_fields() );
        $css = array();
        if ( $fields['fontType'] == 'google' ) {
            $font_name = $this->standard_font_name( $fields['family'], $fields['category'], true );
            if ( $font_name ){
                $css[] = 'font-family: '.$font_name.';';
            }
            $this->add_google_font_subsets( $fields['family'], $fields['subsets'] );

            if ( $fields['variant'] != '' && $fields['variant'] != 'regular' ) {
                $this->add_google_font_variants($fields['family'], $fields['variant']);

                if ( strpos('italic', $fields['variant'] ) > 0 || 'italic' ==  $fields['variant'] ) {
                    $css[] = 'font-style: italic;';
                }
                $weight = intval( $fields['variant'] );
                if ( $weight ) {
                    $css[] = 'font-weight: '.$weight.';';
                }

            }
        } else {
            $font_name = $this->standard_font_name( $fields['family'], false, false );
            if ( $font_name ){
                $css[] = 'font-family: '.$font_name.';';
            }
            if ( $fields['fontStyle'] ) {
                $css[] = 'font-style: '.$fields['fontStyle'].';';
            }
            if ( $fields['fontWeight'] ) {
                $css[] = 'font-style: '.$fields['fontWeight'].';';
            }
        }

        if ( $fields['fontSize'] ) {
            $css[] = 'font-size: '.floatval( $fields['fontSize'] ).$fields['unit'].';';
        }

        if ( $fields['lineHeight'] ) {
            $css[] = 'line-height: '.floatval( $fields['lineHeight'] ).$fields['unit'].';';
        }

        if ( $fields['letterSpacing'] ) {
            $css[] = 'letter-spacing: '.floatval( $fields['letterSpacing'] ).$fields['unit'].';';
        }

        if ( $fields['textTransform'] ) {
            $css[] = 'text-transform: '.( $fields['textTransform'] ).';';
        }

        if ( $fields['textDecoration'] ) {
            $css[] = 'text-decoration: '.( $fields['textDecoration'] ).';';
        }

        if ( $fields['textColor'] ) {
            $css[] = 'color: '.( $fields['textColor'] ).';';
        }

        if ( ! empty ( $css ) ) {
            return "\t".join( "\n\t", $css );
        }
        return false;
    }


}

$GLOBALS['Typography_WP_Render'] = new Typography_WP_Render();