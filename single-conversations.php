<?php
    $cta = get_field('meta_cta');

	if($cta) {
	    $link = $cta['url'];
    } else {
        $link = '/conversations/';
    }

    header("HTTP/1.1 301 Moved Permanently");
    header("Location: " . $link);
    exit();
?>