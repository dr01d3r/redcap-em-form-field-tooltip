$(function() {

    // modify whitelist so these elements dont get sanitized
    if(FormFieldTooltip.bootstrap_version === 4){
        $.fn.popover.Constructor.Default.whiteList.table = [];
        $.fn.popover.Constructor.Default.whiteList.thead = [];
        $.fn.popover.Constructor.Default.whiteList.tbody = [];
        $.fn.popover.Constructor.Default.whiteList.th = [];
        $.fn.popover.Constructor.Default.whiteList.tr = [];
        $.fn.popover.Constructor.Default.whiteList.td = [];
        // also include the style attribute to support rich text editor
        $.fn.popover.Constructor.Default.whiteList["*"].push("style");
    }
    else{
        $.fn.popover.Constructor.Default.allowList.table = [];
        $.fn.popover.Constructor.Default.allowList.thead = [];
        $.fn.popover.Constructor.Default.allowList.tbody = [];
        $.fn.popover.Constructor.Default.allowList.th = [];
        $.fn.popover.Constructor.Default.allowList.tr = [];
        $.fn.popover.Constructor.Default.allowList.td = [];
        // also include the style attribute to support rich text editor
        $.fn.popover.Constructor.Default.allowList["*"].push("style");
    }

    // identify and compile the prepared template
    var templateSource = document.getElementById('form-field-tooltip-template').innerHTML;
	var template = Handlebars.compile(templateSource);

	// append tooltip to elements based on form/survey
	for (var prop in FormFieldTooltip.settings) {
	    var parentElement = $("[var=\'"+ FormFieldTooltip.settings[prop].field_name + "\']");
        var isEmbeddedField = parentElement.length > 0;

        if (isEmbeddedField) {
            parentElement = parentElement.closest("td");
        } else if (FormFieldTooltip.isSurvey) {
            parentElement = $('#label-' + FormFieldTooltip.settings[prop].field_name);
        } else {
            parentElement = $('#label-' + FormFieldTooltip.settings[prop].field_name + ' table tr td')[1];
        }
		if (parentElement) {
			var tooltipHtml = template(FormFieldTooltip.settings[prop]);

            if(isEmbeddedField) {
                $(parentElement).addClass("position-relative");
                $(tooltipHtml).prependTo(parentElement).addClass("position-absolute fft-embedded-field");
            } else if(FormFieldTooltip.isSurvey) {
                $(tooltipHtml).appendTo(parentElement);
            } else {
                $(tooltipHtml).prependTo(parentElement);
            }
		}
	}

    $('.fft-rc-tooltip').popover({
        container: 'body',
        html: true,
        trigger: 'hover'
    });
});