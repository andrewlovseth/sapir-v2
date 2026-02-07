<?php if(have_rows('learning_guide')): ?>
    <section class="learning-guide | grid">
        <div class="learning-guide__header">
            <span class="upper-header">Recommended articles to get your conversation started</span>
        </div>


        <?php while(have_rows('learning_guide')) : the_row(); ?>

            <?php if( get_row_layout() == 'section' ): ?>
                <?php
                    $article = get_sub_field('article');
                    $questions = get_sub_field('questions');
                    $authors = get_field('author', $article->ID);
                ?>

                <div class="learning-guide__article">
                    <?php
                        $args = ['p' => $article];
                        get_template_part('template-parts/global/teaser/teaser-large', null, $args);
                    ?>                        

                    <div class="learning-guide__questions | copy copy-2">
                        <?php echo $questions; ?>
                    </div>                    
                </div>
            <?php endif; ?>

        <?php endwhile; ?>
    </section>
<?php endif; ?>