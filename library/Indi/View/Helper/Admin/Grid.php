<?php
class Indi_View_Helper_Admin_Grid extends Indi_View_Helper_Abstract
{
	public function grid()
	{
		ob_start();?>
<script>
	Ext.onReady(function() {

		var myMask;

			createGrid = function(url) {
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

					grid = Ext.create('Ext.grid.Panel', {
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

					if (currentPanelId) {
						viewport.getComponent(2).remove(currentPanelId);
					}
					viewport.getComponent(2).add(grid);
					currentPanelId = grid.id;

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
		<? $xhtml = ob_get_clean();
        return $xhtml;
    }
}