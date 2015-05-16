<?php

/**
 * Cleanup Twenty Fifteen
 */

function cpd_cleanup_twentyfifteen()
{
    remove_theme_support('custom-background');
    remove_theme_support('custom-header');

    unregister_nav_menu('social');
}
add_action('init', 'cpd_cleanup_twentyfifteen');

/**
 * Set-up CPD
 */
function cpd_setup()
{
    // Add in our footer menu
    register_nav_menus( array(
        'primary' => __( 'Primary Menu', 'cpd'),
        'footer'  => __( 'Footer Menu', 'cpd'),
    ) );
}
add_action('after_setup_theme','cpd_setup');

/**
 * Enqueue assets
 */
function cpd_enqueue_assets()
{
    // Get our font stacks and Google Fonts URL
    $fonts = cpd_get_fonts();

    // Enqueue the additional fonts
    wp_enqueue_style( 'cpd-fonts', $fonts['url'], array(), null );

    // Parent theme CSS
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');

    // Child theme CSS
    wp_enqueue_style('cpd-style', get_stylesheet_uri());

    // Child header JS
    wp_enqueue_script('header', get_stylesheet_directory_uri() . '/js/header.min.js', array(), '0.1', false);

    // Child footer JS
    wp_enqueue_script('footer', get_stylesheet_directory_uri() . '/js/footer.min.js', array('jquery', 'jquery-ui'), '0.1', true);
}
add_action('wp_enqueue_scripts', 'cpd_enqueue_assets');

/**
 * Additional dependencies
 */

require get_stylesheet_directory() . '/inc/customizer.php';
