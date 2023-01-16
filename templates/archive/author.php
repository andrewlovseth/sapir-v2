<?php

    $authors_acf = get_field('authors');
    $header = $authors_acf['header'];
    $copy = $authors_acf['copy'];

    $initial = 's';

    $author_ids = get_posts(array(
        'fields' => 'ids',
        'posts_per_page' => -1,
        'post_type' => 'authors',
    ));

    $authors = [];

    foreach($author_ids as $a_id) {
        $first_name = get_field('first_name', $a_id);
        $last_name = get_field('last_name', $a_id);

        if($last_name) {
            $authors[] = array('id' => $a_id, 'first_name' => $first_name, 'last_name' => $last_name);
        }
    }

    usort($authors, function ($a, $b) {
        if ( $a['last_name'] == $b["last_name"] ) {
        return $a['first_name'] <=> $b['first_name'];
        }
        return $a['last_name'] <=> $b['last_name'];
    });

?>

<section class="author grid">
    <div class="section-header">
        <h5 class="upper-header"><?php echo $header; ?></h5>

        <div class="copy copy-1">
            <p><?php echo $copy; ?></p>
        </div>
    </div>



    <div class="author__tabs-links">
        <?php foreach(range('a','z') as $letter): ?>
            <?php
                $class_list = 'author__tabs-link';

                $filtered_authors = [];
                foreach($authors as $author) {
                    if(str_starts_with($author['last_name'], strtoupper($letter))) {
                        $filtered_authors[] = $author;
                    }
                }

                if($letter == $initial) {
                    $class_list .= ' active';
                }

                if(empty($filtered_authors)) {
                    $class_list .= ' empty';
                }


            ?>            

            <a href="#" data-letter="<?php echo strtoupper($letter); ?>" class="<?php echo $class_list; ?>">
                <?php echo $letter; ?>
            </a>
        <?php endforeach; ?>
    </div>

    <div class="author__tabs">        
        <?php foreach(range('a','z') as $letter): ?>
            <?php
                $class_list = 'author__tabs-group';

                if($letter == $initial) {
                    $class_list .= ' active';
                }
            ?>

            <div class="<?php echo $class_list; ?>" data-letter="<?php echo strtoupper($letter); ?>">            
                <?php 
                    $filtered_authors = [];
                    foreach($authors as $author) {
                        if(str_starts_with($author['last_name'], strtoupper($letter))) {
                            $filtered_authors[] = $author;
                        }
                        
                    }
                ?>

                <?php foreach($filtered_authors as $a): ?>
                    <?php
                        $name = get_the_title($a['id']);
                        $permalink = get_permalink($a['id']);
                    ?>

                    <div class="author__item">
                        <a href="<?php echo $permalink; ?>" class="author__link"><?php echo $name; ?></a>
                    </div>
                <?php endforeach; ?>
            </div>

        <?php endforeach; ?>
    </div>
</section>