<div class="issue__contents">
    <div class="issue__contents-body">
        <?php if(have_rows('table_of_contents')): while(have_rows('table_of_contents')) : the_row(); ?>

            <?php if( get_row_layout() == 'section' ): ?>
                <?php
                    $header = get_sub_field('heading'); 
                ?>
            
                <div class="issue__contents-section">
                    <?php if($header):?>
                        <h2 class="issue__contents-header upper-header"><?php echo $header; ?></h2>
                    <?php endif; ?>

                    <?php if(have_rows('articles')):  while(have_rows('articles')): the_row(); ?>

                        <?php
                            $article = get_sub_field('article');
                            $args = ['p' => $article->ID];
                            get_template_part('template-parts/global/teaser/teaser-contents', null, $args);
                        ?>

                    <?php endwhile; endif; ?>
                </div>
            <?php endif; ?>

        <?php endwhile; endif; ?>
    </div>
</div>