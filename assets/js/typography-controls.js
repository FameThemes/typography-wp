
( function( api, $ ) {

	api.controlConstructor['typography_wp'] = api.Control.extend( {
        styles: [
            'default',
            'normal',
            'italic'
        ],
        fontWeights: [
            'default',
            'normal',
            'bold',
            'initial'
        ],
        optionNone: '<option value="">Default</option>',
        changedValues: {},
		ready: function() {
			var control = this;
            var values;
            try {
                values = JSON.parse( control.params.value );
            } catch ( e ) {

            }

            // Test default value
            /*
            values = {
                family: 'Lato',
                category: '',
                fontId: 'lato',
                fontType: 'google',
                subsets: {
                    'latin': 'latin',
                    'latin-ext': 'latin-ext',
                },
                variant: '300italic',
                color: '#333',
                fontStyle: '',
                fontWeight: '',
                fontSize: '16',
                lineHeight: '22',
                letterSpacing: '4',
                textTransform: 'lowercase',
                textDecoration: 'underline',
                unit: 'px',
            };
            */

            control.changedValues = control.toDefaultValues( values );
            control.container.find( 'select.select-typo-font-families').html( control.selectFontOptions() );
            control.setupEvents();

            control.setupDefaultFields();

		},

        toDefaultValues: function( values ){
            if ( ! values ) {
                values = {};
            }
            return $.extend( true, {
                family         : '',
                category       : '',
                fontId         : '',
                fontType       : '',
                subsets        : '',
                variant        : '',
                color          : '',
                fontStyle      : '',
                fontWeight     : '',
                fontSize       : '',
                lineHeight     : '',
                letterSpacing  : '',
                textTransform  : '',
                textDecoration : '',
                unit           : 'px',
            }, values );
        },

        sendToPreview: function( name, value ){
            var control = this;
            if ( typeof name == 'object' ) {
                $.each( name, function( k, v ){
                    control.changedValues[ k ] = v;
                } );
            } else {
                if ( typeof value !== "undefined" ) {
                    control.changedValues[ name ] = value;
                }
            }
            ///control.container.find( '.debug').text( JSON.stringify( control.changedValues ) );
            //control.setting.set( control.changedValues );
            // Send change vaues to preview
            control.setting.set( JSON.stringify( control.changedValues ) );
        },

        setupEvents: function(){
            var control = this;
            // When select new font
            control.container.on( 'change', 'select.select-typo-font-families', function(){
                var font_id = $( this ).val();
                control.setupFontFamily( font_id )
            } );

            // When change subsets
            // font-subsets

            control.container.on( 'change', '.font-subsets input:checkbox', function(){
                var v = $( this).val();
                var subsets = control.changedValues.subsets;
                if ( $( this).is( ':checked' ) ) {
                    subsets[ v ] = v;
                } else {
                    delete subsets[ v ];
                }
                control.sendToPreview( 'subsets', subsets );
            } );

            // When font Style change
            control.container.on( 'change', 'select.font-style', function(){
                var v = $( this).val();
                if ( v.toLowerCase() == 'default' ) {
                    v = '';
                }
                if ( control.changedValues.fontType == 'google' ) {
                    control.sendToPreview( {
                        variant: v,
                        fontWeight: '',
                    } );
                } else {
                    control.sendToPreview( {
                        variant: '',
                        fontWeight: v,
                    } );
                }

            } );

            //When font wight change
            control.container.on( 'change', 'select.font-weight', function(){
                var v = $( this).val();
                if ( control.changedValues.fontType != 'google' ) {
                    if (v.toLowerCase() == 'default' ) {
                        v = '';
                    }
                    control.sendToPreview( {
                        fontWeight: v,
                    } );
                }
            } );

            // When font size change
            control.container.on( 'keyup change', 'input.font-size', function(){
                var v = $( this).val();
                control.sendToPreview( {
                    fontSize: v,
                } );
            } );

            control.container.on( 'keyup change', 'input.line-height', function(){
                var v = $( this).val();
                control.sendToPreview( {
                    lineHeight: v,
                } );
            } );

            control.container.on( 'change', 'select.text-decoration', function(){
                var v = $( this).val();
                control.sendToPreview( {
                    textDecoration: v,
                } );
            } );

            control.container.on( 'change', 'select.text-transform', function(){
                var v = $( this).val();
                control.sendToPreview( {
                    textTransform: v,
                } );
            } );

            control.container.on( 'keyup change', 'input.text-color', function(){
                var v = $( this).val();
                control.sendToPreview( {
                    textColor: v,
                } );
            } );


        }, // End setup events

        getFont: function( font_id ){
            if ( ! font_id ) {
                this.container.find( '.typography-font-subsets').hide();
                return;
            }
            var font = null;
            $.each( window.typography_wp_webfonts, function ( group_name, fonts ) {

                if ( typeof fonts[ font_id ] !== "undefined" ) {
                    font = fonts[ font_id ];
                }
            });

            return font;
        },

        setupFontFamily: function( font_id ){
            if ( ! font_id ) {
                this.container.find( '.typography-font-subsets').hide();
                this.container.find( '.font-style').html( this.optionNone );
                this.container.find( '.font-weight').html( this.optionNone );
                this.sendToPreview( this.toDefaultValues() );
                return;
            }

            var font = this.getFont( font_id );
            if ( ! font ) {
                // do something
            } else {
                var subsets = '', styles = '', weights = '';
                var values = {
                    subsets : {},
                    variant : '',
                    fontType: '',
                    style: '',
                    weight: '',
                };
                if ( typeof font == 'object' ) { // google font

                    values.family = font.family;
                    values.category = font.category;
                    values.fontId = font_id;
                    values.fontType = 'google';

                    this.setupGoogleFontOptions( font, {} );

                } else { // default font
                    values.fontType = 'normal';
                    this.setupNormalFontOptions( {} );
                }

                // Trigger preview
                this.sendToPreview( values );

            } // End if font

        },

        setupGoogleFontOptions: function( font, valueDefault ){
            valueDefault = this.toDefaultValues( valueDefault );
            var subsets = '', styles = '';
            if ( valueDefault.variant == '' ) {
                valueDefault.variant = 'regular';
            }
            if ( typeof  valueDefault.subsets != 'object' ) {
                valueDefault.subsets = {};
            }
            $.each( font.subsets, function( index, subset ) {
                var checked = '';
                if ( typeof valueDefault.subsets[ subset ] !== "undefined" ) {
                    checked = ' checked="checked" ';
                }
                subsets += '<div><label><input type="checkbox" '+checked+' value="' + _.escape( subset ) + '">' + _.escape( subset ) + '</label></div>';
            } );

            // variants
            $.each( font.variants, function( index, variant ) {
                var selected = '';
                if ( valueDefault.variant == variant ) {
                    selected = ' selected="selected" ';
                }
                styles += '<option '+selected+' value="' + _.escape( variant ) + '">' + _.escape( variant ) + '</option>';
            } );

            if ( subsets ) {
                this.container.find( '.font-subsets').html( subsets );
                this.container.find( '.typography-font-subsets').show();
            } else {
                this.container.find( '.typography-font-subsets').hide();
            }
            this.container.find( '.typography-font-weight').hide();

            if ( styles ) {
                this.container.find( '.font-style').html( styles );
                this.container.find( '.typography-font-style').show();
            } else {
                this.container.find( '.typography-font-subsets').hide();
            }

        },

        setupNormalFontOptions: function( valueDefault ){
            valueDefault = this.toDefaultValues( valueDefault );
            var subsets = '', styles = '', weights = '';
            // Font style
            $.each( this.styles, function( index, s ) {
                var selected = '';
                if ( s == valueDefault.fontStyle ) {
                    selected = ' selected="selected" ';
                }
                styles += '<option '+selected+' value="' + _.escape( s.toLowerCase() ) + '">' + _.escape( s ) + '</option>';
            } );

            // Font wights
            $.each( this.fontWeights, function( index, s ) {
                var selected = '';
                if ( s == valueDefault.fontWieght ) {
                    selected = ' selected="selected" ';
                }
                weights += '<option  '+selected+' value="' + _.escape( s.toLowerCase() ) + '">' + _.escape( s ) + '</option>';
            } );

            this.container.find( '.font-style').html( styles );
            this.container.find( '.font-weight').html( weights );
            this.container.find( '.typography-font-subsets').hide();
            this.container.find( '.typography-font-style').show();
            this.container.find( '.typography-font-weight').show();
        },

        selectFontOptions: function(  ){
            var control = this;
            var selectOptions = '';

            if ( typeof window.fontFamiliesOptions === "undefined" ) {
                $.each( window.typography_wp_webfonts, function ( group_name, fonts ) {
                    selectOptions += control.optionNone;
                    selectOptions += '<optgroup label="'+ _.escape( group_name ) +'" >';
                    $.each( fonts, function( fontId, font ){
                        var label = '';
                        if ( typeof font == 'object' ) {
                            label = font.family;
                        } else {
                            label = font;
                        }
                        selectOptions += '<option value="'+ _.escape( fontId ) +'">'+ _.escape ( label ) + '</option>';
                    } );
                    selectOptions += '</optgroup>';
                });

                window.fontFamiliesOptions = selectOptions;
            }

            return  window.fontFamiliesOptions;

        },

        setupDefaultFields: function(){
            var control = this;

            var font = this.getFont( control.changedValues.fontId );
            if ( ! font ) {
                // do something
            } else {
                if ( typeof font == 'object' ) { // google font
                    this.setupGoogleFontOptions( font, control.changedValues );

                } else { // default font
                    this.setupNormalFontOptions( control.changedValues );
                }
            } // End if font

            // Setup font family
            control.container.find( '.select-typo-font-families option').removeAttr( 'selected' );
            control.container.find( '.select-typo-font-families option[value="'+control.changedValues.fontId+'"]').attr( 'selected', 'selected' );

            $( 'input.font-size', control.container).val( control.changedValues.fontSize );
            $( 'input.line-height', control.container).val( control.changedValues.lineHeight );
            $( 'input.letter-spacing', control.container).val( control.changedValues.letterSpacing );
            $( 'input.text-color', control.container).val( control.changedValues.color );

            $( '.text-decoration option',  control.container ).removeAttr( 'selected' );
            $( '.text-decoration option[value="'+control.changedValues.textDecoration+'"]', control.container ).attr( 'selected', 'selected' );

            $( '.text-transform option',  control.container ).removeAttr( 'selected' );
            $( '.text-transform option[value="'+control.changedValues.textTransform+'"]', control.container ).attr( 'selected', 'selected' );


        },


        preview: function( settings ){

        },


	} );


} )( wp.customize, jQuery );