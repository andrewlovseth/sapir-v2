<?php
    $args = wp_parse_args($args);

    if(!empty($args)) {
        $header = $args['header']; 
        $featured_post = $args['featured_post']; 
        $link = $args['link']; 
    }

    $p = $featured_post->ID;

    $date = get_the_time('F j, Y', $p);
    $title = get_the_title($p);
    $permalink = get_permalink($p);
    $details = get_field('details', $p);
    $description = $details['description'];
    $authors = $details['authors'];
    $additional_authors = $details['additional_authors'];

    $meta = get_field('meta', $p);
    $running_time = $meta['running_time'];

?>

<h5 class="upper-header"><?php echo $header; ?></h5>

<div class="teaser-conversation">
        <div class="teaser-conversation__participants">
        <?php if($authors): ?>
            <?php foreach($authors as $a): ?>
                <div class="teaser-conversation__participant">
                    <div class="photo">
                        <?php echo get_the_post_thumbnail($a->ID, 'thumbnail'); ?>
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
                        <?php echo wp_get_attachment_image($photo['ID'], 'thumbnail'); ?>
                    </div>
                </div>

            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="teaser-conversation__info">

        <div class="teaser-conversation__meta">
            <span class="date"><?php echo $date; ?></span>
            <span class="running-time"><?php echo $running_time; ?></span>

        </div>

        <?php
            $args = ['title' => $title, 'permalink' => $permalink];
            get_template_part('template-parts/global/teaser/headline', null, $args);
        ?>

        <div class="copy copy-2">
            <?php echo $description; ?>
        </div>
    </div>                          
</div>

<?php
    $args = ['link' => $link];
    get_template_part('template-parts/global/cta', null, $args);
?>      