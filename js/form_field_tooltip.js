$(function() {
    // modify whitelist so these elements dont get sanitized
    $.fn.popover.Constructor.Default.whiteList.table = [];
    $.fn.popover.Constructor.Default.whiteList.thead = [];
    $.fn.popover.Constructor.Default.whiteList.tbody = [];
    $.fn.popover.Constructor.Default.whiteList.th = [];
    $.fn.popover.Constructor.Default.whiteList.tr = [];
    $.fn.popover.Constructor.Default.whiteList.td = [];
    // also include the style attribute to support rich text editor
    $.fn.popover.Constructor.Default.whiteList["*"].push("style");

    // identify and compile the prepared template
    var templateSource = document.getElementById('form-field-tooltip-template').innerHTML;
	var template = Handlebars.compile(templateSource);

	// append tooltip to elements based on form/survey
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