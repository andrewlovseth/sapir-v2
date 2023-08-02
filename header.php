<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	
	<?php wp_head(); ?>
	<?php the_field('head_js', 'options'); ?>
	<?php get_template_part('template-parts/header/meta-tags'); ?>
</head>

<?php
    $home = get_option('page_on_front');
    $current_issue = get_field('latest_current_issue', $home);
    $current_issue_slug = 'site-theme-' . sanitize_title_with_dashes(get_field('volume', $current_issue->ID));

    if(is_single() && 'post' == get_post_type()) {
        $issue = get_field('issue');
		$volume = get_field('volume', $issue->ID);
		$issue_class = ' issue-theme-' . sanitize_title_with_dashes($volume);

    } elseif(is_single() && 'issue' == get_post_type())  {
        $volume = get_field('volume');
		$issue_class = ' issue-theme-' . sanitize_title_with_dashes($volume);

    } else {
		$issue_class = '';

	}
?>


<body <?php body_class($current_issue_slug); ?>>
<?php wp_body_open(); ?>
<?php the_field('body_top_js', 'options'); ?>

<div id="page" class="site<?php echo $issue_class; ?>">
	
	<header class="site-header grid">
		<?php get_template_part('template-parts/header/banner'); ?>

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