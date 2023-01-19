<?php
    $quote = get_field('quote');
    $header = $quote['header'];
    $text = $quote['text'];
    $source = $quote['source'];
    $link = $quote['link'];
    if($link) {
        $link_url = $link['url'];
        $link_title = $link['title'];
        $link_target = $link['target'] ? $link['target'] : '_self';
    }

    if($link):
?>

    <section class="quote grid">
        <a href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>">
            <blockquote>
                <div class="quote__header">
                    <h2 class="section-title sans-serif"><?php echo $header; ?></h2>
                </div>

                <div class="quote__text">
                    <p><?php echo $text; ?></p>
                </div>

                <div class="quote__meta">
                    <span class="quote__source">&mdash; <?php echo $source; ?>,</span> <?php echo esc_html($link_title); ?>
                </div>
            </blockquote>
        </a>
    </section>

<?php endif; ?>