<?php
class Indi_View_Helper_Admin_Form extends Indi_View_Helper_Abstract
{
	public function form()
	{
		ob_start();?>
<script>
	Ext.onReady(function() {

		var myMask;

			createForm = function(url) {
				//console.log(url);
				$.post(url, function(json){
					form = Ext.create('Ext.Panel', {
						title: 'Детали',//json.section.title,
						loadMask: true,
						region: "center",
						closable: true,
						cls: 'myform',
						border: 1,
						id: 'someForm',
						html: '' + json.html,
						cls: 'form',
						autoScroll: true
					});
					if (currentPanelId) {
						viewport.getComponent(2).remove(currentPanelId);
					}
					viewport.getComponent(2).add(form);
					currentPanelId = grid.id;
					for (i in json.js) {
						console.log(json.js);
						eval(json.js[i]);
					}
//					$('#trail').html(json.trail);
//					myMask = new Ext.LoadMask(form.getEl(), {msg:"Загрузка..."});
//					myMask.show();
	//				console.log(json);
				}, 'json')
			}
	});
</script>
		<? $xhtml = ob_get_clean();
        return $xhtml;
    }
}