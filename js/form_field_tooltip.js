jQuery(function() {
	var templateSource = document.getElementById('form-field-tooltip-template').innerHTML;
	var template = Handlebars.compile(templateSource, { noEscape: true });
	
	for (const prop in FormFieldTooltip.settings) {
		var parentElement = jQuery('#label-' + FormFieldTooltip.settings[prop].field_name + ' table tr td')[1];
		if (parentElement) {
			var tooltipHtml = template(FormFieldTooltip.settings[prop]);
			$(tooltipHtml).prependTo(parentElement);
		}
	}
	
	$('body').append("<div id='" + FormFieldTooltip.id + "-tooltip' class='tooltip1' style='max-width: 400px;padding:7px;'><span class='tooltip-info'></span></div>");
	
	$('.rc-tooltip').mouseover(function(){
		$(this).css('cursor','pointer');
		var data = $(this).find('.rc-tooltip-content').html();        
		$('#' + FormFieldTooltip.id + '-tooltip .tooltip-info').html(data);
	});
	$('.rc-tooltip').tooltip2({
		tip: '#' + FormFieldTooltip.id + '-tooltip',
		position: 'bottom center',
		offset: [0, 0],
		delay: 0
	});
});