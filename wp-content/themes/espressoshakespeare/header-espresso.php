<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head profile="http://gmpg.org/xfn/11">
<meta name="google-site-verification" content="arsVK1i1j8YwGDZ7xWaNSoukbTC8e3GU1i9SEI-pFZ8" />

<title><?php woo_title(); ?></title>
<?php woo_meta(); ?>
<?php global $woo_options; ?>

<link rel="stylesheet" type="text/css" href="<?php bloginfo('stylesheet_url'); ?>" media="screen" />
<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="<?php if ( $woo_options['woo_feed_url'] ) { echo $woo_options['woo_feed_url']; } else { echo get_bloginfo_rss('rss2_url'); } ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

<?php wp_head(); ?>
<?php woo_head(); ?>

<link id="theme" rel="stylesheet" type="text/css" href="/wp-content/themes/espressoshakespeare/includes/css/jquery.ui.theme.css" />
<link id="theme" rel="stylesheet" type="text/css" href="/wp-content/themes/espressoshakespeare/includes/css/jquery.ui.resizable.css" />
<link id="theme" rel="stylesheet" type="text/css" href="/wp-content/themes/espressoshakespeare/includes/css/jquery.ui.dialog.css" />
<link id="theme" rel="stylesheet" type="text/css" href="/wp-content/themes/espressoshakespeare/includes/css/jquery.ui.core.css" />
<link id="theme" rel="stylesheet" type="text/css" href="/wp-content/themes/espressoshakespeare/includes/css/jquery.ui.base.css" />
<link id="theme" rel="stylesheet" type="text/css" href="/wp-content/themes/espressoshakespeare/includes/css/jquery.ui.all.css" />

<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script src="/wp-content/themes/espressoshakespeare/includes/js/jquery-ui-1.8.16.custom.min.js"></script>

<link rel="stylesheet" href="/wp-content/themes/espressoshakespeare/includes/js/colorbox/colorbox.css" />
<script type="text/javascript" src="/wp-content/themes/espressoshakespeare/includes/js/jquery.colorbox.js"></script>

<link rel="stylesheet" href="/wp-content/themes/espressoshakespeare/includes/css/iosslider.css" />
<script type="text/javascript" src="/wp-content/themes/espressoshakespeare/includes/js/jquery.iosslider.min.js"></script>

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
		
		// $("a").each(function() {
			// $(this).attr('id', $(this).text().toLowerCase().replace(/[\*\^\'\!]/g, '').split(' ').join('-'));
		// });

	});
</script>

<script type="text/javascript" src="http://use.typekit.com/xgk0guj.js"></script>
<script type="text/javascript">try{Typekit.load();}catch(e){}</script>

<script src="/wp-content/themes/espressoshakespeare/includes/js/selectbox.js" type="text/javascript"></script>
					
<script type="text/javascript">
window.addEvent('domready',function(){
	$$('select.mgm_register_field').each(function(el){
		new cSelectBox(el);
	});
});
</script>

<script type="text/javascript">

	$(document).ready(function(){
	
		var href = jQuery(location).attr('href');
		var url = jQuery(this).attr('title');
		
		$('li.current-menu-ancestor').children('a').html('<?php wp_title("",true); ?>');
		$('li.current-menu-ancestor').children('a').mouseover( function() {
			$('#top-nav ul').show('blind','fast');
		});
		
		<?php if(is_home()) { ?>
			$('li.choose').children('a').mouseover( function() {
				$('#top-nav ul').show('blind','fast');
			});
		<?php } else {} ?>
		
		$('a.cboxElement').colorbox({
			iframe:true,
			speed: 300,
			preloading: true,
			width: '90%',
			height: '90%'
		});
		
		$('a.deskcboxpodio').colorbox({
			iframe:true,
			width: 750,
			height:"90%",
			onComplete: function() {
				$("#colorbox").addClass("podio");
				$("#cboxWrapper").addClass("podio");
				$("#cboxContent").parent('div').addClass("podio");
				$("#cboxContent").addClass("podio");
				$("#cboxLoadedContent").addClass("podio");
			},
			onClosed: function() {
				$("#colorbox").removeClass("podio");
				$("#cboxWrapper").removeClass("podio");
				$("#cboxContent").parent('div').removeClass("podio");
				$("#cboxContent").removeClass("podio");
				$("#cboxLoadedContent").removeClass("podio");
			}
		});
		
		$('a.tabcboxpodio').colorbox({
			iframe:true,
			width: "95%",
			height:"95%",
			onComplete: function() {
				$("#colorbox").addClass("podio");
				$("#cboxWrapper").addClass("podio");
				$("#cboxContent").parent('div').addClass("podio");
				$("#cboxContent").addClass("podio");
				$("#cboxLoadedContent").addClass("podio");
			},
			onClosed: function() {
				$("#colorbox").removeClass("podio");
				$("#cboxWrapper").removeClass("podio");
				$("#cboxContent").parent('div').removeClass("podio");
				$("#cboxContent").removeClass("podio");
				$("#cboxLoadedContent").removeClass("podio");
			}
		});
		
		$('#topnavmenu .sub-menu').css('display', 'none');
		$('#topnavmenu li.choose > a').addClass('greet');
		
		$('#topnavmenu').click(function() {
			$('#topnavmenu .sub-menu').slideToggle();
		});
		
		$('#topnavmenu .greet').click(function(e) {
			e.preventDefault();
		});
		
	});
	
</script>

<script>

	$(document).ajaxComplete(function(){
		/*
		$("a.cboxElement").each(function() {
			$(this).attr('id', $(this).text().toLowerCase().replace(/[\*\^\'\!]/g, '').split(' ').join('-'));
		});
		
		if (window.location.hash === "") {
		
		} else { $(window.location.hash).colorbox({open:true}); }
		
		$(document).unbind('ajaxComplete');
		
		// var colorboxId = 
				// (window.location.href.indexOf('open=')==-1) ?
					// false :
					// window.location.href.slice(window.location.href.indexOf('open=') + 'open='.length + 1).split('&')[0];

		// var colorboxId = url.match(/open=([\w\d]*)/) && RegExp.$1 || false;

		// $("a.cboxElement").colorbox();

		// if(colorboxId!==false) {
			// $('#' + colorboxId).colorbox({open:true});
		// }
		
		
*/
	});
	
	// $(document).ready(function(){
	
		// $("a#conflict").click();
	
	// });
	
	
	// $(document).ready(function(){
	
		// alert("document ready occurred!");
	
	// });
	
</script>

<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

<script src='http://connect.facebook.net/en_US/all.js'></script>

</head>

<body <?php body_class(); ?>>
<?php woo_top(); ?>

<div id="wrapper">

	<?php if ( function_exists('has_nav_menu') && has_nav_menu('top-menu') ) { ?>

	<!-- <div id="top">
		<div class="col-full">
			<?php //wp_nav_menu( array( 'depth' => 6, 'sort_column' => 'menu_order', 'container' => 'ul', 'menu_id' => 'top-nav', 'menu_class' => 'nav fl', 'theme_location' => 'top-menu' ) ); ?>
		</div>
	</div> -->

    <?php } ?>

	<div id="header">
	
		<?php if (is_page(59)) { ?>
			
		<?php } else { ?>
			
			<?php // get_template_part('login'); ?>
			
		<?php } ?>
		
		<!--
		
		<ul class="social-icons">
			<li class="blog"><a href="http://bloggingshakespeare.com"><span class="dn">our blog</span></a></li>
			<li class="twitter"><a target="_blank" href="https://twitter.com/intent/tweet?original_referer=http%3A%2F%2Fespressoshakespeare.com%2F&text=Created%20by%20%40ShakespeareBT%20and%20AQA%20to%20inspire%20and%20inform%20links%20between%20Shakespeare's%20plays%20for%20classroom%20teaching.%20http%3A%2F%2Fespressoshakespeare.com%2F"><span class="dn">Tweet</span></a></li>
			<li class="facebook"><a href="https://www.facebook.com/dialog/feed?
  app_id=436550149759438&
  link=http://espressoshakespeare.com/&
  picture=http://espressoshakespeare.com/images/shakespeare-icon90.png&
  name=Espresso%20Shakespeare&
  caption=powered%20by%20Shakespeare%20BirthPlace%20Trust%20and%20exclusively%20for%20AQA& description=Welcome%20to%20Espresso%20Shakespeare.%20This%20resource%2C%20which%20is%20a%20creative%20collaboration%20between%20AQA%20and%20The%20Shakespeare%20Birthplace%20Trust%2C%20has%20been%20designed%20to%20help%20you%20make%20inspired%20and%20informed%20links%20between%20Shakespeare%E2%80%99s%20plays%20for%20classroom%20teaching.&
  redirect_uri=http://espressoshakespeare.com/" target="_blank"><span class="dn">like us</span></a></li>
		</ul>
		
		-->
		
		<div class="header-wrapper">
		
			<a class="mp-link" href="/">EspressoShakespeare</a>
			
			<?php wp_nav_menu( array( 'depth' => 6, 'sort_column' => 'menu_order', 'container' => 'ul', 'menu_id' => 'top-nav', 'menu_class' => 'nav fl', 'theme_location' => 'top-menu' ) ); ?>
			
			<div class="logos">
				<a class="shakespeare-birthplace-trust" href="http://shakespeare.org.uk" target="_blank"><span class="dn">Shakespeare Birthplace Trust</span></a>
				<a class="aqa_logo" href="http://www.aqa.org.uk/" target="_blank"><span class="dn">AQA</span></a>
				<a class="misfit_inclogo" href="http://www.misfit-inc.com/" target="_blank"><span class="dn">Misfit Incorporated</span></a>
			</div>
			
			<?php wp_nav_menu( array( 'depth' => 6, 'sort_column' => 'menu_order', 'container' => 'ul', 'menu_id' => 'top-nav', 'menu_class' => 'nav fl mobile', 'theme_location' => 'top-menu' ) ); ?>
			
			<!--<div id="logo">

				<?php if ($woo_options['woo_texttitle'] <> "true") : $logo = $woo_options['woo_logo']; ?>
					<h1><a href="<?php bloginfo('url'); ?>" title="<?php bloginfo('description'); ?>"><span class="dn">Espresso Shakespeare: Powered by Shakespeare Birthplace Trust, Handcrafted by Blogging Shakespeare in Partnership with misfit inc.</span></a></h1>
				<?php endif; ?>

				<?php if( is_singular() && !is_front_page() ) : ?>
					<span class="site-title"><a href="<?php bloginfo('url'); ?>"><?php bloginfo('name'); ?></a></span>
				<?php else : ?>
					<h1 class="site-title"><a href="<?php bloginfo('url'); ?>"><?php bloginfo('name'); ?></a></h1>
				<?php endif; ?>
					<span class="site-description"><?php bloginfo('description'); ?></span>

			</div>--><!-- /#logo -->
			
			
			
		</div>

	</div><!-- /#header -->

	<?php if ($woo_options['woo_featured'] == 'true' && is_home() && !is_paged()) include ( TEMPLATEPATH . '/includes/featured.php' ); ?>
