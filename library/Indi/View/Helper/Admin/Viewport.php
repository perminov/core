<?php
class Indi_View_Helper_Admin_Viewport extends Indi_View_Helper_Abstract
{
	public function viewport()
	{
		$english = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
		$russian = array('Воскресенье', 'Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота');
        $config = Indi::registry('config');
        $lang = $config['view']->lang;
	ob_start();?>
<script>
var STD = '<?=STD?>';
var COM = '<?=COM ? '' : '/admin'?>';
var PRE = STD+COM;
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
                id: 'center-all',
				items: [{
					region: 'north',
					html: '<div style="display: block;">' +
							'<div style="float: right; text-align: right;">' +
							'<?=$lang=='en'?$this->view->date:str_replace($english, $russian, $this->view->date)?>' +
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
		],
        listeners: {
            afterlayout: function(){
                if (Ext.getCmp('i-center-content')) {
                    Ext.getCmp('i-center-content').doComponentLayout();
                }
            }
        }
	});

    Indi.viewport = viewport;

	loadContent = function(url, iframe){
        Indi.load(url, iframe)

        // Push the given url to a story stack
        //Indi.story.push(url);

        /*if (url.match(/\/form\//) || iframe) {
            Indi.load(url, iframe)
		} else {
			$.post(url, function(response){
                Indi.clearCenter();
                viewport.doComponentLayout();
                $('#center-content-body').html(response);
            });
		}*/
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