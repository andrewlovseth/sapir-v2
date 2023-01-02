<div class="section-header">
    <h3 class="sub-title">Contents</h3>
</div>

<div class="contents">
    <?php if(have_rows('table_of_contents')): while(have_rows('table_of_contents')) : the_row(); ?>

        <?php if( get_row_layout() == 'section' ): ?>

            <div class="sub-section">
                <?php if(get_sub_field('heading')):?>
                    <div class="headline">
                        <h2><?php the_sub_field('heading'); ?></h2>
                    </div>
                <?php endif; ?>

                <?php if(have_rows('articles')): ?>
                    <ul>
                        <?php while(have_rows('articles')): the_row(); ?>
                            <?php
                                $article = get_sub_field('article');
                                $authors = get_field('author', $article->ID);
                                $authors_count = count($authors);
                                $alt_display = get_sub_field('alternate_display');
                                $status = get_post_status($article->ID);

                                $className = 'article-link';
        
                                if( $alt_display ) {
                                    $className .= ' alt';
                                }
                                
                                if( $status == 'draft' || $status == 'future' ) {
                                    $className .= ' draft';
                                }                                
                            ?>
                            <li>
                                <a href="<?php echo get_permalink($article->ID); ?>" class="<?php echo esc_attr($className); ?>">
                                    <span class="author authors-<?php echo $authors_count; ?>">    
                                        <?php foreach($authors as $author): ?>
                                            <span class="name"><?php echo get_the_title($author); ?></span>
                                        <?php endforeach; ?>
                                    </span>

                                    <span class="article-title">
                                        <?php if(get_field('display_title', $article->ID)): ?>
                                            <?php the_field('display_title', $article->ID); ?>
                                        <?php else: ?>
                                            <?php echo get_the_title($article->ID); ?>                                            
                                        <?php endif; ?>
                                    </span>          
                                            
                                    <?php if( $status == 'draft' || $status == 'future' ):?>
                                        <span class="date">Available on <?php echo get_the_time('F j', $article->ID); ?></span>
                                    <?php endif; ?>
                                </a>
                            </li>                            
                        <?php endwhile; ?>
                    </ul>
                <?php endif; ?>
            </div>

        <?php endif; ?>
    
    <?php endwhile; endif; ?>
</div>