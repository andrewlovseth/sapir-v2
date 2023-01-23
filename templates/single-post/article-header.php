<?php

    $issue = get_field('issue');
    $volume = get_field('volume', $issue->ID);
    $season = get_field('season', $issue->ID); 

    $display_title = get_field('display_title');
    if($display_title) {
        $title = $display_title;
    } else {
        $title = get_the_title();
    }

    $dek = get_field('dek'); 
    $authors = get_field('author');
    $authors_count = count($authors);

    $epigraph = get_field('epigraph');
    $simple_epigraph = get_field('simple_epigraph');

    $pdf = get_field('pdf'); 

    $external_links = get_field('external_links');
    $links_header = $external_links['header'];
    $volume = get_field('volume', $issue->ID);

    $banner = get_field('banner'); 
    
?>

<section class="article-header">
    <?php if($banner): ?>
        <div class="article-header__banner">
            <?php echo wp_get_attachment_image($banner['ID'], 'full'); ?>
        </div>
    <?php endif; ?>    

    <?php if($issue): ?>
        <div class="issue">
            <a href="<?php echo get_permalink($issue->ID); ?>">
                <span class="volume"><?php echo $volume ?></span>
                <span class="season"><?php echo $season; ?></span>                
            </a>
        </div>
    <?php endif; ?>

    <?php if($title): ?>
        <div class="headline">
            <h1 class="title"><?php echo $title; ?></h1>
        </div>            
    <?php endif; ?>

    <?php if($dek): ?>
        <div class="dek">
            <p><?php echo $dek; ?></p>
        </div>            
    <?php endif; ?>

    <?php if($authors): ?>
        <div class="authors authors-<?php echo $authors_count; ?>">
            <em>by</em>

            <div class="authors-list">
                <?php foreach($authors as $author): ?><a href="<?php echo get_permalink($author); ?>"><?php echo get_the_title($author); ?></a><?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>    

    <?php if($pdf): ?>
        <div class="pdf">
            <a href="<?php echo $pdf['url']; ?>" target="_blank"><span>Download Print-Edition PDF</span></a>
        </div>
    <?php endif; ?>

    <?php if($epigraph): ?>
        <div class="epigraph">
            <div class="copy">
                <p><?php echo $epigraph ?></p>
            </div>
        </div>
    <?php endif; ?>

    <?php if(have_rows('external_links')): while(have_rows('external_links')): the_row(); ?>

        <?php 
            $header = get_sub_field('header');
            if(have_rows('links')): ?>

            <div class="external-links">
                <div class="header"> 
                    <h5 class="upper-header"><?php echo $header; ?></h5>
                </div>
                
                <ul class="links">
                    <?php while(have_rows('links')): the_row(); ?>

                        <li class="link">
                            <?php
                                $link = get_sub_field('link');
                                $args = ['link' => $link];
                                get_template_part('template-parts/global/link', null, $args);
                            ?>     
                        </li>

                    <?php endwhile; ?>
                </ul>
            </div>    

        <?php endif; ?>

    <?php endwhile; endif; ?>

    <?php if($simple_epigraph): ?>
        <div class="simple-epigraph">
            <div class="copy">
                <p><?php echo $simple_epigraph; ?></p>
            </div>
        </div>
    <?php endif; ?>


</section>