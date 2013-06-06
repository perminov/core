<?php
class Indi_View_Helper_Admin_Menu extends Indi_View_Helper_Abstract
{
	public function menu()
	{
		if ($this->view->menu->count()) {
			foreach ($this->view->menu as $item) if ($item->sectionId) $children[$item->sectionId][] = $item;
			ob_start();?>
<script>
	Ext.onReady(function() {
	menu = Ext.create('Ext.tree.Panel', {
		store: Ext.create('Ext.data.TreeStore', {
			root: {
				expanded: true,
				children: [
					<?foreach($this->view->menu as $item) if (!$item->sectionId){?>
						{
							text: "<?=$item->title?>",
							<?if(count($children[$item->id])){?>
							expanded: false,
							cls: 'root-item',
							children: [
								<?$i=0;foreach($children[$item->id] as $child){?>
									{text: '<?=$child->title?>', iconCls: 'no-icon', leaf: true, cls: 'cycle-<?=$i%2?>', value: '<?=$_SERVER['STD']?><?=$GLOBALS['cmsOnlyMode']?'':'/admin'?>/<?=$child->alias?>/'},
									<?$i++;}?>
							]
							<?}?>
						},
						<?}?>
				]
			}
		}),
		rootVisible: false,
		title: '<?=MENU?>',
		useArrows: true,
		border: 1,
		region: 'west',
		width: 200,
		collapsible: true,
		cls: 'menu',
		padding: '41 0 0 0',
        weight: 300,
		listeners: {
			itemclick: function(view, rec, item, index, eventObj) {
				if(rec.get('leaf') == false) {
					if (rec.data.expanded) rec.collapse(); else rec.expand();
				} else {
					loadContent(rec.raw.value);
				}
			},
			beforecollapse: function(){
				$('#indi-engine-logo').hide();
			},
			expand: function(){
				$('#indi-engine-logo').show();
			}
		}

	})
});
</script>
		<? $xhtml = ob_get_clean();
        }
        return $xhtml;        
    }
}