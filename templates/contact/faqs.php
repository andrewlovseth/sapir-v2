<?php if(have_rows('faqs')): ?>

    <div class="faqs">

        <div class="section-header">
            <h2 class="module-title">FAQs</h2>
        </div>
       
        <?php while(have_rows('faqs')) : the_row(); ?>

            <?php if( get_row_layout() == 'faq' ): ?>

                <div class="faq">
                    <div class="question">
                        <h3><?php echo get_sub_field('question'); ?></h3>
                    </div>

                    <div class="copy copy-3">
                        <?php echo get_sub_field('answer'); ?>
                    </div>
                </div>

            <?php endif; ?>

        <?php endwhile; ?>

    </div>

<?php endif; ?>