<?php
// Set the namespace defined in your config file
namespace ORCA\FormFieldTooltip;

// The next 2 lines should always be included and be the same in every module
use ExternalModules\AbstractExternalModule;
use ExternalModules\ExternalModules;

/**
 * Class FormFieldTooltip
 * @package ORCA\FormFieldTooltip
 */
class FormFieldTooltip extends AbstractExternalModule {
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Hook function to execute on every data entry form
     * @param $project_id
     * @param $record
     * @param $instrument
     * @param $event_id
     * @param $group_id
     * @param $repeat_instance
     */
    function redcap_data_entry_form($project_id, $record, $instrument, $event_id, $group_id, $repeat_instance) {
        // define a unique id for the tooltip container that will be used in this hook
        $name = uniqid();

        // get all fields on the current instrument
        $instrument_fields = \REDCap::getFieldNames($instrument);

        // get all settings that were specified for field name tooltips
        $field_settings = $this->getSubSettings("fields");

        // filter out anything not on the current instrument
        $field_settings = array_filter($field_settings, function ($field) use ($instrument_fields) {
            return in_array($field["field_name"], $instrument_fields);
        });

        // modify the field_tooltip value to be decoded for proper html display
        foreach ($field_settings as $field) {
            $field["field_tooltip"] = html_entity_decode($field["field_tooltip"]);
        }

        // prepare the data for javascript
        $field_settings = json_encode($field_settings);

        // handlebars dependency for templates
        echo "<script src='https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/4.0.11/handlebars.min.js'></script>";

        // handlebars template for tooltips
        echo "<script id='form-field-tooltip-template' type='text/x-handlebars-template'>
                <div class='rc-tooltip'>
                    <div class='rc-tooltip-content'>{{field_tooltip}}</div>
                    <span class='glyphicon glyphicon-info-sign text-info'></span>
                </div>
            </script>";

        echo "<script type='text/javascript'>jQuery(function() {
            var templateSource   = document.getElementById('form-field-tooltip-template').innerHTML;
            var template = Handlebars.compile(templateSource, { noEscape: true });
            var tooltips = {$field_settings};
            
            for (const prop in tooltips) {
                var parentElement = jQuery('#label-' + tooltips[prop].field_name + ' table tr td')[1];
                if (parentElement) {
                    var tooltipHtml = template(tooltips[prop]);
                    $(tooltipHtml).prependTo(parentElement);
                }
            }
            
            $('body').append(\"<div id='{$name}-tooltip' class='tooltip1' style='max-width: 400px;padding:7px;'><span class='tooltip-info'></span></div>\");
            
            $('.rc-tooltip').mouseover(function(){
                $(this).css('cursor','pointer');
                var data = $(this).find('.rc-tooltip-content').html();        
                $('#{$name}-tooltip .tooltip-info').html(data);
            });
            $('.rc-tooltip').tooltip2({
                tip: '#{$name}-tooltip',
                position: 'bottom center',
                offset: [0, 0],
                delay: 0
            });
        });</script>";
    }
}