/*
 * layerWindow class
 *
 * layerWindow method: constructor
 *   Syntax
 *      var myLayerWindowInstance = new layerWindow([options]);
 *   Arguments 
 *	1. options - (object, optional) The options object.
 *   Options
 *	- id (string: default to null) The id attribute of the window container
 *	- bodyId (string: default to null) The id attribute of the body container
 *   	- title (string: default to null) The window title
 * 	- width (int: default to 400) The width in px of the window body
 * 	- height (int: default to null) The height in px of the body. By default its value depends on contained text
 *  	- minWidth (int: default to 300) The minimum width when resizing
 *	- minHeight (int: default to 100) The minimum height when resizing
 * 	- maxHeight (int: default to viewport-height minus 100px) The max-height css property of the window body
 *	- draggable (bool: default to true) Whether or not to make the window draggable
 *	- resize (bool: default to true) Whether or not to make the window resizable
 *	- closeButtonUrl (string: default to null) The url of the image to use as close button
 *	- closeButtonLabel (string: default to close) The string to use as close button if the closeButtonUrl is null
 *	- destroyOnClose (bool: default to true) Whether or not to destroy all object properties when closing the window
 *	- overlay (bool: default to true) Whether or not to set a base overlay with opacity isolating the window from the below elements
 *  	- url (string: default to null) The url to be called by ajax request to get initial window body content
 *	- htmlNode (mixed: default to null) The html node which content is injected into the window body. May be a node element or its id.
 *	- html (string: default to null) The initial html content of the window body if url is null
 *	- closeCallback (function: default to null) The function to be called when the window is closed
 *	- closeCallbackParam (mixed: default to null) The paramether to pass to the callback function when the window is closed
 *	- disableObjects (bool: default to false) Whether or not to hide objects when window is showed (and show them when window is closed)
 *	- reloadZindex (bool: default to false) Whether or not to re-calculate the max z-index present in the document when instantiating the class
 *
 * layerWindow method: setTitle
 *  sets the title of the window and updates it if the window is showed
 *   Syntax
 *	myLayerWindowInstance.setTitle(title);
 *   Arguments
 *	1. title - (string) The title of the window
 *
 * layerWindow method: setHtml
 *  sets the content of the window and updates it if the window is showed
 *   Syntax
 *	myLayerWindowInstance.setHtml(html);
 *   Arguments
 *	1. html - (string) The html content of the window body
 *
 * layerWindow method: setUrl
 *  sets the content of the window and updates it if the window is showed
 *   Syntax
 *	myLayerWindowInstance.setUrl(url);
 *   Arguments
 *	1. url - (string) The url called by ajax request to get window body content
 *
 * layerWindow method: display
 *  displays the window in the position pointed by the element passed, or by the given coordinates. If no element nor coordinates are given,
 *  the window is centered in the viewport.
 *   Syntax
 *	myLayerWindowInstance.display(el, [opt]);
 *   Arguments
 *	1. el - (element) The element respect to which is rendered the window (top left of the window coincide with top left of the element)
 *      2. opt - (object) The top and left coordinates of the top left edge of the window. If only one is given the other is taken from the el passed
 *
 * layerWindow method: setFocus
 *  set focus on the object window, giving it the greatest z-index in the document
 *   Syntax
 *	myLayerWindowInstance.setFocus();
 *
 * layerWindow method: closeWindow
 *  closes the window and destroyes the object properties if the option destroyOnClose is true
 *   Syntax
 *	myLayerWindowInstance.closeWindow();
 *
 */
var layerWindow = new Class({

	Implements: [Options, Chain],
	options: {
		id: null,
		bodyId: null,
		title: null,
		width: 400,
		height: null,
		minWidth: 300,
		minHeight: 100,
		maxHeight: null,
		draggable: true,
		resize: true,
		closeButtonUrl: null,
		closeButtonLabel: 'close',
		destroyOnClose: true,
		overlay: true,
		url:'',
		html: ' ',
		htmlNode: null,
		closeCallback: null,
		closeCallbackParam: null,
		disableObjects: false,
		reloadZindex: false
	},
    	initialize: function(options) {
	
		this.showing = false;	

		if(options != null) this.setOptions(options);
		this.checkOptions();

		this.title = this.options.title;
		this.html = this.options.html;
		this.htmlNode = this.options.htmlNode ? ( typeof this.options.htmlNode == 'element' ? this.options.htmlNode : $(this.options.htmlNode) ) : null;
		this.url = this.options.url;
		if(this.options.maxHeight != null) this.options.maxHeight = getViewport().height-100;

		if(this.options.reloadZindex) window.maxZindex = getMaxZindex();

	},
	checkOptions: function() {
		var rexp = /[0-9]+/;
		if(!rexp.test(this.options.width) || this.options.width<this.options.minWidth) this.options.width = 400;
	},
	setTitle: function(title) {
		this.title = title;	 
		if(this.showing) this.header.set('html', title);
	},
	setHtml: function(html) {
		this.html = html;	 
		if(this.showing) this.body.set('html', html);
	},
	setUrl: function(url) {
		this.url = url;	 
		if(this.showing) this.request();
	},
	display: function(element, opt) {
		this.delement = !element ? null : typeof element == 'element' ? element : $(element);
		this.dopt = opt;
		if(this.options.disableObjects) this.dObjects();
		this.showing = true;
		
		if(this.options.overlay) this.renderOverlay();
		this.renderContainer();
		this.renderHeader();
		this.renderBody();
		this.renderFooter();
		this.container.setStyle('width', (this.body.getCoordinates().width)+'px');
		this.initBodyHeight = this.body.getStyle('height').toInt();
		this.initContainerDim = this.container.getCoordinates();

		if(this.options.draggable) this.makeDraggable();
		if(this.options.resize) this.makeResizable();

	},
	renderOverlay: function() {
		var docDim = document.getScrollSize();
		this.overlay = new Element('div', {'class': 'abiWinOverlay'});
		this.overlay.setStyles({
			'top': '0px',
			'left': '0px',
			'width': docDim.x,
			'height': docDim.y,
			'z-index': ++window.maxZindex
		});

		this.overlay.inject(document.body);
		
	},	
	dObjects: function() {
		for(var i=0;i<window.frames.length;i++) {
			var myFrame = window.frames[i];
			if(sameDomain(myFrame)) {
				var obs = myFrame.document.getElementsByTagName('object');
				for(var ii=0; ii<obs.length; ii++) {
					obs[ii].style.visibility='hidden';
				}
			}
		}
		$$('object').each(function(item) {
			item.style.visibility='hidden';
		})
	},
	eObjects: function() {
		for(var i=0;i<window.frames.length;i++) {
			var myFrame = window.frames[i];
			if(sameDomain(myFrame)) {
				var obs = myFrame.document.getElementsByTagName('object');
				for(var ii=0; ii<obs.length; ii++) {
					obs[ii].style.visibility='visible';
				}
			}
		}
		$$('object').each(function(item) {
			item.style.visibility='visible';
		})
	},
	renderContainer: function() {
		this.container = new Element('div', {'id':this.options.id, 'class':'abiWin'});

		this.container.setStyles({
			'visibility': 'hidden'
		})
		this.setFocus();
		this.container.addEvent('mousedown', this.setFocus.bind(this));
		this.container.inject(document.body, 'top');
	},
	locateContainer: function() {

		if(typeof this.loading != 'undefined') this.loading.dispose();	

		var elementCoord = this.delement ? this.delement.getCoordinates() : null;
		this.top = (this.dopt && typeof this.dopt.top != undefined) ? this.dopt.top : elementCoord 
			? elementCoord.top 
			: (getViewport().cY-this.container.getCoordinates().height/2);
		this.left = (this.dopt && typeof this.dopt.left != undefined) ? this.dopt.left : elementCoord 
			? elementCoord.left 
			: (getViewport().cX-this.container.getCoordinates().width/2);

		if(this.top<0) this.top = 0;
		if(this.left<0) this.left = 0;

		this.container.setStyles({
			'top': this.top+'px',
			'left':this.left+'px',
			'visibility': 'visible'
		})
	},
	renderHeader: function() {
		this.header = new Element('header', {'class':'abiHeader'});
		this.header.set('html', this.title);

		var closeEl;
		if(this.options.closeButtonUrl != null && typeof this.options.closeButtonUrl == 'string') {
			closeEl = new Element('img', {'src':this.options.closeButtonUrl, 'class':'close'});
		}
		else {
			closeEl = new Element('span', {'class':'close'});
			closeEl.set('html', this.options.closeButtonLabel);
		}

		closeEl.addEvent('click', this.closeWindow.bind(this));
		this.header.inject(this.container, 'top');
		closeEl.inject(this.header, 'before');
    				
	},
	renderBody: function() {
		this.body = new Element('div', {'id':this.options.bodyId, 'class':'body'});
		this.body.setStyles({
			'width': this.options.width,
			'height': this.options.height,
			'max-height': this.options.maxHeight
		})
		this.body.inject(this.container, 'bottom');
		this.url ? this.request() : this.htmlNode ? this.body.set('html', this.htmlNode.clone(true, true).get('html')) : this.body.set('html', this.html);
		if(!this.url || this.options.height) this.locateContainer();
	},
	renderFooter: function() {
		this.footer = new Element('footer');
		this.footer.inject(this.container, 'bottom');
    				
	},
	renderResizeCtrl: function() {
		this.resCtrl = new Element('div').setStyles({'position':'absolute', 'right':'0', 'bottom':'0', 'width':'10px', 'height':'10px', 'cursor':'se-resize'});
		this.resCtrl.inject(this.footer, 'top');		
	},
	makeDraggable: function() {
		var docDim = document.getCoordinates();
		if(this.options.draggable) {
			var dragInstance = new Drag(this.container, {
				'handle':this.header, 
				'limit':{'x':[0, (docDim.width-this.container.getCoordinates().width)], 'y':[0, ]}
			});
			this.header.setStyle('cursor', 'move');
		}
    
	},
	makeResizable: function() {
		this.renderResizeCtrl();
		var ylimit = this.options.maxHeight != null 
			? this.options.maxHeight+this.header.getCoordinates().height+this.header.getStyle('margin-top').toInt()+this.header.getStyle('margin-bottom').toInt()+this.container.getStyle('padding-top').toInt()+this.container.getStyle('padding-bottom').toInt() 
			: document.body.getCoordinates().height-20;
		this.container.makeResizable({
			'handle':this.resCtrl, 
			'limit':{'x':[this.options.minWidth, (document.body.getCoordinates().width-20)], 'y':[this.options.minHeight, ylimit]},
			'onDrag': function(container) {this.resizeBody()}.bind(this),
			'onComplete': function(container) {this.makeDraggable()}.bind(this)
		});
	},
	resizeBody: function() {
		this.body.setStyles({
			'width': this.options.width.toInt()+(this.container.getCoordinates().width-this.initContainerDim.width),
			'height': this.initBodyHeight+(this.container.getCoordinates().height-this.initContainerDim.height)		
		});	      
	},
	request: function() {

		this.loading = new Element('div', {'class': 'loading'});
		this.loading.setStyle('visibility', 'hidden'); // ie can't get element dimensions if not in dom
		this.loading.inject(document.body, 'top');
		this.loading.setStyles({
			'position':'absolute',
			'top': (getViewport().cY-this.loading.getCoordinates().height/2)+'px',		
			'left': (getViewport().cX-this.loading.getCoordinates().width/2)+'px',		
			'z-index': window.maxZindex + 1
		});
		this.loading.setStyle('visibility', 'visible');

		ajaxRequest('post', this.url, '', this.body, {'script':true, 'load':this.body, 'callback':this.locateContainer.bind(this)});	 
	},
	setFocus: function() {
		if(!this.container.style.zIndex || (this.container.getStyle('z-index').toInt() < window.maxZindex))
			this.container.setStyle('z-index', ++window.maxZindex);
	},
	closeWindow: function() {
		this.showing = false;
		if(this.options.disableObjects) this.chain(this.container.dispose(), this.eObjects());
		else this.container.dispose();
		if(this.options.overlay) this.overlay.dispose();
    		if(this.options.closeCallback != null) this.options.closeCallback(this.options.closeCallbackParam);		
		if(this.options.destroyOnClose) for(var prop in this) this[prop] = null;
	}
})

function getViewport() {

	var width, height, left, top, cX, cY;

 	// the more standards compliant browsers (mozilla/netscape/opera/IE7) use window.innerWidth and window.innerHeight
 	if (typeof window.innerWidth != 'undefined') {
   		width = window.innerWidth,
   		height = window.innerHeight
 	}

	// IE6 in standards compliant mode (i.e. with a valid doctype as the first line in the document)
 	else if (typeof document.documentElement != 'undefined' && typeof document.documentElement.clientWidth !='undefined' && document.documentElement.clientWidth != 0) {
    		width = document.documentElement.clientWidth,
    		height = document.documentElement.clientHeight
 	}

	top = typeof self.pageYOffset != 'undefined' 
		? self.pageYOffset 
		: (document.documentElement && typeof document.documentElement.scrollTop != 'undefined')
			? document.documentElement.scrollTop
			: document.body.clientHeight;

	left = typeof self.pageXOffset != 'undefined' 
		? self.pageXOffset 
		: (document.documentElement && typeof document.documentElement.scrollTop != 'undefined')
			? document.documentElement.scrollLeft
			: document.body.clientWidth;

	cX = left + width/2;

	cY = top + height/2;

	return {'width':width, 'height':height, 'left':left, 'top':top, 'cX':cX, 'cY':cY};

}

window.maxZindex = getMaxZindex();

function getMaxZindex() {
	
	var maxZ = 0;
	$$('body *').each(function(el) {if(el.getStyle('z-index').toInt()) maxZ = Math.max(maxZ, el.getStyle('z-index').toInt())});

	return maxZ;

}

function toggleAllChecks(container, button, opt) {
  var check_all_toggle_class = opt && typeOf(opt.check_all_toggle_class) != 'null' ? opt.check_all_toggle_class : 'check_all_toggle';
  var uncheck_all_toggle_class = opt && typeOf(opt.uncheck_all_toggle_class) != 'null' ? opt.uncheck_all_toggle_class : 'uncheck_all_toggle';
  if(!container.retrieve('checked')) {
    container.getElements('input[type=checkbox]').each(function(c) {
      c.setProperty('checked', 'checked');
    });
    button.removeClass(uncheck_all_toggle_class).addClass(check_all_toggle_class);
    container.store('checked', true);
  }
  else {
    container.getElements('input[type=checkbox]').each(function(c) {
      c.removeProperty('checked');
    });
    button.removeClass(check_all_toggle_class).addClass(uncheck_all_toggle_class);
    container.store('checked', false);
  }
}

/*
   ---

script: String.Slugify.js

description: Extends the String native object to have a slugify method, useful for url slugs.

license: MIT-style license

authors:
- Stian Didriksen
- Grzegorz Leoniec

...
 */
(function() {
  String.implement({
    slugify: function( replace ) {
      if( !replace ) replace = '-';
      var str = this.toString().tidy().standardize().replace(/[\s\.]+/g,replace).toLowerCase().replace(new RegExp('[^a-z0-9'+replace+']','g'),replace).replace(new RegExp(replace+'+','g'),replace);
      if( str.charAt(str.length-1) == replace ) str = str.substring(0,str.length-1);
      return str;
    }
  });
})();
