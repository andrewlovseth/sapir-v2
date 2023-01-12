<?php

    $header = get_field('header', 'options');
    $tagline = $header['tagline'];

?>

<div class="tagline">
    <em><?php echo $tagline; ?></em>
</div>