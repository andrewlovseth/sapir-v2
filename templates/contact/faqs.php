<?php if(have_rows('faqs')): ?>

    <section class="faqs grid">

        <div class="section-header">
            <h2>FAQs</h2>
        </div>
       
        <?php while(have_rows('faqs')) : the_row(); ?>

            <?php if( get_row_layout() == 'faq' ): ?>

                <div class="faq">
                    <div class="question">
                        <h3><?php the_sub_field('question'); ?></h3>
                    </div>

                    <div class="answer copy copy-2 extended">
                        <?php the_sub_field('answer'); ?>
                    </div>
                </div>

            <?php endif; ?>

        <?php endwhile; ?>

    </section>

<?php endif; ?>