<?php

    $newsletter = get_field('newsletter', 'options');
    $copy = $newsletter['modal_copy'];
    $embed = $newsletter['embed'];

?>

<?php if (isset($_GET['newsletter']) && $_GET['newsletter'] == 'subscribe') : ?>
    <dialog class="newsletter-modal" id="newsletter-modal">
        <div class="newsletter-modal__wrapper">
            <div class="newsletter-modal__content">
                <button class="newsletter-modal__close">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 0C5.31429 0 0 5.31429 0 12C0 18.6857 5.31429 24 12 24C18.6857 24 24 18.6857 24 12C24 5.31429 18.6857 0 12 0ZM16.6286 18L12 13.3714L7.37143 18L6 16.6286L10.6286 12L6 7.37143L7.37143 6L12 10.6286L16.6286 6L18 7.37143L13.3714 12L18 16.6286L16.6286 18Z" fill="#DA9589"/>
                    </svg>                
                </button>

                <div class="newsletter-modal__header">
                    <?php get_template_part('template-parts/header/logo'); ?>
                </div>

                <div class="newsletter-modal__body">
                    <div class="newsletter-modal__copy">
                        <?php echo $copy; ?>
                    </div>

                    <div class="newsletter-modal__embed">
                        <?php echo do_shortcode($embed); ?>
                    </div>
                </div>
            </div>
        </div>
    </dialog>
<?php endif; ?>