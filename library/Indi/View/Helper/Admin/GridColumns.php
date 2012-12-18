<?php
class Indi_View_Helper_Admin_GridColumns extends Indi_View_Helper_Abstract{
	public function gridColumns(){
		$gridFields = $this->view->trail->getItem()->gridFields->toArray();
		$actions    = $this->view->trail->getItem()->actions->toArray();
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
			$buttonIconsPath = $_SERVER['DOCUMENT_ROOT'] . '/core' . '/library/extjs4/resources/themes/images/default/shared/';
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
			$actions = $a;
//			$actions = implode(',', $a);

			// set up dropdown to navigate through related different types of related items
			$sections = $this->view->trail->getItem()->sections->toArray();
			if (count($sections)) {
				$sectionsDropdown = "'->', 'Подраздел:  ', '";
				$sectionsDropdown .= '<span><select style="border: 0;" name="sectionId" onchange=alert(this.value) id="subsectionSelect">';
				$sectionsDropdown .= '<option value="">--Выберите--</option>';
				for ($i = 0; $i < count($sections); $i++)
					$sectionsDropdown .= '<option value="' . $sections[$i]['alias'] . '">' . $sections[$i]['title'] . '</option>';
				$sectionsDropdown .= '</select></span>';
				$sectionsDropdown .= "'";
			}
			$tbarItems = array();
			if ($actions) $tbarItems[] = $actions;
			if ($sectionsDropdown) $tbarItems[] = $sectionsDropdown;

			if ($defaultSortField = $this->view->trail->getItem()->section->getForeignRowByForeignKey('defaultSortField')){
				$this->view->trail->getItem()->section->defaultSortFieldAlias = $defaultSortField->alias;
			}
			return array(
				'columns' => $columns,
				'tbar' => $tbarItems,
				'fields' => $fields,
				'params' => $this->view->trail->requestParams,
				'section' => $this->view->trail->getItem()->section->toArray(),
				'trail' => $this->view->trail(),
				'entity' => $this->view->trail->getItem()->section->getForeignRowByForeignKey('entityId')->title
			);
		}
	}
}