<?php
abstract class Indi_Db_Table_Abstract {
    /**
     * Constants for internal usage
     */
    const NAME             = 'name';
    const ROW_CLASS        = 'rowClass';
    const ROWSET_CLASS     = 'rowsetClass';

    /**
     * Default Indi_Db object.
     *
     * @var Indi_Db
     */
    protected static $_defaultDb;

    /**
     * Construct object. Set up table name and DB adapter
     *
     * @return void
     */
    public function __construct() {
        $this->_name = strtolower(substr(get_class($this),0,1)) . substr(get_class($this),1);
        $this->_db = self::$_defaultDb;
    }

    /**
     * Fetches one row in an object of type Indi_Db_Table_Row,
     * or returns null if no row matches the specified criteria.
     *
     * @param string|array $where  OPTIONAL An SQL WHERE clause.
     * @param string|array $order  OPTIONAL An SQL ORDER clause.
     * @return Indi_Db_Table_Row|null
     */
    public function fetchRow($where = null, $order = null)
    {
        if (is_array($where) && count($where)) $where = implode(' AND ', $where);
        if (is_array($order) && count($order)) $order = implode(', ', $order);
        if ($data = self::$_defaultDb->query('SELECT * FROM `' . $this->_name . '`' . ($where ? ' WHERE ' . $where : '') . ($order ? ' ORDER BY ' . $order : ''))->fetch()) {
            $constructData = array(
                'table'    => $this,
                'original' => $data,
            );
            $rowClass = $this->getRowClass();
            if (!class_exists($rowClass)) {
                require_once 'Indi/Loader.php';
                Indi_Loader::loadClass($rowClass);
            }
            return new $rowClass($constructData);
        }
        return null;
    }

    /**
     * Create empty row
     *
     * @param array $input
     * @return Indi_Db_Table_Row object
     */
    public function createRow($input = array()) {
        $original['id'] = null;
        foreach ($this->info('cols') as $col) {
            $original[$col] = null;
            if (isset($input[$col])) $modified[$col] = $input[$col];
        }
        $constructData = array(
            'table'   => $this,
            'original'     => $original,
            'modified' => is_array($modified) ? $modified : array()
        );
        $rowClass = $this->getRowClass();
        if (!class_exists($rowClass)) {
            require_once 'Indi/Loader.php';
            Indi_Loader::loadClass($rowClass);
        }
        return new $rowClass($constructData);
    }

    public function createRowset($input = array()) {
        $data = array(
            'table'   => $this,
            'data'     => $input['data'],
            'rowClass' => $this->_rowClass,
            'foundRows'=> isset($input['foundRows']) ? $input['foundRows'] : count($input['data'])
        );
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
        self::$_defaultDb->query($sql);
        return(self::$_defaultDb->getPDO()->lastInsertId());
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
            return self::$_defaultDb->query($sql);
        } else {
            return -1;
        }
    }

    public function delete($where) {
        $sql = 'DELETE FROM `' . $this->_name . '`';
        if ($where) {
            if (is_array($where) && count($where)) $where = implode(' AND ', $where);
            $sql .= ' WHERE ' . $where;
            return self::$_defaultDb->query($sql);
        }
        die($sql . '<br>No WHERE clause<br>');
    }



    /**
     * Sets the default Indi_Db for all Indi_Db_Table objects.
     *
     * @param  $db Indi_Db object
     * @return void
     */
    public static function setDefaultAdapter($db = null)
    {
        self::$_defaultDb = $db;
    }

    /**
     * Gets the default Indi_Db for all Indi_Db_Table objects.
     *
     * @return Indi_Db or null
     */
    public static function getDefaultAdapter()
    {
        return self::$_defaultDb;
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
     * Gets the Indi_Db_Adapter_Abstract for this particular Indi_Db_Table object.
     *
     * @return Indi_Db_Adapter_Abstract
     */
    public function getAdapter()
    {
        return $this->_db;
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
    public function info($key = null)
    {
        $info = array(
            self::NAME             => $this->_name,
            self::ROW_CLASS        => $this->getRowClass(),
            self::ROWSET_CLASS     => $this->getRowsetClass(),
        );

        if ($key === null) {
            return $info;
        }

        if ($key ==  'cols') {
            $fields = $this->_db->query('
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
        $entityId = $this->_db->query('SELECT `id` FROM `entity` WHERE `table` = "' . $this->_name . '"')->fetchColumn(0);
        $fieldRs = Misc::loadModel('Field')->fetchAll('`entityId` = "' . $entityId . '" AND `columnTypeId` !="0"', 'move');
        $fieldRs->setForeignRowsByForeignKeys('columnTypeId');
        $fields = array(); foreach ($fieldRs as $fieldR) $fields[$fieldR->alias] = $fieldR;
        return $fields;
    }
}