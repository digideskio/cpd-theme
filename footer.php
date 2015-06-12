<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the "site-content" div and all content after.
 */
?>

    </div><!-- .site-content -->

</div><!-- .site -->


    <footer id="colophon" role="contentinfo">

        <?php do_action('cpd_footer'); ?>

        <div class="credits">

            <?php
            $watermark = get_theme_mod('cpd_watermark');
            if ($watermark) { ?>
                <div class="left">
                    <img src='<?php echo esc_url($watermark); ?>' alt="<?php echo get_bloginfo('name'); ?>">
                </div>
                <?php
            } ?>

            <?php
            $credit = get_theme_mod('cpd_credit');
            if ($credit) {
                $url = get_theme_mod('cpd_credit_url');
                ?>
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
                <a href="<?php echo network_site_url(); ?>">Back to Main Blog</a>
                <ul class="footer-menu">
                    <?php
                    $args = array(
                        'theme_location' => 'footer',
                        'container'      => '',
                        'items_wrap'     => '%3$s'
                    );
                    wp_nav_menu($args); ?>
                </ul>
            </div>

            <div class="right">
                <p>
                    Powered by <a href="http://aspirecpd.org" title="Aspire CPD">Aspire CPD</a>.
                    <br/>
                    &copy; <?php echo date('Y'); ?> <?php echo bloginfo('name'); ?>.
                </p>
            </div>

        </div>

    </div>

<?php wp_footer(); ?>

</body>
</html>
