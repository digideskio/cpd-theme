<?php

/**
 * Require our custom control classes
 */
require_once 'customizer-classes.php';

/**
 * Remove/Edit customizer options added by Twenty Fifteen
 */
function cpd_customize_cleanup($wp_customize)
{
    // Remove existing header/sidebar controls
    $wp_customize->remove_control('header_background_color');
    $wp_customize->remove_control('sidebar_textcolor');

    // Change 'Colors' to 'Colour Scheme'
    $wp_customize->get_section('colors')->title = __('Colour Scheme');

    // Change label 'Base Color Scheme' to 'Scheme'
    $wp_customize->get_control('color_scheme')->label = __('Scheme');
}
add_action('customize_register', 'cpd_customize_cleanup', 20);

/**
 * Restrict access to options for network admins and supervisors
 */
function cpd_restrict_access($wp_customize)
{
    // An array of sections we want participants/supervisors to access:
    // just add the identifer if they need to have access to a section
    $user_allowed = array(
        'title_tagline',
        'colors',
    );

    $super_allowed = array(
        'sidebar-widgets-sidebar-1'
    );

    // Get all the sections
    $sections = $wp_customize->sections();

    // We just need the section identifiers
    foreach ($sections as $key => $section) {

        // Strip participants of privileges
        if (!in_array($key, $user_allowed)) {
            $wp_customize->get_section($key)->capability = 'manage_network';
        }

        // Then grant supervisors privileges
        if (in_array($key, array_merge($user_allowed, $super_allowed))) {
            $wp_customize->get_section($key)->capability = 'supervise_users';
        }

        // Let participants have access
        if (in_array($key, $user_allowed)) {
            $wp_customize->get_section($key)->capability = 'edit_posts';
        }
    }

    // We don't want them to have access to the other title/tagline controls
    $wp_customize->get_setting('blogdescription')->capability = 'manage_network';
    $wp_customize->get_setting('cpd_tagline_pos')->capability = 'manage_network';
    $wp_customize->get_setting('cpd_intro_color')->capability = 'manage_network';
}
add_action('customize_register', 'cpd_restrict_access', 100);

/**
 * Customizer options - Branding
 */
function cpd_customize_branding($wp_customize)
{
    $wp_customize->add_section('cpd_branding', array(
        'title'    => __('Branding', 'cpd'),
        'priority' => 10,
    ));

    // Main Logo
    $wp_customize->add_setting('cpd_logo');

    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'cpd_logo', array(
        'label'       => __('Logo', 'cpd'),
        'description' => 'Upload a custom logo that will be visible in the header/sidebar area. Maximum width should be 248 pixels.',
        'section'     => 'cpd_branding',
        'settings'    => 'cpd_logo',
    )));

    // Watermark Logo
    $wp_customize->add_setting('cpd_watermark');

    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'cpd_watermark', array(
        'label'       => __('Watermark', 'cpd'),
        'description' => 'Upload a custom logo that will be visible in the footer area.',
        'section'     => 'cpd_branding',
        'settings'    => 'cpd_watermark',
    )));
}
add_action('customize_register', 'cpd_customize_branding', 21);

/**
 * Customizer options - Site Title & Tagline
 */
function cpd_customize_intro($wp_customize)
{
    $color_scheme = twentyfifteen_get_color_scheme();

    // Position
    $wp_customize->add_setting('cpd_tagline_pos', array(
        'default' => 'left'
    ));

    $wp_customize->add_control(new WP_Customize_Control($wp_customize, 'cpd_tagline_pos', array(
        'label'       => __('Position', 'cpd'),
        'description' => 'Choose whether you want the title & tagline to be visible in the sidebar/header area, or above the main content.',
        'section'     => 'title_tagline',
        'settings'    => 'cpd_tagline_pos',
        'type'        => 'select',
            'choices'    => array(
                'left'   => __('Left  - in the sidebar/header'),
                'right'  => __('Right - above the main content')
            )
    )));

    // Colour
    $wp_customize->add_setting('cpd_intro_color', array(
        'default'           => $color_scheme[18],
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'cpd_intro_color', array(
        'label'       => __('Colour', 'cpd'),
        'description' => __('Set the colour of the title & tagline text.', 'cpd'),
        'section'     => 'title_tagline',
        'settings'    => 'cpd_intro_color'
    )));
}
add_action('customize_register', 'cpd_customize_intro', 22);

/**
 * Customizer options - Top & Bottom Sections
 */
function cpd_customize_fonts($wp_customize)
{
    $wp_customize->add_section('cpd_fonts', array(
        'title'    => __('Font Choices', 'cpd'),
        'priority' => 30
    ));

    // Body Text
    $wp_customize->add_setting('cpd_font_body', array(
        'default' => 'noto-sans'
    ));

    $wp_customize->add_control(new WP_Customize_Control($wp_customize, 'cpd_font_body', array(
        'label'       => __('Body', 'cpd'),
        'description' => 'Set the font to be used for the body text.',
        'section'     => 'cpd_fonts',
        'settings'    => 'cpd_font_body',
        'type'        => 'select',
            'choices'    => array(
                'noto-serif'  => 'Noto Serif',
                'pt-serif'    => 'PT Serif',
                'noto-sans'   => 'Noto Sans',
                'open-sans'   => 'Open Sans',
            )
    )));

    // Heading Text
    $wp_customize->add_setting('cpd_font_heading', array(
        'default' => 'noto-serif'
    ));

    $wp_customize->add_control(new WP_Customize_Control($wp_customize, 'cpd_font_heading', array(
        'label'       => __('Headings', 'cpd'),
        'description' => 'Set the font to be used for the headings.',
        'section'     => 'cpd_fonts',
        'settings'    => 'cpd_font_heading',
        'type'        => 'select',
            'choices'    => array(
                'noto-serif'  => 'Noto Serif',
                'pt-serif'    => 'PT Serif',
                'noto-sans'   => 'Noto Sans',
                'open-sans'   => 'Open Sans',
            )
    )));

}
add_action('customize_register', 'cpd_customize_fonts', 23);

/**
 * Customizer options - Header & Sidebar
 */
function cpd_customize_sidebar($wp_customize)
{
    $color_scheme = twentyfifteen_get_color_scheme();

    $wp_customize->add_section('cpd_sidebar' , array(
        'title'    => __('Sidebar & Header Area', 'cpd'),
        'priority' => 40
    ));

    // Header & Sidebar Background Colour
    $wp_customize->add_setting('cpd_sidebar_bg_color', array(
        'default'           => $color_scheme[17],
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'cpd_sidebar_bg_color', array(
        'label'       => __('Background Colour', 'cpd'),
        'description' => __('Set the background colour for the sidebar/header area.', 'cpd'),
        'section'     => 'cpd_sidebar',
        'settings'    => 'cpd_sidebar_bg_color'
    )));

    // Widget Link Background Colour
    $wp_customize->add_setting('cpd_widget_link_bg_color', array(
        'default'           => $color_scheme[6],
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'cpd_widget_link_bg_color', array(
        'label'       => __('Widget Link Background Colour', 'cpd'),
        'description' => __('Set the background colour for all widget links.', 'cpd'),
        'section'     => 'cpd_sidebar',
        'settings'    => 'cpd_widget_link_bg_color'
    )));

    // Widget Link Background Colour Alternative
    $wp_customize->add_setting('cpd_widget_link_bg_color_alt', array(
        'default'           => $color_scheme[7],
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'cpd_widget_link_bg_color_alt', array(
        'label'       => __('Widget Link Alternative Background Colour', 'cpd'),
        'description' => __('Set the background colour for all widget links when in hover/active state.', 'cpd'),
        'section'     => 'cpd_sidebar',
        'settings'    => 'cpd_widget_link_bg_color_alt'
    )));

    // Widget Text Link Colour
    $wp_customize->add_setting('cpd_widget_link_color', array(
        'default'           => $color_scheme[8],
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'cpd_widget_link_color', array(
        'label'       => __('Widget Link Colour', 'cpd'),
        'description' => __('Set the text colour for all widget links.', 'cpd'),
        'section'     => 'cpd_sidebar',
        'settings'    => 'cpd_widget_link_color'
    )));

    // Widget Link Text Color Alternative
    $wp_customize->add_setting('cpd_widget_link_color_alt', array(
        'default'           => $color_scheme[9],
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'cpd_widget_link_color_alt', array(
        'label'       => __('Widget Link Alternative Colour', 'cpd'),
        'description' => __('Set the text colour for all widget links when in hover/active state.', 'cpd'),
        'section'     => 'cpd_sidebar',
        'settings'    => 'cpd_widget_link_color_alt'
    )));

    // Widget Heading Background Colour
    $wp_customize->add_setting('cpd_widget_heading_bg_color', array(
        'default'           => $color_scheme[10],
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'cpd_widget_heading_bg_color', array(
        'label'       => __('Widget Heading Background Colour', 'cpd'),
        'description' => __('Set the background colour for all widget headings.', 'cpd'),
        'section'     => 'cpd_sidebar',
        'settings'    => 'cpd_widget_heading_bg_color'
    )));

    // Widget Heading Text Colour
    $wp_customize->add_setting('cpd_widget_heading_color', array(
        'default'           => $color_scheme[11],
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'cpd_widget_heading_color', array(
        'label'       => __('Widget Heading Text Colour', 'cpd'),
        'description' => __('Set the text colour for all widget headings.', 'cpd'),
        'section'     => 'cpd_sidebar',
        'settings'    => 'cpd_widget_heading_color'
    )));
}
add_action('customize_register', 'cpd_customize_sidebar', 24);

/**
 * Customizer options - Main Content Area
 */
function cpd_customize_main($wp_customize)
{
    $color_scheme = twentyfifteen_get_color_scheme();

    $wp_customize->add_section('cpd_main' , array(
        'title'    => __('Main Content Area', 'cpd'),
        'priority' => 50
    ));

    // Main Content Area Background Colour
    $wp_customize->add_setting('cpd_main_bg_color', array(
        'default'           => $color_scheme[12],
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'cpd_main_bg_color', array(
        'label'       => __('Background Colour', 'cpd'),
        'description' => __('Set the background colour for the main content area.', 'cpd'),
        'section'     => 'cpd_main',
        'settings'    => 'cpd_main_bg_color'
    )));

    // Article Background Colour
    $wp_customize->add_setting('cpd_article_bg_color', array(
        'default'           => $color_scheme[13],
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'cpd_article_bg_color', array(
        'label'       => __('Article Background Colour', 'cpd'),
        'description' => __('Set the background colour for the article boxes, comment area etc.', 'cpd'),
        'section'     => 'cpd_main',
        'settings'    => 'cpd_article_bg_color'
    )));

    // Article Text Colour
    $wp_customize->add_setting('cpd_article_color', array(
        'default'           => $color_scheme[14],
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'cpd_article_color', array(
        'label'       => __('Article Text Colour', 'cpd'),
        'description' => __('Set the text colour for the article boxes, comment area etc.', 'cpd'),
        'section'     => 'cpd_main',
        'settings'    => 'cpd_article_color'
    )));

    // Article Footer Background Colour
    $wp_customize->add_setting('cpd_article_foot_bg_color', array(
        'default'           => $color_scheme[15],
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'cpd_article_foot_bg_color', array(
        'label'       => __('Article Footer Background Colour', 'cpd'),
        'description' => __('Set the background colour for the footer of the article boxes.', 'cpd'),
        'section'     => 'cpd_main',
        'settings'    => 'cpd_article_foot_bg_color'
    )));

    // Article Footer Text Colour
    $wp_customize->add_setting('cpd_article_foot_color', array(
        'default'           => $color_scheme[16],
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'cpd_article_foot_color', array(
        'label'       => __('Article Footer Text Colour', 'cpd'),
        'description' => __('Set the text colour for the footer of the article boxes.', 'cpd'),
        'section'     => 'cpd_main',
        'settings'    => 'cpd_article_foot_color'
    )));
}
add_action('customize_register', 'cpd_customize_main', 25);

/**
 * Customizer options - Tables
 */
function cpd_customize_tables($wp_customize)
{
    $color_scheme = twentyfifteen_get_color_scheme();

    $wp_customize->add_section('cpd_tables' , array(
        'title'       => __('Table Content', 'cpd'),
        'priority'    => 55
    ));

    // Table Header Background Colour
    $wp_customize->add_setting('cpd_table_head_bg_color', array(
        'default'           => $color_scheme[24],
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'cpd_table_head_bg_color', array(
        'label'       => __('Table Header Background Colour', 'cpd'),
        'description' => __('Set the background colour for the table header.', 'cpd'),
        'section'     => 'cpd_tables',
        'settings'    => 'cpd_table_head_bg_color'
    )));

    // Table Header Text Colour
    $wp_customize->add_setting('cpd_table_head_color', array(
        'default'           => $color_scheme[25],
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'cpd_table_head_color', array(
        'label'       => __('Table Header Text Colour', 'cpd'),
        'description' => __('Set the text colour for the table header.', 'cpd'),
        'section'     => 'cpd_tables',
        'settings'    => 'cpd_table_head_color'
    )));

    // Table Row Background Colour
    $wp_customize->add_setting('cpd_table_row_bg_color', array(
        'default'           => $color_scheme[26],
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'cpd_table_row_bg_color', array(
        'label'       => __('Table Header Background Colour', 'cpd'),
        'description' => __('Set the background colour for all table rows.', 'cpd'),
        'section'     => 'cpd_tables',
        'settings'    => 'cpd_table_row_bg_color'
    )));

    // Table Row Text Colour
    $wp_customize->add_setting('cpd_table_row_color', array(
        'default'           => $color_scheme[27],
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'cpd_table_row_color', array(
        'label'       => __('Table Row Text Colour', 'cpd'),
        'description' => __('Set the text colour for all table rows.', 'cpd'),
        'section'     => 'cpd_tables',
        'settings'    => 'cpd_table_row_color'
    )));

    // Table Row Link Colour
    $wp_customize->add_setting('cpd_table_row_link_color', array(
        'default'           => $color_scheme[28],
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'cpd_table_row_link_color', array(
        'label'       => __('Table Row Link Colour', 'cpd'),
        'description' => __('Set the text colour for all table row links.', 'cpd'),
        'section'     => 'cpd_tables',
        'settings'    => 'cpd_table_row_link_color'
    )));
}
add_action('customize_register', 'cpd_customize_tables', 26);

/**
 * Customizer options - Footer
 */
function cpd_customize_footer($wp_customize)
{
    $color_scheme = twentyfifteen_get_color_scheme();

    $wp_customize->add_section('cpd_footer' , array(
        'title'       => __('Footer Area', 'cpd'),
        'priority'    => 50
    ));

    // Footer Background Colour
    $wp_customize->add_setting('cpd_footer_bg_color', array(
        'default'           => $color_scheme[21],
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'cpd_footer_bg_color', array(
        'label'       => __('Background Colour', 'cpd'),
        'description' => __('Set the background colour of the site footer.', 'cpd'),
        'section'     => 'cpd_footer',
        'settings'    => 'cpd_footer_bg_color'
    )));

    // Footer Bottom Background Colour
    $wp_customize->add_setting('cpd_footer_bottom_bg_color', array(
        'default'           => $color_scheme[22],
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'cpd_footer_bottom_bg_color', array(
        'label'       => __('Bottom Background Colour', 'cpd'),
        'description' => __('Set the background colour of the bottom bar in the site footer.', 'cpd'),
        'section'     => 'cpd_footer',
        'settings'    => 'cpd_footer_bottom_bg_color'
    )));

    // Footer Text Color
    $wp_customize->add_setting('cpd_footer_color', array(
        'default'           => $color_scheme[23],
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'cpd_footer_color', array(
        'label'       => __('Text Colour', 'cpd'),
        'description' => __('Set the text colour of the site footer.', 'cpd'),
        'section'     => 'cpd_footer',
        'settings'    => 'cpd_footer_color'
    )));

    // Credit Image
    $wp_customize->add_setting('cpd_credit');

    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'cpd_credit', array(
        'label'       => __('Credit Logo', 'cpd'),
        'description' => 'Set an image that will appear in the footer, acting as a credit. It is recommended that this image is a JPG rather than a PNG.',
        'section'     => 'cpd_footer',
        'settings'    => 'cpd_credit',
    )));

    // Credit URL
    $wp_customize->add_setting('cpd_credit_url', array(
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Control($wp_customize, 'cpd_credit_url', array(
        'label'    => __('Credit Link URL', 'cpd'),
        'description' => 'Enter a valid URL which.',
        'section'  => 'cpd_footer',
        'settings' => 'cpd_credit_url',
        'type'     => 'text'
    )));
}
add_action('customize_register', 'cpd_customize_footer', 27);

/**
 * Customizer options - Advisory Notice
 */
function cpd_customize_advisory($wp_customize)
{
    $color_scheme = twentyfifteen_get_color_scheme();

    $wp_customize->add_section('cpd_advisory' , array(
        'title'       => __('Advisory Notice', 'cpd'),
        'priority'    => 60
    ));

    // Show Advisory
    $wp_customize->add_setting('cpd_advisory_show', array(
        'transport'      => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Control($wp_customize, 'cpd_advisory_show', array(
        'label'    => __('Show notice?', 'cpd'),
        'section'  => 'cpd_advisory',
        'settings' => 'cpd_advisory_show',
        'type'     => 'checkbox'
    )));

    // Advisory Notice Text
    $wp_customize->add_setting('cpd_advisory_notice', array(
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Control($wp_customize, 'cpd_advisory_notice', array(
        'label'    => __('Notice Text', 'cpd'),
        'section'  => 'cpd_advisory',
        'settings' => 'cpd_advisory_notice',
        'type'     => 'textarea'
    )));

    // Advisory Background Colour
    $wp_customize->add_setting('cpd_advisory_bg_color', array(
        'default'           => $color_scheme[19],
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'cpd_advisory_bg_color', array(
        'label'       => __('Background Colour', 'cpd'),
        'description' => __('Set the background colour of the advisory notice.', 'cpd'),
        'section'     => 'cpd_advisory',
        'settings'    => 'cpd_advisory_bg_color'
    )));

     // Advisory Text Colour
    $wp_customize->add_setting('cpd_advisory_color', array(
        'default'           => $color_scheme[15],
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'cpd_advisory_color', array(
        'label'       => __('Text Colour', 'cpd'),
        'description' => __('Set the text colour of the advisory notice.', 'cpd'),
        'section'     => 'cpd_advisory',
        'settings'    => 'cpd_advisory_color'
    )));
}
add_action('customize_register', 'cpd_customize_advisory', 28);

/**
 * Add/Remove Colour Schemes
 * NOTE: We have to declare a new default because we've added lots of
 * our own colour options.
 * ---------------------------------------
 * 6.  Widget Link Background Colour
 * 7.  Widget Link Background Colour Alt
 * 8.  Widget Link Colour
 * 9.  Widget Link Colour Alt
 * 10. Widget Heading Background Colour
 * 11. Widget Heading Colour
 * 12. Main Content Background Colour
 * 13. Article Background Colour
 * 14. Article Text Colour
 * 15. Article Footer Background Colour
 * 16. Article Footer Text Colour
 * 17. Header & Sidebar Background Colour
 * 18. Site Title & Tagline Colour
 * 19. Advisory Notice Background Colour
 * 20. Advisory Notice Text Colour
 * 21. Footer Background Colour
 * 22. Footer Bottom Background Colour
 * 23. Footer Text Colour
 * 24. Table Head Background Colour
 * 25. Table Head Text Colour
 * 26. Table Row Background Colour
 * 27. Table Row Text Colour
 * 28. Table Row Link Colour
 * ---------------------------------------
 */
function cpd_color_schemes($schemes)
{
    // Remove the schemes that ship with Twenty Fifteen
    foreach ($schemes as $key => $scheme) {
            unset($schemes[$key]);
    }

    // Default
    $schemes['default'] = array(
        'label'  => __('Default', 'cpd'),
        'colors' => array(
            '#f1f1f1',
            '#f1f1f1',
            '#ffffff',
            '#333333',
            '#333333',
            '#f7f7f7',
            '#ffffff', // 6
            '#f7f7f7', // 7
            '#333333', // 8
            '#333333', // 9
            '#ffffff', // 10
            '#333333', // 11
            '#f1f1f1', // 12
            '#ffffff', // 13
            '#333333', // 14
            '#f7f7f7', // 15
            '#333333', // 16
            '#ffffff', // 17
            '#333333', // 18
            '#008000', // 19
            '#ffffff', // 20
            '#f7f7f7', // 21
            '#030000', // 22
            '#ffffff', // 23
            '#030000', // 24
            '#ffffff', // 25
            '#f7f7f7', // 26
            '#333333', // 27
            '#333333'  // 28
        ),
    );

    // Grey
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
            '#f1f1f1', // 17
            '#414042', // 18
            '#008000', // 19
            '#ffffff', // 20
            '#414042', // 21
            '#030000', // 22
            '#ffffff', // 23
            '#030000', // 24
            '#ffffff', // 25
            '#414042', // 26
            '#ffffff', // 27
            '#ffffff'  // 28
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
            '#f1f1f1', // 17
            '#414042', // 18
            '#008000', // 19
            '#ffffff', // 20
            '#0066b3', // 21
            '#030000', // 22
            '#ffffff', // 23
            '#030000', // 24
            '#ffffff', // 25
            '#ffffff', // 26
            '#030000', // 27
            '#0066b3'  // 28
        ),
    );

    return $schemes;
}
add_filter('twentyfifteen_color_schemes', 'cpd_color_schemes');

/**
 * Enqueues front-end CSS for a CPD specific color scheme.
 */
function cpd_enqueue_css()
{
    $color_scheme_option = get_theme_mod('color_scheme', 'default');

    // Set the current colors based on the saved options
    $colors = array(
        'cpd_widget_link_bg_color'     => get_theme_mod('cpd_widget_link_bg_color'),
        'cpd_widget_link_bg_color_alt' => get_theme_mod('cpd_widget_link_bg_color_alt'),
        'cpd_widget_link_color'        => get_theme_mod('cpd_widget_link_color'),
        'cpd_widget_link_color_alt'    => get_theme_mod('cpd_widget_link_color_alt'),
        'cpd_widget_heading_bg_color'  => get_theme_mod('cpd_widget_heading_bg_color'),
        'cpd_widget_heading_color'     => get_theme_mod('cpd_widget_heading_color'),
        'cpd_main_bg_color'            => get_theme_mod('cpd_main_bg_color'),
        'cpd_article_bg_color'         => get_theme_mod('cpd_article_bg_color'),
        'cpd_article_color'            => get_theme_mod('cpd_article_color'),
        'cpd_article_foot_bg_color'    => get_theme_mod('cpd_article_foot_bg_color'),
        'cpd_article_foot_color'       => get_theme_mod('cpd_article_foot_color'),
        'cpd_sidebar_bg_color'         => get_theme_mod('cpd_sidebar_bg_color'),
        'cpd_intro_color'              => get_theme_mod('cpd_intro_color'),
        'cpd_advisory_bg_color'        => get_theme_mod('cpd_advisory_bg_color'),
        'cpd_advisory_color'           => get_theme_mod('cpd_advisory_color'),
        'cpd_footer_bg_color'          => get_theme_mod('cpd_footer_bg_color'),
        'cpd_footer_bottom_bg_color'   => get_theme_mod('cpd_footer_bottom_bg_color'),
        'cpd_footer_color'             => get_theme_mod('cpd_footer_color'),
        'cpd_table_head_bg_color'      => get_theme_mod('cpd_table_head_bg_color'),
        'cpd_table_head_color'         => get_theme_mod('cpd_table_head_color'),
        'cpd_table_row_bg_color'       => get_theme_mod('cpd_table_row_bg_color'),
        'cpd_table_row_color'          => get_theme_mod('cpd_table_row_color'),
        'cpd_table_row_link_color'     => get_theme_mod('cpd_table_row_link_color')
    );

    // Alternative row colour - RGBA version of the main row background colour
    $color_row_alt_color_rgb              = twentyfifteen_hex2rgb($colors['cpd_table_row_bg_color']);
    $colors['cpd_table_row_alt_bg_color'] = vsprintf('rgba( %1$s, %2$s, %3$s, 0.85)', $color_row_alt_color_rgb);

    // Child link background colour - RGBA version of the main row background colour
    $color_child_link_bg_color_rgb            = twentyfifteen_hex2rgb($colors['cpd_widget_link_bg_color']);
    $colors['cpd_widget_child_link_bg_color'] = vsprintf('rgba( %1$s, %2$s, %3$s, 0.6)', $color_child_link_bg_color_rgb);

    // Get our font stacks and Google Fonts URL
    $font_data = cpd_get_fonts();

    // Enqueue the additional fonts
    wp_enqueue_style('cpd-customizer-fonts', $font_data['url'], array(), null);

    // Fetch our CSS output
    $css_output = cpd_get_css($colors, $font_data['stacks']);

    // Add the inline CS
    wp_add_inline_style('cpd-style', $css_output);
}
add_action('wp_enqueue_scripts', 'cpd_enqueue_css', 100);

/**
 * Returns Font stack selections and an encoded Google Fonts URL
 */
function cpd_get_fonts()
{
    $fonts_stacks  = array();
    $fonts_enqueue = array();
    $fonts_url     = '';
    $pattern       = "/^'(.*)'/";

    // Get the current font choices
    $font_choices = array(
        'body'    => get_theme_mod('cpd_font_body'),
        'heading' => get_theme_mod('cpd_font_heading'),
    );

    // Let's get the relevant stack for the body and heading choices
    foreach ($font_choices as $type => $choice) {

        switch ($choice) {
            case 'noto-serif':
                $chosen = "'Noto Serif', serif";
            break;

            case 'pt-serif':
                $chosen = "'PT Serif', serif";
            break;

            case 'noto-sans':
                $chosen = "'Noto Sans', sans-serif";
            break;

            case 'open-sans':
                $chosen = "'Open Sans', sans-serif";
            break;

            default:
                if ($type === 'body') {
                    $chosen = "'Noto Sans', sans-serif";
                } else {
                    $chosen = "'Noto Serif', serif";
                }
            break;
        }

        // Now we can pass these for output
        $fonts_stacks[$type]    = $chosen;

        // Construct a Google Fonts URL
        $pos = strpos($chosen, 'Noto');
        if ($pos === false) {
            preg_match($pattern, $chosen, $matches);
            $fonts_enqueue[] = $matches[1] . ":400italic,700italic,400,700";
        }
    };

    // Construct our Google Fonts URL
    if ($fonts_enqueue) {
        $fonts_url = add_query_arg(array(
            'family' => urlencode(implode( '|', array_unique($fonts_enqueue))),
        ), '//fonts.googleapis.com/css' );
    }

    // Prepare the data to send back
    $results = array(
        'stacks' => $fonts_stacks,
        'url'    => $fonts_url);

    return $results;
}

/**
 * Returns CSS for the CPD color schemes.
 */
function cpd_get_css($colors, $fonts)
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

    /* Fonts */
    body,
    .site-description {
        font-family: {$fonts['body']};
    }

    h1, h2, h3, h4, h5, h6,
    .site-title {
        font-family: {$fonts['heading']};
    }

    /* Site Title & Tagline Color */
    .intro .site-title,
    .intro .site-description {
        color: {$colors['cpd_intro_color']};
    }

    /* Widget Links & Text Widgets */
    .main-navigation li,
    .widget li,
    .textwidget,
    .tagcloud {
        background-color: {$colors['cpd_widget_link_bg_color']};
        color: {$colors['cpd_widget_link_color']};
    }

    .main-navigation li a,
    .widget li a {
        color: {$colors['cpd_widget_link_color']};
    }

    .main-navigation li:hover,
    .main-navigation li:hover a,
    .main-navigation li:focus,
    .main-navigation li:focus a,
    .main-navigation li a:hover,
    .main-navigation li a:focus,
    .main-navigation li.current_page_item,
    .main-navigation li.current_page_item a,
    .main-navigation li.current_page_parent,
    .main-navigation li.current_page_parent a,
    .main-navigation li.current_page_ancestor,
    .main-navigation li.current_page_ancestor a,
    .widget li:hover,
    .widget li:hover a,
    .widget li:focus,
    .widget li:focus a,
    .widget li a:hover,
    .widget li a:focus,
    .widget li.current_page_item,
    .widget li.current_page_item a,
    .widget li.current_page_parent,
    .widget li.current_page_parent a,
    .widget li.current_page_ancestor,
    .widget li.current_page_ancestor a {
        background-color: {$colors['cpd_widget_link_bg_color_alt']};
        color: {$colors['cpd_widget_link_color_alt']};
    }

    .main-navigation .sub-menu,
    .widget .children {
        background-color: {$colors['cpd_widget_link_bg_color']};
    }

    .main-navigation .sub-menu li,
    .main-navigation .sub-menu li a,
    .widget .children li,
    .widget .children li a {
        background-color: {$colors['cpd_widget_child_link_bg_color']};
        color: {$colors['cpd_widget_link_color']};
    }

    .main-navigation .sub-menu li:hover,
    .main-navigation .sub-menu li:hover a,
    .main-navigation .sub-menu li:focus,
    .main-navigation .sub-menu li:focus a,
    .main-navigation .sub-menu li a:hover,
    .main-navigation .sub-menu li a:focus,
    .main-navigation .sub-menu li.current_page_item,
    .main-navigation .sub-menu li.current_page_item a,
    .widget .children li:hover,
    .widget .children li:hover a,
    .widget .children li:focus,
    .widget .children li:focus a,
    .widget .children li a:hover,
    .widget .children li a:focus,
    .widget .children li.current_page_item,
    .widget .children li.current_page_item a {
        background-color: {$colors['cpd_widget_link_bg_color_alt']};
        color: {$colors['cpd_widget_link_color_alt']};
    }

    .widget .recentcomments {
        font-family: {$fonts['heading']};
    }

    .widget .recentcomments,
    .widget .recentcomments a,
    .widget .recentcomments span {
        transition: color ease-in-out .3s;
    }

    .widget .recentcomments:hover a,
    .widget .recentcomments:hover span,
    .widget .recentcomments:focus a,
    .widget .recentcomments:focus span {
        color: {$colors['cpd_widget_link_color_alt']};
    }

    .widget .tagcloud a,
    .widget .tagcloud a:hover,
    .widget .tagcloud a:focus {
        color: {$colors['cpd_widget_link_color']};
    }

    /* Widget Headings */
    h2.widget-title {
        background-color: {$colors['cpd_widget_heading_bg_color']};
        color: {$colors['cpd_widget_heading_color']};
    }

    /* Search Widget */

    .widget .search-form {
        color: {$colors['cpd_widget_link_color']};
    }

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
    .site-main h1,
    .site-main h1 a,
    .site-main h2,
    .site-main h2 a,
    .site-main h3,
    .site-main h3 a,
    .site-main h4,
    .site-main h4 a,
    .site-main h5,
    .site-main h5 a,
    .site-main h6,
    .site-main h6 a,
    .site-main .comment-content {
        color: {$colors['cpd_article_color']};
    }

    .site-main blockquote,
    .site-main .entry-content a,
    .site-main .entry-summary a,
    .site-main .page-content a,
    .site-main .comment-meta a,
    .site-main .comment-content a,
    .site-main .comment-respond a,
    .site-main .pingback .comment-body > a {
        border-color: {$colors['cpd_article_color']};
        color: {$colors['cpd_article_color']};
    }

    blockquote,
    .main-navigation .menu-item-description,
    .post-navigation .meta-nav,
    .post-navigation a,
    .post-navigation a:hover .post-title,
    .post-navigation a:focus .post-title,
    .image-navigation, .image-navigation a,
    .comment-navigation, .comment-navigation a,
    .widget, .author-heading, .taxonomy-description,
    .page-links > .page-links-title,
    .entry-caption, .comment-author,
    .comment-metadata, .comment-metadata a,
    .pingback .edit-link,
    .pingback .edit-link a,
    .post-password-form label,
    .comment-form label,
    .comment-notes,
    .comment-awaiting-moderation,
    .logged-in-as,
    .form-allowed-tags,
    .no-comments,
    .site-info,
    .site-info a,
    .wp-caption-text,
    .gallery-caption,
    .comment-list .reply a,
    .widecolumn label,
    .widecolumn .mu_register label {
        color: {$colors['cpd_article_color']} !important;
    }

    pre,
    abbr[title],
    table,
    th,
    td,
    input,
    textarea,
    .main-navigation ul,
    .main-navigation li,
    .post-navigation,
    .post-navigation div + div,
    .pagination,
    .comment-navigation,
    .widget li,
    .widget_categories .children,
    .widget_nav_menu .sub-menu,
    .widget_pages .children,
    .site-header,
    .site-footer,
    .hentry + .hentry,
    .author-info,
    .entry-content .page-links a,
    .page-links > span,
    .page-header,
    .comments-area,
    .comment-list + .comment-respond,
    .comment-list article,
    .comment-list .pingback,
    .comment-list .trackback,
    .comment-list .reply a,
    .no-comments {
        border-color: {$colors['cpd_article_color']} !important;
    }

    ins {
        background-color: {$colors['cpd_article_foot_bg_color']};
        color: {$colors['cpd_article_foot_color']};
    }

    /* Article Entry Footer */
    .site-main .entry-footer {
        background-color: {$colors['cpd_article_foot_bg_color']};
    }

    .site-main .entry-footer,
    .site-main .entry-footer a,
    .site-main .entry-footer a:hover,
    .site-main .entry-footer a:focus {
        color: {$colors['cpd_article_foot_color']};
    }

    .site-main .entry-footer a:hover,
    .site-main .entry-footer a:focus {
        border-color: {$colors['cpd_article_foot_color']};
    }

    /* Advisory Notice */
    .advisory-notice {
        background-color: {$colors['cpd_advisory_bg_color']};
    }

    .advisory-notice p {
        color: {$colors['cpd_advisory_color']};
    }

    /* Footer */
    footer[role="contentinfo"] {
        background-color: {$colors['cpd_footer_bg_color']};
    }

    .bottom-wrapper {
        background-color: {$colors['cpd_footer_bottom_bg_color']};
    }

    .bottom p,
    .bottom a {
        color: {$colors['cpd_footer_color']};
    }

    .bottom a {
        border-color: {$colors['cpd_footer_bottom_bg_color']};;
    }

    .bottom a:hover,
    .bottom a:focus {
        border-color: {$colors['cpd_footer_color']};
        color: {$colors['cpd_footer_color']};
    }

    /* PPD Archive */

    .ppd-archive th {
        background-color: {$colors['cpd_table_head_bg_color']};
        color: {$colors['cpd_table_head_color']};
    }

    .ppd-archive .odd {
        background-color: {$colors['cpd_table_row_bg_color']};
    }

    .ppd-archive .even {
        background-color: {$colors['cpd_table_row_alt_bg_color']};
    }

    .ppd-archive .odd,
    .ppd-archive .even {
        color: {$colors['cpd_table_row_color']};
    }

    .ppd-archive .odd a,
    .ppd-archive .even a {
        border-color: {$colors['cpd_table_row_link_color']};
    }

    .ppd-archive .odd a,
    .ppd-archive .odd a:hover,
    .ppd-archive .odd a:focus,
    .ppd-archive .even a,
    .ppd-archive .even a:hover,
    .ppd-archive .even a:focus {
        color: {$colors['cpd_table_row_link_color']};
    }

    .ppd-archive a {
        border-color: {$colors['cpd_table_row_link_color']};
        color: {$colors['cpd_table_row_link_color']};
    }

    /* PPD Single */

    .type-ppd .panel {
        background-color: {$colors['cpd_article_foot_bg_color']};
        color: {$colors['cpd_article_foot_color']};
    }

    .type-ppd .panel h2 {
        color: {$colors['cpd_article_foot_color']};
    }

CSS;

    return $css;
}

/**
 * Output an Underscore template for generating CSS for the CPD color scheme.
 */
function cpd_css_template()
{
    $colors = array(
        'cpd_widget_link_bg_color'       => '{{ data.cpd_widget_link_bg_color }}',
        'cpd_widget_link_bg_color_alt'   => '{{ data.cpd_widget_link_bg_color_alt }}',
        'cpd_widget_link_color'          => '{{ data.cpd_widget_link_color }}',
        'cpd_widget_link_color_alt'      => '{{ data.cpd_widget_link_color_alt }}',
        'cpd_widget_child_link_bg_color' => '{{ data.cpd_widget_child_link_bg_color }}',
        'cpd_widget_heading_bg_color'    => '{{ data.cpd_widget_heading_bg_color }}',
        'cpd_widget_heading_color'       => '{{ data.cpd_widget_heading_color }}',
        'cpd_main_bg_color'              => '{{ data.cpd_main_bg_color }}',
        'cpd_article_bg_color'           => '{{ data.cpd_article_bg_color }}',
        'cpd_article_color'              => '{{ data.cpd_article_color }}',
        'cpd_article_foot_bg_color'      => '{{ data.cpd_article_foot_bg_color }}',
        'cpd_article_foot_color'         => '{{ data.cpd_article_foot_color }}',
        'cpd_sidebar_bg_color'           => '{{ data.cpd_sidebar_bg_color }}',
        'cpd_intro_color'                => '{{ data.cpd_intro_color }}',
        'cpd_advisory_bg_color'          => '{{ data.cpd_advisory_bg_color }}',
        'cpd_advisory_color'             => '{{ data.cpd_advisory_color }}',
        'cpd_footer_bg_color'            => '{{ data.cpd_footer_bg_color }}',
        'cpd_footer_bottom_bg_color'     => '{{ data.cpd_footer_bottom_bg_color }}',
        'cpd_footer_color'               => '{{ data.cpd_footer_color }}',
        'cpd_table_head_bg_color'        => '{{ data.cpd_table_head_bg_color }}',
        'cpd_table_head_color'           => '{{ data.cpd_table_head_color }}',
        'cpd_table_row_bg_color'         => '{{ data.cpd_table_row_bg_color }}',
        'cpd_table_row_alt_bg_color'     => '{{ data.cpd_table_row_alt_bg_color }}',
        'cpd_table_row_color'            => '{{ data.cpd_table_row_color }}',
        'cpd_table_row_link_color'       => '{{ data.cpd_table_row_link_color }}',
    );
    ?>
    <script type="text/html" id="tmpl-cpd-color-scheme">
        <?php echo cpd_get_css($colors, $fonts); ?>
    </script>
    <?php
}
add_action('customize_controls_print_footer_scripts', 'cpd_css_template', 100);

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
