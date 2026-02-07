<?php

    $latest = get_field('latest');
    $header = $latest['header'];
    $latest_articles  = $latest['featured_articles'];

?>

<div class="articles">
    <span class="upper-header"><?php echo $header; ?></span>

    <?php if($latest_articles ): ?>
        <?php foreach($latest_articles  as $p): ?>

            <?php
                $args = ['p' => $p];
                get_template_part('template-parts/global/teaser/teaser-large', null, $args);
            ?>

        <?php endforeach; ?>
    <?php endif; ?>

</div>