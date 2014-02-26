<?php
abstract class Indi_Db_Table_Abstract {
    /**
     * Constants for internal usage
     */
    const NAME             = 'name';
    const ROW_CLASS        = 'rowClass';
    const ROWSET_CLASS     = 'rowsetClass';

    /**
     * Store column name, which is used to detect parent-child relationship between
     * rows within rowset
     *
     * @var string
     */
    public $treeColumn = '';

    /**
     * @var array
     */
    protected $_evalFields = array();

    /**
     * Construct object. Set up table name, DB adapter and tree column name if exists
     *
     * @return void
     */
    public function __construct($config = array()) {

        // Set db table name and db adapter
        $this->_name = strtolower(substr(get_class($this),0,1)) . substr(get_class($this),1);

        // Set fields
        $this->_fields = is_array($config['fields']) ? $config['fields'] : array();

        // Detect tree column name
        $treeColumnName = $this->_name . 'Id';
        $this->treeColumn = $this->fields($treeColumnName) ? $treeColumnName : '';
    }

    /**
     * Fetches one row in an object of type Indi_Db_Table_Row,
     * or returns null if no row matches the specified criteria.
     *
     * @param null|array|string $where
     * @param null|array|string $order
     * @param null|int $offset
     * @return null|Indi_Db_Table_Row object
     */
    public function fetchRow($where = null, $order = null, $offset = null) {
        // Build WHERE and ORDER clauses
        if (is_array($where) && count($where)) $where = implode(' AND ', $where);
        if (is_array($order) && count($order)) $order = implode(', ', $order);

        // Build query, fetch row and return it as an Indi_Db_Table_Row object
        if ($data = Indi::db()->query(
            'SELECT * FROM `' . $this->_name . '`' .
                ($where ? ' WHERE ' . $where : '') .
                ($order ? ' ORDER BY ' . $order : '') .
                ($offset ? ' LIMIT ' . $offset . ',1' : '')
            )->fetch()) {

            // Release memory
            unset($where, $order, $offset);

            // Prepare data for Indi_Db_Table_Row object construction
            $constructData = array(
                'table'    => $this->_name,
                'original' => $data,
            );

            // Release memory
            unset($data);

            // Load class if need
            if (!class_exists($this->_rowClass)) {
                require_once 'Indi/Loader.php';
                Indi_Loader::loadClass($this->_rowClass);
            }

            // Construct and return Indi_Db_Table_Row object
            return new $this->_rowClass($constructData);
        }

        // NULL return
        return null;
    }

    /**
     * Create empty row
     *
     * @param array $input
     * @return Indi_Db_Table_Row object
     */
    public function createRow($input = array()) {

        // Prepare data for construction
        $constructData = array(
            'table'   => $this->_name,
            'original'     => is_array($input['original']) ? $input['original'] : array(),
            'modified' => is_array($input['modified']) ? $input['modified'] : array()
        );

        // Get row class name
        $rowClass = $this->getRowClass();

        // Load row class if need
        if (!class_exists($rowClass)) {
            require_once 'Indi/Loader.php';
            Indi_Loader::loadClass($rowClass);
        }

        // Construct and return Indi_Db_Table_Row object
        return new $rowClass($constructData);
    }

    /**
     * Create Indi_Db_Table_Rowset object with some data, if passed
     *
     * @param array $input
     * @return mixed
     */
    public function createRowset($input = array()) {

        // Prepare data for Indi_Db_Table_Rowset object construction
        $data = array(
            'table'   => $this,
            'data'     => is_array($input['data']) ? $input['data'] : array(),
            'rowClass' => $this->_rowClass,
            'foundRows'=> isset($input['foundRows']) ? $input['foundRows'] : (is_array($input['data']) ? count($input['data']) : 0)
        );

        // Construct and return Indi_Db_Table_Rowset object
        return new $this->_rowsetClass($data);
    }

    /**
     * Inserts new row into db table
     *
     * @param array $data
     * @return string
     */
    public function insert($data) {
        $fields = $this->getDbFields();
        $sql = 'INSERT INTO `' . $this->_name . '` SET ';
        $set = array();
        foreach ($fields as $key => $info) {
            if (!is_null($data[$key])) {
                $set[] = '`' . $key . '` = "' . str_replace('"', '\"', $data[$key]) .'"';
            } else if ($info->foreign['columnTypeId']['type'] == 'TEXT') {
                $set[] = '`' . $key . '` = "' . str_replace('"', '\"', $info->defaultValue) .'"';
            }
        }
        $sql .= count($set) ? implode(', ', $set) : '`id` = NULL';
        Indi::db()->query($sql);
        return(Indi::db()->getPDO()->lastInsertId());
    }

    /**
     * Update one or more db table columns within rows matching WHERE clause, specified by $where param
     *
     * @param array $data
     * @param string $where
     * @return int Number of affected rows
     */
    public function update($data = array(), $where = '') {
        if (is_array($data) && count($data)) {
            $sql = 'UPDATE `' . $this->_name . '` SET ';
            $set = array();
            foreach ($data as $key => $value) {
                $set[] = '`' . $key . '` = "' . str_replace('"', '\"', $value) .'"';
            }
            $sql .= implode(', ', $set);
            if ($where) {
                if (is_array($where) && count($where)) $where = implode(' AND ', $where);
                $sql .= ' WHERE ' . $where;
            }
            return Indi::db()->query($sql);
        } else {
            return -1;
        }
    }

    public function delete($where) {
        $sql = 'DELETE FROM `' . $this->_name . '`';
        if ($where) {
            if (is_array($where) && count($where)) $where = implode(' AND ', $where);
            $sql .= ' WHERE ' . $where;
            return Indi::db()->query($sql);
        }
        die($sql . '<br>No WHERE clause<br>');
    }

    /**
     * @return string
     */
    public function getRowClass()
    {
        return $this->_rowClass;
    }

    /**
     * @return string
     */
    public function getRowsetClass()
    {
        return $this->_rowsetClass;
    }

    /**
     * Returns table information.
     *
     * You can elect to return only a part of this information by supplying its key name,
     * otherwise all information is returned as an array.
     *
     * @param  $key The specific info part to return OPTIONAL
     * @return mixed
     */
    public function info($key = '')
    {
        $info = array(
            self::NAME             => $this->_name,
            self::ROW_CLASS        => $this->getRowClass(),
            self::ROWSET_CLASS     => $this->getRowsetClass(),
        );

        if ($key == '') {
            return $info;
        }

        if ($key ==  'cols') {
            $fields = Indi::db()->query('
                SELECT
                    `f`.`alias`
                FROM
                    `field` `f`,
                    `entity` `e`
                WHERE 1
                    AND `e`.`table` = "' . $this->_name . '"
                    AND `f`.`entityId` = `e`.`id`
                    AND `f`.`columnTypeId` != "0"
            ')->fetchAll();
            foreach ($fields as $field) $cols[] = $field['alias'];
            return $cols;
        }

        return $info[$key];
    }

    /**
     * Gets associative array of Field_Row objects that are presented in
     * current entity structure and that are about fields linked to db table columns,
     * mean there will be no fields such as 'span' and 'upload'
     *
     * @return array
     */
    public function getDbFields() {
        $entityId = Indi::db()->query('SELECT `id` FROM `entity` WHERE `table` = "' . $this->_name . '"')->fetchColumn(0);
        $fieldRs = Indi::model('Field')->fetchAll('`entityId` = "' . $entityId . '" AND `columnTypeId` !="0"', 'move');
        $fieldRs->setForeignRowsByForeignKeys('columnTypeId');
        $fields = array(); foreach ($fieldRs as $fieldR) $fields[$fieldR->alias] = $fieldR;
        return $fields;
    }
}