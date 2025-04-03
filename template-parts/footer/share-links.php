<div class="share-links">
     <div class="share-link">
        <a href="mailto:?subject=Check out this article from SAPIR Journal&body=<?php echo urlencode(get_the_title() . "\n" . get_permalink()); ?>" class="share-link__item">
            <span class="share-link__item-icon">
                <?php get_template_part('svg/icon-email'); ?>
            </span>
            <span class="share-link__item-text">Email</span>
        </a>
    </div>

    <div class="share-link">
        <a href="#" class="share-link__item share-link__copy">
            <span class="share-link__item-icon">
                <?php get_template_part('svg/icon-link'); ?>
            </span>
            <span class="share-link__item-text">Copy link</span>
            <span class="share-link__copy-notification">Copied to clipboard</span>
        </a>
    </div>

    <div class="share-link share-link__sms">
        <a href="sms:?body=<?php echo urlencode(get_the_title() . "\n" . get_permalink()); ?>" class="share-link__item" target="_blank">
            <span class="share-link__item-icon">
                <?php get_template_part('svg/icon-sms'); ?>
            </span>
            <span class="share-link__item-text">SMS</span>
        </a>
    </div>

    <div class="share-link">
        <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo urlencode(get_permalink()); ?>" class="share-link__item" target="_blank">
            <span class="share-link__item-icon">
                <?php get_template_part('svg/icon-linkedin'); ?>
            </span>
            <span class="share-link__item-text">LinkedIn</span>
        </a>
    </div>

    <div class="share-link">
        <a href="https://twitter.com/intent/tweet?text=<?php echo urlencode(get_the_title()); ?>&url=<?php echo urlencode(get_permalink()); ?>" class="share-link__item" target="_blank">
            <span class="share-link__item-icon">
                <?php get_template_part('svg/icon-x'); ?>
            </span>
            <span class="share-link__item-text">X</span>
        </a>
    </div>

    <div class="share-link">
        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink()); ?>&t=<?php echo urlencode(get_the_title()); ?>" class="share-link__item" target="_blank">
            <span class="share-link__item-icon">
                <?php get_template_part('svg/icon-fb'); ?>
            </span>
            <span class="share-link__item-text">Facebook</span>
        </a>
    </div>
</div>