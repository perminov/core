<?php
class Indi_Cache_Fetcher {
	public $params = array();
	public function __construct($params){
		$this->params = $params;
		$where = preg_match('/^(.*)(ORDER BY|GROUP BY|LIMIT)*/', $this->params[3], $matches);
		$where = explode(' AND ', trim($matches[1]));
		for ($i = 0; $i < count($where); $i++) {
			if (preg_match('/=/', $where[$i])) {
				$where[$i] = explode('=', $where[$i]);
				$this->conds[trim($where[$i][0], '` ')] = trim($where[$i][1], '" ');
			} else if (preg_match('/^(.*)\s*IN\s*\(([^\)]+)\)/', $where[$i], $matches)) {
				$set = explode(',', $matches[2]);
				for ($j = 0; $j < count($set); $j++) {
					$set[$j] = trim($set[$j], '"');
				}
				$this->conds[trim($matches[1], '` ')] = $set;
			}
		}
		if (preg_match('/ORDER BY (.*) *(LIMIT ([0-9]+,)*([0-9]+))/', $this->params[3], $matches)) {
			preg_match('/(ASC|DESC)/', $matches[1], $dir);
			$this->order = array(trim(preg_replace('/(ASC|DESC)/', '', $matches[1]), ' `'), $dir[1]);
		}
	}
	public function fetchAll() {
		return $this->search(true);
	}
	public function fetch() {
		return $this->search();
	}
	public function search($multiple = false) {
		$conds = $this->conds;
		$fields = array_keys($GLOBALS['cache'][ucfirst($this->params[2])]);
		$condKeys = array_keys($this->conds);
		$condVals = array_values($this->conds);
		$indexes = array();
		$conds = array('entityId' => '137', 'alias' => array('cityId', 'countryId'));
		if (is_array($condVals[0])) {
			for ($l = 0; $l < count($condVals[0]); $l++) {
				$tmpIndexes = array_keys($GLOBALS['cache'][ucfirst($this->params[2])][$condKeys[0]], $condVals[0][$l]);
				if ($tmpIndexes !== false) $indexes = array_merge($indexes, $tmpIndexes);
			}
		} else {
			$indexes = array_keys($GLOBALS['cache'][ucfirst($this->params[2])][$condKeys[0]], $condVals[0]);
		}
		$rows = array();
		if (is_array($indexes)) {
			for ($j = 0; $j < count($indexes); $j++) {
				foreach ($fields as $field) {
					$rows[$j][$field] = $GLOBALS['cache'][ucfirst($this->params[2])][$field][$indexes[$j]];
				}
				if ($condKeys[0] != 'id') {
					for ($i = 1; $i < count($condKeys); $i++) {
						if (is_array($condVals[$i])) {
							if (!in_array($rows[$j][$condKeys[$i]], $condVals[$i])) {
								unset($rows[$j]);
							}
						} else {

							if ($rows[$j][$condKeys[$i]] != $condVals[$i]) {
								unset($rows[$j]);
							}
						}
					}
				}
				if ($multiple == false && $rows[$j]) {
					if ($this->params[1] == '*') {
						return $rows[$j];
					} else if (preg_match('/^`([^`]+)`$/', trim($this->params[1]), $col)) {
						foreach ($fields as $field) if ($field != $col[1]) unset($rows[$j][$field]);
						return $rows[$j];
					} else if (preg_match('/^`([a-zA-Z0-9_]+)`\s*(,\s*`([a-zA-Z0-9_]+)`)*$/', trim($this->params[1]), $cols)) {
						$cols = explode(',', trim($cols[0]));
						for ($m = 0; $m < count($cols); $m++) $cols[$m] = trim($cols[$m], ' `');
						foreach ($fields as $field) if (!in_array($field, $cols)) unset($rows[$j][$field]);
					}
				}
			}
			if (!function_exists('order')) {
				function order($item1, $item2){
					$dir = $GLOBALS['sort']['dir'] == 'DESC' ? -1 : 1;
					return $item1[$GLOBALS['sort']['by']] > $item2[$GLOBALS['sort']['by']] ? $dir : -1 * $dir;
				}
			}
			if (count($rows)) {
				if ($this->order[0]) {
					$GLOBALS['sort'] = array('by' => $this->order[0], 'dir' => $this->order[1]);
					usort($rows, 'order');
					unset($GLOBALS['sort']);
				}
				return array_values($rows);
			} else {
				return array();
			}
		}
	}
}
