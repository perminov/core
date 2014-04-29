<?php
class Indi_View_Helper_Admin_FormSpan extends Indi_View_Helper_Abstract
{
    public function formSpan($alias)
    {
        ob_start();?>
		<script>
            $('#td-left-<?=$alias?>').attr('colspan', '2').parent().addClass('i-form-subheader');
            $('#td-right-<?=$alias?>').remove();
        </script>
        <? return ob_get_clean();
    }    
}