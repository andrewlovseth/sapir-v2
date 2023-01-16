<?php

    $departures = get_field('departures');
    $header = $departures['header'];
    $posts = $departures['posts'];

?>

<section class="departures">
    <div class="section-header">
        <h2 class="section-title sans-serif"><?php echo $header; ?></h2>
    </div>

    <div class="article-list">
        <?php if($posts): ?>
            <?php foreach( $posts as $p ): ?>
            
                <?php
                    $args = ['p' => $p];
                    get_template_part('template-parts/global/teaser/teaser-small', null, $args);
                ?>

            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</section>