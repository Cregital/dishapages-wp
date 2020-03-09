<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">

	<?php if ( ! get_theme_support( 'title-tag' ) ) : ?>
		<title><?php wp_title(); ?></title>
	<?php endif; 

	wp_head(); 

	$options = get_option( 'wpdisha_pages' );

	$custom_classes = 'disha-blank-slate';
	if ($options['disable_header'] == 1) {
		$custom_classes .= " disabled-header";
	}

	?>
	<style type="text/css">
		.disabled-header a.back {
		    position: absolute; padding: 20px; mix-blend-mode: difference; color: #fff; font-family: Sans-Serif; font-size: 14px;font-weight: 700;
		}

		.disabled-header a.back svg { stroke: #fff;vertical-align: -7px; margin-right: 10px; width: 20px; }
	</style>
</head>

<body <?php body_class( $custom_classes ); ?>>

<?php

if ($options['disable_header'] != 1) { get_header(); } else {
	echo '<a href="'. home_url() .'" class="back"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H6M12 5l-7 7 7 7"/></svg> Back to Home</a>';
} ?>

<?php while ( have_posts() ) : ?>

	<?php the_post(); ?>

	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

		<?php the_content(); ?>

	</article>

<?php endwhile; ?>

<?php if ($options['disable_footer'] != 1) { get_footer(); } ?>

<?php wp_footer(); ?>

</body>
</html>
