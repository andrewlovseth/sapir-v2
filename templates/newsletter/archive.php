<?php

    $archive = get_field('archive');
    $link = $archive['link'];
    $graphic = $archive['graphic'];

	if( $link ): 
	$link_url = $link['url'];
	$link_title = $link['title'];
	$link_target = $link['target'] ? $link['target'] : '_self';

?>

    <div class="archive">
        <a class="archive__link" href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>">

            <div class="archive__graphic">
                <?php echo wp_get_attachment_image($graphic['ID'], 'full'); ?>
            </div>

            <div class="archive__copy copy copy-1">
                <p><?php echo esc_html($link_title); ?></p>
            </div>

        </a>
    </div>

<?php endif; ?>