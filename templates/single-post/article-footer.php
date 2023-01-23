<?php

    $issue = get_field('issue');
    $issue_url = get_permalink($issue->ID);
    $authors = get_field('author');

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

    <div class="article-footer__links">
        <div class="cta">
            <a class="btn small-upper" href="<?php echo site_url('/contact/'); ?>">
                <span class="label">Write a Letter<br/> to the editor</span>
            </a>
        </div>

        <div class="link cta">
            <a class="btn small-upper" href="<?php echo $issue_url; ?>">
                <span class="label">View contents<br/> of this issue</span>
            </a>
        </div>

    </div>

</section>