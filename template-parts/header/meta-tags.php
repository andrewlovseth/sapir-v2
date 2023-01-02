<?php

    $issue = get_field('issue'); 
    $volume = get_field('volume', $issue->ID);
    $issue_slug = sanitize_title_with_dashes($volume);
    $url = get_permalink();
    $title = get_the_title();

    if( is_single() && $issue_slug == "volume-seven") {
        $photo = get_bloginfo('template_directory') . "/images/seo-issue-7.jpg";    
    }elseif( is_single() && $issue_slug == "volume-six") {
        $photo = get_bloginfo('template_directory') . "/images/seo-issue-6.jpg";    
    } elseif( is_single() && $issue_slug == "volume-five") {
        $photo = get_bloginfo('template_directory') . "/images/seo-issue-5.jpg";    
    } elseif( is_single() && $issue_slug == "volume-four") {
        $photo = get_bloginfo('template_directory') . "/images/seo-issue-4.jpg";
    } elseif( is_single() && $issue_slug == "volume-three") {
        $photo = get_bloginfo('template_directory') . "/images/seo-issue-3.jpg";
    } elseif( is_single() && $issue_slug == "volume-two") {
        $photo = get_bloginfo('template_directory') . "/images/seo-issue-2.jpg";
    } elseif( is_single() && $issue_slug == "volume-one") {
        $photo = get_bloginfo('template_directory') . "/images/seo-image.jpg";
    } else {
        $photo = get_bloginfo('template_directory') . "/images/seo-default.jpg";
    }

    $char_limit = 140;
    $content = $post->post_content; 
    $description = substr(strip_tags($content), 0, $char_limit)  . '...'; 

?>

<meta property="og:title" content="<?php echo $title; ?> | Sapir Journal" />
<meta property="og:image" content="<?php echo $photo; ?>" />
<meta property="og:url" content="<?php echo $url; ?>" />
<meta property="og:type" content="article" />
<meta property="og:description" content="<?php echo $description; ?>" />

<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:image" content="<?php echo $photo; ?>" />
<meta name="twitter:site" content="@SapirJournal">

<meta name="twitter:text" content="<?php echo $description; ?>" />