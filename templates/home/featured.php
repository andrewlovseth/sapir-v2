<?php if(have_rows('featured')): ?>
    <div class="featured">
        <?php while(have_rows('featured')) : the_row(); ?>

            <?php if( get_row_layout() == 'conversation' ): ?>
                <?php
                    $header = get_sub_field('header');
                    $post = get_sub_field('post');
                    $link = get_sub_field('cta');
                    if($link) {
                        $link_url = $link['url'];
                        $link_title = $link['title'];
                        $link_target = $link['target'] ? $link['target'] : '_self';
                    }
                ?>



                <div class="conversations">
                    <h5 class="upper-header"><?php echo $header; ?></h5>

                    <?php if($link): ?>

                        <div class="cta">
                            <a class="btn small-upper" href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>"><?php echo esc_html($link_title); ?></a>
                        </div>

                    <?php endif; ?>
                    
                </div>

            <?php endif; ?>

        <?php endwhile; ?>
    </div>
<?php endif; ?>