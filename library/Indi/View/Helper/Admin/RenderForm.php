<?php
class Indi_View_Helper_Admin_RenderForm extends Indi_View_Helper_Abstract{
	public function renderForm()
	{
		echo $this->view->formHeader();
		foreach ($this->view->trail->getItem()->fields as $field) {
			if(!$field->getForeignRowByForeignKey('elementId')->hidden) echo $this->view->formField($field);
		} 
		
		echo $this->view->formFooter();
		echo '<js>' . $this->view->trail->getItem()->section->javascriptForm . '</js>';
	}

}