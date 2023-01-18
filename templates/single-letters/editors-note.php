<?php
    $editors_note = get_field('response');
    $copy = $editors_note['copy'];
    
    if($copy):    
?>

    <section class="editors-note">

        <div class="copy copy-2">
            <?php echo $copy; ?>
        </div>
        
    </section>

<?php endif; ?>