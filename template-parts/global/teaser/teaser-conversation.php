<?php
    $args = wp_parse_args($args);

    if(!empty($args)) {
        $p = $args['p']; 
    }
    
    $date = get_the_time('F j, Y');
    $title = get_the_title($p);
    $permalink = get_permalink($p);
    $details = get_field('details', $p);
    $description = $details['description'];
    $authors = $details['authors'];
    $additional_authors = $details['additional_authors'];

    $meta = get_field('meta', $p);
    $running_time = $meta['running_time'];
    $cta = $meta['cta'];
    $apple_podcasts = $meta['apple_podcasts'];
    $spotify = $meta['spotify'];
    $google_podcasts = $meta['google_podcasts'];

?>

<article class="teaser teaser-large teaser-conversation">
   
    <div class="teaser-conversation__participants">
        <?php if($authors): ?>
            <?php foreach($authors as $a): ?>
                <div class="teaser-conversation__participant">
                    <div class="photo">
                        <?php if(get_the_post_thumbnail($a->ID, 'thumbnail')): ?>
                            <?php echo get_the_post_thumbnail($a->ID, 'thumbnail'); ?>
                        <?php else: ?>
                            <div class="no-photo"></div>
                        <?php endif; ?>
                        <?php get_template_part('svg/icon-speech-bubble'); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <?php if($additional_authors): ?>
            <?php foreach($additional_authors as $a): ?>
                <?php 
                    $photo = $a['photo'];
                ?>

                <div class="teaser-conversation__participant">
                    <div class="photo">
                        <?php if(get_the_post_thumbnail($photo['ID'], 'thumbnail')): ?>
                            <?php echo get_the_post_thumbnail($photo['ID'], 'thumbnail'); ?>
                        <?php else: ?>
                            <div class="no-photo"></div>
                        <?php endif; ?>

                        <?php get_template_part('svg/icon-speech-bubble'); ?>
                    </div>
                </div>

            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="teaser-conversation__info">

        <div class="teaser-conversation__meta">
            <span class="date"><?php echo $date; ?></span>
            <span class="running_time"><?php echo $running_time; ?></span>

        </div>

        <div class="teaser__headline">
            <h3 class="teaser__title"><?php echo $title; ?></h3>
        </div>

        <div class="copy copy-2">
            <?php echo $description; ?>
        </div>

        <?php if($apple_podcasts || $spotify || $google_podcasts): ?>
            <div class="listen">
                <strong>Listen:</strong>

                <?php if($apple_podcasts): ?>
                    <a href="<?php echo $apple_podcasts; ?>" class="listen__icon apple-podcasts" target="window"><?php get_template_part('svg/icon-apple-podcasts'); ?></a>
                <?php endif; ?>
                
                <?php if($spotify): ?>
                    <a href="<?php echo $spotify; ?>" class="listen__icon spotify" target="window"><?php get_template_part('svg/icon-spotify'); ?></a>
                <?php endif; ?>

                <?php if($google_podcasts): ?>
                    <a href="<?php echo $google_podcasts; ?>" class="listen__icon google-podcasts" target="window"><?php get_template_part('svg/icon-google-podcasts'); ?></a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php
            $args = ['link' => $cta];
            get_template_part('template-parts/global/cta', null, $args);
        ?>
    </div>

</article>