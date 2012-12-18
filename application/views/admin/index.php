<?if($this->trail->requestParams['section'] == 'index' && $this->trail->requestParams['action'] == 'index'){?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>Border Layout Example</title>
	<link rel="stylesheet" type="text/css" href="/library/extjs4/resources/css/ext-all.css" />
	<script type="text/javascript" src="/library/extjs4/ext-all.js"></script>
	<script type="text/javascript" src="/js/jquery-1.6.1.min.js"></script>
	<?
	$english = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
	$russian = array('Воскресенье', 'Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота');
	?>
</head>
<body>
<style>
	*{color: #04408C;}
	.no-icon {
		display : none;
		background-image:url('/library/extjs4/resources/images/default/s.gif') !important;
		width: 0;
	}
	.mygrid .x-grid-row-selected.x-grid-row-focused td.x-grid-cell, .x-grid-row-selected.x-grid-row-focused .x-grid-rowwrap-div {
		background-color: #dfffdf !important;
		border-top: 1px solid #dfffdf;
		border-bottom: 1px solid #e0eeff;
	}
	.mygrid .x-grid-row-over td.x-grid-cell, .x-grid-row-over .x-grid-rowwrap-div {
		background-color: #ecffeb !important;
		border-top: 1px solid #ecffeb;
		border-bottom: 1px solid #e0eeff;
	}
	.x-panel.menu .x-grid-cell.root-item{
		background-color: #D9E7F8!important;
		border-bottom: 1px solid #C8D6E7;
	}
	.x-panel.menu .x-grid-cell.cycle-0{
		border-bottom: 1px solid #e0eeff;
	}
	.x-panel.menu .x-grid-cell.cycle-1{
		border-bottom: 1px solid #e0eeff;
	}
	.x-panel.menu .x-grid-row-over.x-grid-tree-node-leaf td div{
		background-color: #ecffeb !important;
	}

	.x-panel.menu .x-grid-row-selected.x-grid-tree-node-leaf td div{
		background-color: #dfffdf !important;
	}
	.add {
		background-image: url('/core/library/extjs4/resources/themes/images/default/shared/add.gif') !important;
	}
	.form{
		background-image: url('/core/library/extjs4/resources/themes/images/default/shared/form.gif') !important;
	}
	.delete{
		background-image: url('/core/library/extjs4/resources/themes/images/default/shared/delete.gif') !important;
	}
	.subsection-select .x-form-text{font-size:11px !important; height: 19px !important; line-height: 14px !important;}
	.subsection-select .x-form-trigger-wrap{margin-top: 1px;}
	.subsection-select .x-form-trigger{height: 19px;}
	.x-boundlist ul li {font-size: 11px;}
	.x-btn-default-toolbar-small-over{
		-moz-border-radius: 0px !important;
		-webkit-border-radius: 0px !important;
		-o-border-radius: 0px !important;
		-khtml-border-radius: 0px !important;
		border-radius: 0px !important;
	}
	#trail a{text-decoration: none;}
	#trail a:hover{color: #99BCE8}
	.top-center .x-panel-body{
		background-color: #DFE8F6 !important;
	}
</style>
	<?foreach ($this->menu as $item) if ($item->sectionId) $children[$item->sectionId][] = $item;?>
<script>
	Ext.require(['*']);

	Ext.onReady(function() {
		var store = Ext.create('Ext.data.TreeStore', {
			root: {
				expanded: true,
				children: [
					<?foreach($this->menu as $item) if (!$item->sectionId){?>
						{
							text: "<?=$item->title?>",
							<?if(count($children[$item->id])){?>
							expanded: false,
							cls: 'root-item',
							children: [
								<?$i=0;foreach($children[$item->id] as $child){?>
									{text: '<?=$child->title?>', iconCls: 'no-icon', leaf: true, cls: 'cycle-<?=$i%2?>', value: '/admin/<?=$child->alias?>/'},
									<?$i++;}?>
							]
							<?}?>
						},
						<?}?>
				]
			}
		});

		var treeview = Ext.create('Ext.tree.Panel', {
			store: store,
			rootVisible: false,
			title: 'Меню',
			useArrows: true,
			border: 1,
			region: 'west',
			width: 200,
			collapsible: true,
			cls: 'menu',
			padding: '41 0 0 0',
			listeners: {
				itemclick: function(view, rec, item, index, eventObj) {
					if(rec.get('leaf') == false) {
						if (rec.data.expanded) rec.collapse(); else rec.expand();
					} else {
						createGrid(rec.raw.value);
					}
				}
			}

		})

		var viewport = Ext.create('Ext.Viewport', {
			layout: {
				type: 'border',
				padding: 5
			},
			defaults: {
				split: true
			},
			items: [treeview,{
				region: 'center',
				defaults: {
					split: true
				},
				border: 0,
				layout: {
					type: 'border',
					padding: '0 0 0 0'
				},
				items: [{
					region: 'north',
					html: '<div style="display: block;">' +
							'<div style="float: right; text-align: right;">' +
							'<?=str_replace($english, $russian, $this->date) ?>' +
							'</div>' +
							'<div style="text-align: left; margin-bottom: 5px;">' +
							'<?=$this->admin?>' +
							'</div>' +
							'<div style="height: 17px; border: 1px solid #99BCE8; background-color: white; padding-top: 0px; padding-left: 2px; " id="trail">' +
							'<?=$this->trail()?>' +
							'</div>'+
					'</div>',
					height: 36,
					cls: 'top-center',
					border: 0
				}, {
					region: 'center',
					id: 'center-content'
				}]
			}]
		});

		var myMask;

		var createGrid = function(url) {
			//console.log(url);
			$.post(url, function(json){
				var gridStore = Ext.create('Ext.data.Store', {
					fields: json.fields,
					method: 'POST',
					remoteSort: true,
					sorters:  (json.section.defaultSortField ? [{
						property : json.section.defaultSortFieldAlias,
						direction: json.section.defaultSortDirection
					}] : []),
					proxy:  new Ext.data.HttpProxy({
						url: '/admin/' + json.params.section + '/index/' + (json.params.id ? 'id/' + json.params.id + '/' : '') + 'json/1/',  // works
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

				var grid = Ext.create('Ext.grid.Panel', {
					multiSelect: false,
					columns: json.columns,
					title: json.section.title,
					loadMask: true,
					region: "center",
					closable: true,
					store: gridStore,
					cls: 'mygrid',
					tbar: eval('['+json.tbar+']'),
					border: 1,
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
				viewport.getComponent(2).add(grid);
				eval(json.section.javascript);
				var transformed = Ext.create('Ext.form.field.ComboBox', {
					valueField: 'sectionId',
					hiddenName: 'sectionId',
					typeAhead: false,
					transform: 'subsectionSelect',
					width: 135,
					style: 'font-size: 10px',
					cls: 'subsection-select',
					editable: false,
					listeners: {
						change: function(cmb, newv, oldv){
							var selection = grid.getSelectionModel().getSelection();
							if (selection.length) {
								if (this.getValue()) {
									createGrid('/admin/' + cmb.getValue() + '/index/id/' + selection[0].data.id + '/');
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
//				console.log(json);
			}, 'json')
		}
	});
</script>
</body>
</html>

<?} else if ($this->jsonData){
	echo $this->jsonData;
} else {
	echo json_encode($this->gridColumns());
}?>