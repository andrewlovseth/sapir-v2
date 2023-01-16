<?php

    $most_read = get_field('most_read');
    $header = $most_read['header'];
    $posts = $most_read['posts'];

?>

<section class="most-read">
    <div class="section-header">
        <h2 class="section-title"><?php echo $header; ?></h2>
    </div>

    <?php if($posts): ?>
        <?php foreach( $posts as $p ): ?>

            <?php
                $args = ['p' => $p];
                get_template_part('template-parts/global/teaser/teaser-small', null, $args);
            ?>

        <?php endforeach; ?>
    <?php endif; ?>

</section>