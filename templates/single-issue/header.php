<div class="contents-header">
    <div class="headline">
        <h2 class="sub-title"><?php the_field('volume'); ?> &middot; <?php the_field('season'); ?> </h2>
        <h1 class="title"><?php the_title(); ?></h1>
    </div>

    <?php if(get_field('full_issue_pdf')): ?>
        <div class="cta">
            <a href="<?php the_field('full_issue_pdf'); ?>" rel="external" class="btn">Download Issue (PDF)</a>
        </div>
    <?php endif; ?>
</div>