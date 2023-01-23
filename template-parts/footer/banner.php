<?php
    $home = get_option('page_on_front');
    $current_issue = get_field('latest_current_issue', $home);

    if(is_single() && 'post' == get_post_type()) {
        $issue = get_field('issue');
        $banner_image = get_field('banner', $issue->ID);

        if($banner_image) {
            $banner = $banner_image;
        } else {
            $banner = get_field('banner', $current_issue->ID);
        }

    } elseif(is_single() && 'issue' == get_post_type())  {
        $banner_image = get_field('banner');
        
        if($banner_image) {
            $banner = $banner_image;
        } else {
            $banner = get_field('banner', $current_issue->ID);
        }

    } else {
        $banner = get_field('banner', $current_issue->ID);
    }

?>

<div class="footer-banner site-banner">
    <div class="image">
        <?php echo wp_get_attachment_image($banner['ID'], 'full'); ?>
    </div>
</div>