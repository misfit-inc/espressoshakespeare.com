// JavaScript Document for custom functions
// hash to query
mgm_hash_to_queryvar=function(qVar,hash){
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
mgm_tab_index=function(type){
	var _index = 0;	
	var qtabs  = jQuery.query.get('tabs');	
	var tabs   = qtabs.split(',');	
	switch(type){	
		case 'main':			
			jQuery('#mgm-panel-content ul li a[href]').each(function(i){
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

// date oicker
mgm_date_picker=function(filter,image,_options){
	// image not 
	var image = image || false;
	var _defaults = {changeYear: true, changeMonth: true, yearRange: '-50:+10', showOn:'focus'};
	// date
	if(image){
		_options = jQuery.extend(_options, {showOn:'button',buttonImage:image+'/images/icons/calendar.png',buttonImageOnly:true});
	}		
	// extend
	_options = jQuery.extend(_defaults,_options);	
	// trigger
	jQuery(filter).each(function(i){jQuery(this).attr('size','11').attr('maxlength','10').datepicker(_options);});
	// wrap scope, not so elegant solution, but works at least
	jQuery("#ui-datepicker-div").wrap("<div class='mgm'></div>");		
}

// attach tips	
mgm_attach_tips=function(){
	jQuery(".box-description, .box-video").click(function(){	
		var contentclass= jQuery(this).attr('class')+'-content';	
				
		var descheading = jQuery(this).parent().prev("h3").html();	            
		
		var desctext = jQuery(this).parent().parent().children("."+contentclass).html();					
		
		switch(jQuery(this).attr('class')){
			case "box-description":
				var id='mgm-custom-lbox';
				jQuery('body').append("<div id='mgm-custom-lbox'><div class='shadow'></div><div class='box-desc'><div class='box-desc-top'></div><div class='box-desc-content'><h3>"+descheading+"</h3>"+desctext+"<div class='lightboxclose'></div> </div> <div class='box-desc-bottom'></div></div></div>");
			break;
			case "box-video":
				var id='mgm-custom-lbox2';
				jQuery('body').append("<div id='mgm-custom-lbox2'><div class='shadow'></div><div class='box-desc'><div class='box-desc-top'></div><div class='box-desc-content'><h3>"+descheading+"</h3>"+desctext+"<div class='lightboxclose'></div> </div> <div class='box-desc-bottom'></div></div></div>");
			break;
		}

		jQuery(".shadow").animate({ opacity: "show" }, "fast").fadeTo("fast", 0.75);

		jQuery('.lightboxclose').click(function(){

			jQuery(".shadow").animate({ opacity: "hide" }, "fast", function(){jQuery("#"+id).remove();});	

		});

	});
}	

// convert pager links to ajax call
mgm_set_pager=function(id, index, section){		
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
				case 'affiliate':
					// set new url							   	
					jQuery('#ma-panel-content').tabs( 'url', index, url );	   
					// reload
					jQuery('#ma-panel-content').tabs( 'load', index);
				break;
			}
			// exit
			return;
		});					
	});
}

// toggle
mgm_toggle= function(id){	
	jQuery('#'+id).toggle();	
}

// module logo upload
mgm_upload_logo=function(obj, messages){	
	// set default	
	messages = messages || {'e_unsafe_files':'Please upload only gif,jpg and png files'};	
	
	// langs	
	// check empty
	if(jQuery(obj).val().toString().is_empty()==false){	
		// check ext	
		if(!(/\.(png|jpe?g|gif)$/i).test(jQuery(obj).val().toString())){
			alert(messages.e_unsafe_files);
			return;
		}				
		// process upload 		
		// vars				
		var module  = jQuery(obj).attr('name').replace('logo_','');	
		var form_id = jQuery(jQuery(obj).get(0).form).attr('id');		
		// before send, remove old message
		jQuery('#'+form_id+' #message').remove();		
		// create new message
		jQuery('#'+form_id).prepend('<div id="message" class="running"><span>Processing...</span></div>');
		// remove old hidden
		jQuery("#"+form_id+" :input[name='logo_new_"+module+"']").remove();						
		// upload 
		jQuery.ajaxFileUpload({
				url:'admin.php?page=mgm/admin/payments&method=module_file_upload&module='+module, 
				secureuri:false,
				fileElementId:jQuery(obj).attr('id'),
				dataType: 'json',						
				success: function (data, status){		
					// show message
					mgm_show_message('#'+form_id, data);					
					// uploaded	
					if(data.status=='success'){		
						// set hidden
						jQuery('#'+form_id).append('<input type="hidden" name="logo_new_'+module+'" value="'+data.logo.image_url+'">');	
						// change logo sample
						jQuery('#'+form_id+' #logo_image_'+module).attr({'src': data.logo.image_url, title: data.logo.image_url, width: '100px', height: '100px'});						
						// box setting will update update button
						if(/^frmmodbox_/.test(form_id)){
							jQuery("#box_logo_elements_"+module).html('<input type="button" class="button" value="Update" onclick="mgm_update_logo(\''+form_id+'\')">');	
						}else{
						// just remove upload file element	
							jQuery("#"+form_id+" :file[name='"+jQuery(obj).attr('name')+"']").remove();
						}
					}											
				}
			}
		)		
		// end
	}				
}		

// uploader
mgm_file_uploader=function(selector, callback){	
	// bind
	jQuery(selector+" :file").bind("change", function(f){												   
		if(callback) callback(this);
	});
}

// message
mgm_show_message=function(selector, data, is_focus){
	// def
	is_focus = is_focus || false;
	// remove message										   														
	jQuery(selector+' #message').remove();
	// create message
	jQuery(selector).prepend('<div id="message"></div>');	
	// show message
	if(data) jQuery(selector+' #message').addClass(data.status).html(data.message);			
	// scroll 
	if(is_focus) jQuery.scrollTo(selector, 400);
}

// form toggle
mgm_paymentform_toggle=function(code){
	jQuery('form').hide();
	jQuery('form#'+code+'_form :input[type=image]').hide(); 
	jQuery('form#'+code+'_form').show();
	jQuery('#'+code+'_form_cc').fadeIn();
	return false;	
}

// mgm_submit_cc_payment
mgm_submit_cc_payment=function(code){
	// validate		
	//check whether validate function exists
	if(jQuery.isFunction( jQuery.fn.validate)) {
		jQuery('form#'+code+'_form').validate({			  	
			errorClass: "invalid",
			validClass: "valid",						
			errorPlacement: function(error, element) {		
				if(element.is(":input[name='mgm_expiry[month]']"))
					error.insertAfter(element.next());
				else												
					error.insertAfter(element);
			}			  
		});
	}		
	//submit form(not really required as 'button' has been changed to 'submit')										   
	jQuery('form#'+code+'_form').submit();										   
}

// cancel
mgm_cancel_cc_payment=function(url){	
	//jQuery('#'+code+'_form_cc').hide();
	//jQuery('form#'+code+'_form').hide();
	//jQuery('form#'+code+'_form :input[type=image]').fadeIn(); 
	//jQuery('form').show();
	window.location.href = url;
	
}

// tab url
mgm_set_tab_url=function(p, s){	
	// set
	var p_idx = p;
	var s_idx = s;			
	// primary onload
	$primary.bind('tabsload', function(event, ui) {								   
		// load if
		if(s_idx!=null) {	
			// secondary onload
			$secondary.bind('tabsload', function(event, ui) {	
				// set null
				if(s_idx!=null) {
					p_idx = s_idx = null;	
				}
			});								   
			// load secondary	
			$secondary.tabs('select', s_idx); 								
		}		
	});
	// select		
	$primary.tabs('select', p_idx);
}

// mgm_toggle_trial
mgm_toggle_trial=function(elem){
	var pack = jQuery(elem).attr('name').toString().replace('packs[','').replace('][trial_on]','');
	if(jQuery(elem).val() == 1){
		jQuery('.pack_trial_'+pack).fadeIn();	
	}else{
		jQuery('.pack_trial_'+pack).fadeOut();
	}	
}

// convert pager links to ajax call
mgm_set_pager=function(id, index, section){		
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
					jQuery(id).load(url, jQuery("#search-table :input").serializeArray()); 
				break;				
			}
			// exit
			return;
		});					
	});
}

// html textbox editor
mgm_wysiwyg_editor=function(scope){
	var scope = scope || '.wysiwyg';
	jQuery(scope).wysiwyg({
		controls: {
			strikeThrough : { visible : true },
			underline     : { visible : true },
	
			justifyLeft   : { visible : true },
			justifyCenter : { visible : true },
			justifyRight  : { visible : true },
			justifyFull   : { visible : true },
	
			indent  : { visible : true },
			outdent : { visible : true },
	
			subscript   : { visible : true },
			superscript : { visible : true },
	
			undo : { visible : true },
			redo : { visible : true },
	
			insertOrderedList    : { visible : true },
			insertUnorderedList  : { visible : true },
			insertHorizontalRule : { visible : true },
	
			h4: {
				  visible: true,
				  className: 'h4',
				  command: jQuery.browser.msie ? 'formatBlock' : 'heading',
				  arguments: [jQuery.browser.msie ? '<h4>' : 'h4'],
				  tags: ['h4'],
				  tooltip: 'Header 4'
			},
			h5: {
				  visible: true,
				  className: 'h5',
				  command: jQuery.browser.msie ? 'formatBlock' : 'heading',
				  arguments: [jQuery.browser.msie ? '<h5>' : 'h5'],
				  tags: ['h5'],
				  tooltip: 'Header 5'
			},
			h6: {
				  visible: true,
				  className: 'h6',
				  command: jQuery.browser.msie ? 'formatBlock' : 'heading',
				  arguments: [jQuery.browser.msie ? '<h6>' : 'h6'],
				  tags: ['h6'],
				  tooltip: 'Header 6'
			},
	
			cut   : { visible : true },
			copy  : { visible : true },
			paste : { visible : true },
			html  : { visible: true }
		}
	}); 
}
// ajax loading mask
mgm_ajax_loader=function(){			
	jQuery(document).ajaxStart(function() {
        // wait cursor							
		jQuery('body').css({opacity:'.70',cursor:'wait'});	
    });
	
    jQuery(document).ajaxStop(function() {
		// default cursor							   
        jQuery('body').css({opacity:'',cursor:'default'});	
		// attach tips
		try{ mgm_attach_tips(); }catch(x){}
    });
}