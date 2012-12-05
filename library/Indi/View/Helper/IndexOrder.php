<?php
class Indi_View_Helper_IndexOrder extends Indi_View_Helper_Abstract{
	public function indexOrder(){
		return $this->view->formSelect('order', $this->view->section->getOrder(), $this->view->indexParams['order'], array(
				'class' => 'saas-select', 
				'onchange' => 'document.getElementById(\'indexOrder\').value=this.value;document.getElementById(\'indexParams\').submit()'
			)
		);
	}
}