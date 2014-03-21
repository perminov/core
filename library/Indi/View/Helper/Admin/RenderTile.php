<?php
class Indi_View_Helper_Admin_RenderTile extends Indi_View_Helper_Abstract
{
    public function renderTile()
    {
		ob_start();
		$gridFields = $this->view->trail->getItem()->gridFields->toArray();
		$actions    = $this->view->trail->getItem()->actions->toArray();
		$section = $this->view->trail->getItem()->section;
		$canadd = false; foreach ($actions as $action) if ($action['alias'] == 'save') {$canadd = true; break;}
		if (!count($gridFields)) {
			echo 'Отсутствуют сведения о структуре ExtJs таблицы для этого раздела.';
		} else {
			// set up grid columns
			for($i = 0; $i < count($gridFields); $i++) {
				$aliases[] = $gridFields[$i]['alias'];
				$columns[] = '{header:"' . str_replace('"','\"',$gridFields[$i]['title']) . '", dataIndex: "' . $gridFields[$i]['alias'] . '", sortable: true' . ($gridFields[$i]['alias'] == 'move' ? ', hidden: true' : '') . '}';
			}
			$columns = implode(',', $columns);
			$aliases = implode("','", $aliases);
			$a = array();
			$buttonIconsPath = $_SERVER['DOCUMENT_ROOT'] . '/core' . '/library/ExtJS/images/default/shared/';
			for($i = 0; $i < count($actions); $i++) if ($actions[$i]['display'] == 'y'){

				$a[] =  ($actions[$i]['alias'] == 'form' && $canadd && ! $section->disableAdd ? '{
					text: "Add",
					iconCls: "add",
					handler: function(){
	                    window.location = "/admin/' . $section->alias . '/' . $actions[$i]['alias'] . '/";
					}

					},' : '') . '{
					text: "' . $actions[$i]['title'] . '",
					'.(file_exists($buttonIconsPath . $actions[$i]['alias'] . '.gif') ? 'iconCls: "' . $actions[$i]['alias'] . '",' : '').'
					handler: function(){
						var row = ' . $section->alias . 'View.getSelectedNodes()[0];
                                          ' .
						(
						$actions[$i]['rowRequired'] == 'y' ?
								'if (!row) {
							alert("Select a row");
							return;
						}
						if (' . $actions[$i]['condition'] . ') {
		                    			window.location = "/admin/' . $section->alias . '/' . $actions[$i]['alias'] . '/id/" + $(row).attr("id") + "/";
						} else {
							return false;
						}
						' : '
						if (' . $actions[$i]['condition'] . ') {
							' . $actions[$i]['javascript'] . '
						} else {
							return false;
						}') . '
					}
				}';
			}
			$actions = implode(',', $a);

			// set up dropdown to navigate through related different types of related items
			$sections = $this->view->trail->getItem()->sections->toArray();
			if (count($sections)) {
				$sectionsDropdown = "'->', 'Subsections:  ', '";
				$sectionsDropdown .= '<select name="sectionId" onchange=redirect(this.value)>';
				$sectionsDropdown .= '<option value="">--Select--</option>';
				for ($i = 0; $i < count($sections); $i++)
					$sectionsDropdown .= '<option value="' . $sections[$i]['alias'] . '">' . $sections[$i]['title'] . '</option>';
				$sectionsDropdown .= '</select>';
				$sectionsDropdown .= "'";
			}
			$tbarItems = array();
			if ($actions) $tbarItems[] = $actions;
			if ($sectionsDropdown) $tbarItems[] = $sectionsDropdown;
			?>
		<script>
			var row;
			Ext.onReady(function general(){
				var gridWidth = document.getElementById('trail').clientWidth-12;
				var gridHeight = document.getElementById('centerTr').clientHeight-150;
				var <?php echo $section->alias?>Store = new Ext.data.JsonStore({
					root: 'blocks',
					totalProperty: 'totalCount',
					idProperty: 'id',
					remoteSort: true,
					fields: [
						{name: 'id', type: 'int'}, '<?php echo $aliases?>'
					],

					proxy: new Ext.data.HttpProxy({
						url: '/admin/<?php echo $section->alias?>/index/<?=Indi::uri('id') ? 'id/' . Indi::uri('id') . '/' : ''?>json/1/'
					})
				});

				var tpl = new Ext.XTemplate(
						'<tpl for=".">',
						'<div class="thumb-wrap" id="{id}">',
						'<div class="thumb">{image}</div>',
						'<span class="x-editable">{title}</span></div>',
						'</tpl>',
						'<div class="x-clear"></div>'
				);

				var <?php echo $section->alias?>View = new Ext.DataView({
					autoScroll: true,
					store: <?php echo $section->alias?>Store,
					tpl: tpl,
					autoHeight: false,
					height: gridHeight, multiSelect: false,
					singleSelect: true,
					overClass: 'x-view-over',
					itemSelector: 'div.thumb-wrap',
					emptyText: '',
					style: 'border-bottom: 1px solid #99BBE8;'
				})

				<?php echo count($tbarItems) ? ' var tbar  = new Ext.Toolbar({items: [' . implode(',', $tbarItems) . ']})' : 'false'?>

				var pagingbar = new Ext.PagingToolbar({
					pageSize: <?php echo $section->rowsOnPage?>,
					store: <?php echo $section->alias?>Store,
					displayInfo: true,
					displayMsg: 'Записи {0} - {1} из {2}',
					emptyMsg: "No rows to display",
					items:[
						'-'
					],
					style: 'border:0px;'
				});

				var <?php echo $section->alias?>viewPanel = new Ext.Panel({
					id: 'images-view',
					frame: false,
					width: gridWidth,
					height: gridHeight,
					autoHeight: true,
					layout: 'auto',
					title: '<?php echo $section->title?>',
					items: [
						<?=count($tbarItems) ? 'tbar,' : ''?>
						<?php echo $section->alias?>View,
						pagingbar
					],
					loadMask: true
				});


					<?php echo $section->alias?>Grid = new Ext.grid.GridPanel({
					width: gridWidth,
					height: gridHeight,
					title: '<?php echo $section->title?>',
					store: <?php echo $section->alias?>Store,
					trackMouseOver:false,
					disableSelection:false,
					loadMask: true,

					// grid columns
					columns:[
						{
							header: "id",
							dataIndex: 'id',
							width: 30,
							sortable: true,
							align: 'right',
							hidden: true
						},
						<?php echo $columns?>]
				});

				<?=$section->alias?>viewPanel.render('<?php echo $section->alias?>view');
				<?=$section->javascript?>
				<?=$section->defaultSortField ? $section->alias . 'Store.setDefaultSort(\''. $section->foreign('defaultSortField')->alias.'\', \'' . $section->defaultSortDirection .'\');' : '' ?>
				<?=$section->alias?>Store.load({params:{start:0, limit:<?=$section->rowsOnPage?>}});
				view = <?=$section->alias?>View;
			});
			function redirect(section){
				if (section) {
					var id = <?php echo $section->alias?>Grid.getSelectionModel().selections.keys;
					if (id != '') {
						var url = '/admin/' + section + '/index/id/' + id + '/';
						window.location = url;
					} else {
						alert('Select a row');
					}
				}
			}
		</script>
		<link rel="stylesheet" type="text/css" href="/library/ExtJS/css/data-view.css"/>
		<style>#images-view .thumb img {width: auto !important; height: 100px;}</style>
		<div id="<?php echo $section->alias?>view" style="padding-left: 10px; padding-right: 0px; width: 900px;"></div>
		</html>
		<?php
		}

		$xhtml = ob_get_clean();
		return $xhtml;
	}
}
