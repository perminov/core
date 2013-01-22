<?php
class Indi_View_Helper_Admin_Viewport extends Indi_View_Helper_Abstract
{
	public function viewport()
	{
		$english = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
		$russian = array('Воскресенье', 'Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота');
	ob_start();?>
<script>
Ext.onReady(function() {

	viewport = Ext.create('Ext.Viewport', {
		layout: {type: 'border', padding: 5},
		defaults: {split: true},
		items: [
			menu,
			{
				region: 'center',
				defaults: {split: true},
				border: 0,
				layout: {type: 'border', padding: '0 0 0 0'},
				items: [{
					region: 'north',
					html: '<div style="display: block;">' +
							'<div style="float: right; text-align: right;">' +
							'<?=str_replace($english, $russian, $this->view->date) ?>' +
							'</div>' +
							'<div style="text-align: left; margin-bottom: 5px;">' +
							'<?=$this->view->admin?>' +
							'</div>' +
							'<div style="height: 17px; border: 1px solid #99BCE8; background-color: white; padding-top: 0px; padding-left: 2px; " id="trail">' +
							'<?=$this->view->trail()?>' +
							'</div>'+
							'</div>',
					height: 36,
					cls: 'top-center',
					border: 0
				}, {
					region: 'center',
					id: 'center-content'
				}]
			}
		]
	});
});
</script>
		<?$xhtml = ob_get_clean();
    	return $xhtml;
    }
}