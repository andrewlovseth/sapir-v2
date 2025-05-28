<?php
    $url = get_permalink();
    $title = get_the_title();
    $char_limit = 140;

    // Single page
    if('page' == get_post_type()) {
        $image = get_field('meta_image');
        $description = get_field('meta_description');
    }
    // Single post
    elseif('post' == get_post_type()) {
        $issue = get_field('issue');
        $image = get_field('meta_image', $issue);
        if (get_field('meta_description', $post->ID)) {
            $description = get_field('meta_description', $post->ID);
        } elseif ($post !== NULL && $post->post_content) {
            $description = substr(strip_tags($post->post_content), 0, $char_limit) . '...';
        } else {
            $description = '';
        }
    }
    // Single issue
    elseif('issue' == get_post_type()) {
        $image = get_field('meta_image');
        if (get_field('meta_description', $post->ID)) {
            $description = get_field('meta_description', $post->ID);
        } elseif ($post !== NULL && $post->post_content) {
            $description = substr(strip_tags($post->post_content), 0, $char_limit) . '...';
        } else {
            $description = '';
        }
    }
    // Default fallback
    else {
        $image = get_field('header_default_meta_image', 'options');
        $description = 'Teset';
    }
?>

<meta property="og:title" content="<?php echo htmlspecialchars($title); ?> | SAPIR Journal" />
<meta property="og:image" content="<?php echo $image['url']; ?>" />
<meta property="og:url" content="<?php echo $url; ?>" />
<meta property="og:type" content="article" />
<meta property="og:description" content="<?php echo $description; ?>" />

<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:image" content="<?php echo $image['url']; ?>" />
<meta name="twitter:site" content="@SapirJournal">
<meta name="twitter:text" content="<?php echo $description; ?>" />