<?php

    $issue = get_field('issue');
    $cover = get_field('cover', $issue->ID);


    $description = get_field('description');
    $getting_started_guide = get_field('getting_started_guide');

?>

<section class="salon-hero | grid">
    <div class="salon-hero__cover">
        <a href="<?php echo get_permalink($issue->ID); ?>">
            <?php echo wp_get_attachment_image($cover['ID'], 'full'); ?>
        </a>
    </div>
    <div class="salon-hero__info">
        <h1 class="salon-hero__title"><?php the_title(); ?></h1>

        <div class="salon-hero__description copy copy-2">
            <?php echo $description; ?>
        </div>

        <div class="ctas">
            <?php if($getting_started_guide): ?>
                <div class="cta">
                    <a href="<?php echo $getting_started_guide['url']; ?>" class="btn">
                        <?php echo $getting_started_guide['title']; ?>
                    </a>
                </div>
            <?php endif; ?>

            <div class="cta">
                <a href="<?php echo get_permalink($issue->ID); ?>" class="btn">Read the issue</a>
            </div>
        </div>


    </div>
</section>

