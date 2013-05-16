<?php
class Section_Row extends Indi_Db_Table_Row
{
	public function delete(){
		// delete controller class if exists
		$this->deleteControllerClassIfExists();

		// standart Indi_Db_Table_Row deletion
		parent::delete();
	}

	public function deleteControllerClassIfExists(){
		$controllersDir = rtrim($_SERVER['DOCUMENT_ROOT'] . '/www', '/') . $_SERVER['STD'] . '/application/controllers/admin/';
		$controllerFile = $controllersDir . ucfirst($this->alias) . 'Controller.php';
		if (file_exists($controllerFile)) {
			unlink($controllerFile);
		}
	}

    public function getFilters() {
        $filterM = Misc::loadModel('Search');
        $filterRs = $filterM->fetchAll('`sectionId` = "' . $this->id . '" AND `toggle` = "y"', 'move');
        $data['foundRows'] = $filterRs->foundRows;
        $filterA = $filterRs->toArray();
        $fieldIds = array(); foreach ($filterA as $filterI) $fieldIds[] = $filterI['fieldId'];
        $fieldRs = Misc::loadModel('Field')->fetchAll('FIND_IN_SET(`id`, "' . implode(',', $fieldIds) . '")');
        $fieldRs->setForeignRowsByForeignKeys('columnTypeId,elementId');
        for ($i = 0; $i < count($filterA); $i++) {
            foreach ($fieldRs as $fieldR) {
                if ($filterA[$i]['fieldId'] == $fieldR->id) {
                    $filterA[$i]['foreign']['fieldId'] = $fieldR;
                    $data['data'][] = $filterA[$i];
                }
            }
        }
        return $filterM->createRowset($data);
    }
}