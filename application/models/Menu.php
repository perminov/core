<?php
class Menu extends Indi_Db_Table{
	protected $_rowsetClass = 'Menu_Rowset';
	public function init($uri = '', $parentId = 0, $onlyToggledOn = true, $recursive = true, $level = 0, $order = '`move`', $condition = null)
	{
		$uri = $_SERVER['REQUEST_URI'];
		$treeKeyName = 'menuId';
		$rowset = $this->fetchAll(($parentId ? '`' . $treeKeyName . '` = "' . $parentId . '"' : '`' . $treeKeyName . '` = 0') . ($onlyToggledOn ? ' AND `toggle`="y"' : '') . ($condition ? ' AND ' . $condition : ''), $order);
		$rowset->foreign('staticpageId');
		$rowset = $rowset->toArray();
		$i = 0;
        $dec = 0;
		foreach ($rowset as $row) {
            if($row['foreign']['staticpageId']['toggle'] == 'n') {
                $dec++;
                continue;
            };
			$row['indent'] = indent($level);
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
			$data[] = $row;
			$i++;
		}
        $data[count($rowset) - $dec - 1]['last'] = true;
		$data = array ('table' => $this, 'original' => $data, 'rowClass' => $this->_rowClass, 'stored' => true, 'found' => count($data) - $dec);
		if ($activeItemHere) $data['activeBranch'] = true;
		return new $this->_rowsetClass($data);
	}

}
