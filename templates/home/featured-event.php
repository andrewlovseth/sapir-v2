<?php if( get_row_layout() == 'event' ): ?>

    <?php
        $header = get_sub_field('header');
        $icon = get_sub_field('icon');
        $date_time = get_sub_field('date_time');
        $location = get_sub_field('location');
        $title = get_sub_field('title');
        $description = get_sub_field('description');
        $link = get_sub_field('cta');
    ?>

    <span class="upper-header"><?php echo $header; ?></span>

    <div class="teaser-conversation">
        <div class="teaser-conversation__icon">
            <?php echo wp_get_attachment_image($icon['ID'], 'full'); ?>
        </div>
        
        <div class="teaser-conversation__info">
            <div class="teaser-conversation__meta">
                <span class="date"><?php echo $date_time; ?></span>
                <?php if($location): ?>
                    <span class="location"><?php echo $location; ?></span>
                <?php endif; ?>
            </div>

            <div class="teaser__headline">
                <h3 class="teaser__title"><?php echo $title; ?></h3>
            </div>

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