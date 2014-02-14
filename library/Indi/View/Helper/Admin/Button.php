<?php
class Indi_View_Helper_Admin_Button extends Indi_View_Helper_Abstract
{
    public function button($title = 'Назад', $action = 'window.parent.locationHistoryBack()')
    {
		ob_start();
		?><script>
		Ext.onReady(function() {
			Ext.create('Ext.Button', {
				renderTo: 'td-button-<?=$title?>',
				text: '<?=$title?>',
				padding: '3 10 3 10',
                height: 26,
                width: Indi.metrics.getWidth('<?=$title?>') + 40,
				margin: 6,
				handler: function(){
					<?=str_replace('javascript: ', '', $action)?>
				}
			});
		});
		</script><?
		$xhtml = ob_get_clean();
        return $xhtml;
    }
}
