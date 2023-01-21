<?php

    $footer = get_field('footer', 'options');
    $copyright = $footer['copyright'];

?>

<div class="copyright">
    <p><?php echo $copyright; ?></p>
</div>