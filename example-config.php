<?php
/**
 * Just a example see how to use/config this plugin
 */
add_theme_support( 'typography_wp',
    array(
        'id' => 'body_typo',
        'label' => 'Body Typography',
        'selector'       => 'body #page',
        'priority'      => 100,
        'fields' => array(
            'family'         => '', // remove key if don't want to use
            'category'       => '',
            'fontId'         => '',
            'fontType'       => '',
            'subsets'        => '',
            'variant'        => '',
            'textColor'      => '', // remove key if don't want to use
            'fontStyle'      => '', // remove key if don't want to use
            'fontWeight'     => '', // remove key if don't want to use
            'fontSize'       => '', // remove key if don't want to use
            'lineHeight'     => '', // remove key if don't want to use
            'letterSpacing'  => '', // remove key if don't want to use
            'textTransform'  => '', // remove key if don't want to use
            'textDecoration' => '', // remove key if don't want to use
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
            'textColor'      => '#888888',
            'fontStyle'      => '',
            'fontWeight'     => 'bold',
            'fontSize'       => '17',
            'lineHeight'     => '26',
            'letterSpacing'  => '',
            'textTransform'  => 'uppercase',
            'textDecoration' => '',
        )
    )

);