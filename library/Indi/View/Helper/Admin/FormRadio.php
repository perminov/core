<?php
class Indi_View_Helper_Admin_FormRadio {
    public function formRadio($name = 'toggle', $texts = 'Да,Нет', $values = 'y,n', $value = 'y', $meta = false, $attrib = '', $add = null)
    {
		if (isset(Indi::view()->row->$name)){
			$value = Indi::view()->row->$name;
			$field = Indi::trail()->model->fields($name);
			if(empty($value)) {
				$value = $field->defaultValue;
                Indi::$cmpTpl = $value; eval(Indi::$cmpRun); $value = Indi::cmpOut();
            }
			if ($field->relation == 6) {
				$enumset = Indi::model('Enumset');
				$array = $enumset->fetchAll('`fieldId` = "' . $field->id . '"', '`move`')->toArray();
				$texts = $values = array();
				for ($i = 0; $i < count ($array); $i++) {
					$texts[] =  str_replace(',', '&sbquo;', $array[$i]['title']);
					$values[] =  $array[$i]['alias'];
					$attrib[] = ' onclick="javascript: ' . (trim($array[$i]['javascript'] . '' . $field->javascript) ? str_replace('"', '\'', trim($array[$i]['javascript'] . '' . $field->javascript)) : "void(0)") . '"'; 
				}
				$texts = implode(',' , $texts);
				$values = implode(',' , $values);
			} else {
			    $data  = Indi::view()->row->getComboData($name);
                $texts = array();
                $values = array();
                $key = $data->enumset ? 'alias' : 'id';
                foreach ($data as $item) {
                    $texts[] = $item->title;
                    $values[] = $item->$key;
                }
				$texts = implode(',', $texts);
				$values = implode(',', $values);
			}
		}
		$rowsHeightLimit = 10;
		if (count($data) > $rowsHeightLimit) $xhtml .= '<div style="overflow-y: scroll; height: ' . (17 * $rowsHeightLimit) . 'px;">';
		$xhtml .= Indi::view()->formRadios($name, $texts, $values, $value, $meta, $attrib, $add);
		if (count($data) > $rowsHeightLimit) $xhtml .= '</div>';
		return $xhtml;
    }
}
