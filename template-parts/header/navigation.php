<?php if(have_rows('navigation', 'options')): ?>
    <nav class="site-nav">
        <ul>
            <?php while(have_rows('navigation', 'options')): the_row(); ?>

                <?php 
                    $link = get_sub_field('link');
                    if( $link ): 
                    $link_url = $link['url'];
                    $link_title = $link['title'];
                    $link_target = $link['target'] ? $link['target'] : '_self';
                ?>

                    <li><a href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>"><?php echo esc_html($link_title); ?></a></li>

                <?php endif; ?>

            <?php endwhile; ?>
        </ul>

        <div class="dingbat">
            <img src="<?php bloginfo('template_directory'); ?>/images/icon-dingbat-orange.svg" alt="" />
        </div>
    </nav>
<?php endif; ?>