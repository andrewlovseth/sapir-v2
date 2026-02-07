<?php
    $theme = get_field('theme');
    $header = $theme['header'];

    $home = get_option('page_on_front');
    $explore = get_field('explore', $home);
    $topics = $explore['topics'];
?>

<section class="theme grid" id="theme">

    <div class="section-header">
        <span class="upper-header"><?php echo $header; ?></span>
    </div>


    <div class="theme__list">
        <?php foreach($topics as $t): ?>

            <div class="theme__item">
                <a href="<?php echo esc_url( get_term_link( $t ) ); ?>">
                    <?php echo esc_html( $t->name ); ?>
                </a>
            </div>

        <?php endforeach; ?>
    </div>

</section>