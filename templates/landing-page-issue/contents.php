<?php

    $issue = get_field('content_issue');
    $shortcode = get_field('content_form_shortcode');

?>

<div class="issue__contents">
    <div class="issue-landing-page__form">
        <?php echo do_shortcode($shortcode); ?>
    </div>

    <h4 class="issue__contents-header | upper-header">Contents of the Issue</h4>
    
    <?php if(have_rows('table_of_contents', $issue->ID)): while(have_rows('table_of_contents', $issue->ID)) : the_row(); ?>

        <?php if( get_row_layout() == 'section' ): ?>
            <?php
                $header = get_sub_field('heading'); 
            ?>

            <div class="issue__contents-section">
                <?php if($header):?>
                    <h2 class="issue__contents-header upper-header"><?php echo $header; ?></h2>
                <?php endif; ?>

                <?php if(have_rows('articles')):  while(have_rows('articles')): the_row(); ?>

                    <?php
                        $article = get_sub_field('article');
                        $args = ['p' => $article->ID];
                        get_template_part('template-parts/global/teaser/teaser-contents', null, $args);
                    ?>

                <?php endwhile; endif; ?>
            </div>

        <?php endif; ?>
    
    <?php endwhile; endif; ?>
</div>