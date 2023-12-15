<?php
    $args = wp_parse_args($args);

    if(!empty($args)) {
        $link = $args['link']; 
    }  

    if( $link ) {
        $link_url = $link['url'];
        $link_title = $link['title'];
        $link_target = $link['target'] ? $link['target'] : '_self';
    }

    if($link):
?>

    <a href="<?php echo esc_url($link_url); ?>" class="link-<?php echo sanitize_title_with_dashes($link_title); ?>" target="<?php echo esc_attr($link_target); ?>">
        <?php echo $link_title; ?>
    </a>

<?php endif; ?>