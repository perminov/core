<?php
class Indi_View_Helper_IndexRppSelect extends Indi_View_Helper_Abstract
{
	public function indexRppSelect($rppId = null){
		if (!$rppId && $this->view->section->rppId) {
			$rpp = $this->view->section->foreign('rppId')->title;
	 	} else if ($rppId) {
			$rpp = Indi::model('Rpp')->fetchRow('`id` = "' . $rppId . '"')->title;
		} else {
			$rpp = Indi::model('Rpp')->fetchRow(null, 'id ASC')->title;
		}
		$xhtml = '<select class="saas-select" onchange="$(\'#indexLimit\').attr(\'value\', this.value);$(\'#indexParams\').submit()">';
		$rpp = explode(',', $rpp);
		for ($i = 0; $i < count($rpp); $i++ ) $xhtml .= '<option value="' . $rpp[$i] . '"' . ($this->view->indexParams['limit'] == $rpp[$i] ? ' selected' : '') . '>' . $rpp[$i] . '</option>';
        $xhtml .= '</select>';
		return $xhtml;
	}
}