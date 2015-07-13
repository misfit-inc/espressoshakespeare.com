$(document).ready(function(){

	//Hide (Collapse) the toggle containers on load
	$(".toggle_container").hide(); 

	//Switch the "Open" and "Close" state per click then slide up/down (depending on open/close state)
	$('h2.trigger').click(function(){
		$(this).toggleClass("active").next().slideToggle("fast");
		return false; //Prevent the browser jump to the link anchor
	});
	
	$('#main-nav .sub-menu .post:first-child').addClass('first-child');
	
	$('.iosSlider').iosSlider({
		snapToChildren: true,
		desktopClickDrag: true,
		infiniteSlider: true,
		snapSlideCenter: true,
		onSlideChange: slideChange,
		autoSlideTransTimer: 2000,
		keyboardControls: true,
		onSlideComplete: slideComplete,
		navNextSelector: $('.iosslider-next'),
		navPrevSelector: $('.iosslider-prev'),
	});
	
	var $great = $('.slider .item').length;
	for($mult = 0; $mult < $great; $mult++) {
		$('.slideSelectors').append("<div class='item'></div>");
	}
	
	$('.slideSelectors .item').eq(0).addClass('selected');
	
	function slideComplete(args) {
	
		$('.iosslider-next, .iosslider-prev').removeClass('unselectable');
		
		if(args.currentSlideNumber == 1) {
			$('.iosslider-prev').addClass('unselectable');
		} else if(args.currentSliderOffset == args.data.sliderMax) {
			$('.iosslider-next').addClass('unselectable');
		}
	}
	
	function slideChange(args) {
		try {
			console.log('changed: ' + (args.currentSlideNumber - 1));
		} catch(err) {}
		
		$('.indicators .item').removeClass('selected');
		$('.indicators .item:eq(' + (args.currentSlideNumber - 1) + ')').addClass('selected');
		
		$('.slideSelectors .item').removeClass('selected');
		$('.slideSelectors .item:eq(' + (args.currentSlideNumber - 1) + ')').addClass('selected');
		
		$('.iosSlider .item').removeClass('current');
		$(args.currentSlideObject).addClass('current');
	}
	
});

function animUp() {
	$("#arrow").animate({ top: "1px" }, "normal", "swing", animDown);
}
  
function animDown() {
  $("#arrow").animate({ top: "10px" }, "normal", "swing", animUp);
}

$(document).ready(function() {
  animUp();
  
  $(window).scroll(function() {
	$('#arrow-wrapper').fadeOut("fast");
  });

});