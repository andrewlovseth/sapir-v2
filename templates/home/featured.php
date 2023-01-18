<?php if(have_rows('featured')): ?>
    <div class="featured">
        <?php while(have_rows('featured')) : the_row(); ?>

            <?php if( get_row_layout() == 'conversation' ): ?>

                <?php
                    $header = get_sub_field('header');
                    $featured_post = get_sub_field('post');
                    $link = get_sub_field('cta');

                    $args = ['header' => $header, 'featured_post' => $featured_post, 'link' => $link];
                    get_template_part('templates/home/featured-conversation', null, $args);
                ?>
                
            <?php endif; ?>

        <?php endwhile; ?>
    </div>
<?php endif; ?>