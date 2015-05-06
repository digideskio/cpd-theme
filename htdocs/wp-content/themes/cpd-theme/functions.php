<?php

/**
 * Enqueue scripts & styles
 */
function cpd_enqueue_assets()
{
    // Parent theme CSS
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );

    // Child header JS
    wp_enqueue_script( 'header', get_stylesheet_directory_uri() . '/js/header.min.js', array(), '0.1', false );

    // Child footer JS
    wp_enqueue_script( 'footer', get_stylesheet_directory_uri() . '/js/footer.min.js', array('jquery', 'jquery-ui'), '0.1', true );

}
add_action( 'wp_enqueue_scripts', 'cpd_enqueue_assets' );
