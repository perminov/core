<?php
class Indi_View_Helper_Admin_RenderGrid extends Indi_View_Helper_Abstract
{
    public function renderGrid()
    {
		$gridFields = $this->view->trail->getItem()->gridFields->toArray();
		$actions    = $this->view->trail->getItem()->actions->toArray();
        $filtersCount = $this->view->trail->getItem()->filtersCount;
		$canadd = false; foreach ($actions as $action) if ($action['alias'] == 'save') {$canadd = true; break;}
		if (!count($gridFields)) {
			echo 'Отсутствуют сведения о структуре ExtJs таблицы для этого раздела.';
		} else {
			// set up grid columns
			for($i = 0; $i < count($gridFields); $i++) {
				$aliases[] = array('name' => $gridFields[$i]['alias'], 'type' => 'string');
				$column = array('header' => $gridFields[$i]['title'], 'dataIndex' => $gridFields[$i]['alias'], 'sortable' => true);
				if ($i == 0) $column['flex'] = 1;
				if ($gridFields[$i]['alias'] == 'move')  $column['hidden'] = true;
				$columns[] = $column;
			}
			$fields = array_merge(array(array('name' => 'id', 'type' => 'int')), $aliases);

			$columns = array_merge(array(array('header' => 'id', 'dataIndex' => 'id', 'width' => 30, 'sortable' => true, 'align' =>'right', 'hidden' => true)), $columns);
			$a = array();
			$buttonIconsPath = $_SERVER['DOCUMENT_ROOT'] . $_SERVER['STD'] . '/core/library/extjs4/resources/themes/images/default/shared/';
			for($i = 0; $i < count($actions); $i++) if ($actions[$i]['display'] == 'y'){

				$a[] =  ($actions[$i]['alias'] == 'form' && $canadd && ! $this->view->trail->getItem()->section->disableAdd ? '{
					text: "Создать",
					iconCls: "add",
					handler: function(){
	                    loadContent("/admin/' . $this->view->trail->getItem()->section->alias . '/' . $actions[$i]['alias'] . '/");
					}

					},' : '') . '{
					text: "' . $actions[$i]['title'] . '",
					'.(file_exists($buttonIconsPath . $actions[$i]['alias'] . '.gif') ? 'iconCls: "' . $actions[$i]['alias'] . '",' : '').'
					handler: function(){
						var selection = grid.getSelectionModel().getSelection();
						if (selection.length) var row = selection[0].data;
                        ' .
						(
						$actions[$i]['rowRequired'] == 'y' ?
						'if (!selection.length) {
							Ext.MessageBox.show({
								title: "Сообщение",
								msg: "Выберите строку",
								buttons: Ext.MessageBox.OK,
								icon: Ext.MessageBox.WARNING
							});
							return false;
						}
						if (' . $actions[$i]['condition'] . ') {
						    var url = "/admin/' . $this->view->trail->getItem()->section->alias . '/' . $actions[$i]['alias'] . '/id/" + row.id + "/";
							' . $actions[$i]['javascript'] . '
                   			loadContent(url);
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
			$actions = $a;
//			$actions = implode(',', $a);

			// set up dropdown to navigate through related different types of related items
			$sections = $this->view->trail->getItem()->sections->toArray();
			if (count($sections)) {
				$sectionsDropdown = "'Подраздел:  ', '";
				$sectionsDropdown .= '<span><select style="border: 0;" name="sectionId" id="subsectionSelect">';
				$sectionsDropdown .= '<option value="">--Выберите--</option>';
                $maxLength = 12;
				for ($i = 0; $i < count($sections); $i++){
					$sectionsDropdown .= '<option value="' . $sections[$i]['alias'] . '">' . $sections[$i]['title'] . '</option>';
                    $str = preg_replace('/&[a-z]+;/', '&', $sections[$i]['title']);
                    $len = mb_strlen($str, 'utf-8');
                    if ($len > $maxLength) $maxLength = $len;
                }
				$sectionsDropdown .= '</select></span>';
				$sectionsDropdown .= "'";
			}
			$tbarItems = array();
			if ($actions) $tbarItems[] = $actions;
            $tbarItems[] = "
                '->',
                'Искать: ',
                {
                    xtype: 'textfield',
                    name: 'fast-search-keyword',
                    height: 19,
                    cls: 'fast-search-keyword',
					margin: '0 4 0 0',
                    placeholder: 'Искать',
                    listeners: {
                        change: function(obj, newValue, oldValue, eOpts){
                            clearTimeout(timeout);
                            timeout = setTimeout(function(keyword){
                                grid.store.proxy.url = '" . $_SERVER['STD'] . ($GLOBALS['cmsOnlyMode']?'':'/admin') . "/' + json.params.section + '/index/' + (json.params.id ? 'id/' + json.params.id + '/' : '') + 'json/1/' + (keyword ? 'keyword/' + keyword + '/' : '');
                                gridStore.load();
                            }, 500, newValue);
                        }
                    }
                }
            ";
			if ($sectionsDropdown) $tbarItems[] = $sectionsDropdown;

			if ($defaultSortField = $this->view->trail->getItem()->section->getForeignRowByForeignKey('defaultSortField')){
				$this->view->trail->getItem()->section->defaultSortFieldAlias = $defaultSortField->alias;
			}
			$meta = array(
				'columns' => $columns,
				'tbar' => $tbarItems,
				'fields' => $fields,
                'filtersCount' => $filtersCount,
				'params' => $this->view->trail->requestParams,
				'section' => $this->view->trail->getItem()->section->toArray(),
				'trail' => $this->view->trail(),
				'entity' => $this->view->trail->getItem()->section->getForeignRowByForeignKey('entityId')->title
			);
            if ($_SERVER['STD']) $meta = json_decode(str_replace('\/admin\/', str_replace('/', '\/', $_SERVER['STD']) . '\/admin\/', json_encode($meta)));
			if ($GLOBALS['cmsOnlyMode']) $meta = json_decode(str_replace('\/admin\/', '\/', json_encode($meta)));
			ob_start();?>
			<script>
			var json = <?=json_encode($meta)?>;
            var timeout;
			Ext.onReady(function() {
				var myMask;
				var gridStore = Ext.create('Ext.data.Store', {
					fields: json.fields,
					method: 'POST',
					remoteSort: true,
					sorters:  (json.section.defaultSortField ? [{
						property : json.section.defaultSortFieldAlias,
						direction: json.section.defaultSortDirection
					}] : []),
					proxy:  new Ext.data.HttpProxy({
						url: '<?=$_SERVER['STD']?><?=$GLOBALS['cmsOnlyMode']?'':'/admin'?>/' + json.params.section + '/index/' + (json.params.id ? 'id/' + json.params.id + '/' : '') + 'json/1/',  // works
						method: 'POST',
						reader: {
							type: 'json',
							root: 'blocks',
							totalProperty: 'totalCount',
							idProperty: 'id'
						}
					}),
					loadMask: true,
					listeners: {
						beforeload: function(){
							myMask.show();
						},
						load: function (){
							var columnWidths = {};
							var metrics = new Ext.util.TextMetrics();
							var totalColumnsWidth = 0;
							for(i in grid.columns) {
								if (grid.columns[i].hidden == false) {
									columnWidths[i] = metrics.getWidth(grid.columns[i].text) + 10;
									for (j in gridStore.data.items) {
										var cellWidth = metrics.getWidth(gridStore.data.items[j].data[grid.columns[i].dataIndex]) + 7;
										if (cellWidth > columnWidths[i]) columnWidths[i] = cellWidth;
									}
									totalColumnsWidth += columnWidths[i];
								}
							}
							var totalGridWidth = grid.getWidth();
							if (totalColumnsWidth < totalGridWidth) {
								var first = true;
								for(i in columnWidths) {
									if (first) {
										first = false;
									} else {
										grid.columns[i].width = columnWidths[i];
									}
								}
								//console.log(columnWidths);
							} else {
								var percent = totalGridWidth/totalColumnsWidth;
								var first = true;
								for(i in columnWidths) {
									if (first) {
										first = false;
									} else if (grid.columns[i].width > 100) {
										grid.columns[i].width = columnWidths[i] * percent;
									}
								}
							}
							myMask.hide()
						}
					}
				});

				grid = Ext.create('Ext.grid.Panel', {
					multiSelect: false,
					columns: json.columns,
					title: json.section.title,
					loadMask: true,
					region: "center",
					closable: true,
					store: gridStore,
					align: "stretch",
					cls: 'mygrid',
					tbar: eval('['+json.tbar+']'),
					border: 1,
					height: 400,
					id: json.section.alias + 'Grid',
					bbar: new Ext.PagingToolbar({
						beforePageText: 'Страница',
						afterPageText: 'из {0}',
						pageSize: json.section.rowsOnPage,
						store: gridStore,
						displayInfo: true,
						displayMsg: 'Записи {0} - {1} из {2}',
						emptyMsg: "Нет записей",
						firstText      : "Первая",
						prevText       : "Предыдущая",
						nextText       : "Следующая",
						lastText       : "Последняя",
						refreshText    : "Обновить",
						items:[
							'-'
						]
					})
				});
                json.filtersCount = 0;
                if (json.filtersCount > 0) {
                    var maxImgWidth = Math.floor(($('#center-content-body').width()-36)/2);
                    var filter = {
                        region: 'south',
                        title: 'Поиск',
                        collapsible: true,
                        collapsed: false,
                        height: 300,
                        html: '<iframe src="/admin/' + json.section.alias + '/form?width='+maxImgWidth+'" width="100%" height="100%" scrolling="auto" frameborder="0" id="form-frame" name="form-frame"></iframe>',
                        closable: true,
                        weight: 10,
                        id: 'search-panel'
                    }
                }
                var index;
                if (viewport.getComponent(3).cls == 'center-all') {
                    index = 3;
                } else if (viewport.getComponent(4).cls == 'center-all') {
                    index = 4
                }
                if (json.filtersCount > 0 && (viewport.getComponent(index+2) == undefined || viewport.getComponent(index+2).id != 'search-panel-placeholder')) {
                    viewport.add(filter);
                    /*$.post('/admin/' + json.section.alias + '/form', function(response){
                        viewport.getComponent(index+2).html = 'shit';
                    })*/
                } else if (json.filtersCount == 0 && viewport.getComponent(index+2) != undefined && viewport.getComponent(index+2).id == 'search-panel') {
                    viewport.getComponent(index+2).close();
                }
                viewport.getComponent(index).add(grid);
				currentPanelId = grid.id;

				eval(json.section.javascript);
				var transformed = Ext.create('Ext.form.field.ComboBox', {
					valueField: 'sectionId',
					hiddenName: 'sectionId',
					typeAhead: false,
					transform: 'subsectionSelect',
					width: <?=$maxLength*6+10?>,
					style: 'font-size: 10px',
					cls: 'subsection-select',
					editable: false,
					listeners: {
						change: function(cmb, newv, oldv){
							var selection = grid.getSelectionModel().getSelection();
							if (selection.length) {
								if (this.getValue()) {
									loadContent('<?=$_SERVER['STD']?><?=$GLOBALS['cmsOnlyMode']?'':'/admin'?>/' + cmb.getValue() + '/index/id/' + selection[0].data.id + '/');
								}
							} else {
								cmb.reset();
								Ext.MessageBox.show({
									title: 'Сообщение',
									msg: 'Выберите строку',
									buttons: Ext.MessageBox.OK,
									icon: Ext.MessageBox.WARNING
								});
							}
						}
					}
				});
				$('#trail').html(json.trail);
				myMask = new Ext.LoadMask(grid.getEl(), {msg:"Загрузка..."});
				myMask.show();
				gridStore.load([{params:{start:0, limit: json.section.rowsOnPage, sort: {property: 'title', direction: 'ASC'}}}]);
			});

			</script>

		<? $xhtml = ob_get_clean();
		}
        return $xhtml;
    }    
}