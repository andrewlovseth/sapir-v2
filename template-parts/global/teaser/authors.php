<?php
    $args = wp_parse_args($args);

    if(!empty($args)) {
        $authors = $args['authors']; 
        $authors_count = $args['authors_count']; 
    }  
?>

<?php if($authors): ?>
    <div class="teaser__authors teaser__authors-<?php echo $authors_count; ?>">
        <em class="teaser__authors-by">by</em>
        
        <?php foreach($authors as $author): ?>
            <div class="teaser__author"><a href="<?php echo get_permalink($author); ?>" class="teaser__author-link"><span class="teaser__author-name"><?php echo get_the_title($author); ?></span></a></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>