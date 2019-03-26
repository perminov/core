<?php
class Field_Base extends Indi_Db_Table {

    /**
     * Classname for row
     *
     * @var string
     */
    protected $_rowClass = 'Field_Row';

    /**
     * Classname for rowset
     *
     * @var string
     */
    protected $_rowsetClass = 'Field_Rowset';

    /**
     * Array of fields, which contents will be evaluated with php's eval() function
     * @var array
     */
    protected $_evalFields = array('defaultValue', 'filter');

    /**
     * Default order for combo data
     *
     * @var string
     */
    public $comboDataOrder = 'move';

    /**
     * Create Indi_Db_Table_Rowset object with some data, if passed
     *
     * @param array $input
     * @return mixed
     */
    public function createRowset($input = array()) {

        // If no $input arg given - assume empty rowset
        if (!$input) $input['rows'] = array();

        // Get the type of construction
        $index = isset($input['rows']) ? 'rows' : 'data';

        // Prepare data for Indi_Db_Table_Rowset object construction
        $data = array(
            'table'   => $this->_table,
            $index     => is_array($input[$index]) ? $input[$index] : array(),
            'rowClass' => $this->_rowClass,
            'found'=> isset($input['found'])
                ? $input['found']
                : (is_array($input[$index]) ? count($input[$index]) : 0)
        );

        // Provide special 'aliases' construction property to be set up
        if ($input['aliases'] && is_array($input['aliases'])) $data['aliases'] = $input['aliases'];
        else foreach($input[$index] as $item) $data['aliases'][$index == 'rows' ? $item->alias : $item['alias']] = count($data['aliases']);

        // Construct and return Indi_Db_Table_Rowset object
        return new $this->_rowsetClass($data);
    }
}