<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the "site-content" div and all content after.
 *
 * @package WordPress
 * @subpackage Twenty_Fifteen
 * @since Twenty Fifteen 1.0
 */
?>

    </div><!-- .site-content -->

</div><!-- .site -->


    <footer id="colophon" role="contentinfo">

        <?php
            /**
             * Fires before the Twenty Fifteen footer text for footer customization.
             *
             * @since Twenty Fifteen 1.0
             */
            do_action('cpd_footer');
        ?>

        <div class="credits">

            <?php
            $logo = get_theme_mod('cpd_logo');
            if ($logo) { ?>
                <div class="left">
                    <img src='<?php echo esc_url($logo); ?>' alt="<?php echo get_bloginfo('name'); ?>">
                </div>
                <?php
            } ?>

            <?php
            $credit = get_theme_mod('cpd_credit');
            $url    = get_theme_mod('cpd_credit_url');
            if ($credit) { ?>
                <div class="right">
                    <?php if ($url) { ?>
                        <a href="<?php echo $url; ?>" target="_title"><img src='<?php echo esc_url($credit); ?>'></a>
                    <?php } else { ?>
                        <img src='<?php echo esc_url($credit); ?>'>
                    <?php } ?>
                </div>
                <?php
            } ?>

        </div>

    </footer><!-- .site-footer -->

    <div class="bottom-wrapper">

        <div class="bottom">

            <div class="left">
                <?php
                $args = array(
                    'theme_location' => 'footer',
                    'container'      => '',
                    'menu_class'     => 'footer-menu'
                );
                wp_nav_menu($args); ?>
            </div>

            <div class="right">
                <p>&copy; <?php echo date('Y'); ?> <?php echo bloginfo('name'); ?>. Powered by <a href-"#" title="Aspire CPD">Aspire CPD</a></p>
            </div>

        </div>
    </div>

<?php wp_footer(); ?>

</body>
</html>
