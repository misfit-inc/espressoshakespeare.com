var cSelectBox = new Class({
	Implements: [Options, Events],
	options: {
		dropdownClass: 'cDropdown',
		selectClass: 'cSelect',
		optionsClass: 'cOptions',
		activeClass: 'cActive',
        zIndex: 1
	},
	initialize: function(select, options){
		this.setOptions(options);
		this.select = select;
		try{
			this.setElements();
			this.setEvents(this.container);
			if(this.trigger && this.list && this.items.length && this.select){
				this.setPositions();
				this.setToggling();
				this.setSelecting();
			}
		}catch(e){}
	},
	setElements: function(){
		var form = this.select.getParent('form');
		var div = new Element ('div',{
			'class': this.options.dropdownClass + ' ' + this.select.name
		});
		var a = new Element('a',{
			'href': '#',
			'class': this.options.selectClass
		});
		var ul = new Element('ul',{
			'class': this.options.optionsClass
		});

		this.select.getChildren('option').each(function(el){
			var li = new Element('li',{});
			var lia = new Element('a',{
				'href': '#',
				'rel': el.value,
				'text': el.innerHTML
			});
			if(el.selected) lia.addClass(this.options.activeClass);
			li.grab(lia);
			ul.grab(li);
		}.bind(this));
		
		div.grab(a);
		div.grab(ul);
		div.inject(this.select, 'after');
		
		this.container = div;
		this.trigger = a;
		this.list = ul;
		this.items = this.list.getElements('a');
		
		var selected = this.list.getElements('a.'+this.options.activeClass);
		if(selected.length > 0) selected = selected[0];
		if(selected.className == this.options.activeClass){
			this.trigger.innerHTML = selected.innerHTML;
			this.select.value = selected.rel;
			this.active = selected;
		}else{
			this.trigger.innerHTML = this.items[0].innerHTML;
			this.select.value = this.items[0].rel;
			this.active = null;
		}
	},
	setEvents: function(div){
		var self = this;
		
		var selectOnChange = self.select.getProperty('onchange');
		if(selectOnChange != ''){
			try{
				self.select.addEvent('change',function(){
					eval(selectOnChange);
				});
			}
			catch(e){}
		}

		document.addEvent('keydown',function(e){
			//if(self.opened(self.list)) e.preventDefault();
			div.store('keys',false);
			if(e.key == 'up'){
                e.preventDefault();
				div.store('keys',true);
				var el = self.active;
				if(el) var previous = el.getParent('li').getPrevious('li');
				if(previous && self.opened(self.list)){
					el.removeClass(self.options.activeClass);
					previous.getFirst('a').addClass(self.options.activeClass);
					self.active = previous.getFirst('a');
					var top = previous.getFirst('a').getCoordinates(self.list).top;
					if(top <= self.list.scrollTop) self.list.scrollTo(0, top);
				}
				var t = function(){self.container.store('keys',false);}.delay(100);
			}
			if(e.key == 'down' && self.opened(self.list)){
                e.preventDefault();
				div.store('keys',true);
				var el = self.active;
				if(el) var next = el.getParent('li').getNext('li');
				if(next){
					el.removeClass(self.options.activeClass);
					next.getFirst('a').addClass(self.options.activeClass);
					self.active = next.getFirst('a');
					var top = next.getFirst('a').getCoordinates(self.list).top;
					if(top >= self.list.getSize().y + self.list.scrollTop) self.list.scrollTo(0, top - self.list.getSize().y + next.getSize().y);
				}
				var t = function(){self.container.store('keys',false);}.delay(100);
			}
			if(e.key == 'enter' && self.opened(self.list)){
                e.preventDefault();
				var el = self.active;
				if(el){
					self.select.value = el.rel;
					self.trigger.innerHTML = el.innerHTML;
				}
				self.close(self.list);
			}
		});
		div.addEvent('keypress',function(e){			
			if(e.key == 'enter'){
                e.preventDefault();
				var el = self.active;
				self.select.value = el.rel;
				self.trigger.innerHTML = el.innerHTML;
				self.close(self.list);
			}
		});
	},
	setToggling: function(){
		var self = this;
		self.list.setStyle('display','none');
		self.trigger.addEvent('click',function(e){
			e.stop();
			document.removeEvents('click');
			$$('.'+self.options.optionsClass).each(function(el){
				if(el != self.list) self.close(el);
			});
			self.toggle(self.list);
			document.addEvent('click',function(){
				$$('.'+self.options.optionsClass).setStyle('display','none');
			});
		});
		self.select.setStyle('display','none');
	},
	setPositions: function(){
		this.container.setStyle('position','relative');
		this.list.setStyle('position','absolute');
		this.container.setStyle('z-index', this.options.zIndex);
		this.list.setStyle('z-index', 9999);
	},
	close: function(el){
        el.setStyle('display','none');
	},
	open: function(el){
		el.setStyle('display','block');
	},
	opened: function(el){
		return el.getStyle('display') == 'block';
	},
	toggle: function(el){
		var self = this;
		var display = (el.getStyle('display') == 'block') ? 'none' : 'block';
		el.setStyle('display',display);
		if(display == 'block'){
			if(!self.active){
				self.items[0].addClass(self.options.activeClass);
				self.active = self.items[0];
			}
			self.list.scrollTo(0, self.active.getCoordinates(self.list).top);
		}
	},
	setSelecting: function(){
		var self = this;
		this.items.each(function(el){
			el.addEvent('click',function(e){
				e.stop();
				self.select.value = el.rel;
				self.select.fireEvent('change');
				self.trigger.innerHTML = el.innerHTML;
				self.close(self.list);
			}).addEvent('mouseenter',function(e){
				e.stop();
				if(!self.container.retrieve('keys')){
					self.items.removeClass(self.options.activeClass);
					el.addClass(self.options.activeClass);
					self.active = el;
				}
			});
		});
	}
});