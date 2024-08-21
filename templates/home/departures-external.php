<?php 
	$link = get_sub_field('link');
	if( $link ): 
	$link_url = $link['url'];
	$link_title = $link['title'];
	$link_target = $link['target'] ? $link['target'] : '_self';
 ?>

    <article class="departures__external-link | teaser teaser-small">
        <div class="teaser__headline">
            <h3 class="teaser__title">
                <a class="teaser__title-link" href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>">
                    <?php echo esc_html($link_title); ?>
                </a>
            </h3>
        </div>
    </article>

<?php endif; ?>