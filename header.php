<?php
/**
 * The template for displaying the header
 *
 * Displays all of the head element and everything up until the "site-content" div.
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width">
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">
    <!--[if lt IE 9]>
        <script src="<?php echo esc_url(get_template_directory_uri()); ?>/js/html5.js"></script>
    <![endif]-->
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="page" class="hfeed site">
    <a class="skip-link screen-reader-text" href="#content"><?php _e('Skip to content', 'cpd'); ?></a>

    <div class="sidebar">
        <header id="masthead" class="site-header" role="banner">
            <div class="branding">
                <?php
                    $logo    = get_theme_mod('cpd_logo');
                    $name    = get_bloginfo('name','display');
                    $tagtext = get_bloginfo( 'description','display');
                    $tagpos  = get_theme_mod('cpd_tagline_pos');

                    if ($logo) { ?>
                            <a href='<?php echo esc_url(home_url('/')); ?>' title='<?php echo esc_attr($name); ?>' rel='home'>
                            <img src='<?php echo esc_url($logo); ?>' alt='<?php echo esc_attr($name); ?>'></a>
                        <?php
                    } ?>

                        <button class="secondary-toggle"><?php _e( 'Menu and widgets', 'twentyfifteen' ); ?></button>

                    </div><!-- .site-branding -->

                    <?php
                        if ($tagpos === 'left') {
                            if ($tagtext) { ?>
                                <div class="intro left">
                                    <h1 class="site-title"><?php echo $name ?></h1>
                                    <p class="site-description"><?php echo $tagtext; ?></h2>
                                </div>
                            <?php
                            }
                        }
                    ?>
        </header><!-- .site-header -->

        <?php get_sidebar(); ?>

    </div><!-- .sidebar -->

    <div id="content" class="site-content">

        <?php
            if ($tagpos === 'right') { ?>
                <div class="intro right">
                    <h1 class="site-title"><?php echo $name ?></h1>
                    <p class="site-description"><?php echo $tagtext; ?></h2>
                </div>
            <?php
            }
        ?>

        <?php
            $show   = get_theme_mod('cpd_advisory_show');
            $notice = get_theme_mod('cpd_advisory_notice');
            if ($notice === '') {
                $notice = 'No advisory notice text has been entered.';
            }

            if ($show || is_customize_preview()) {
                ?>
                <div class="advisory-notice">
                    <p><?php echo $notice; ?></p>
                </div>
            <?php
            }
        ?>
