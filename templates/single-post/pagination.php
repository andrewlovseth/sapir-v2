<?php
// Get the current post and its associated issue
$current_post_id = get_the_ID();
$issue = get_field('issue');

// Initialize pagination variables
$prev_article = null;
$next_article = null;
$all_articles = [];

// If we have an issue, get all articles from the table of contents
if ($issue && have_rows('table_of_contents', $issue->ID)) {
    while (have_rows('table_of_contents', $issue->ID)) {
        the_row();
        
        if (get_row_layout() == 'section' && have_rows('articles')) {
            while (have_rows('articles')) {
                the_row();
                $article = get_sub_field('article');
                if ($article) {
                    $all_articles[] = $article;
                }
            }
        }
    }
}

// Find current article position and set prev/next
if (!empty($all_articles)) {
    $current_index = -1;
    
    // Find the current article's position
    foreach ($all_articles as $index => $article) {
        if ($article->ID == $current_post_id) {
            $current_index = $index;
            break;
        }
    }
    
    // Set previous and next articles with circular navigation
    if ($current_index >= 0) {
        $total_articles = count($all_articles);
        
        // Previous article (circular: if at first, go to last)
        if ($current_index == 0) {
            $prev_article = $all_articles[$total_articles - 1];
        } else {
            $prev_article = $all_articles[$current_index - 1];
        }
        
        // Next article (circular: if at last, go to first)
        if ($current_index == $total_articles - 1) {
            $next_article = $all_articles[0];
        } else {
            $next_article = $all_articles[$current_index + 1];
        }
    }
}

// Only show pagination if we have articles and found the current one
if (!empty($all_articles) && $current_index >= 0):
?>

<div class="article-footer__pagination">
    <div class="article-footer__pagination-item article-footer__pagination-prev">
        <a href="<?php echo get_permalink($prev_article->ID); ?>" class="article-footer__pagination-link">


            <div class="article-footer__pagination-icon">
                <svg width="26" height="18" viewBox="0 0 26 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                   <path d="M9 17L1 9M1 9L9 1M1 9H25" stroke="black" stroke-width="2" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            
            <div class="article-footer__pagination-content">
                 <span class="article-footer__pagination-label">Previous article</span>
                 <span class="article-footer__pagination-title"><?php echo get_the_title($prev_article->ID); ?></span>
                 <?php 
                 $prev_authors = get_field('author', $prev_article->ID);
                 if ($prev_authors && is_array($prev_authors) && !empty($prev_authors)) {
                     $authors_count = count($prev_authors);
                     echo '<div class="authors authors-' . $authors_count . '">';
                     echo '<span class="authors-list">';
                     $i = 1;
                     foreach($prev_authors as $author) {
                         echo '<span class="authors-item">';
                         if($i == 1) {
                             echo '<em class="authors-by">by </em>';
                         }
                         echo get_the_title($author);
                         echo '</span> ';
                         $i++;
                     }
                     echo '</span>';
                     echo '</div>';
                 }
                 
                 $prev_interviewers = get_field('interviewers', $prev_article->ID);
                 if ($prev_interviewers && is_array($prev_interviewers) && !empty($prev_interviewers)) {
                     $interviewers_count = count($prev_interviewers);
                     echo '<div class="interviewers interviewers-' . $interviewers_count . '">';
                     echo '<em>Interviewed by </em>';
                     echo '<span class="interviewers-list">';
                     foreach($prev_interviewers as $interviewer) {
                         echo get_the_title($interviewer);
                     }
                     echo '</span>';
                     echo '</div>';
                 }
                 ?>
             </div>

        </a>
    </div>
  
    <div class="article-footer__pagination-item article-footer__pagination-next">
        <a href="<?php echo get_permalink($next_article->ID); ?>" class="article-footer__pagination-link">
            <div class="article-footer__pagination-content">
                 <span class="article-footer__pagination-label">Next article</span>
                 <span class="article-footer__pagination-title"><?php echo get_the_title($next_article->ID); ?></span>
                 <?php 
                 $next_authors = get_field('author', $next_article->ID);
                 if ($next_authors && is_array($next_authors) && !empty($next_authors)) {
                     $authors_count = count($next_authors);
                     echo '<span class="authors authors-' . $authors_count . '">';
                     echo '<span class="authors-list">';
                     $i = 1;
                     foreach($next_authors as $author) {
                         echo '<span class="authors-item">';
                         if($i == 1) {
                             echo '<em class="authors-by">by </em>';
                         }
                         echo get_the_title($author);
                         echo '</span>';
                         $i++;
                     }
                     echo '</span>';
                     echo '</span>';
                 }
                 
                 $next_interviewers = get_field('interviewers', $next_article->ID);
                 if ($next_interviewers && is_array($next_interviewers) && !empty($next_interviewers)) {
                     $interviewers_count = count($next_interviewers);
                     echo '<div class="interviewers interviewers-' . $interviewers_count . '">';
                     echo '<em>Interviewed by </em>';
                     echo '<span class="interviewers-list">';
                     foreach($next_interviewers as $interviewer) {
                         echo get_the_title($interviewer);
                     }
                     echo '</span>';
                     echo '</div>';
                 }
                 ?>
             </div>
            
            <div class="article-footer__pagination-icon">
                <svg width="26" height="18" viewBox="0 0 26 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M17 1L25 9M25 9L17 17M25 9L1 9" stroke="black" stroke-width="2" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>

</a>
    </div>
</div>

<?php endif; ?>
