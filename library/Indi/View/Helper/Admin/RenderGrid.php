<?php
class Indi_View_Helper_Admin_RenderGrid extends Indi_View_Helper_Abstract
{
    public function getFirstColumnWidthFraction(){
        return 0.4;
    }
    public function renderGrid()
    {
        $gridFields = $this->view->trail->getItem()->gridFields->toArray();
		$actions    = $this->view->trail->getItem()->actions->toArray();
		$canadd = false; foreach ($actions as $action) if ($action['alias'] == 'save') {$canadd = true; break;}
		$currentPage = $this->view->getScope('page') ? $this->view->getScope('page') : 1;
        $filterFieldAliases = array();
        $icons = array('form', 'delete', 'save', 'toggle', 'up', 'down');
        $comboFilters = array();
        foreach ($this->view->trail->getItem()->filters as $filter) {
            if (in_array($filter->foreign['fieldId']->foreign['elementId']['alias'], array('number','calendar','datetime'))) {
                $filterFieldAliases[] = $filter->foreign['fieldId']->alias . '-gte';
                $filterFieldAliases[] = $filter->foreign['fieldId']->alias . '-lte';
            } else {
                if ($filter->foreign['fieldId']->relation) $comboFilters[] = $this->view->filterCombo($filter);
                $filterFieldAliases[] = $filter->foreign['fieldId']->alias;
            }
        }

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
			for($i = 0; $i < count($actions); $i++) if ($actions[$i]['display'] == 1){

				$a[] =  ($actions[$i]['alias'] == 'form' && $canadd && ! $this->view->trail->getItem()->section->disableAdd ? '{
					text: "' . ACTION_CREATE . '",
					iconCls: "add",
					actionAlias: "' . $actions[$i]['alias'] . '",
					handler: function(){
	                    loadContent(grid.indi.href + this.actionAlias + "/ph/" + Indi.trail.item().section.primaryHash + "/");
					}

					},' : '') . '{
					text: "' . $actions[$i]['title'] . '",
					actionAlias: "' . $actions[$i]['alias'] . '",
					id: "action-button-' . $actions[$i]['alias'] . '",
					'.(in_array($actions[$i]['alias'], $icons) ? 'iconCls: "' . $actions[$i]['alias'] . '",' : '').'
					handler: function(){
						var selection = grid.getSelectionModel().getSelection();
						if (selection.length) {
						    var row = selection[0].data;
						    var aix = selection[0].index + 1;
						}
                        ' .
						(
						$actions[$i]['rowRequired'] == 'y' ?
						'if (!selection.length) {
							Ext.MessageBox.show({
								title: "' . GRID_WARNING_SELECTROW_TITLE . '",
								msg: "' . GRID_WARNING_SELECTROW_MSG . '",
								buttons: Ext.MessageBox.OK,
								icon: Ext.MessageBox.WARNING
							});
							return false;
						} else {
						    ' . $actions[$i]['javascript'] . '
						}
						' : $actions[$i]['javascript']) . '
					}
				}';
			}
			$actions = $a;
//			$actions = implode(',', $a);

			// set up dropdown to navigate through related different types of related items
			$sections = $this->view->trail->getItem()->sections->toArray();
			if (count($sections)) {
				$sectionsDropdown = "'" . GRID_SUBSECTIONS_LABEL . ":  ', '";
				$sectionsDropdown .= '<span><select style="border: 0;" name="sectionId" id="subsectionSelect">';
				$sectionsDropdown .= '<option value="">' . GRID_SUBSECTIONS_EMPTY_OPTION . '</option>';
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
                '" . GRID_SUBSECTIONS_SEARCH_LABEL . ": ',
                {
                    xtype: 'textfield',
                    name: 'fast-search-keyword',
                    value: '" . urldecode($this->view->getScope('keyword')) . "',
                    height: 19,
                    cls: 'i-form-text',
					margin: '0 4 0 0',
                    placeholder: 'Искать',
                    id: 'fast-search-keyword',
                    listeners: {
                        change: function(obj, newValue, oldValue, eOpts){
                            clearTimeout(timeout);
                            timeout = setTimeout(function(keyword){
                                grid.store.proxy.url = '" . STD . (COM?'':'/admin') . "/' + json.params.section + '/index/' + (json.params.id ? 'id/' + json.params.id + '/' : '') + 'json/1/' + (keyword ? 'keyword/' + keyword + '/' : '');
                                filterChange({});
                            }, 500, newValue);
                        }
                    }
                }
            ";
			if ($sectionsDropdown) $tbarItems[] = $sectionsDropdown;
            if ($savedOrder = json_decode($this->view->getScope('order'))) {
                $this->view->trail->getItem()->section->defaultSortFieldAlias = $savedOrder[0]->property;
                $this->view->trail->getItem()->section->defaultSortDirection = $savedOrder[0]->direction;
            } else if ($defaultSortField = $this->view->trail->getItem()->section->getForeignRowByForeignKey('defaultSortField')){
				$this->view->trail->getItem()->section->defaultSortFieldAlias = $defaultSortField->alias;
			}
			$meta = array(
				'columns' => $columns,
				'tbar' => $tbarItems,
				'fields' => $fields,
				'params' => $this->view->trail->requestParams,
				'section' => $this->view->trail->getItem()->section->toArray(),
				'trail' => $this->view->trail(),
				'entity' => $this->view->trail->getItem()->section->getForeignRowByForeignKey('entityId')->title
			);
            if (STD) $meta = json_decode(str_replace('\/admin\/', str_replace('/', '\/', STD) . '\/admin\/', json_encode($meta)));
			if (COM) $meta = json_decode(str_replace('\/admin\/', '\/', json_encode($meta)));
			ob_start();?>
			<script>
            Indi.section = '<?=$this->view->trail->getItem()->section->alias?>';
			var json = <?=json_encode($meta)?>;
            var timeout, timeout2;
            var filterChange;
			Ext.onReady(function() {
                var filterAliases = <?=json_encode($filterFieldAliases)?>;
                var gridColumnsAliases = [];
                for (var i =0; i < json.columns.length; i++) {
                    if (json.columns[i].dataIndex != 'id' && json.columns[i].dataIndex != 'move') {
                        gridColumnsAliases.push(json.columns[i].dataIndex);
                    }
                }
                filterChange = function(obj, newv, oldv){
                    var params = [];
                    var usedFilterAliasesThatHasGridColumnRepresentedBy = [];
                    for (var i in filterAliases) {
                        var filterValue = Ext.getCmp('filter-'+filterAliases[i]).getValue();
                        if (filterValue != '%' && filterValue != '' && filterValue !== null) {
                            var param = {};
							if (Ext.getCmp('filter-'+filterAliases[i]).xtype == 'datefield') {
                                if(Ext.getCmp('filter-'+filterAliases[i]).format != 'Y-m-d') {
                                    param[filterAliases[i]] = Ext.Date.format(Ext.Date.parse(Ext.getCmp('filter-'+filterAliases[i]).getRawValue(), Ext.getCmp('filter-'+filterAliases[i]).format), 'Y-m-d');
                                } else {
                                    param[filterAliases[i]] = Ext.getCmp('filter-'+filterAliases[i]).getRawValue();
                                }
							} else {
								param[filterAliases[i]] = Ext.getCmp('filter-'+filterAliases[i]).getValue();
							}
                            params.push(param);
                            for (var j =0; j < gridColumnsAliases.length; j++) {
                                if (gridColumnsAliases[j] == filterAliases[i]) {
                                    usedFilterAliasesThatHasGridColumnRepresentedBy.push(filterAliases[i]);
                                }
                            }
                        }
                    }
                    gridStore.getProxy().extraParams = {search: JSON.stringify(params)};

                    var keyword = Ext.getCmp('fast-search-keyword').disabled == false && Ext.getCmp('fast-search-keyword').getValue() ? Ext.getCmp('fast-search-keyword').getValue() : '';
                    gridStore.getProxy().url = Indi.pre + '/' + json.params.section + '/index/' +
                        (json.params.id ? 'id/' + json.params.id + '/' : '') + 'json/1/' +
                        (keyword ? 'keyword/' + keyword + '/' : '');

                    Ext.getCmp('fast-search-keyword').setDisabled(usedFilterAliasesThatHasGridColumnRepresentedBy.length == gridColumnsAliases.length);
                    if (!obj.noReload) {
                        gridStore.currentPage = 1;
                        gridStore.lastOptions.page = 1;
                        gridStore.lastOptions.start = 0;
                        if (obj.xtype == 'combobox') {
                            gridStore.reload();
                        } else if (obj.xtype == 'datefield' && (/^([0-9]{4}-[0-9]{2}-[0-9]{2}|[0-9]{2}\.[0-9]{2}\.[0-9]{4})$/.test(obj.getRawValue()) || !obj.getRawValue().length)) {
                            clearTimeout(timeout);
                            timeout = setTimeout(function(){
                                gridStore.reload();
                            }, 500);
                        } else if (obj.xtype != 'datefield') {
                            clearTimeout(timeout);
                            timeout = setTimeout(function(){
                                gridStore.reload();
                            }, 500);
                        }
                    }
                }
                var myMask;
				var gridStore = Ext.create('Ext.data.Store', {
					fields: json.fields,
					method: 'POST',
                    pageSize: json.section.rowsOnPage,
                    remoteSort: true,
					sorters:  (json.section.defaultSortField ? [{
						property : json.section.defaultSortFieldAlias,
						direction: json.section.defaultSortDirection
					}] : []),
					proxy:  new Ext.data.HttpProxy({
						url: '<?=PRE?>/' + json.params.section + '/index/' + (json.params.id ? 'id/' + json.params.id + '/' : '') + 'json/1/',
						method: 'POST',
						reader: {
							type: 'json',
							root: 'blocks',
							totalProperty: 'totalCount',
							idProperty: 'id'
						}
					}),
					currentPage: <?=$currentPage?>,
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
                                var smallColumnsWidth = 0;
                                var first = true;
                                for(i in columnWidths) {
                                    if (first) {
                                        first = false;
                                    } else if (columnWidths[i] <= 100) {
                                        smallColumnsWidth += columnWidths[i];
                                    }
                                }
                                var firstColumnWidth = Math.ceil(totalGridWidth*<?=$this->getFirstColumnWidthFraction()?>);
                                var percent = (totalGridWidth-firstColumnWidth-smallColumnsWidth)/(totalColumnsWidth-columnWidths[1]-smallColumnsWidth);
                                var first = true;
								for(i in columnWidths) {
									if (first) {
                                        grid.columns[i].width = firstColumnWidth;
                                        first = false;
                                    } else if (columnWidths[i] > 100) {
										grid.columns[i].width = columnWidths[i] * percent;
									} else {
                                        grid.columns[i].width = columnWidths[i];
                                    }
								}
							}
                            // Mark rows as disabled if such rows exist
							myMask.hide();

                            // Set up full request string (but without paging params)
                            var url = grid.store.getProxy().url;
                            var get = [];
                            if (grid.store.getProxy().extraParams.search) get.push('search=' + encodeURIComponent(grid.store.getProxy().extraParams.search));
                            if (grid.store.getSorters().length) get.push('sort=' + encodeURIComponent(JSON.stringify(grid.store.getSorters())));
                            grid.indi.request = url + (get.length ? '?' + get.join('&') : '');
                        }
					}
				});

				grid = Ext.create('Ext.grid.Panel', {
					multiSelect: <?=$this->view->multiSelect ? 'true' : 'false'?>,
					columns: json.columns,
					title: json.section.title,
					region: "center",
                    indi: {
                        href : '<?=PRE?>/' + json.params.section + '/',
                        msgbox: {
                            confirm: {
                                title: '<?=MSGBOX_CONFIRM_TITLE?>',
                                message: '<?=MSGBOX_CONFIRM_MESSAGE?>'
                            }
                        }
                    },
                    closable: true,
                    viewConfig: {
                        getRowClass: function (record, index) {
                            if (record.raw._system && record.raw._system.disabled) return 'disabled-row';
                        }
                    },
                    listeners: {
                        beforeselect: function (sm, record) {
                            if (record.raw._system && record.raw._system.disabled) return false;
                        },
                        selectionchange: function (sm, selected) {
                            if (selected.length > 0) {
                                Ext.Array.each(selected, function (record) {
                                    if (record.raw._system && record.raw._system.disabled) {
                                        // deselect
                                        sm.deselect(record, true);
                                    }
                                });
                            }
                        },
                        itemdblclick: function(view, row, el, index, e, eOpts){
                            if (Ext.getCmp('action-button-form')) Ext.getCmp('action-button-form').handler();
                        }
                    },
                    store: gridStore,
					align: "stretch",
					cls: 'mygrid',
                    tools: [<?if(count($filterFieldAliases)){?>{
                        type: 'search',
                        handler: function(event, target, owner, tool){
                            if (grid.getDockedComponent('search-toolbar').hidden) {
                                grid.getDockedComponent('search-toolbar').show();
                            } else {
                                grid.getDockedComponent('search-toolbar').hide();
                            }
                        }
                    }<?}?>],
					//tbar: eval('['+json.tbar+']'),
                    dockedItems: [<?=$this->view->gridFilters()?>{
                        xtype: 'toolbar',
                        dock: 'top',
                        items: eval('['+json.tbar+']')
                    }],
                    border: 1,
					id: 'i-center-content',
					bbar: new Ext.PagingToolbar({
						store: gridStore,
						displayInfo: true,
						items:[
							'-'
						]
					})
				});
                mainPanel = grid;

                json.filtersCount = 0;
                if (json.filtersCount > 0) {
                    var maxImgWidth = Math.floor(($('#center-content-body').width()-36)/2);
                    var filter = {
                        region: 'south',
                        title: 'Поиск',
                        collapsible: true,
                        collapsed: false,
                        height: 300,
                        html: '<iframe src="<?=STD.($_SERVER['cmsOnlyMode']?'':'/admin')?>/' + json.section.alias + '/form?width='+maxImgWidth+'" width="100%" height="100%" scrolling="auto" frameborder="0" id="form-frame" name="form-frame"></iframe>',
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
					width: <?=$maxLength*7+10?>,
					style: 'font-size: 10px',
					cls: 'subsection-select',
					editable: false,
					listeners: {
						change: function(cmb, newv, oldv){
							var selection = grid.getSelectionModel().getSelection();
							if (selection.length) {
								if (this.getValue()) {
									loadContent(
                                        '<?=PRE?>/' + cmb.getValue() + '/index/id/' + selection[0].data.id + '/' +
                                        'ph/'+Indi.scope.hash+'/aix/'+(selection[0].index + 1)+'/'
                                    );
								}
							} else {
								cmb.reset();
								Ext.MessageBox.show({
									title: '<?=GRID_WARNING_SELECTROW_TITLE?>',
									msg: '<?=GRID_WARNING_SELECTROW_MSG?>',
									buttons: Ext.MessageBox.OK,
									icon: Ext.MessageBox.WARNING
								});
							}
						}
					}
				});
				$('#trail').html(json.trail);
                $('.trail-item-section').hover(function(){
                    $('.trail-siblings').hide();
                    var itemIndex = $(this).attr('item-index');
                    var width = (parseInt($(this).width()) + 27);
                    if ($('#trail-item-' + itemIndex + '-sections ul li').length) {
                        $('#trail-item-' + itemIndex + '-sections').css('min-width', width + 'px');
                        $('#trail-item-' + itemIndex + '-sections').css('display', 'inline-block');
                    }
                }, function(){
                    if (parseInt(event.pageY) < parseInt($(this).offset().top) || parseInt(event.pageX) < parseInt($(this).offset().left)) $('.trail-siblings').hide();
                });
                $('.trail-siblings').mouseleave(function(){
                    $(this).hide();
                });
                myMask = new Ext.LoadMask(grid.getView(), {});
				myMask.show();
                filterChange({noReload: true});
				gridStore.load();
			});
			</script>
            <script>
            Indi.trail.apply(<?=json_encode($this->view->trail->toArray())?>);
            Indi.scope = <?=json_encode($this->view->getScope())?>;
            top.Indi.scope = Indi.scope;
            </script>
        <?if (count($comboFilters)){
                echo implode('', $comboFilters);
                ?><script>Indi.combo.filter = Indi.combo.filter || new Indi.proto.combo.filter(); Indi.combo.filter.run();</script><?
            }?>

		<? $xhtml = ob_get_clean();
		}
        return $xhtml;
    }    
}