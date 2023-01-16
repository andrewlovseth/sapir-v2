<?php if(have_rows('navigation', 'options')): ?>
    <nav class="site-nav">
        <?php while(have_rows('navigation', 'options')) : the_row(); ?>

            <?php if( get_row_layout() == 'group' ): ?>
                <?php
                    $header = get_sub_field('header');
                ?>

                <div class="site-nav__group">
                    <div class="site-nav__group-header">
                        <h4 class="site-nav__title upper-header"><?php echo $header; ?></h4>
                    </div>

                    <ul class="site-nav__list">
                        <?php if(have_rows('links')): while(have_rows('links')): the_row(); ?>
                        
                            <li class="site-nav__list-item">
                                <?php
                                    $link = get_sub_field('link');
                                    $args = ['link' => $link];
                                    get_template_part('template-parts/global/link', null, $args);
                                ?>                            
                            </li>

                        <?php endwhile; endif; ?>
                    </ul>
                    
                    
                </div>
            <?php endif; ?>

        <?php endwhile; ?>
    </nav>
<?php endif; ?>