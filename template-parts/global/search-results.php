<?php

    global $wp_query;


?>


<section class="search-results-list grid">
    <?php if ( have_posts() ):?>
        <div class="search-results-list__summary">
            <h5 class="upper-header">Showing Results for</h5>
            <h2 class="search-results-list__title">“<?php echo get_search_query(); ?>”</h2>
        </div>

        <div class="search-results-list__header">
            <div class="count">
                <?php echo $wp_query->found_posts.' results'; ?>
            </div>

        </div>

        <?php while ( have_posts() ): the_post(); ?>

            <?php
                $args = ['p' => $post->ID];
                get_template_part('template-parts/global/teaser/teaser-search-result', null, $args);
            ?>

        <?php endwhile; ?>
        
        <?php global $wp_query; if($wp_query->found_posts > 10): ?>
            <div class="pagination">
                <?php                    
                    $big = 999999999; // need an unlikely integer
                        
                    echo paginate_links( array(
                        'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
                        'format' => '?paged=%#%',
                        'current' => max( 1, get_query_var('paged') ),
                        'total' => $wp_query->max_num_pages
                    ) );
                ?>
            </div>
        <?php else: ?>
            <div class="no-pagination"></div>
        <?php endif; ?>

    <?php else: ?>

        <div class="search-results-list__summary">
            <h5 class="upper-header">There are 0 results for</h5>
            <h2 class="search-results-list__title">“<?php echo get_search_query(); ?>”</h2>
        </div>

    <?php endif; ?>
    

</section>

