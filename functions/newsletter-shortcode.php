<?php
/**
 * Newsletter signup shortcode with AJAX submission
 *
 * Usage: [sapir_newsletter]
 * Optional attributes:
 *   - placeholder: Input placeholder text (default: "Enter your email address here")
 *   - button_text: Submit button text (default: "Sign up ››")
 */

function sapir_newsletter_shortcode($atts) {
    $atts = shortcode_atts([
        'placeholder' => 'Enter your email address here',
        'button_text' => 'Sign up ››',
    ], $atts);

    // Mailchimp configuration
    $mc_user = 'b6eaf3c7791bf01e7abc09b9f';
    $mc_list = 'e87ce862e6';
    $mc_action = "https://maimonidesfund.us1.list-manage.com/subscribe/post?u={$mc_user}&id={$mc_list}";

    ob_start();
    ?>
    <form action="<?php echo esc_url($mc_action); ?>" method="post" class="newsletter-form" novalidate>
        <div class="newsletter-form__fields">
            <input type="email"
                   name="EMAIL"
                   placeholder="<?php echo esc_attr($atts['placeholder']); ?>"
                   class="newsletter-form__input"
                   required
                   aria-label="Email address">
            <button type="submit" class="newsletter-form__button">
                <?php echo esc_html($atts['button_text']); ?>
            </button>
            <!-- Honeypot field to prevent bot signups -->
            <div style="position: absolute; left: -5000px;" aria-hidden="true">
                <input type="text" name="b_<?php echo esc_attr($mc_user); ?>_<?php echo esc_attr($mc_list); ?>" tabindex="-1" value="">
            </div>
        </div>
        <div class="newsletter-form__messages" aria-live="polite">
            <div class="newsletter-form__message newsletter-form__message--error" hidden></div>
            <div class="newsletter-form__message newsletter-form__message--success" hidden></div>
        </div>
    </form>
    <?php
    return ob_get_clean();
}
add_shortcode('sapir_newsletter', 'sapir_newsletter_shortcode');
