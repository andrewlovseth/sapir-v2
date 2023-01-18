<div class="letter__header">
    <div class="letter__meta">
        <?php
            $date = get_the_time('F j, Y');
        ?>

        <span class="date"><?php echo $date; ?></span>
    </div>

    <h1 class="letter__title"><?php the_title(); ?></h1>
</div>