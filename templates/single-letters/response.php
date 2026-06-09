<?php
    $response = get_field('response');
    $copy = $response['copy'];

    if($copy):
?>

    <section class="response">
        <div class="copy copy-1">
            <?php echo $copy; ?>
        </div>
    </section>

<?php endif; ?>
