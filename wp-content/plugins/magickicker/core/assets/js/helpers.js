// JavaScript Document for custom functions
// hash to query
mgk_hash_to_queryvar=function(qVar,hash){
	var hash    = hash || window.location.hash.replace("#","");	
	var qVarVal = ""; 
	if(hash){
		hash_vars=hash.toString().split("/");	
		for(var i=0;i<hash_vars.length;i++){
			if(hash_vars[i].toString().is_empty()==false){
				if(hash_vars[i]==qVar){
					qVarVal=hash_vars[i+1];
				}
				i++;
			}
		}
	}
	return qVarVal;
}
// tab index
mgk_tab_index=function(type){
	var _index = 0;	
	var qtabs  = jQuery.query.get('tabs');	
	var tabs   = qtabs.split(',');	
	switch(type){	
		case 'main':			
			jQuery('#mk-panel-content ul li a[href]').each(function(i){
				if(jQuery(this).attr('href').toString().replace('#','')==tabs[0]){
					_index=i;
					return false;
				}
			});	
		break;
		case 'sub':			
			if(tabs[1]){					
				jQuery('#'+tabs[0]+' .content-div ul li a[href]').each(function(i){		
					if(jQuery(this).attr('href').toString().replace('#','')==tabs[1]){
						_index=i;
						return false;
					}
				});				
			}
		break;	
	}
	
	return _index;
}
// attach tips		
mgk_attach_tips=function(){	
	jQuery(".box-description, .box-video").click(function(){	
		var contentclass= jQuery(this).attr('class')+'-content';	
				
		var descheading = jQuery(this).parent().prev("h3").html();	            
		
		var desctext = jQuery(this).parent().parent().children("."+contentclass).html();					
		
		switch(jQuery(this).attr('class')){
			case "box-description":
				var id='mk-custom-lbox';
				jQuery('body').append("<div id='mk-custom-lbox'><div class='shadow'></div><div class='box-desc'><div class='box-desc-top'></div><div class='box-desc-content'><h3>"+descheading+"</h3>"+desctext+"<div class='lightboxclose'></div> </div> <div class='box-desc-bottom'></div></div></div>");
			break;
			case "box-video":
				var id='mk-custom-lbox2';
				jQuery('body').append("<div id='mk-custom-lbox2'><div class='shadow'></div><div class='box-desc'><div class='box-desc-top'></div><div class='box-desc-content'><h3>"+descheading+"</h3>"+desctext+"<div class='lightboxclose'></div> </div> <div class='box-desc-bottom'></div></div></div>");
			break;
		}

		jQuery(".shadow").animate({ opacity: "show" }, "fast").fadeTo("fast", 0.75);

		jQuery('.lightboxclose').click(function(){

			jQuery(".shadow").animate({ opacity: "hide" }, "fast", function(){jQuery("#"+id).remove();});	

		});

	});
}
// action
mgk_send_action=function(options){
	// callback	
	if(typeof(options.onclick)=="function"){
		options.onclick(options);
		return;
	}	
}
// loader
mgk_ajax_loader=function(){			
	// var loader = jQuery('body').prepend('<div id="ajax-loading" class="running"> Loading...</div>').css({position: "absolute", top: "0px", left: "0px", width:"250px"}).hide();		
    // var loader = jQuery(body).append("#wait").css({position: "absolute", top: "0px", left: "0px", width:"250px"}).hide();
    jQuery().ajaxStart(function() {
		loader.show();		
		jQuery(body).css({opacity:'.70',cursor:"wait"});
	}).ajaxStop(function() {
		loader.hide();
		jQuery(body).css({opacity:'',cursor:"default"});
	}).ajaxError(function(a, b, e) {
		throw e;
	}); 	
}
// convert pager links to ajax call
mgk_set_pager=function(id, index, section){		
	var index    = index || 0;
	var section  = section || 'admin';
	jQuery(id+" .pager-wrap a").each(function(){
		// get url
		var url = jQuery(this).attr('href');			
		// disable href
		jQuery(this).attr('href','javascript:void(0)');
		// bind click
		jQuery(this).bind('click',function(){	
			switch(section){
				case 'admin':
					// set new url							   	
					jQuery(id+' .content-div').tabs( 'url', index, url );	   
					// reload
					jQuery(id+' .content-div').tabs( 'load', index);	
				break;				
			}
			// exit
			return;
		});					
	});
}	

// message
mgk_show_message=function(selector, data, is_focus){
	// def
	is_focus = is_focus || false;
	// remove message										   														
	jQuery(selector+' #message').remove();
	// create message
	jQuery(selector).prepend('<div id="message"></div>');
	
	// show message
	if(data)
		jQuery(selector+' #message').addClass(data.status).html(data.message);		
	
	// scroll 
	if(is_focus)
		jQuery.scrollTo(selector, 400);
}