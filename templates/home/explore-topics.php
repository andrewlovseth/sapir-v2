<?php

    $explore = get_field('explore');
    $topics_header = $explore['topics_header'];
    $topics_icon = $explore['topics_icon'];
    $topics = $explore['topics'];

?>

<div class="explore__topics">
    <div class="explore__topics-header">
        <div class="icon"><?php echo get_svg($topics_icon['url']); ?></div>

        <h3><?php echo $topics_header; ?></span>
    </div>

    <div class="explore__topics-list">
        <?php foreach($topics as $t): ?>

            <div class="topic">
                <a href="<?php echo esc_url( get_term_link( $t ) ); ?>">
                    <?php echo esc_html( $t->name ); ?>
                </a>
            </div>

        <?php endforeach; ?>
    </div>
</div>