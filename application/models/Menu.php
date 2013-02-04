<?php
class Menu extends Indi_Db_Table{
	protected $_rowsetClass = 'Menu_Rowset';
	public function init($uri = '', $parentId = 0, $onlyToggledOn = false, $recursive = true, $level = 0, $order = null, $condition = null)
	{
		$uri = $_SERVER['REQUEST_URI'];
		$treeKeyName = 'menuId';
		$rowset = $this->fetchAll(($parentId ? '`' . $treeKeyName . '` = "' . $parentId . '"' : '`' . $treeKeyName . '` = 0') . ($onlyToggledOn ? ' AND `toggle`="y"' : '') . ($condition ? ' AND ' . $condition : ''), $order);
		$rowset->setForeignRowsByForeignKeys('staticpageId');
		$rowset = $rowset->toArray();
		$i = 0;
		foreach ($rowset as $row) {
			$row['indent'] = Misc::indent($level);
			if ($recursive) {
				$row['children'] = $this->init($uri, $row['id'], $onlyToggledOn, $recursive, $level+1, $order, $condition);
			}
			if ($row['linked'] == 'n') {
				$row['href'] = $row['url'];
			} else if ($row['foreign']['staticpageId']['alias'] == 'index'){
				$row['href'] = '/';
			} else {
				$row['href'] = '/' . $row['foreign']['staticpageId']['alias'] . '/';
			}
			if (trim($row['href'], '/') == trim($uri, '/') || $row['children']->activeBranch) {
				$row['active'] = true;
				$activeItemHere = true;
			}
			if ($i == count($rowset) - 1) $row['last'] = true;
			$data[] = $row;
			$i++;
		}
		$data = array ('table' => $this, 'data' => $data, 'rowClass' => $this->_rowClass, 'stored' => true, 'foundRows' => count($data));
		if ($activeItemHere) $data['activeBranch'] = true;
		return new $this->_rowsetClass($data);
	}

}
