<?php

    $args = wp_parse_args($args);

    if(!empty($args)) {
        $p = $args['p']; 
        $status = $args['status']; 
        
    }

    if( $status == 'draft' || $status == 'future' ):

?>

    <div class="teaser__status">
        <span class="teaser__date upper-header">
            <span class="coming-soon">Available on <?php echo get_the_time('F j', $p); ?></span>
        </span>
    </div>

<?php endif; ?>