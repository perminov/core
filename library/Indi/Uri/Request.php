<?php
class Indi_Uri_Request {
	public $module = '';
	public $section = '';
	public $action = '';
	public $params = array();

	public function __construct($module, $section, $action, $params) {
		$this->module = $module;
		$this->section = $section;
		$this->action = $action;
		$this->params = $params;
	}

	public function dispatch() {
		$controllerClassName = ($this->module == 'front' ? '' : $this->module . '_') . ucfirst($this->section) . 'Controller';
		$actionName = $this->action . 'Action';

		if (!class_exists($controllerClassName)) {
			eval('class ' . $controllerClassName . ' extends Indi_Conroller_Action_' . ($this->module == 'front' ? 'Frontend' : 'Admin') . '{}');
		}

		$controller = new $controllerClassName();
		$controller->dispatch($actionName);
	}
}
