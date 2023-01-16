<?php
    $args = wp_parse_args($args);

    if(!empty($args)) {
        $authors = $args['authors']; 
        $authors_count = $args['authors_count']; 
    }  
?>

<?php if($authors): ?>
    <div class="teaser__authors authors-<?php echo $authors_count; ?>">
        <em>by</em>

        <div class="teaser__authors-list">
            <?php foreach($authors as $author): ?><a href="<?php echo get_permalink($author); ?>"><?php echo get_the_title($author); ?></a><?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>