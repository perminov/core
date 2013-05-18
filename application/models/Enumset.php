<?php
class Enumset extends Indi_Db_Table
{
    protected $_rowClass = 'Enumset_Row';

	public function getOptions($table, $fieldId, $existing = false)
	{
		if ($field = Misc::loadModel('Field')->fetchRow('`id` = "' . $fieldId . '"')) {
			if ($options = $this->fetchAll('`fieldId` = "' . $fieldId . '"', 'move')) {
				if ($existing) {
					$distinct = self::$_defaultDb->query('SELECT GROUP_CONCAT(DISTINCT `' . $field->alias . '`) as `distinct` FROM `' . $table . '`')->fetch();
					$distinct = explode(',', $distinct['distinct']);
				}
				foreach ($options as $option) {
					if ($existing) {
						if (in_array($option->alias, $distinct)) {
							$data[$option->alias] = $option->title;
						}
					} else {
						$data[$option->alias] = $option->title;
					}
				}
			}
		}
		return $data;
	}
}