<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	
	<?php wp_head(); ?>
	<?php the_field('head_js', 'options'); ?>
	<?php // get_template_part('template-parts/header/meta-tags'); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<?php the_field('body_top_js', 'options'); ?>

<div id="page" class="site">
	
	<header class="site-header grid">
		<?php get_template_part('template-parts/header/border'); ?>

		<div class="site-header__wrapper">
			<?php get_template_part('template-parts/header/hamburger'); ?>

			<?php get_template_part('template-parts/header/logo'); ?>

			<?php get_template_part('template-parts/header/search'); ?>
		</div>
		
		<?php get_template_part('template-parts/header/navigation'); ?>

		<?php get_template_part('template-parts/header/tagline'); ?>

		<?php get_template_part('template-parts/header/divider'); ?>

	</header>


	<main class="site-content">