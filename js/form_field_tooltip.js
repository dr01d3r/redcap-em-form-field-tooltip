$(function() {
	var templateSource = document.getElementById('form-field-tooltip-template').innerHTML;
	var template = Handlebars.compile(templateSource);
	
	for (var prop in FormFieldTooltip.settings) {
	    var parentElement;
	    if (FormFieldTooltip.isSurvey) {
            parentElement = $('#label-' + FormFieldTooltip.settings[prop].field_name);
        } else {
            parentElement = $('#label-' + FormFieldTooltip.settings[prop].field_name + ' table tr td')[1];
        }
		if (parentElement) {
			var tooltipHtml = template(FormFieldTooltip.settings[prop]);

            if (FormFieldTooltip.isSurvey) {
                $(tooltipHtml).appendTo(parentElement);
            } else {
                $(tooltipHtml).prependTo(parentElement);
            }
		}
	}

    $('.rc-tooltip').popover({
        container: 'body',
        html: true,
        trigger: 'hover'
    });
});