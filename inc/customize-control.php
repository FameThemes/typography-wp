<?php
if ( class_exists( 'WP_Customize_Control' ) ) {

    /**
     * Typography control class.
     *
     * @since  1.0.0
     * @access public
     */
    class WP_Customize_Typography_WP_Control extends WP_Customize_Control {

        /**
         * The type of customize control being rendered.
         *
         * @since  1.0.0
         * @access public
         * @var    string
         */
        public $type = 'typography_wp';

        /**
         * Array
         *
         * @since  1.0.0
         * @access public
         * @var    string
         */
        public $l10n = array();

        /**
         * CSS selector
         *
         * @var string
         */
        public $css_selector ='';

        /**
         * Settings fields
         * @var array
         */
        public $fields = array();

        /**
         * Set up our control.
         *
         * @since  1.0.0
         * @access public
         * @param  object  $manager
         * @param  string  $id
         * @param  array   $args
         * @return void
         */
        public function __construct( $manager, $id, $args = array() ) {

            // Let the parent class do its thing.
            parent::__construct( $manager, $id, $args );

            $args = wp_parse_args( $args, array(
                'fields' => array(),
            ) );

            if ( ! isset( $args['fields'] ) ) {
                $args['fields'] = array();
            }

            $this->fields = $args['fields'];

        }

        /**
         * Add custom parameters to pass to the JS via JSON.
         *
         * @since  1.0.0
         * @access public
         * @return void
         */
        public function json() {
            $json = parent::json();

            $value = $this->value();

            $json['value'] = $value;
            $support_fields = array(
                'family'         => false,
                'textColor'      => false,
                'fontStyle'      => false,
                'fontSize'       => false,
                'lineHeight'     => false,
                'letterSpacing'  => false,
                'textTransform'  => false,
                'textDecoration' => false,
            );

            //$json['fields']  = wp_parse_args( $this->fields, $support_fields );

            foreach ( $support_fields as $k => $v ) {
                if ( isset( $this->fields[ $k ] ) ) {
                    $this->fields[ $k ] = true;
                } else {
                    $this->fields[ $k ] = false;
                }
            }

            $json['fields'] =  $this->fields;

            $json['labels']  = array(
                'family'            => esc_html__( 'Font Family', 'typography_wp' ),
                'option_default'    => esc_html__( 'Default', 'typography_wp' ),
                'size'              => esc_html__( 'Font Size',   'typography_wp' ),
                'weight'           => esc_html__( 'Font Weight',  'typography_wp' ),
                'style'             => esc_html__( 'Font Style',  'typography_wp' ),
                'lineHeight'       => esc_html__( 'Line Height', 'typography_wp' ),
                'textDecoration'   => esc_html__( 'Text Decoration', 'typography_wp' ),
                'letterSpacing'    => esc_html__( 'Letter Spacing', 'typography_wp' ),
                'textTransform'    => esc_html__( 'Text Transform', 'typography_wp' ),
                'textColor'        => esc_html__( 'Color', 'typography_wp' ),
            );

            return $json;
        }


        /**
         * Get url of any dir
         *
         * @param string $file full path of current file in that dir
         * @return string
         */
        public static function get_url( ){
            return  TYPOGRAPHY_WP_URL;
        }

        public static function get_default_fonts() {

            $fonts = array(
                'Georgia, serif',
                'Palatino Linotype, serif',
                'Book Antiqua, serif',
                'Palatino, serif',
                'Times New Roman, serif',
                'Times, serif',
                'Arial, sans-serif',
                'Helvetica, sans-serif',
                'Arial Black, sans-serif',
                'Gadget, sans-serif',
                'Comic Sans MS, sans-serif',
                'cursive, sans-serif',
                'Impact, sans-serif',
                'Lucida Sans Unicode, sans-serif',
                'Lucida Grande, sans-serif',
                'Tahoma, sans-serif',
                'Geneva, sans-serif',
                'Trebuchet MS, sans-serif',
                'Helvetica, sans-serif',
                'Verdana, sans-serif',
                'Geneva, sans-serif',
                'Courier New, monospace',
                'Courier, monospace',
                'Lucida Console, monospace',
                'Monaco, monospace'
            );

            $array = array();
            foreach ( $fonts as $font ) {
                $key = sanitize_title( $font );
                $array[ $key ] = $font;
            }

            return $array;
        }

        /**
         * Returns the available fonts.  Fonts should have available weights, styles, and subsets.
         *
         * @todo Integrate with Google fonts.
         *
         * @since  1.0.0
         * @access public
         * @return array
         */
        static function get_google_fonts(){
            $google_fonts = include TYPOGRAPHY_WP_PATH.'inc/google-fonts.php';
            $array = array();
            if ( is_array( $google_fonts ) && is_array( $google_fonts['items'] ) ) {
                foreach ( $google_fonts['items'] as $font ) {
                    $id = sanitize_title( $font['family'] );
                    $array[ $id ] = $font;
                }
            }

            return $array;
        }

        public static function get_fonts(){
            return apply_filters( 'typography_wp_get_fonts', array(
                    'Normal Fonts' => self::get_default_fonts(),
                    'Google Fonts' => self::get_google_fonts(),
                )
            );
        }

        public static function get_font_by_id( $id ){
            $id = sanitize_title( $id );
            if ( ! $id ) {
                return false;
            }
            $fonts = self::get_fonts();
            if ( isset( $fonts[ $id ] ) ) {
                return $fonts[ $id ];
            }
            return false;
        }


        /**
         * Enqueue scripts/styles.
         *
         * @since  1.0.0
         * @access public
         * @return void
         */
        public function enqueue() {
            $key = $this->type.'_loaded_scripts';
            if ( isset( $GLOBALS[ $key ] ) && $GLOBALS[ $key ] ) {
                return true;
            }
            //End sure just run once.
            $GLOBALS[ $key ] = true;

            $uri = $this->get_url();
            wp_enqueue_script( 'wp-color-picker' );
            wp_enqueue_style( 'wp-color-picker' );

            wp_localize_script('jquery', 'typography_wp_webfonts', $this->get_fonts() );
            wp_localize_script('jquery', 'typography_wp_config', array(
                'nonce' => wp_create_nonce( 'typography_wp' ),
            ) );

            wp_register_script( 'typography-customize-controls', esc_url( $uri . 'assets/js/typography-controls.js' ), array( 'customize-controls' ) );
            wp_enqueue_script('typography-customize-controls');

        }


        /**
         * Underscore JS template to handle the control's output.
         *
         * @since  1.0.0
         * @access public
         * @return void
         */
        public function content_template() {

            ?>
            <div class="typography-wp-wrap">

                <div class="typography-header">
                    <# if ( data.label ) { #>
                        <span class="customize-control-title">{{ data.label }}</span>
                    <# } #>

                    <# if ( data.description ) { #>
                            <span class="description customize-control-description">{{{ data.description }}}</span>
                    <# } #>
                </div>

                <div class="typography-wp-settings">
                    <# if ( data.fields ) { #>
                    <ul>
                        <# if ( data.fields.family ) { #>
                            <li class="typography-font-family">
                                <span class="customize-control-title">{{ data.labels.family }}</span>
                                <select class="font-family select-typo-font-families"></select>
                            </li>
                            <li class="typography-font-subsets" style="display: none;">
                                <span class="customize-control-title">Subsets</span>
                                <div class="font-subsets"></div>
                            </li>
                        <# } #>

                        <# if ( data.fields.fontStyle && data.fields.fontStyle ) { #>
                            <li class="typography-font-style">
                                <span class="customize-control-title">{{ data.labels.style }}</span>
                                <select class="font-style"></select>
                            </li>
                            <li class="typography-font-weight">
                                <span class="customize-control-title">{{ data.labels.weight }}</span>
                                <select class="font-weight"></select>
                            </li>
                        <# } #>

                        <# if ( data.fields.fontSize ) { #>
                            <li class="typography-font-size typography-half right">
                                <span class="customize-control-title">{{ data.labels.size  }}</span>
                                <input class="unit-value font-size" placeholder="<?php esc_attr_e( 'Default', 'typography_wp' ); ?>" type="number" min="1" />
                            </li>
                        <# } #>

                        <# if ( data.fields.lineHeight ) { #>
                            <li class="typography-line-height first typography-half">
                                <span class="customize-control-title">{{ data.labels.lineHeight }}</span>
                                <input class="unit-value line-height" placeholder="<?php esc_attr_e( 'Default', 'typography_wp' ); ?>" type="number" min="1" />
                            </li>
                        <# } #>

                        <# if ( data.fields.letterSpacing ) { #>
                            <li class="typography-letter-spacing typography-half right">
                                <span class="customize-control-title">{{ data.labels.letterSpacing }}</span>
                                <input class="unit-value letter-spacing" placeholder="<?php esc_attr_e( 'Default', 'typography_wp' ); ?>" type="number" />
                            </li>
                        <# } #>

                        <# if ( data.fields.textDecoration ) { #>
                            <li class="typography-text-decoration clr">
                                <span class="customize-control-title">{{ data.labels.textDecoration }}</span>
                                <select class="text-decoration">
                                    <option value=""><?php esc_attr_e( 'Default', 'typography_wp' ); ?></option>
                                    <option value="none"><?php esc_attr_e( 'None', 'typography_wp' ); ?></option>
                                    <option value="overline"><?php esc_attr_e( 'Overline', 'typography_wp' ); ?></option>
                                    <option value="underline"><?php esc_attr_e( 'Underline', 'typography_wp' ); ?></option>
                                    <option value="line-through"><?php esc_attr_e( 'Line through', 'typography_wp' ); ?></option>
                                </select>
                            </li>
                        <# } #>

                        <# if ( data.fields.textTransform ) { #>
                            <li class="typography-text-transform clr">
                                <span class="customize-control-title">{{ data.labels.textTransform }}</span>
                                <select class="text-transform" >
                                    <option value=""><?php esc_attr_e( 'Default', 'typography_wp' ); ?></option>
                                    <option value="none"><?php esc_attr_e( 'None', 'typography_wp' ); ?></option>
                                    <option value="uppercase"><?php esc_attr_e( 'Uppercase', 'typography_wp' ); ?></option>
                                    <option value="lowercase"><?php esc_attr_e( 'Lowercase', 'typography_wp' ); ?></option>
                                    <option value="capitalize"><?php esc_attr_e( 'Capitalize', 'typography_wp' ); ?></option>
                                </select>
                            </li>
                        <# } #>

                        <# if ( data.fields.textColor ) { #>
                            <li class="typography-text-transform clr">
                                <span class="customize-control-title">{{ data.labels.textColor }}</span>
                                <input type="text" class="text-color" />
                            </li>
                        <# } #>

                    </ul>
                    <# } #>
                </div>
            </div>
            <?php
        }

    }
}
