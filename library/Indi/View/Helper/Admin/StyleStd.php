<?php
class Indi_View_Helper_Admin_StyleStd extends Indi_View_Helper_Abstract{
    public function styleStd(){
        ob_start();?>
        <style>
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
            controls.upload{
                background: url(<?=STD?>/i/admin/transparentBg.png);
            }
            .i-combo .i-combo-multiple .i-combo-selected-item .i-combo-selected-item-delete{
                background-image: url(<?=STD?>/i/admin/combo-multiple-remove-item-from.png);
            }
        </style>
        <? return ob_get_clean();
    }
}