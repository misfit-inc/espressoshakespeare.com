(function($){
    function DarkNavSlideDeck( deckFrame ){
		var darknavDeckFrame = $(deckFrame);
		var darknavDeckFrameHeight = darknavDeckFrame.outerHeight();
		var footer;
		var footerHeight = 0;
		var footerTitle;
		var darknavNext;
		var darknavPrev;
		var navigation = '<div class="slidedeckFooter"><div class="navigation"><a class="prevSlide" href="#prev">&larr;</a><a class="nextSlide" href="#next">&rarr;</a></div><div class="slideTitle"></div></div>';
		
		var darknavDeck = darknavDeckFrame.find('.slidedeck').slidedeck({
			start: 3,
			autoPlay: false,
			cycle: false, 
			autoPlayInterval: 2500, // 2.5 seconds
			hideSpines: false
		});
		
		function updateFooterTitle(){
			var currentTitle = $(darknavDeck.spines[darknavDeck.current-1]).html();
			footerTitle.html(currentTitle);
		}
		
		function updateDisabledNavigation(){
			if(darknavDeck.options.cycle == false){
				darknavNext.removeClass('disabled');
				darknavPrev.removeClass('disabled');
				if( darknavDeck.current == 1 ){
					darknavPrev.addClass('disabled');
				}
				if( darknavDeck.current == darknavDeck.slides.length ){
					darknavNext.addClass('disabled');
				}							
			}				
		}
		
		darknavDeck.loaded(function(){
			if( darknavDeckFrame.find('.slidedeckFooter').length ){
				darknavDeckFrame.find('.slidedeckFooter').remove();
			}
			darknavDeckFrame.append(navigation);
			
			footer = darknavDeckFrame.find( '.slidedeckFooter' );
			footerHeight = footer.outerHeight();					
			//darknavDeckFrame.height( darknavDeckFrameHeight + footerHeight );
			
			footerTitle = darknavDeckFrame.find( '.slideTitle' );
			darknavNext = darknavDeckFrame.find('a.nextSlide');
			darknavPrev = darknavDeckFrame.find('a.prevSlide');
			
			darknavNext.click(function(event){
				event.preventDefault();    				
				darknavDeck.next();				
			});
			darknavPrev.click(function(event){
				event.preventDefault();    				
				darknavDeck.prev();				
			});			
						
			updateFooterTitle();
			updateDisabledNavigation();
		});	
		
		darknavDeck.options.complete = function(deck){
			updateFooterTitle();
			updateDisabledNavigation();
		}
	}
	
	
	$(document).ready(function(){
		for(var i=0, decks=$('.slidedeck_frame.skin-darknav'); i<decks.length; i++){
			var thisDeck = decks[i];
			
			if(typeof(thisDeck.SlideDeck_skinDarkNav) == 'undefined'){
				thisDeck.SlideDeck_skinDarkNav = DarkNavSlideDeck( thisDeck );
			}
		}
	});    
})(jQuery);
