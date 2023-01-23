<section class="article-body">
    <?php the_content(); ?>        

    <?php if(get_field('dropcap')): $dropcap = get_field('dropcap'); ?>
        <style>
            .dropcap .article-body p:first-child:before,
            .dropcap .article-body p.dropcap:before {
                background-image: url(<?php echo $dropcap['url']; ?>);
            }
        </style>
    <?php endif; ?>
        
    <div id="dingbat" style="display: none;">
        <?php get_template_part('svg/icon-dingbat'); ?>
    </div>
</section>