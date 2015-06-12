<?php
/**
 * The template for displaying 404 pages (not found)
 */

get_header(); ?>

    <div id="primary" class="content-area">
        <main id="main" class="site-main" role="main">

            <section class="hentry error-404 not-found">
                <header class="entry-header">
                    <h1 class="entry-title"><?php _e( 'Oops! That page can&rsquo;t be found.', 'cpd' ); ?></h1>
                </header>

                <div class="entry-content">
                    <p><?php _e( 'It looks like nothing was found at this location. Maybe try a search?', 'cpd' ); ?></p>
                    <?php get_search_form(); ?>
                </div>
            </section>

        </main>
    </div>

<?php get_footer(); ?>
