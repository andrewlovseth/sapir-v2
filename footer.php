
			<?php get_template_part('template-parts/footer/newsletter-sign-up'); ?>
	</main> <!-- .site-content -->

	<footer class="site-footer grid">
		<?php get_template_part('template-parts/footer/logo'); ?>

		<?php get_template_part('template-parts/footer/navigation'); ?>

		<?php get_template_part('template-parts/footer/epigraph'); ?>
	</footer>

	<?php wp_footer(); ?>
	<?php the_field('body_bottom_js', 'options'); ?>

</div> <!-- .site -->

</body>
</html>