<?php
class Indi_Trail_Admin_Item_Scope {

    /**
     * Steps-up index of a trail item, that current Scope object is related to
     *
     * @var int
     */
    protected $_level;

    /**
     * ORDER clause
     *
     * @var
     */
    public $ORDER;

    /**
     * WHERE clause
     *
     * @var
     */
    public $WHERE;

    /**
     * Index of last accessed row
     *
     * @var
     */
    public $aix;

    /**
     * Ids of other bulk-accessed rows
     *
     * @var
     */
    public $lastIds = array();

    /**
     * Json-encoded array of filters, that were used to setup scope bounds
     *
     * @var string
     */
    public $filters = '[]';

    /**
     * Number of found rows within current scope
     *
     * @var
     */
    public $found;

    /**
     * Scope identifier
     *
     * @var
     */
    public $hash;

    /**
     * Keyword, that was used to setup scope bounds
     *
     * @var
     */
    public $keyword;

    /**
     * Json-encoded array of ORDER clauses. Each ORDER clause is represented by a 'property' and 'direction' params
     *
     * @var string
     */
    public $order = '[]';

    /**
     * Number of page within all results, related to current scope
     *
     * @var
     */
    public $page;

    /**
     * Primary WHERE clause. The difference from $this->WHERE is that $this->primary does not contain WHERE clauses,
     * related to filters and keyword search
     *
     * @var
     */
    public $primary;

    /**
     * Index of parent-level row within it's scope (mean parent-level scope)
     *
     * @var
     */
    public $upperAix;

    /**
     * Primary hash of parent-level scope. Used for jumping from current scope to parent scopes
     *
     * @var
     */
    public $upperHash;

    /**
     * Constructor
     *
     * @param $level
     */
    public function __construct($level) {
        $this->_level = $level;
    }

    /**
     * Convert current scope object to array
     *
     * @return array
     */
    public function toArray() {
        $array = (array) $this;
        array_shift($array);
        return $array;
    }

    /**
     * Get the value of filter, identified as $alias, from current scope
     *
     * @param $alias
     * @return mixed
     */
    public function filter($alias = null) {

        // Try to decode json-string, stored in $this->filters
        $filterA =  json_decode($this->filters);

        // If decoding was successful, as decode result is an array
        if (is_array($filterA))

            // If $alias argument is given
            if ($alias) {

                // Try to find an $alias key within each $filterA array, and return it's value if such a key found
                foreach ($filterA as $filterI) if (key($filterI) == $alias) return current($filterI);

            // Else
            } else {

                // Declare $assoc array
                $assoc = array();

                // Build simple associative array
                foreach ($filterA as $filterI) $assoc[key($filterI)] = current($filterI);

                // Return
                return $assoc;
            }

        // Return
        return array();
    }

    /**
     * Apply new values for current object's properties and update current object's version, stored in $_SESSION
     *
     * @param array $data
     */
    public function apply(array $data) {

        // If key 'primary' exists within $data, and if it's value is an array
        // - we convert it to string by imploding by ' AND '
        if (array_key_exists('primary', $data) && is_array($data['primary']))
            $data['primary'] = implode(' AND ', $data['primary']);

        // Setup new values for internal properties
        foreach ($data as $prop => $value) $this->$prop = $value;

        // Update session
        $_SESSION['indi']['admin'][Indi::trail($this->_level)->section->alias][$this->hash]
            = is_array($_SESSION['indi']['admin'][Indi::trail($this->_level)->section->alias][$this->hash])
                ? array_merge($_SESSION['indi']['admin'][Indi::trail($this->_level)->section->alias][$this->hash], $data)
                : $this->toArray();

        // Update current object, for case if at the moment of apply() call there have already been some
        // related scope data in $_SESSION, so we replicate session scope data to current object
        foreach ($_SESSION['indi']['admin'][Indi::trail($this->_level)->section->alias][$this->hash] as $prop => $value)
            $this->$prop = $value;

        if ($this->hash)Indi::trail($this->_level)->section->primaryHash = $this->hash;
        if ($this->aix) Indi::trail($this->_level)->section->rowIndex = $this->aix;

        if ($this->upperHash && $this->upperAix && Indi::trail($this->_level+1)->scope)
            Indi::trail($this->_level+1)->scope->apply(array('hash' => $this->upperHash, 'aix' => $this->upperAix));
    }
}
