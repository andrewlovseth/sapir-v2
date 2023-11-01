<?php
    $url = get_permalink();
    $title = get_the_title();
    $char_limit = 140;

    if(is_single() && 'post' == get_post_type()) {
        $issue = get_field('issue');
        $image = get_field('meta_image', $issue);
    } elseif(is_single() && 'issue' == get_post_type())  {
        $image = get_field('meta_image');
    } else {
        $image = get_field('header_default_meta_image', 'options');
    }
    
    if($post !== NULL) {
        $content = $post->post_content; 
        if($content) {
            $description = substr(strip_tags($content), 0, $char_limit)  . '...'; 
        } else {
            $description = '';
        }
    }
?>

<meta property="og:title" content="<?php echo $title; ?> | SAPIR Journal" />
<meta property="og:image" content="<?php echo $image['url']; ?>" />
<meta property="og:url" content="<?php echo $url; ?>" />
<meta property="og:type" content="article" />
<meta property="og:description" content="<?php echo $description; ?>" />

<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:image" content="<?php echo $image['url']; ?>" />
<meta name="twitter:site" content="@SapirJournal">

<meta name="twitter:text" content="<?php echo $description; ?>" />