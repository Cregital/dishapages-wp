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

	$wpdisha_pages_options = get_option( 'wpdisha_pages' );

	$custom_classes = 'wpdisha-pages-template';

	if ($wpdisha_pages_options['disable_header'] == 1) { $custom_classes .= " disabled-header"; }

	//SEO from Disha Pages
	$pages_content = get_transient( 'wpdisha_pages_content' );
 
	if( false === $pages_content ) {
		
	    // Transient expired, refresh the data
	    $wpdisha_response = wp_remote_get( $wpdisha_pages_options['url'] );

        if ( ! is_wp_error( $wpdisha_response )  && isset( $wpdisha_response['response']['code'] ) && 200 === $wpdisha_response['response']['code'] ) {
        	$wpdisha_body = wp_remote_retrieve_body($wpdisha_response );
	    	set_transient( 'wpdisha_pages_content', $wpdisha_body, 60*60 );
        }
	    
	}

	$dom = new DOMDocument;
	@$dom->loadHTML($pages_content);

	$info = json_decode(explode(";", $dom->textContent)[1], true);

	$username = $info['username'];
	$settings = json_decode($info['settings'], true);
	$pageTitle = $settings['pageTitle'] ?? '';
	$bio = $settings['bio'] ?? '';
	$profileImage = $settings['profileImage'] ?? plugins_url( 'opengraph.png',  __FILE__ );

	?>

	<meta name="description" content="<?php echo $bio ?>"/>
    <!-- Google / Search Engine Tags -->
    <meta itemprop="name" content="<?php echo $pageTitle; ?>"/>
    <meta itemprop="description" content="<?php echo $bio; ?>"/>
    <meta itemprop="image" content="<?php echo $profileImage ?>"/>
    <!-- Facebook Meta Tags -->
    <meta property="og:url" content="<?php echo $wpdisha_options['url']; ?>"/>
    <meta property="og:type" content="website"/>
    <meta property="og:title" content="<?php echo $pageTitle; ?>"/>
    <meta property="og:description" content="<?php echo $bio; ?>"/>
    <meta property="og:image" content="<?php echo $profileImage ?>"/>
    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image"/>
    <meta name="twitter:title" content="<?php echo $pageTitle; ?>"/>
    <meta name="twitter:description" content="<?php echo $bio; ?>"/>
    <meta name="twitter:image" content="<?php echo $profileImage ?>"/>

	<style type="text/css">
		.disabled-header a.back { position: absolute; padding: 20px; mix-blend-mode: difference; color: #fff; font-family: Sans-Serif; font-size: 14px;font-weight: 700; }
		.disabled-header a.back svg { stroke: #fff;vertical-align: -7px; margin-right: 10px; width: 20px; }
		iframe { margin:0px !important; padding: !important; }
	</style>
</head>

<body <?php body_class( $custom_classes ); ?>>

<?php

if ($wpdisha_pages_options['disable_header'] != 1) { get_header(); } else {
	echo '<a href="'. home_url() .'" class="back"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H6M12 5l-7 7 7 7"/></svg> Back to Home</a>';
} ?>

<?php while ( have_posts() ) : ?>

	<?php the_post(); ?>

	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

		<?php the_content(); ?>

	</article>

<?php endwhile; ?>

<?php if ($wpdisha_pages_options['disable_footer'] != 1) { get_footer(); } ?>

<?php wp_footer(); ?>

</body>
</html>
