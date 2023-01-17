<?php

    $issue = get_field('issue');
    $issue_url = get_permalink($issue->ID);
    $authors = get_field('author');
    $authors_count = count($authors);


?>

<section class="article-footer">

    <?php if($authors): ?>
        <div class="authors">

            <?php foreach($authors as $author): ?>

                <div class="author">
                    <?php if(get_the_post_thumbnail($author)): ?>
                        <div class="author__photo">
                            <a href="<?php echo get_permalink($author); ?>">
                                <?php echo get_the_post_thumbnail($author, 'medium'); ?>
                            </a>
                        </div>
                    <?php endif; ?>

                    <div class="copy copy-2">
                        <?php echo $author->post_content; ?>
                    </div>
                </div>

            <?php endforeach; ?>

        </div>
    <?php endif; ?>

</section>