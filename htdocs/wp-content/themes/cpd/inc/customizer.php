<?php

/**
 * Binds JS listener to make Customizer color_scheme control in CPD
 */
function cpd_customize_control_js()
{
    wp_enqueue_script('cpd-color-scheme-control', get_stylesheet_directory_uri() . '/js/color-scheme-control.min.js', array('customize-controls', 'iris', 'underscore', 'wp-util'), '20141216', true);
    wp_localize_script('cpd-color-scheme-control', 'colorSchemeCPD', twentyfifteen_get_color_schemes());
}
add_action('customize_controls_enqueue_scripts', 'cpd_customize_control_js', 20);

/**
 * Binds JS handlers to make the Customizer preview reload changes asynchronously for CPD
 */
function cpd_customize_preview_js()
{
    wp_enqueue_script('cpd-customize-preview', get_stylesheet_directory_uri() . '/js/customize-preview.min.js', array('customize-preview'), '20141216', true);
}
add_action('customize_preview_init', 'cpd_customize_preview_js', 20);

/**
 * Remove/Edit customizer options added by Twenty Fifteen
 */
function cpd_customize_cleanup($wp_customize)
{
    // Remove - we're going to define this in our option panels
    $wp_customize->remove_control( 'header_background_color' );
    $wp_customize->remove_control( 'sidebar_textcolor' );

    // Rename this now it's mostly empty
    $wp_customize->get_section('colors')->title = __( 'Color Scheme' );
}
add_action('customize_register', 'cpd_customize_cleanup', 20);

/**
 * Additional customizer options
 */
function cpd_customize_register($wp_customize)
{
    $color_scheme = twentyfifteen_get_color_scheme();

    // Logo
    $wp_customize->add_section('cpd_logo_section' , array(
        'title'       => __('Custom Logo', 'cpd'),
        'priority'    => 10,
        'description' => 'Upload a custom logo that will be visible at the top of the sidebar. Maximum width should be 248 pixels.',
        // 'capability'  => 'manage_network'
    ));

    $wp_customize->add_setting('cpd_logo');

    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'cpd_logo', array(
        'label'    => __('Image', 'cpd'),
        'section'  => 'cpd_logo_section',
        'settings' => 'cpd_logo',
    )));

    // Site Description Position
    $wp_customize->add_setting('cpd_tagline_pos');

    $wp_customize->add_control(new WP_Customize_Control($wp_customize, 'cpd_tagline_pos', array(
        'label'    => __('Position', 'cpd'),
        'section'  => 'title_tagline',
        'settings' => 'cpd_tagline_pos',
        'type'     => 'select',
            'choices'    => array(
                'left'   => __('Left  - underneath the logo'),
                'right'  => __('Right - above the main content')
            )
    )));

    // ### SIDEBAR COLORS ###
    $wp_customize->add_section('cpd_sidebar_colors' , array(
        'title'       => __('Header & Sidebar Colors', 'cpd'),
        'priority'    => 40
    ));

    // Header & Sidebar Background Color
    $wp_customize->add_setting('cpd_sidebar_bg_color', array(
        'default'           => $color_scheme[17],
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'cpd_sidebar_bg_color', array(
        'label'       => __('Header & Sidebar Background Color', 'cpd'),
        'description' => __('Change the background color for the header/sidebar area.', 'cpd'),
        'section'     => 'cpd_sidebar_colors',
        'settings'    => 'cpd_sidebar_bg_color'
    )));

    // Widget Link Background Color
    $wp_customize->add_setting('cpd_widget_link_bg_color', array(
        'default'           => $color_scheme[6],
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'cpd_widget_link_bg_color', array(
        'label'       => __('Widget Link Background Color', 'cpd'),
        'description' => __('Applied to all widget links in the sidebar.', 'cpd'),
        'section'     => 'cpd_sidebar_colors',
        'settings'    => 'cpd_widget_link_bg_color'
    )));

    // Widget Link Background Color Alternative
    $wp_customize->add_setting('cpd_widget_link_bg_color_alt', array(
        'default'           => $color_scheme[7],
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'cpd_widget_link_bg_color_alt', array(
        'label'       => __('Widget Link Alternative Background Color', 'cpd'),
        'description' => __('Applied to all widget links in the sidebar when in hover/active state.', 'cpd'),
        'section'     => 'cpd_sidebar_colors',
        'settings'    => 'cpd_widget_link_bg_color_alt'
    )));

    // Widget Link Color
    $wp_customize->add_setting('cpd_widget_link_color', array(
        'default'           => $color_scheme[8],
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'cpd_widget_link_color', array(
        'label'       => __('Widget Link Color', 'cpd'),
        'description' => __('Applied to all widget links in the sidebar.', 'cpd'),
        'section'     => 'cpd_sidebar_colors',
        'settings'    => 'cpd_widget_link_color'
    )));

    // Widget Link Color Alt
    $wp_customize->add_setting('cpd_widget_link_color_alt', array(
        'default'           => $color_scheme[9],
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'cpd_widget_link_color_alt', array(
        'label'       => __('Widget Link Alternative Color', 'cpd'),
        'description' => __('Applied to all widget links in the sidebar.', 'cpd'),
        'section'     => 'cpd_sidebar_colors',
        'settings'    => 'cpd_widget_link_color_alt'
    )));

    // Widget Heading Background Color
    $wp_customize->add_setting('cpd_widget_heading_bg_color', array(
        'default'           => $color_scheme[10],
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'cpd_widget_heading_bg_color', array(
        'label'       => __('Widget Heading Background Color', 'cpd'),
        'description' => __('Applied to all widget headings in the sidebar.', 'cpd'),
        'section'     => 'cpd_sidebar_colors',
        'settings'    => 'cpd_widget_heading_bg_color'
    )));

    // Widget Heading Color
    $wp_customize->add_setting('cpd_widget_heading_color', array(
        'default'           => $color_scheme[11],
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'cpd_widget_heading_color', array(
        'label'       => __('Widget Heading Text Color', 'cpd'),
        'description' => __('Applied to all widget headings in the sidebar.', 'cpd'),
        'section'     => 'cpd_sidebar_colors',
        'settings'    => 'cpd_widget_heading_color'
    )));

    // ### MAIN CONTENT COLORS ###
    $wp_customize->add_section('cpd_main_content_colors' , array(
        'title'       => __('Main Content Area Colors', 'cpd'),
        'priority'    => 50
    ));

    // Main Content Area Background Color
    $wp_customize->add_setting('cpd_main_bg_color', array(
        'default'           => $color_scheme[12],
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'cpd_main_bg_color', array(
        'label'       => __('Main Content Background Color', 'cpd'),
        'description' => __('Change the background color for the main content area.', 'cpd'),
        'section'     => 'cpd_main_content_colors',
        'settings'    => 'cpd_main_bg_color'
    )));

    // Article Background Color
    $wp_customize->add_setting('cpd_article_bg_color', array(
        'default'           => $color_scheme[13],
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'cpd_article_bg_color', array(
        'label'       => __('Article Background Color', 'cpd'),
        'description' => __('Change the background color for the article boxes, comment area etc.', 'cpd'),
        'section'     => 'cpd_main_content_colors',
        'settings'    => 'cpd_article_bg_color'
    )));

    // Article Text Color
    $wp_customize->add_setting('cpd_article_text_color', array(
        'default'           => $color_scheme[14],
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'cpd_article_text_color', array(
        'label'       => __('Article Text Color', 'cpd'),
        'description' => __('Change the text color for the article boxes, comment area etc.', 'cpd'),
        'section'     => 'cpd_main_content_colors',
        'settings'    => 'cpd_article_text_color'
    )));

    // Article Footer Background Color
    $wp_customize->add_setting('cpd_article_foot_bg_color', array(
        'default'           => $color_scheme[15],
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'cpd_article_foot_bg_color', array(
        'label'       => __('Article Footer Background Color', 'cpd'),
        'description' => __('Change the background color for the footer of the article boxes.', 'cpd'),
        'section'     => 'cpd_main_content_colors',
        'settings'    => 'cpd_article_foot_bg_color'
    )));

    // Article Footer Text Color
    $wp_customize->add_setting('cpd_article_foot_text_color', array(
        'default'           => $color_scheme[16],
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'cpd_article_foot_text_color', array(
        'label'       => __('Article Footer Text Color', 'cpd'),
        'description' => __('Change the text color for the footer of the article boxes.', 'cpd'),
        'section'     => 'cpd_main_content_colors',
        'settings'    => 'cpd_article_foot_text_color'
    )));
}
add_action('customize_register', 'cpd_customize_register', 30);

/**
 * Add/Remove Colour Schemes
 * 6.  Widget Link Background Color
 * 7.  Widget Link Background Color Alt
 * 8.  Widget Link Color
 * 9.  Widget Link Color Alt
 * 10. Widget Heading Background Color
 * 11. Widget Heading Color
 * 12. Main Content Background Color
 * 13. Article Background Color
 * 14. Article Text Color
 * 15. Article Footer Background Color
 * 16. Article Footer Text Color
 * 17. Header & Sidebar Background Color
 */

function cpd_color_schemes($schemes)
{
    // Remove out the schemes that ship with Twenty Fifteen
    foreach ($schemes as $key => $scheme) {
        unset($schemes[$key]);
    }

    // White
    $schemes['cpd_sheff_grey'] = array(
        'label'  => __('Sheffield - Grey', 'cpd'),
        'colors' => array(
            '#f1f1f1',
            '#f1f1f1',
            '#ffffff',
            '#333333',
            '#333333',
            '#f7f7f7',
            '#030000', // 6
            '#ffffff', // 7
            '#ffffff', // 8
            '#030000', // 9
            '#414042', // 10
            '#ffffff', // 11
            '#f1f1f1', // 12
            '#ffffff', // 13
            '#414042', // 14
            '#414042', // 15
            '#ffffff', // 16
            '#f1f1f1'  // 17
        ),
      );

    // Blue
    $schemes['cpd_sheff_blue'] = array(
        'label'  => __('Sheffield - Blue', 'cpd'),
        'colors' => array(
            '#f1f1f1',
            '#f1f1f1',
            '#ffffff',
            '#333333',
            '#333333',
            '#f7f7f7',
            '#030000', // 6
            '#ffffff', // 7
            '#ffffff', // 8
            '#0066b3', // 9
            '#0066b3', // 10
            '#ffffff', // 11
            '#f1f1f1', // 12
            '#0066b3', // 13
            '#ffffff', // 14
            '#030000', // 15
            '#ffffff', // 16
            '#f1f1f1'  // 17
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
    $color_scheme_option = get_theme_mod('color_scheme', 'default');

    // Don't do anything if the default color scheme is selected.
    if ('default' === $color_scheme_option) {
        return;
    }

    $color_scheme = twentyfifteen_get_color_scheme();

    $colors = array(
        'cpd_widget_link_bg_color'     => get_theme_mod('cpd_widget_link_bg_color', 'default'),
        'cpd_widget_link_bg_color_alt' => get_theme_mod('cpd_widget_link_bg_color_alt', 'default'),
        'cpd_widget_link_color'        => get_theme_mod('cpd_widget_link_color', 'default'),
        'cpd_widget_link_color_alt'    => get_theme_mod('cpd_widget_link_color_alt', 'default'),
        'cpd_widget_heading_bg_color'  => get_theme_mod('cpd_widget_heading_bg_color', 'default'),
        'cpd_widget_heading_color'     => get_theme_mod('cpd_widget_heading_color', 'default'),
        'cpd_main_bg_color'            => get_theme_mod('cpd_main_bg_color', 'default'),
        'cpd_article_bg_color'         => get_theme_mod('cpd_article_bg_color', 'default'),
        'cpd_article_text_color'       => get_theme_mod('cpd_article_text_color', 'default'),
        'cpd_article_foot_bg_color'    => get_theme_mod('cpd_article_foot_bg_color', 'default'),
        'cpd_article_foot_text_color'  => get_theme_mod('cpd_article_foot_text_color', 'default'),
        'cpd_sidebar_bg_color'         => get_theme_mod('cpd_sidebar_bg_color', 'default')
    );

    $color_scheme_css = cpd_get_color_scheme_css($colors);

    wp_add_inline_style('cpd-style', $color_scheme_css);
}
add_action('wp_enqueue_scripts', 'cpd_color_scheme_css', 100);

/**
 * Returns CSS for the CPD color schemes.
 */
function cpd_get_color_scheme_css($colors)
{

    $css = <<<CSS
    /* CPD Color Scheme */

    /* Body Background Color (Main Content Area) */
    html body {
        background-color: {$colors['cpd_main_bg_color']};
    }

    /* Body Background Color (Sidebar) */
    html body:before {
        background-color: {$colors['cpd_sidebar_bg_color']};
    }

    /* Widget Links */
    .widget li a {
        background-color: {$colors['cpd_widget_link_bg_color']};
        color: {$colors['cpd_widget_link_color']};
    }

    .widget li a:hover,
    .widget li a:focus,
    .widget li.current_page_item a {
        background-color: {$colors['cpd_widget_link_bg_color_alt']};
        color: {$colors['cpd_widget_link_color_alt']};
    }

    /* Widget Headings */
    h2.widget-title {
        background-color: {$colors['cpd_widget_heading_bg_color']};
        color: {$colors['cpd_widget_heading_color']};
    }

    /* Style the search widget using the alt colors! */
    .widget .search-field {
        background-color: {$colors['cpd_widget_link_bg_color']};
        color: {$colors['cpd_widget_link_color']};
    }

    .widget .search-field:focus {
        background-color: {$colors['cpd_widget_link_bg_color_alt']};
        color: {$colors['cpd_widget_link_color_alt']};
    }

    .widget .search-field::-webkit-input-placeholder {
        color: {$colors['cpd_widget_link_color']};
    }

    .widget .search-field::-moz-placeholder {
        color: {$colors['cpd_widget_link_color']};
    }

    .widget .search-field:-ms-input-placeholder {
        color: {$colors['cpd_widget_link_color']};
    }

    .widget .search-field::placeholder {
        color: {$colors['cpd_widget_link_color']};
    }

    /* Article Background */
    .site-main .post-navigation,
    .site-main .pagination,
    .site-main .site-footer,
    .site-main .hentry,
    .site-main .page-header,
    .site-main .page-content,
    .site-main .comments-area,
    .site-main .widecolumn {
        background-color: {$colors['cpd_article_bg_color']};
    }

    /* Article Text */
    .site-main .entry-content,
    .site-main h1, .site-main h1 a,
    .site-main h2, .site-main h2 a,
    .site-main h3, .site-main h3 a,
    .site-main h4, .site-main h4 a,
    .site-main h5, .site-main h5 a,
    .site-main h6, .site-main h6 a {
        color: {$colors['cpd_article_text_color']};
    }

    .site-main blockquote,
    .site-main .entry-content a,
    .site-main .entry-summary a,
    .site-main .page-content a,
    .site-main .comment-meta a,
    .site-main .comment-content a,
    .site-main .comment-respond a,
    .site-main .pingback .comment-body > a {
        border-color: {$colors['cpd_article_text_color']};
        color: {$colors['cpd_article_text_color']};
    }

    blockquote, .main-navigation .menu-item-description, .post-navigation .meta-nav, .post-navigation a, .post-navigation a:hover .post-title, .post-navigation a:focus .post-title, .image-navigation, .image-navigation a, .comment-navigation, .comment-navigation a, .widget, .author-heading, .taxonomy-description, .page-links > .page-links-title, .entry-caption, .comment-author, .comment-metadata, .comment-metadata a, .pingback .edit-link, .pingback .edit-link a, .post-password-form label, .comment-form label, .comment-notes, .comment-awaiting-moderation, .logged-in-as, .form-allowed-tags, .no-comments, .site-info, .site-info a, .wp-caption-text, .gallery-caption, .comment-list .reply a, .widecolumn label, .widecolumn .mu_register label {
        color: {$colors['cpd_article_text_color']} !important;
    }

    pre, abbr[title], table, th, td, input, textarea, .main-navigation ul, .main-navigation li, .post-navigation, .post-navigation div + div, .pagination, .comment-navigation, .widget li, .widget_categories .children, .widget_nav_menu .sub-menu, .widget_pages .children, .site-header, .site-footer, .hentry + .hentry, .author-info, .entry-content .page-links a, .page-links > span, .page-header, .comments-area, .comment-list + .comment-respond, .comment-list article, .comment-list .pingback, .comment-list .trackback, .comment-list .reply a, .no-comments {
        border-color: {$colors['cpd_article_text_color']} !important;
    }

    /* Article Entry Footer */
    .site-main .entry-footer {
        background-color: {$colors['cpd_article_foot_bg_color']};
    }

    .site-main .entry-footer,
    .site-main .entry-footer a,
    .site-main .entry-footer a:hover,
    .site-main .entry-footer a:focus {
        color: {$colors['cpd_article_foot_text_color']};
    }

    .site-main .entry-footer a:hover,
    .site-main .entry-footer a:focus {
        border-color: {$colors['cpd_article_foot_text_color']};
     }

CSS;

    return $css;
}

/**
 * Output an Underscore template for generating CSS for the CPD color scheme.
 */
function cpd_color_scheme_css_template()
{
    $colors = array(
        'cpd_widget_link_bg_color'     => '{{ data.cpd_widget_link_bg_color }}',
        'cpd_widget_link_bg_color_alt' => '{{ data.cpd_widget_link_bg_color_alt }}',
        'cpd_widget_link_color'        => '{{ data.cpd_widget_link_color }}',
        'cpd_widget_link_color_alt'    => '{{ data.cpd_widget_link_color_alt }}',
        'cpd_widget_heading_bg_color'  => '{{ data.cpd_widget_heading_bg_color }}',
        'cpd_widget_heading_color'     => '{{ data.cpd_widget_heading_color }}',
        'cpd_main_bg_color'            => '{{ data.cpd_main_bg_color }}',
        'cpd_article_bg_color'         => '{{ data.cpd_article_bg_color }}',
        'cpd_article_text_color'       => '{{ data.cpd_article_text_color }}',
        'cpd_article_foot_bg_color'    => '{{ data.cpd_article_foot_bg_color }}',
        'cpd_article_foot_text_color'  => '{{ data.cpd_article_foot_text_color }}',
        'cpd_sidebar_bg_color'         => '{{ data.cpd_sidebar_bg_color }}',
    );
    ?>
    <script type="text/html" id="tmpl-cpd-color-scheme">
        <?php echo cpd_get_color_scheme_css($colors); ?>
    </script>
    <?php
}
add_action('customize_controls_print_footer_scripts', 'cpd_color_scheme_css_template', 100);
