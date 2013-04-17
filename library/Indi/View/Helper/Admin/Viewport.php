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
			{
				html: '<img src="/i/admin/logo.png" id="indi-engine-logo"/>',
				width: 200,
				height: 36,
				border: 0,
				cls: 'indi-engine-logo'
			},
			menu,
			{
				region: 'center',
				defaults: {split: true},
				border: 1,
				layout: {type: 'border', padding: '0 0 0 0'},
				cls: 'center-all',
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
                    id: 'center-content',
                    border: 1
                }]
			}
		]
	});
	loadContent = function(url){
		locationHistory.push(url);
		if (url.match(/\/form\//)) {
			if (currentPanelId) {
				if (viewport.getComponent(3).cls == 'center-all') {
					viewport.getComponent(3).remove(currentPanelId);
				} else if (viewport.getComponent(4).cls == 'center-all') {
					viewport.getComponent(4).remove(currentPanelId);
				}
			}
			var maxImgWidth = Math.floor(($('#center-content-body').width()-36)/2);
			$('#center-content-body').html('');
            form = Ext.create('Ext.Panel', {
                region: 'center',
                border: 0,
                align: 'stretch',
                html: '<iframe src="'+url+'?width='+maxImgWidth+'" width="100%" height="100%" scrolling="auto" frameborder="0" id="form-frame" name="form-frame"></iframe>',
                renderTo: 'center-content-body'
            });
		} else {
			$.post(url, function(response){
				if (currentPanelId) {
					if (viewport.getComponent(3).cls == 'center-all') {
						viewport.getComponent(3).remove(currentPanelId);
					} else if (viewport.getComponent(4).cls == 'center-all') {
						viewport.getComponent(4).remove(currentPanelId);
					}
				}
				$('#center-content-body').html(response);
			});
		}
	}
	locationHistoryBack = function(){
		if (locationHistory.length > 1) {
			locationHistory.pop();
			loadContent(locationHistory[locationHistory.length]);
		}
	}
});
</script>
		<?$xhtml = ob_get_clean();
    	return $xhtml;
    }
}