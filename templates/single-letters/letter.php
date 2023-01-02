<?php
    $letter = get_field('letter');

    $copy = $letter['copy'];
    $name = $letter['name'];
    $meta = $letter['meta'];
?>

<section class="letter">
    <div class="copy">
        <?php echo $copy; ?>
    </div>

    <div class="meta">
        <h3 class="name"><?php echo $name; ?></h3>
        <?php if($meta): ?>
            <h4 class="info"><?php echo $meta; ?></h4>
        <?php endif; ?>
    </div>

</section>