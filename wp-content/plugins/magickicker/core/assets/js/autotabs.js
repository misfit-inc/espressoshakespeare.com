// JavaScript Document
(function( $ ){
  $.fn.mgkAutoTabs = function(options) { 
  		// defaults
		var settings= {
						index   : 1, 	
						select  : -1,
						label   : 'New Tab', 
						url     : 'ajaxdata.html', 
						tabhash : '#ui-tab-'+ (new Date().getTime() + Math.round(Math.random()*100))
					  };
		// extend
		if ( options ) { 
			$.extend( settings, options );
		}		
		// select
		if(settings.select == '-1')
			settings.select = settings.index;
					
		// remove old at same index		
		this.tabs('remove', settings.index);
		// add new 
		this.tabs('add'   , settings.tabhash, settings.label, settings.index);
		// set url
		this.tabs('url'   , settings.index, settings.url);
		// select
		this.tabs('select', settings.select );	
  };
})( jQuery );