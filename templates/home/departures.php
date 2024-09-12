<?php

    $departures = get_field('departures');
    $header = $departures['header'];
    $banner = $departures['banner'];

if(have_rows('departures')): while(have_rows('departures')): the_row(); ?>
 

    <section class="departures">

        <div class="departures__header">
            <div class="banner banner__header">
                <?php echo wp_get_attachment_image($banner['ID'], 'full'); ?>
            </div>

            <h2 class="section-title"><?php echo $header; ?></h2>
        </div>
        
        <?php if(have_rows('departures_links')): ?>
            <div class="departures__body">
                <div class="departures__body-wrapper">
           
                    <?php while(have_rows('departures_links')) : the_row(); ?>

                        <?php if( get_row_layout() == 'sapir_story' ): ?>
                            <?php $p = get_sub_field('link'); if($p): ?>

                            <?php
                                $args = ['p' => $p];
                                get_template_part('template-parts/global/teaser/teaser-small', null, $args);
                            ?>

                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if( get_row_layout() == 'external_link' ): ?>

                            <?php get_template_part('templates/home/departures-external'); ?>

                        <?php endif; ?>

                    <?php endwhile; ?>

                </div>           
            </div>
        <?php endif; ?>


        <div class="departures__footer">
            <div class="banner banner__footer">
                <?php echo wp_get_attachment_image($banner['ID'], 'full'); ?>
            </div>    
        </div>

    </section>

<?php endwhile; endif; ?>