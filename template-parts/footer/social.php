<?php if(have_rows('footer_social', 'options')): ?>
    <div class="social">
    
        <?php while(have_rows('footer_social', 'options')): the_row(); ?>

            <?php
                $icon = get_sub_field('icon');
                $link = get_sub_field('link');
            ?>

            <div class="social__item">
                <a class="social__link" href="<?php echo $link; ?>" target="window">
                    <?php echo get_svg($icon['url']); ?>
                </a>
            </div>

        <?php endwhile; ?>
    </div>
<?php endif; ?>