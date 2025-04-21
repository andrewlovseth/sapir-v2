<?php
    $letter = get_field('letter');

    $copy = $letter['copy'];
    $name = $letter['name'];
    $meta = $letter['meta'];
    $meta_line_2 = $letter['meta_line_2'];
?>

<section class="letter">
    <div class="copy copy-1">
        <?php echo $copy; ?>
    </div>

    <div class="meta">
        <h3 class="name"><?php echo $name; ?></h3>
        <?php if($meta || $meta_line_2): ?>
            <h4 class="info">
                <?php if($meta): ?>
                    <span class="meta-line"><?php echo $meta; ?></span>
                <?php endif; ?>

                <?php if($meta_line_2): ?>
                    <span class="meta-line"><?php echo $meta_line_2; ?></span>
                <?php endif; ?>
            </h4>
        <?php endif; ?>
    </div>
</section>