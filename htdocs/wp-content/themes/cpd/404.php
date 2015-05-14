<?php
/**
 * The template for displaying 404 pages (not found)
 */

get_header(); ?>

    <div id="primary" class="content-area">
        <main id="main" class="site-main" role="main">

            <section class="hentry error-404 not-found">
                <header class="entry-header">
                    <h1 class="entry-title"><?php _e( 'Oops! That page can&rsquo;t be found.', 'twentyfifteen' ); ?></h1>
                </header><!-- .page-header -->

                <div class="entry-content">
                    <p><?php _e( 'It looks like nothing was found at this location. Maybe try a search?', 'twentyfifteen' ); ?></p>

                    <?php get_search_form(); ?>
                </div><!-- .page-content -->
            </section><!-- .error-404 -->

        </main><!-- .site-main -->
    </div><!-- .content-area -->

<?php get_footer(); ?>
