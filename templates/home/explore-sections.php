<?php if(have_rows('explore_sections')): ?>
    <div class="explore__sections">
        <?php while(have_rows('explore_sections')): the_row(); ?>

            <?php
                $icon = get_sub_field('icon');
                $link = get_sub_field('link');
                if($link) {
                    $link_url = $link['url'];
                    $link_title = $link['title'];
                    $link_target = $link['target'] ? $link['target'] : '_self';
                }
            ?>

            <div class="explore__sections-link">
                <a href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>">
                    <span class="icon">
                        <?php echo get_svg($icon['url']); ?>
                    </span>

                    <span class="label">
                        <?php echo esc_html($link_title); ?>
                    </span>
                </a>
            </div> 

        <?php endwhile; ?>
    </div>
<?php endif; ?>