<?php if(have_rows('recordings')): ?>
    <section class="recordings events grid">
        <div class="section-header">
            <h2 class="sub-title">Recordings</h2>
        </div>

        <?php while(have_rows('recordings')) : the_row(); ?>

            <?php if( get_row_layout() == 'event' ): ?>

                <div class="event recording">
                    <div class="info">
                        <div class="event-title">
                            <h3><?php echo get_sub_field('title'); ?></h3>
                        </div>

                        <div class="event-description">
                            <?php echo get_sub_field('description'); ?>
                        </div>

                        <?php 
                            $link = get_sub_field('link');
                            if( $link ): 
                            $link_url = $link['url'];
                            $link_title = $link['title'];
                            $link_target = $link['target'] ? $link['target'] : '_self';
                        ?>

                            <div class="event-cta">
                                <a class="btn" href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>"><?php echo esc_html($link_title); ?></a>
                            </div>

                        <?php endif; ?>
                    </div>
                    
                </div>

            <?php endif; ?>

        <?php endwhile; ?>


    </section>

<?php else: ?>

    <section class="recordings events grid">
        <div class="section-header">
            <h2 class="sub-title">Recordings</h2>
        </div>

        <div class="event no-events">
            <div class="info">
                <h3>No recordings at this time. Check back at a later date. Thank you.</h3>
            </div>
        </div>
    </section>

<?php endif; ?>