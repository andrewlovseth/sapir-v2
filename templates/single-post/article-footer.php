<?php

    $issue = get_field('issue');
    $issue_url = get_permalink($issue->ID);
    $authors = get_field('author');
    $authors_count = count($authors);


?>

<section class="article-footer">

    <?php if($authors): ?>
        <div class="authors">
            <div class="section-header">
                <h3>Author<?php if($authors_count > 1): ?>s<?php endif; ?></h3>
            </div>

            <?php foreach($authors as $author): ?>
                <div class="author copy">
                    <a class="name-link" href="<?php echo get_permalink($author); ?>">
                        <?php echo get_the_title($author); ?>
                    </a>
                    <?php echo $author->post_content; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="back">
        <a href="<?php echo $issue_url; ?>">Back to the table of contents</a>
    </div>
</section>