<?php

    $latest = get_field('latest');
    $header = $latest['header'];
    $latest_articles  = $latest['featured_articles'];

?>

<div class="articles">
    <h4 class="upper-header"><?php echo $header; ?></h4>

    <?php if($latest_articles ): ?>
        <?php foreach($latest_articles  as $p): ?>
            <?php
                if(get_field('display_title', $p)) {
                    $title = get_field('display_title', $p);
                } else {
                    $title = get_the_title($p);
                }

                $permalink = get_permalink($p);
                $dek = get_field('dek', $p);
                $authors = get_field('author', $p);
                $authors_count = count($authors);
            ?>

            <article class="teaser">
                <div class="teaser__headline">
                    <h3 class="teaser__title"><a href="<?php echo $permalink; ?>"><?php echo $title; ?></a></h3>
                </div>

                <div class="teaser__dek">
                    <?php echo $dek; ?>
                </div>

                <?php if($authors): ?>
                    <div class="teaser__authors authors-<?php echo $authors_count; ?>">
                        <em>by</em>

                        <div class="teaser__authors-list">
                            <?php foreach($authors as $author): ?><a href="<?php echo get_permalink($author); ?>"><?php echo get_the_title($author); ?></a><?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </article>

        <?php endforeach; ?>
    <?php endif; ?>

    <?php get_template_part('templates/home/featured'); ?>
</div>