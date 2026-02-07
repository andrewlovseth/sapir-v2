<?php

    $info = get_field('info');
    $headline = $info['headline'];
    $copy = $info['copy'];
    $cta = $info['cta'];

    $newsletter = get_field('newsletter', 'options');
    $embed = $newsletter['embed'];


?>

<div class="info">   
    <div class="info__header">
        <h1 class="info__title"><?php echo $headline; ?></h1>
    </div>

    <div class="info__copy copy copy-1">
        <?php echo $copy; ?>
    </div>

    <?php if($cta == "newsletter-form"): ?>

        <?php echo do_shortcode($embed); ?>

    <?php elseif($cta == "button"): ?>

        <?php 
            $link = $info['button'];
            if( $link ): 
            $link_url = $link['url'];
            $link_title = $link['title'];
            $link_target = $link['target'] ? $link['target'] : '_self';
        ?>

            <div class="cta">
                <a class="subscribe-btn btn small-upper" href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>"><?php echo esc_html($link_title); ?></a>
            </div>

        <?php endif; ?>

    <?php else: ?>

        <?php // Nothing ?>

    <?php endif; ?>
    
</div>