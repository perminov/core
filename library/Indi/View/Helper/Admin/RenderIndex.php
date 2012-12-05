<?php
class Indi_View_Helper_Admin_RenderIndex extends Indi_View_Helper_Abstract
{
    public function renderIndex()
    {
		ob_start();
		$gridFields = $this->view->trail->getItem()->gridFields->toArray();
		$actions    = $this->view->trail->getItem()->actions->toArray();
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

				$a[] =  ($actions[$i]['alias'] == 'form' && $canadd && ! $this->view->trail->getItem()->section->disableAdd ? '{
					text: "Add", 
					iconCls: "add",
					handler: function(){
	                    window.location = "/admin/' . $this->view->trail->getItem()->section->alias . '/' . $actions[$i]['alias'] . '/";
					}

					},' : '') . '{
					text: "' . $actions[$i]['title'] . '", 
					'.(file_exists($buttonIconsPath . $actions[$i]['alias'] . '.gif') ? 'iconCls: "' . $actions[$i]['alias'] . '",' : '').'
					handler: function(){
						var row = ' . $this->view->trail->getItem()->section->alias . 'Grid.getSelectionModel().getSelected();
                                          ' . 
						(
						$actions[$i]['rowRequired'] == 'y' ? 
						'if (!row) {
							alert("Select a row");
							return;
						}
						if (' . $actions[$i]['condition'] . ') {
		                    			window.location = "/admin/' . $this->view->trail->getItem()->section->alias . '/' . $actions[$i]['alias'] . '/id/" + row.id + "/";
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
var row = 'asd';
Ext.onReady(function general(){
	var gridWidth = document.getElementById('trail').clientWidth-12;
	var gridHeight = document.getElementById('centerTr').clientHeight-130;
	var <?php echo $this->view->trail->getItem()->section->alias?>Store = new Ext.data.JsonStore({
        root: 'blocks',
        totalProperty: 'totalCount',
        idProperty: 'id',
        remoteSort: true,
        fields: [
            {name: 'id', type: 'int'}, '<?php echo $aliases?>'
        ],
 
        proxy: new Ext.data.HttpProxy({
            url: '/admin/<?php echo $this->view->trail->getItem()->section->alias?>/index/<?php echo $this->view->trail->requestParams['id'] ? 'id/' . $this->view->trail->requestParams['id'] . '/' : ''?>json/1/'
        })
    });
    <?php echo $this->view->trail->getItem()->section->alias?>Grid = new Ext.grid.GridPanel({
        width: gridWidth,
        height: gridHeight,
        title: '<?php echo $this->view->trail->getItem()->section->title?>',
        store: <?php echo $this->view->trail->getItem()->section->alias?>Store,
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
		<?php echo $columns?>],
 
        viewConfig: {
            forceFit: true
        },
		<?php echo count($tbarItems) ? 'tbar : [' . implode(',', $tbarItems) . '], ' : ''?>
        // paging bar on the bottom
        bbar: new Ext.PagingToolbar({
            pageSize: <?php echo $this->view->trail->getItem()->section->rowsOnPage?>,
            store: <?php echo $this->view->trail->getItem()->section->alias?>Store,
            displayInfo: true,
            displayMsg: 'Записи {0} - {1} из {2}',
            emptyMsg: "No rows to display",
            items:[
                '-'             
            ]
        })
    });
    <?php echo $this->view->trail->getItem()->section->alias?>Grid.render('<?php echo $this->view->trail->getItem()->section->alias?>grid');
    <?php echo $this->view->trail->getItem()->section->javascript; ?>
    <?php  if ($this->view->trail->getItem()->section->defaultSortField) {?>
		<?php echo $this->view->trail->getItem()->section->alias?>Store.setDefaultSort('<?php echo $this->view->trail->getItem()->section->getForeignRowByForeignKey('defaultSortField')->alias?>', '<?php echo $this->view->trail->getItem()->section->defaultSortDirection?>'); 
    <?php  } ?>
    <?php echo $this->view->trail->getItem()->section->alias?>Store.load({params:{start:0, limit:<?php echo $this->view->trail->getItem()->section->rowsOnPage?>}});     
	grid = <?php echo $this->view->trail->getItem()->section->alias?>Grid;
}); 
function redirect(section){
	if (section) {
		var id = <?php echo $this->view->trail->getItem()->section->alias?>Grid.getSelectionModel().selections.keys;
		if (id != '') {
			var url = '/admin/' + section + '/index/id/' + id + '/';
			window.location = url;
		} else {
			alert('Select a row');
		}
	}
}
function dump(target)
{
    var obj = target;
    var info = '';
    for (i in obj) {
    	info += i + '=' + obj[i] + '\n';
    }
    alert(info);
}
</script> 
<div id="<?php echo $this->view->trail->getItem()->section->alias?>grid" style="padding-left: 10px; padding-right: 0px; width: 900px;"></div> 
</html> 
<?php
		}
		$xhtml = ob_get_clean();
        return $xhtml;
    }    
}