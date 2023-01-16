<?php
    $args = wp_parse_args($args);

    if(!empty($args)) {
        $dek = $args['dek']; 
    }
    
    if($dek):
?>

    <div class="teaser__dek">
        <?php echo $dek; ?>
    </div>

<?php endif; ?>