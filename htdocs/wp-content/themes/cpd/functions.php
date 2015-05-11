<?php

/**
 * Enqueue assets
 */
function cpd_enqueue_assets()
{

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
 * Cleanup Twenty Fifteen
 */

function cpd_cleanup_twentyfifteen()
{
    remove_theme_support('custom-background');
    remove_theme_support('custom-header');
}
add_action( 'init', 'cpd_cleanup_twentyfifteen' );

/**
 * Additional dependencies
 */

require get_stylesheet_directory() . '/inc/customizer.php';
