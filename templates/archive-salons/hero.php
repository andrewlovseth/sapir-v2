<?php

    $salons = get_field('salons', 'option');

    $headline = $salons['headline'];
    $description = $salons['description'];
    $diy_guide = $salons['diy_guide'];

?>

<section class="salons-archive-hero | grid">
    <div class="salons-archive-hero__header">
        <h1 class="salons-archive-hero__title">
            <?php echo $headline; ?>
        </h1>
    </div>

    <div class="salons-archive-hero__body">
        <div class="copy copy-1">
            <?php echo $description; ?>
        </div>

        <?php if ($diy_guide) : ?>
            <div class="diy-guide">

            <div class="cta">
                <a class="btn" href="<?php echo $diy_guide['url']; ?>" target="_blank">
                    <?php echo $diy_guide['title']; ?>
                </a>
            </div>

            </div>
        <?php endif; ?>
    </div>
</section>

