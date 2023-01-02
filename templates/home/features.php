<section class="features grid">
    <div class="features-wrapper">
        <?php if(have_rows('features')): while(have_rows('features')) : the_row(); ?>

            <?php if( get_row_layout() == 'feature' ): ?>

                <div class="feature<?php if(!get_sub_field('dek')): ?> no-dek<?php endif; ?>">
                    <div class="headline">
                        <h4><?php the_sub_field('headline'); ?></h4>
                    </div>

                    <?php if(get_sub_field('dek')): ?>
                        <div class="dek">
                            <p><?php the_sub_field('dek'); ?></p>
                        </div>
                    <?php endif; ?>

                    <?php 
                        $link = get_sub_field('link');
                        if( $link ): 
                        $link_url = $link['url'];
                        $link_title = $link['title'];
                        $link_target = $link['target'] ? $link['target'] : '_self';
                    ?>

                        <div class="cta">
                            <a class="underline" href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>"><?php echo esc_html($link_title); ?></a>
                        </div>

                    <?php endif; ?>                
                </div>

            <?php endif; ?>

        <?php endwhile; endif; ?>
    </div>
</section>