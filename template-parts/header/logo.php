<div class="site-logo">
    <a href="<?php echo site_url('/'); ?>">
        <img src="<?php $image = get_field('header_logo', 'options'); echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" />
    </a>
</div>