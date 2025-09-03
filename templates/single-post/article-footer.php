<?php

    $issue = get_field('issue');
    $issue_url = $issue ? get_permalink($issue) : '';
    $authors = get_field('author');
    $tags = get_the_tags();

?>

<section class="article-footer">

    <?php if($tags): ?>
        <div class="tags">
            <span class="label">Themes:</span>

            <div class="tags-list">
                <?php foreach($tags as $tag): ?>
                    <a href="<?php echo get_tag_link($tag->term_id); ?>" class="tag-link"> <?php echo $tag->name; ?></a><?php if($tag !== end($tags)): ?><span class="comma">, </span><?php endif; ?>
                <?php endforeach; ?>
        </div>

        </div>
    <?php endif; ?>


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


    <?php get_template_part('templates/single-post/pagination'); ?>


    <div class="article-footer__links">
        <div class="cta">
            <a class="btn small-upper" href="<?php echo site_url('/newsletter/'); ?>">
                <span class="label">Subscribe to the<br/> <span class="small-caps">Sapir</span> newsletter</span>
            </a>
        </div>

        <?php if ($issue_url): ?>
        <div class="link cta">
            <a class="btn small-upper" href="<?php echo $issue_url; ?>">
                <span class="label">View contents<br/> of this issue</span>
            </a>
        </div>
        <?php endif; ?>

    </div>

</section>