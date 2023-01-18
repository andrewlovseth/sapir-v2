<?php
    $recordings_cta = get_field('recordings_cta');
?>

<section class="events grid">

    <?php if(have_rows('events')): ?>

        <?php while(have_rows('events')) : the_row(); ?>

            <?php if( get_row_layout() == 'event' ): ?>

                <?php get_template_part('templates/events/event'); ?>

            <?php endif; ?>

        <?php endwhile; ?>

    <?php else: ?>
        
        <div class="event no-events">
            <h3 class="no-events__title">No events scheduled at the moment. Check back at a later date. Thank you.</h3>
        </div>
    
    <?php endif; ?>

    <div class="recordings-cta">
        <?php
            $args = ['link' => $recordings_cta];
            get_template_part('template-parts/global/cta', null, $args);
        ?>    
    </div>

</section>