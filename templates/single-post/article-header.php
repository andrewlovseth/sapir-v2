<?php

    $issue = get_field('issue'); 
    $authors = get_field('author');
    $authors_count = count($authors);

    $pdf = get_field('pdf'); 

    $external_links = get_field('external_links');
    $links_header = $external_links['header'];
    $volume = get_field('volume', $issue->ID);
    
?>


<section class="article-header">
    <?php if($issue): ?>
        <div class="issue">
            <a href="<?php echo get_permalink($issue->ID); ?>">
                <span class="volume"><?php echo get_field('volume', $issue->ID); ?></span>
                <span class="season"><?php echo get_field('season', $issue->ID); ?></span>
                
            </a>
        </div>
    <?php endif; ?>

    <?php if(get_field('epigraph')): ?>
        <div class="epigraph">
            <div class="copy">
                <p><?php the_field('epigraph'); ?></p>
            </div>
        </div>
    <?php endif; ?>

    <?php if($authors): ?>
        <div class="author  authors-<?php echo $authors_count; ?>">
            <?php foreach($authors as $author): ?>
                <div class="sub-title name">
                    <a class="name-link" href="<?php echo get_permalink($author); ?>">
                        <?php echo get_the_title($author); ?>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="headline">
        <?php if(get_field('display_title')): ?>
            <h1 class="title"><?php the_field('display_title'); ?></h1>
        <?php else: ?>
            <h1 class="title"><?php the_title(); ?></h1>
        <?php endif; ?>    
    </div>            

    <?php if($pdf): ?>
        <div class="pdf">
            <a class="mono" href="<?php echo $pdf['url']; ?>" target="_blank"><span>Download and Print Article</span></a>
        </div>
    <?php endif; ?>

    <?php if(have_rows('external_links')): while(have_rows('external_links')): the_row(); ?>

        <?php 
            $header = get_sub_field('header');
            if(have_rows('links')): ?>

            <div class="external-links">
                <div class="header"> 
                    <h5 class="mono"><?php echo $header; ?></h5>
                </div>
                
                <ul class="links">
                    <?php while(have_rows('links')): the_row(); ?>

                        <?php 
                            $link = get_sub_field('link');
                            if( $link ): 
                            $link_url = $link['url'];
                            $link_title = $link['title'];
                            $link_target = $link['target'] ? $link['target'] : '_self';
                        ?>

                            <li class="link">
                                <a class="mono" href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>"><?php echo esc_html($link_title); ?></a>
                            </li>

                        <?php endif; ?>
                    <?php endwhile; ?>
                </ul>
            </div>    

        <?php endif; ?>

    <?php endwhile; endif; ?>

    <?php if(get_field('simple_epigraph')): ?>
        <div class="simple-epigraph">
            <div class="copy">
                <p><?php the_field('simple_epigraph'); ?></p>
            </div>
        </div>
    <?php endif; ?>


</section>