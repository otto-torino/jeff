window.addEvent('domready', function() {

	updateTooltips();

});

function updateTooltips() {

	$$('[class$=tooltip]').each(function(el) {
		var title = el.getProperty('title').split("::")[0];
		var text = el.getProperty('title').split("::")[1];

		el.store('tip:title', title);
		el.store('tip:text', text);
	});

	var myTips = new Tips('[class$=tooltip]', {
		className: 'tips',
		hideDelay: 50,
		showDelay: 50
	});

}
