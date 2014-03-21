<?php
class Indi_Trail_Admin_Item
{
	/**
	 * Store number of fields that associated with a ExtJs grid, in case if
	 * there is an entity attached to section, and the current action is 'index'
	 *
	 * @var Indi_Db_Table_Rowset
	 */
	public $gridFields = null;

	/**
	 * Store number of fields that are unaccessible in grid and form
	 *
	 * @var array
	 */
	public $disabledFields = array('save' => array(), 'form' => array());

    public function __get($property) {
        if ($property == 'model' && $this->section->entityId)
            return Indi::model($this->section->entityId);
    }

    /**
     * Set up all internal properties
     *
     * @param Indi_Db_Table_Row $sectionR
     */
    public function __construct($sectionR)
    {
        // Setup $this->section
        $config = array();
        $dataTypeA = array('original', 'temporary', 'compiled', 'foreign');
        foreach ($dataTypeA as $dataTypeI) $config[$dataTypeI] = $sectionR->$dataTypeI();
        $this->section = Indi::model('Section')->createRow($config);

        // Setup $this->actions
        foreach ($sectionR->nested('section2action') as $section2actionR)
            $actionA[] = $section2actionR->foreign('actionId')->original();
        $this->actions = Indi::model('Action')->createRowset(array('original' => $actionA));

        // Setup $this->sections
        $this->sections = $sectionR->nested('section');

        // If current trail item will be a first item
        if (count(Indi_Trail_Admin::$items) == 0) {

            // Setup fields
            $this->fields = $this->model->fields()->foreign(array(
                'elementId' => array(
                    'nested' => 'possibleElementParam'
                ),
                'columnTypeId' => array()
            ))->nested('param')->params();


            // Setup filters
            $this->filters = $sectionR->nested('search');

            // Setup action
            foreach ($this->actions as $actionR)
                if ($actionR->alias == Indi::uri('action'))
                    $this->action = $actionR;

            // Set fields, that will be used as grid columns in case if current action is 'index'
            if (Indi::uri('action') == 'index') {
                $gridFieldA = array();
                foreach ($sectionR->nested('grid') as $gridR) {
                    foreach ($this->fields as $fieldR) {
                        if ($gridR->fieldId == $fieldR->id) {
                            $gridFieldI = $fieldR->original();
                            if ($gridR->alterTitle) $gridFieldI['title'] = $gridR->alterTitle;
                            $gridFieldA[] = $gridFieldI;
                        }
                    }
                }
                $this->gridFields = Indi::model('Field')->createRowset(array('original' => $gridFieldA));
            }

            // Setup disabled fields
            foreach ($sectionR->nested('disabledField') as $disabledFieldR) {
                foreach ($this->fields as $fieldR) {
                    if ($disabledFieldR->fieldId == $fieldR->id) {
                        $this->disabledFields['save'][] = $fieldR->alias;
                        if ($disabledFieldR->displayInForm) $this->disabledFields['form'][] = $fieldR->alias;
                    }
                }
            }

        } else {

            // Setup action as 'index'
            foreach ($this->actions as $actionR) if ($actionR->alias == 'index') $this->action = $actionR;
        }
    }

    public function row($index){
        if ($index == 0) {
            if (Indi::uri('id') && Indi::uri('action') != 'index') {
                // primaryWHERE
                $this->row = $this->model->fetchRow('`id` = "' . (int) Indi::uri('id') . '"');
                if (!$this->row) die('Нет доступа к этой записи');
            } else {
                $this->row = $this->model->createRow();
                for ($i = 1; $i < count(Indi_Trail_Admin::$items) - 1; $i++) {
                    $connector = Indi_Trail_Admin::$items[$i-1]->section->parentSectionConnector && $i == 1
                        ? Indi_Trail_Admin::$items[$i-1]->section->foreign('parentSectionConnector')->alias
                        : Indi_Trail_Admin::$items[$i]->model->name() . 'Id';

                    if ($this->model->fields($connector))
                        $this->row->$connector = $_SESSION['indi']['admin']['trail']['parentId'][$this->section->sectionId];
                }
            }
        } else if ($this->section->sectionId) {
            $id = Indi::uri('action') == 'index'
                ? (int) Indi::uri('id')
                : (int) Indi_Trail_Admin::$items[$index-1]->row->{$this->model->name() . 'Id'};
            $this->row = $this->model->fetchRow('`id` = "' . $id . '"');
        }
    }

    /**
     * Get array version of internal variables
     *
     * @return array
     */
    public function toArray()
    {
        $array = array();
        if ($this->section) {
            $array['section'] = $this->section->toArray();
            $array['section']['defaultSortFieldAlias'] = $this->section->foreign('defaultSortField')->alias;
        }
        if ($this->sections) $array['sections'] = $this->sections->toArray();
        if ($this->action) $array['action'] = $this->action->toArray();
        if ($this->actions) $array['actions'] = $this->actions->toArray();
        if ($this->row) $array['row'] = $this->row->toArray();
        if ($this->model) $array['model'] = $this->model->toArray();
        if ($this->fields) $array['fields'] = $this->fields->toArray();
        if ($this->gridFields) $array['gridFields'] = $this->gridFields->toArray();
        if ($this->filters) $array['filters'] = $this->filters->toArray(true);
        return $array;
    }
}