<?php
class Menu_Rowset extends Indi_Db_Table_Rowset {
	public $activeBranch = false;
	public function __construct(array $config)
	{
		if (isset($config['activeBranch'])) {
			$this->activeBranch = $config['activeBranch'];
		}
		parent::__construct($config);
	}

	public function active($level = 1, $rowset = null, $currentLevel = 1) {
		if ($level == 0) return $this;
		if ($rowset == null) $rowset = $this;
		foreach ($rowset as $item) {
			if ($item->active) return ($level == $currentLevel ? $item->children : $this->active($level, $item->children, $currentlevel + 1));
		}
	}
}