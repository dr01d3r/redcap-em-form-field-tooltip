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
     * Hook function to execute on every survey
     * @param $project_id
     * @param $record
     * @param $instrument
     * @param $event_id
     * @param $group_id
     * @param $survey_hash
     * @param $response_id
     * @param $repeat_instance
     */
    function redcap_survey_page ($project_id, $record, $instrument, $event_id, $group_id, $survey_hash, $response_id, $repeat_instance) {
        $this->render_tooltips($instrument, true);
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
        $this->render_tooltips($instrument, false);
    }

    function render_tooltips($instrument, $is_survey) {
        // get all fields on the current instrument
        $instrument_fields = \REDCap::getFieldNames($instrument);

        // get all settings that were specified for field name tooltips
        $field_settings = $this->getSubSettings("fields");

        // filter out anything not on the current instrument
        $field_settings = array_filter($field_settings, function ($field) use ($instrument_fields) {
            return !empty($field["field_name"]) && !empty($field["field_tooltip"]) && in_array($field["field_name"], $instrument_fields);
        });

        // prepare the data for javascript
        $field_settings = json_encode($field_settings);

        // stylesheet
        echo "<link href='" . $this->getUrl('css/form_field_tooltip.css') . "' media='all' rel='stylesheet' />";

        // handlebars dependency for templates
        echo "<script src='" . $this->getUrl('js/handlebars-v4.7.6.js') . "'></script>";
        ?>
        <script type='text/javascript'>
            if(typeof FormFieldTooltip === 'undefined') {
                var FormFieldTooltip = {
                    id: '<?=uniqid()?>',
                    isSurvey: <?=$is_survey?"true":"false"?>,
                    settings: <?=$field_settings?>,
                    bootstrap_version: <?=(version_compare(REDCAP_VERSION, "13.4.0", ">=") ? 5 : 4)?>,
                };
            }
        </script>
        <!-- handlebars template for tooltips -->
        <script id='form-field-tooltip-template' type='text/x-handlebars-template'>
            <span class='fft-rc-tooltip text-primary' data-toggle='popover' data-bs-toggle='popover' data-bs-content='{{field_tooltip}}' data-content='{{field_tooltip}}'>
                <i class='fas fa-info-circle' style='display: inline;'></i>
            </span>
        </script>

        <?php
        // form_field_tooltip
        echo "<script src='" . $this->getUrl('js/form_field_tooltip.js') . "'></script>";
    }
}