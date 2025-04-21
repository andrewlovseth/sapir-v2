<?php

    $contact = get_field('contact');
    $header = $contact['header'];
    $link = $contact['link'];



?>

<section class="salon-contact | grid">
    <div class="salon-contact__content">
        <h2 class="salon-contact__title"><?php echo $header; ?></h2>

        <?php 
            if( $link ): 
            $link_url = $link['url'];
            $link_title = $link['title'];
            $link_target = $link['target'] ? $link['target'] : '_self';
        ?>

            <div class="cta">
                <a class="btn" href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>"><?php echo esc_html($link_title); ?></a>
            </div>

        <?php endif; ?>
    </div>
</section>