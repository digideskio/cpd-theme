<?php


/**
 * Additional customizer options
 */
function cpd_customize_register($wp_customize)
{

    $color_scheme = twentyfifteen_get_color_scheme();

    // Logo
    $wp_customize->add_section('cpd_logo_section' , array(
        'title'       => __('Custom Logo Image', 'cpd'),
        'priority'    => 10,
        'description' => 'Upload a custom logo that will be visible at the top of the sidebar. Maximum width should be 249 pixels.',
    ));

    $wp_customize->add_setting('cpd_logo');

    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'cpd_logo', array(
        'label'    => __('Image', 'cpd'),
        'section'  => 'cpd_logo_section',
        'settings' => 'cpd_logo',
    )));

    // Site Description Position
    $wp_customize->add_section('cpd_tagline_pos_section' , array(
        'title'       => __('Site Tagline Position', 'cpd'),
        'priority'    => 20,
        'description' => 'Choose whether you want the site tagline to appear under the logo, more prominently above the main content or not at all.',
    ));

    $wp_customize->add_setting('cpd_tagline_pos');

    $wp_customize->add_control(new WP_Customize_Control( $wp_customize, 'cpd_tagline_pos', array(
        'label'    => __('Position', 'cpd'),
        'section'  => 'cpd_tagline_pos_section',
        'settings' => 'cpd_tagline_pos',
        'type'     => 'select',
            'choices'    => array(
                'left'   => __('Left - underneath logo'),
                'right'  => __('Right - above content')
            )
    )));

    // Widget Background Color
    $wp_customize->add_setting('cpd_widget_bg_color', array(
        'default'           => $color_scheme[6],
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ));

    $wp_customize->add_control( new WP_Customize_Color_Control($wp_customize, 'cpd_widget_bg_color', array(
        'label'       => __('Widget Background Color', 'cpd'),
        'description' => __('Applied to all widgets in the sidebar.', 'cpd'),
        'section'     => 'colors',
        'settings'    => 'cpd_widget_bg_color'
    )));

}
add_action( 'customize_register', 'cpd_customize_register', 20 );

/**
 * Add/Remove Colour Schemes
 * The order of colors in a colors array:
 * 1. Main Background Color.
 * 2. Sidebar Background Color.
 * 3. Box Background Color.
 * 4. Main Text and Link Color.
 * 5. Sidebar Text and Link Color.
 * 6. Meta Box Background Color.
 * 7. NEW - Widget Background Color
 */

function cpd_color_schemes($schemes)
{
    // Remove out the schemes that ship with Twenty Fifteen
    // with the exception of the default scheme

    foreach ($schemes as $key => $scheme) {
        if ($key !== 'default') {
            unset($schemes[$key]);
        }
    }

    // Add our own colour scheme with a generic name 'CPD'
    $schemes['cpd'] = array(
        'label'  => __( 'CPD', 'cpd' ),
        'colors' => array(
            '#f1f1f1',
            '#f1f1f1',
            '#ffffff',
            '#333333',
            '#333333',
            '#f7f7f7',
            '#ffffff'
        ),
      );

    return $schemes;
}
add_filter('twentyfifteen_color_schemes', 'cpd_color_schemes');

/**
 * Enqueues front-end CSS for a CPD specific color scheme.
 */
function cpd_color_scheme_css()
{
    $color_scheme_option = get_theme_mod( 'color_scheme', 'default' );

    // Don't do anything if the default color scheme is selected.
    if ('default' === $color_scheme_option) {
        return;
    }

    $color_scheme = twentyfifteen_get_color_scheme();

    $colors = array(
        'cpd_widget_bg_color'   => $color_scheme[6],
    );

    $color_scheme_css = cpd_get_color_scheme_css($colors);

    wp_add_inline_style('cpd-style', $color_scheme_css);
}
add_action('wp_enqueue_scripts', 'cpd_color_scheme_css', 20);

/**
 * Binds JS listener to make Customizer color_scheme control in CPD
 */
function cpd_customize_control_js()
{
    wp_enqueue_script( 'cpd-color-scheme-control', get_stylesheet_directory_uri() . '/js/color-scheme-control.min.js', array( 'customize-controls', 'iris', 'underscore', 'wp-util' ), '20141216', true );
    wp_localize_script( 'cpd-color-scheme-control', 'colorScheme', twentyfifteen_get_color_schemes() );
}
add_action( 'customize_controls_enqueue_scripts', 'cpd_customize_control_js', 20 );

/**
 * Binds JS handlers to make the Customizer preview reload changes asynchronously for CPD
 */
function cpd_customize_preview_js()
{
    wp_enqueue_script( 'cpd-customize-preview', get_stylesheet_directory_uri() . '/js/customize-preview.min.js', array( 'customize-preview', 'twentyfifteen-customize-preview' ), '20141216', true );
}
add_action( 'customize_preview_init', 'cpd_customize_preview_js', 20 );

/**
 * Returns CSS for the CPD color schemes.
 */
function cpd_get_color_scheme_css($colors)
{
    $colors = wp_parse_args($colors, array(
        'cpd_widget_bg_color'   => '{{ data.cpd_widget_bg_color }}',
    ) );

    $css = <<<CSS
    /* Color Scheme */

    /* Widget Background Color */
    .widget {
        background-color: {$colors['cpd_widget_bg_color']};
    }

CSS;

    return $css;
}

/**
 * Output an Underscore template for generating CSS for the color scheme.
 *
 * The template generates the css dynamically for instant display in the Customizer
 * preview.
 *
 * @since Twenty Fifteen 1.0
 */
function cpd_color_scheme_css_template()
{
    $colors = array(
        'cpd_widget_bg_color'   => '{{ data.cpd_widget_bg_color }}',
    );
    ?>
    <script type="text/html" id="tmpl-cpd-color-scheme">
        <?php echo cpd_get_color_scheme_css($colors); ?>
    </script>
    <?php
}
add_action('customize_controls_print_footer_scripts', 'cpd_color_scheme_css_template', 100);
