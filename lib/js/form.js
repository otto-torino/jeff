var dojo_textareas;

function confirmSubmit(msg) {
	var message = msg!='' ? msg:"Are you sure you wish to continue?";
	var agree = confirm(message.replace("'", "\'"));
	return agree? true:false;
}

function validateForm(formObj) {

	$$('.formErrMsg]').dispose();
	$$('label[class*=empty]').removeClass('empty');

	var formid = formObj.getProperty('name');

	var fsubmit = true;
	var labels = $$('#'+formid+' label:not([class^=diji])');
	for(var i=0; i<labels.length; i++) {

		var err_detected = false;
		var label = labels[i];
		var felement_name = label.getProperty('for');
		    
		var match_sb = /(.*?)\[\]/.exec(felement_name);
		var felements = (match_sb && match_sb.length>0)
		    	? $$('#'+formid+' [name^='+match_sb[1]+'[]')
	    		: $$('#'+formid+' [name='+felement_name+']');
    		var felement = felements[felements.length-1];

		if(typeof dojo_textareas != 'undefined') {
		    if(typeof dojo_textareas[formid+'_'+felement_name] != 'undefined') {
			felement.value = dojo_textareas[formid+'_'+felement_name].get('value');
			if(/^<br>\n$/.test(felement.value)) felement.value = '';
		    }
		}

		if(label.hasClass('required') && felement.getParent('div').style.display != 'none') {
			if(felement.type=='text' || felement.type=='password' || felement.match('textarea') || felement.match('select') || felement.type=='hidden') {
				if(felement.value=='') err_detected = true;
			}
			else if(felement.type=='radio' || felement.type=='checkbox') {
				var checked = false;
				for(var ii=0;ii<felements.length;ii++) 
					if(felements[ii].checked) {checked = true;break;}
			       	if(!checked) err_detected = true;
			}
		}

		if(err_detected) {
			divMsg = new Element('span', {'class':'formErrMsg'});
			divMsg.set('html', ' &#160; campo obbligatorio');
			divMsg.inject(label, 'bottom');
			label.addClass('empty');
			fsubmit = false;
		}

		if(felement.type=='text') {
			var pattern = felement.getProperty('pattern') ?  felement.getProperty('pattern'):null;
			if(felement.getParent('div').style.display != 'none' && felement.value != '') {
				if((pattern && !new RegExp(pattern).test(felement.value))) {
					divMsg = new Element('span', {'class':'formErrMsg'});
					divMsg.set('html', ' &#160; '+felement.getProperty('data-hint'));
					divMsg.inject(felement, 'after');
					label.addClass('empty');
					fsubmit = false;
				}
			}
		}

	}

	return fsubmit;
}

function chargeEditor(selector, stylesheets) {
	dojo_textareas = [];
	dojo.ready(function(){
	      	var textareas = dojo.query(selector);
  		if(textareas && textareas.length){
    			dojo.addClass(dojo.body(), "claro");
			for(var i=0; i<textareas.length; i++) {
				var textarea = textareas[i];
				var key = $(textarea).getParents('form')[0].get('name')+'_'+$(textarea).get('id');
				dojo_textareas[key] = new dijit.Editor({
      				styleSheets: stylesheets,
      				plugins: [
        				"collapsibletoolbar",
        				"fullscreen", "viewsource", "|",
        				"undo", "redo", "|",
        				"cut", "copy", "paste", "|",
        				"bold", "italic", "underline", "strikethrough", "|",
        				"insertOrderedList", "insertUnorderedList", "indent", "outdent", "||",
        				"formatBlock", "fontName", "fontSize", "||",
        				"findreplace", "insertEntity", "blockquote", "|",
        				"createLink", "insertImage", "insertanchor", "|",
       				 	"foreColor", "hiliteColor", "|",
       	 				"showblocknodes", "pastefromword",
        				// headless plugins
        				"normalizeindentoutdent", "prettyprint",
        				"autourllink", "dijit._editor.plugins.EnterKeyHandling"
      				]
    				}, textareas[i]);
			}
  		}
	});
}


