<?php if(have_rows('events')): ?>
    <section class="events grid">
        <div class="section-header">
            <h2 class="sub-title">Upcoming</h2>
        </div>
        <?php while(have_rows('events')) : the_row(); ?>

            <?php if( get_row_layout() == 'event' ): ?>

                <div class="event">
                    <?php 
                        $date_string = get_sub_field('date');
                        $date = DateTime::createFromFormat('Ymd', $date_string);
                    ?>
                    <div class="date">
                        <span class="month"><?php echo $date->format('M'); ?></span>
                        <span class="day"><?php echo $date->format('j'); ?></span>
                    </div>

                    <div class="info">
                        <div class="event-title">
                            <h3><?php the_sub_field('title'); ?></h3>
                        </div>

                        <div class="event-date-time">
                            <h4><?php echo $date->format('l, F j, Y'); ?><?php if(get_sub_field('time')): ?> | <?php the_sub_field('time'); ?><?php endif; ?></h4>
                        </div>

                        <div class="event-description">
                            <?php the_sub_field('description'); ?>
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

    <section class="events grid">
        <div class="section-header">
            <h2 class="sub-title">Upcoming</h2>
        </div>
        
        <div class="event no-events">
            <div class="info">
                <h3>No events scheduled at the moment. Check back at a later date. Thank you.</h3>
            </div>
        </div>
    </section>

<?php endif; ?>