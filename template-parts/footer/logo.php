<div class="footer-logo">
    <a href="<?php echo site_url('/'); ?>">
        <img src="<?php $image = get_field('footer_logo', 'options'); echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" />
    </a>
</div>