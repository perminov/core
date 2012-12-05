<?php
class Section_Row extends Indi_Db_Table_Row
{
	public function delete($branchId = null){
		// delete controller class if exists
		$this->deleteControllerClassIfExists();

		// standart Db_Table_Row deletion
		parent::delete($branchId);
	}

	public function deleteControllerClassIfExists(){
		$controllersDir = rtrim($_SERVER['DOCUMENT_ROOT'] . '/www', '/') . '/application/controllers/admin/';
		$controllerFile = $controllersDir . ucfirst($this->alias) . 'Controller.php';
		if (file_exists($controllerFile)) {
			unlink($controllerFile);
		}
	}
}