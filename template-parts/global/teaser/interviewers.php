<?php
    $args = wp_parse_args($args);

    if(!empty($args)) {
        $interviewers = $args['interviewers']; 
        $interviewers_count = $args['interviewers_count']; 
    }  
?>

<?php if($interviewers): ?>
    <div class="teaser__interviewers teaser__interviewers-<?php echo $interviewers_count; ?>">
        <em class="teaser__interviewers-by">Interviewed by</em>
        
        <?php foreach($interviewers as $interviewer): ?>
            <div class="teaser__interviewer"><a href="<?php echo get_permalink($interviewer); ?>" class="teaser__interviewer-link"><span class="teaser__interviewer-name"><?php echo get_the_title($interviewer); ?></span></a></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>    