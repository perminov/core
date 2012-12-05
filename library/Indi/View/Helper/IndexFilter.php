<?php
class Indi_View_Helper_IndexFilter extends Indi_View_Helper_Abstract{
	public function indexFilter($alias, $value = 0, $attribs = array()) {
		if (!$alias) return '';
		$filter = $this->view->section->getFilter($alias);
		$title = $filter->title . ':';
		$alias = $filter->getForeignRowByForeignKey('fieldId')->alias;
		$attribs['onchange'] = 'document.getElementById(\'indexWhere[' . $alias . ']\').value=this.value;document.getElementById(\'indexParams\').submit();';
		if (!$value) $value = $_SESSION['indexParams'][$this->view->section->alias]['where'][$alias];
		$options = $filter->getOptions();
	    $control .= $this->view->formSelect($alias, $options, $value, $attribs);
		if (!is_array($this->view->filters)) {
			$this->view->filters = array($alias => $options);
		} else {
			$this->view->filters[$alias] = $options;
		}
		return array('title' => $title, 'control' => $control);
	}
}