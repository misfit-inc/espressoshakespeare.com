// $(document).ready(function() {
				   
	// var hash = window.location.hash.substr(1);
	
	// var href = $('#espresso-nav li.load-content a').each(function(){
			// var href = $(this).attr('href');
			// if(hash==href.substr(0,href.length-0)){
				// var toLoad = hash+'.html #the-post';
				// $('#the-post').load(toLoad)
			// }											
	// });

	// var toLoad = $('#espresso-nav li.first-child a').attr('href')+' #the-post';
	// $('#the-post').html('<span id="load"><span class="dn">LOADING...</span></span>').load(toLoad);
	
	// $('#espresso-nav li.load-content a').click(function(){
								  
		// var toLoad = $(this).attr('href')+' #the-post';
		
		// $('#the-post').html('');
		// loadContent();
				
		// function loadContent() {
			// $('#the-post').html('<span id="load"><span class="dn">LOADING...</span></span>').load(toLoad);
		// }
		
		// function showNewContent() {
			// $('#the-post');
		// }
		
		// return false;
		
	// });

// });

$(document).ready(function() {

	$('#espresso-nav li:first-child').addClass('first-child');
	$('#espresso-nav li:nth-child(2)').addClass('second-child');
	$('#espresso-nav li:nth-child(3)').addClass('third-child');
	$('#espresso-nav li:nth-child(4)').addClass('fourth-child');

	$('#espresso-nav .first-child').addClass('active');
	
	var hash = window.location.hash.substr(1);
	var href = $('#espresso-nav li a').each(function(){
		var href = $(this).attr('href');
		if(hash==href.substr(0,href.length-0)){
			var toLoad = hash+'.html #the-post';
			$('#the-post').load(toLoad)
		}											
	});

	var toLoadtwo = $('#espresso-nav li.first-child a').attr('href')+' #the-post';
		
	$('#the-post').fadeOut('slow').hide('slow',loadContenttwo);
	
	$('#load').fadeIn('slow');
		
	function loadContenttwo() {
		$('#main').append('<span id="load"><span class="dn">LOADING...</span></span>');
		$('#the-post').load(toLoadtwo, showNewContenttwo);
	}
	
	function showNewContenttwo() {
		$('#load').fadeOut('slow').remove();
		$('#the-post').fadeIn('slow').show('1000');
	}
	
	$('#espresso-nav li a').click(function(){
		
		$('#espresso-nav li').removeClass('active');
		$(this).parent('li').addClass('active');
		
		var toLoad = $(this).attr('href')+' #the-post';
		
		$('#the-post').fadeOut('slow').hide('slow',loadContent);
		
		$('#load').fadeIn('slow');
		
		//window.location.hash = $(this).attr('href').substr(0,$(this).attr('href').length-0);
		
		function loadContent() {
			$('.entry').append('<span id="load"><span class="dn">LOADING...</span></span>');
			$('#the-post').load(toLoad, showNewContent);
		}
		
		function showNewContent() {
			$('#load').fadeOut('slow').remove();
			$('#the-post').fadeIn('slow').show('1000');
		}
		
		function hideLoader() {
			$('#load').fadeOut('normal');
		}
		
		return false;
		
	});

});

$(function() {
		
	$('.post-loader').hover(onHover, hoverOut);
	
		function onHover(){
								
			$('li.loaded-post').fadeOut(loadContentone);
						
				function loadContentone() {
					$('ul#main-nav ul.sub-menu').append('<li id="nav-load"><span class="dn">LOADING...</span></li>');
					$(this).load('/latest-blog-post', showNewContentone);
				}
				
					function showNewContentone() {
						$(this).fadeIn();
						$('li#nav-load').fadeOut().remove();
					}
					
		}

		function hoverOut(){
			$('li.loaded-post').hide();	
		}
		
	$('li.loaded-post').hover( function() { $(this).show(); });

});

$(function() {
		
	// $('.resources-loader').hover(onHover, hoverOut);
	
		// function onHover(){
								
			// $('li.loaded-resources').fadeOut(loadContentone);
						
				// function loadContentone() {
					// $('ul#main-nav ul.sub-menu').append('<li id="nav-load"><span class="dn">LOADING...</span></li>');
					// $(this).load('/resources-list', showNewContentone);
				// }
				
					// function showNewContentone() {
						// $(this).fadeIn();
						// $('li#nav-load').fadeOut().remove();
					// }
					
		// }

		// function hoverOut(){
			// $('li.loaded-resources').hide();	
		// }
		
	// $('li.loaded-resources').hover( function() { $(this).show(); });
	

});