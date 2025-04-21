<?php

    $salons = get_field('salons', 'option');
    $current = $salons['current'];
    $issue = get_field('issue', $current->ID);



    $current_issue_id = $issue->ID;

    $issue_title = get_the_title($current_issue_id);
    $issue_link = get_permalink($current_issue_id);
    $volume = get_field('volume', $current_issue_id);
    $season = get_field('season', $current_issue_id);
    $cover = get_field('cover', $current_issue_id);

?>


<?php if(have_rows('learning_guide', $current->ID)): ?>

    <section class="current-salon | grid">
        <div class="learning-guide learning-guide__archive">
            <div class="learning-guide__header">
                <h5 class="upper-header">Recommended articles to get your conversation started</h5>
            </div>

            <?php while(have_rows('learning_guide', $current->ID)) : the_row(); ?>

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
        </div>

        <div class="latest-issue">
            <div class="header">
                <h3>
                    <span class="upper-header">The Issue On</span>
                    <a href="<?php echo $issue_link; ?>" class="issue__title"><?php echo $issue_title; ?></a>
                </h3>

                <div class="ornament">
                    <?php get_template_part('svg/icon-ornament'); ?>
                </div>

                <a href="<?php echo $issue_link; ?>" class="meta">
                    <span class="small-upper volume"><?php echo $volume; ?></span>
                    <span class="small-upper season"><?php echo $season; ?></span>
                </a>
            </div>

            <div class="cover">
                <a href="<?php echo $issue_link; ?>">
                    <?php echo wp_get_attachment_image($cover['ID'], 'medium'); ?>
                </a>
            </div>

            <div class="cta">
                <a href="<?php echo $issue_link; ?>" class="btn small-upper">Read it now</a>
            </div>
        </div>
    </section>

<?php endif; ?>