<?php
class Indi_View_Helper_Admin_StyleStd {
    public function styleStd(){
        ob_start();?>
<style>
    button span.add {
        background-image: url('<?=STD?>/library/extjs4/resources/themes/images/default/shared/add.gif') !important;
    }
    button span.form{
        background-image: url('<?=STD?>/library/extjs4/resources/themes/images/default/shared/form.gif') !important;
    }
    button span.delete{
        background-image: url('<?=STD?>/library/extjs4/resources/themes/images/default/shared/delete.gif') !important;
    }
    .i-btn-icon-back{
        background-image: url('<?=STD?>/i/admin/i-btn-icon-back.png') !important;
    }
    button span.save{
        background-image: url('<?=STD?>/i/admin/icon-action-save.png') !important;
    }
    button span.toggle{
        background-image: url('<?=STD?>/i/admin/icon-action-toggle.png') !important;
    }
    button span.up{
        background-image: url('<?=STD?>/i/admin/icon-action-upper.png') !important;
    }
    button span.down{
        background-image: url('<?=STD?>/i/admin/icon-action-lower.png') !important;
    }
    .i-btn-icon-xls{
        background-image: url('<?=STD?>/i/admin/icon-action-xls.png') !important;
    }
    .i-combo .i-combo-multiple .i-combo-selected-item .i-combo-selected-item-delete{
        background-image: url(<?=STD?>/i/admin/combo-multiple-remove-item-from.png);
    }
    .i-multislider-color .x-slider-horz .x-slider-inner {
        background: url('<?=STD?>/i/admin/i-color-slider-bg.png') no-repeat 0px 3px;
    }
    .i-multislider-color .x-slider-horz .x-slider-inner .x-slider-thumb:first-child {
        background-image: url(<?=STD?>/i/admin/i-color-slider-thumb-first.png);
    }
    .i-multislider-color .x-slider-horz .x-slider-inner .x-slider-thumb:last-child {
        background-image: url(<?=STD?>/i/admin/i-color-slider-thumb-last.png);
    }
    span.radio{
        background: url(<?=STD?>/i/admin/radio.png) no-repeat;
    }
    span.radio.disabled{
        background: url(<?=STD?>/i/admin/radio-disabled.png) no-repeat;
    }
    span.radio.checked{
        background: url(<?=STD?>/i/admin/radioChecked.png) no-repeat;
    }
    span.radio.checked.disabled{
        background: url(<?=STD?>/i/admin/radio-checked-disabled.png) no-repeat;
    }
    span.checkbox{
        background: url(<?=STD?>/i/admin/checkbox.png) no-repeat;
    }
    span.checkbox.disabled, table.multicheckbox.disabled span.checkbox{
        background: url(<?=STD?>/i/admin/checkbox-disabled.png) no-repeat;
    }
    span.checkbox.checked{
        background: url(<?=STD?>/i/admin/checkboxChecked.png) no-repeat;
    }
    span.checkbox.checked.disabled, table.multicheckbox.disabled span.checkbox.checked{
        background: url(<?=STD?>/i/admin/checkbox-checked-disabled.png) no-repeat;
    }
    span.upload{
        background: url(<?=STD?>/i/admin/transparentBg.png);
    }
</style>
        <? return ob_get_clean();
    }
}