<?php

/**
 * Additional customizer options
 */
function cpd_customize_register($wp_customize)
{
    // Logo
    $wp_customize->add_section('cpd_logo_section' , array(
        'title'       => __('Custom Logo Image', 'cpd' ),
        'priority'    => 10,
        'description' => 'Upload a custom logo that will be visible at the top of the sidebar. Maximum width should be 249 pixels.',
    ));

    $wp_customize->add_setting('cpd_logo' );

    $wp_customize->add_control(new WP_Customize_Image_Control( $wp_customize, 'cpd_logo', array(
        'label'    => __( 'Image', 'cpd' ),
        'section'  => 'cpd_logo_section',
        'settings' => 'cpd_logo',
    )));

    // Site Description Position
    $wp_customize->add_section('cpd_tagline_pos_section' , array(
        'title'       => __('Site Tagline Position', 'cpd' ),
        'priority'    => 20,
        'description' => 'Choose whether you want the site tagline to appear under the logo, more prominently above the main content or not at all.',
    ));

    $wp_customize->add_setting('cpd_tagline_pos' );

    $wp_customize->add_control(new WP_Customize_Control( $wp_customize, 'cpd_tagline_pos', array(
        'label'    => __( 'Position', 'cpd' ),
        'section'  => 'cpd_tagline_pos_section',
        'settings' => 'cpd_tagline_pos',
        'type'     => 'select',
            'choices'        => array(
                'left'   => __( 'Left - underneath logo' ),
                'right'  => __( 'Right - above content' )
            )
    )));

}
add_action( 'customize_register', 'cpd_customize_register', 12 );
