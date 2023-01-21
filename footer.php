	</main> <!-- .site-content -->

	<footer class="site-footer grid">
		<?php get_template_part('template-parts/footer/banner'); ?>

		<?php get_template_part('template-parts/footer/logo'); ?>

		<?php get_template_part('template-parts/global/navigation'); ?>

		<?php get_template_part('template-parts/footer/epigraph'); ?>

		<?php get_template_part('template-parts/footer/copyright'); ?>
	</footer>

	<?php wp_footer(); ?>
	<?php the_field('body_bottom_js', 'options'); ?>

</div> <!-- .site -->

</body>
</html>