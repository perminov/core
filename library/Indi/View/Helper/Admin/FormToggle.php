<?php
class Indi_View_Helper_Admin_FormToggle extends Indi_View_Helper_Abstract
{
    public function formToggle($name = 'toggle', $texts = 'Да,Нет', $values = 'y,n', $value = 'y', $meta = false, $attrib = '', $add = null)
    {
		if (isset($this->view->row->$name)){
			$value = $this->view->row->$name;
			if(empty($this->view->row->id)) {
				$value = $this->view->trail->getItem()->getFieldByAlias($name)->defaultValue;
			}
			$field = $this->view->trail->getItem()->getFieldByAlias($name);
			if ($field->relation == 6) {
				$enumset = Indi::model('Enumset');
				$array = $enumset->fetchAll('`fieldId` = "' . $field->id . '"', 'title')->toArray();
				$texts = $values = $attrib = array();
				for ($i = 0; $i < count ($array); $i++) {
					$texts[] =  str_replace(',', '&sbquo;', $array[$i]['title']);
					$values[] = $array[$i]['alias'];
					$attrib[] = ' onclick="javascript: ' . (trim($array[$i]['javascript']) ? str_replace('"', '\'', trim($array[$i]['javascript'])) : "void(0)") . '"'; 
				}
				$texts = implode(',' , $texts);
				$values = implode(',' , $values);
			}
		}
		$xhtml = $this->view->formRadios($name, $texts, $values, $value, $meta, $attrib, $add);
		return $xhtml;
    }
}
