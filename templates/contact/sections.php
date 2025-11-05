<?php if(have_rows('sections')): while(have_rows('sections')) : the_row(); ?>

	<?php if( get_row_layout() == 'section' ): ?>

        <?php
            $header = get_sub_field('header');
            $copy = get_sub_field('copy');
        ?>  

        <div class="contact-section">
            <h2 class="contact-section__header"><?php echo $header; ?></h2>
            <div class="contact-section__copy copy copy-2">
                <?php echo $copy; ?>
            </div>
        </div>	

	<?php endif; ?>

<?php endwhile; endif; ?>