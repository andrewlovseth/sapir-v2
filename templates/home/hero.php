<?php

    $hero = get_field('hero');
    $photo = $hero['photo'];
    $headline = $hero['headline'];
    $dek = $hero['dek'];
    $link = $hero['link'];
    $link_url = $link['url'];
    $link_title = $link['title'];
?>

<section class="hero grid">
    <div class="photo">
        <a href="<?php echo esc_url($link_url); ?>"><img src="<?php echo $photo['url']; ?>" alt="<?php echo $photo['alt']; ?>" /></a>
    </div>
    
    <div class="info">
        <div class="headline">
            <h2 class="title"><a class="btn" href="<?php echo esc_url($link_url); ?>"><?php echo $headline; ?></a></h2>
        </div>

        <div class="dek">
            <p><?php echo $dek; ?></p>
        </div>

        <div class="cta">
            <a class="btn" href="<?php echo esc_url($link_url); ?>"><?php echo esc_html($link_title); ?></a>
        </div>
    </div>
</section>