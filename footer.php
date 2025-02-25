	</main> <!-- .site-content -->

	<footer class="site-footer grid">
		<?php get_template_part('template-parts/footer/banner'); ?>

		<?php get_template_part('template-parts/footer/logo'); ?>

		<?php get_template_part('template-parts/global/navigation'); ?>

		<?php get_template_part('template-parts/footer/epigraph'); ?>

		<?php get_template_part('template-parts/footer/social'); ?>

		<?php get_template_part('template-parts/footer/copyright'); ?>
	</footer>
	
	<?php get_template_part('template-parts/global/newsletter-modal'); ?>

	<?php get_template_part('template-parts/footer/share-modal'); ?>

	<?php wp_footer(); ?>
	<?php echo get_field('body_bottom_js', 'options'); ?>

</div> <!-- .site -->

</body>
</html>