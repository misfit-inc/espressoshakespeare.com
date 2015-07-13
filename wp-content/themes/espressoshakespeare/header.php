<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head profile="http://gmpg.org/xfn/11">

<title><?php woo_title(); ?></title>
<?php woo_meta(); ?>
<?php global $woo_options; ?>

<link rel="stylesheet" type="text/css" href="<?php bloginfo('stylesheet_url'); ?>" media="screen" />
<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="<?php if ( $woo_options['woo_feed_url'] ) { echo $woo_options['woo_feed_url']; } else { echo get_bloginfo_rss('rss2_url'); } ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

<?php wp_head(); ?>
<?php woo_head(); ?>

<!-- <link id="theme" rel="stylesheet" type="text/css" href="/wp-content/themes/shakespeareguides/includes/css/jquery.ui.theme.css" />
<link id="theme" rel="stylesheet" type="text/css" href="/wp-content/themes/shakespeareguides/includes/css/jquery.ui.resizable.css" />
<link id="theme" rel="stylesheet" type="text/css" href="/wp-content/themes/shakespeareguides/includes/css/jquery.ui.dialog.css" />
<link id="theme" rel="stylesheet" type="text/css" href="/wp-content/themes/shakespeareguides/includes/css/jquery.ui.core.css" />
<link id="theme" rel="stylesheet" type="text/css" href="/wp-content/themes/shakespeareguides/includes/css/jquery.ui.base.css" />
<link id="theme" rel="stylesheet" type="text/css" href="/wp-content/themes/shakespeareguides/includes/css/jquery.ui.all.css" /> -->

<script type="text/javascript" src="http://use.typekit.com/xgk0guj.js"></script>
<script type="text/javascript">try{Typekit.load();}catch(e){}</script>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script src="/wp-content/themes/espressoshakespeare/includes/js/jquery-ui-1.8.16.custom.min.js"></script>

<script type="text/javascript" src="/wp-content/themes/espressoshakespeare/includes/js/content-loader.js"></script>
<script type="text/javascript" src="/wp-content/themes/espressoshakespeare/includes/js/scripts.js"></script>

<script>
	// increase the default animation speed to exaggerate the effect
	// $.fx.speeds._default = 300;
	$(function() {
		$( "#dialog" ).dialog({
			minHeight: 166,
			width: 'auto',
			autoOpen: false,
			show: "fade",
			hide: "fade"
		});

		$( "#opener" ).click(function() {
			$( "#dialog" ).dialog( "open" );
			return false;
		});
		
		$( "#opener-1" ).click(function() {
			$( "#dialog" ).dialog( "open" );
			return false;
		});
	});
</script>

<script type="text/javascript">

	$(document).ready(function(){
	
		$('li.choose').children('a').mouseover( function() {
			$('#top-nav ul').show('blind');
		});
		
	});
	
</script>

</head>

<body <?php body_class(); ?>>
<?php woo_top(); ?>

<div id="wrapper">

	<?php if ( function_exists('has_nav_menu') && has_nav_menu('top-menu') ) { ?>
	
		<?php if (!is_home()) { ?>

			<div id="top">
				<div class="col-full">
					<?php wp_nav_menu( array( 'depth' => 6, 'sort_column' => 'menu_order', 'container' => 'ul', 'menu_id' => 'top-nav', 'menu_class' => 'nav fl', 'theme_location' => 'top-menu' ) ); ?>
				</div>
			</div><!-- /#top -->
		
		<?php } else {} ?>

    <?php } ?>

	<div id="header">
	
		<?php get_template_part('login'); ?>
		
		<ul class="social-icons">
			<li class="blog"><a href="http://shakespeareguides.com" target="_blank"><span class="dn">our blog</span></a></li>
			<li class="twitter"><a href="http://www.twitter.com/ShakespeareBT" target="_blank"><span class="dn">follow us</span></a></li>
			<li class="facebook"><a href="http://www.facebook.com/ShakespeareBT" target="_blank"><span class="dn">like us</span></a></li>
		</ul>
		
		<div class="header-wrapper">

			<div id="logo">

			<?php if( is_singular() && !is_front_page() ) : ?>
				<span class="site-title"><a href="<?php bloginfo('url'); ?>"><?php bloginfo('name'); ?></a></span>
			<?php else : ?>
				<h1 class="site-title"><a href="<?php bloginfo('url'); ?>"><?php bloginfo('name'); ?></a></h1>
			<?php endif; ?>
				<span class="site-description"><?php bloginfo('description'); ?></span>

			</div><!-- /#logo -->
			
			<a class="shakespeare-birthplace-trust" href="http://shakespeare.org.uk" target="_none"><span class="dn">Shakespeare Birthplace Trust</span></a>
			
			<div class="blogging-shakespeare-with-misfit-inc">
						
				<p>Handcrafted by <a href="http://bloggingshakespeare.com" target="_blank">Blogging Shakespeare</a> <br/>in Partnership with <a href="http://misfit-inc.com" target="_blank">Misfit Inc.</a></p>
			
			</div>
			
		</div>

	</div><!-- /#header -->

	<?php if ($woo_options['woo_featured'] == 'true' && is_home() && !is_paged()) include ( TEMPLATEPATH . '/includes/featured.php' ); ?>
