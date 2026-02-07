<?php if( get_row_layout() == 'conversation' ): ?>

    <?php
        $header = get_sub_field('header');
        $icon = get_sub_field('icon');
        $featured_post = get_sub_field('post');
        $link = get_sub_field('cta');
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

    <span class="upper-header"><?php echo $header; ?></span>

    <div class="teaser-conversation">
        <div class="teaser-conversation__icon">
            <?php echo wp_get_attachment_image($icon['ID'], 'full'); ?>
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

            <?php
                $args = ['link' => $link];
                get_template_part('template-parts/global/cta', null, $args);
            ?>      
        </div>                          
    </div>

<?php endif; ?>