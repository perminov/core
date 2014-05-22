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
     * @param $index
     */
    public function __construct($level) {

        $this->_level = $level;

        // Get the section alias, as it is an first-dimension key for getting scope data from $_SESSION
        /*$section = Indi::trail($index)->section->alias;

        // Get the primary hash, as it is an second-dimension key for getting scope data from $_SESSION
        $hash = $index == 0 ? Indi::trail($index)->section->primaryHash : Indi::trail($index-1)->scope->upperHash;

        // If scope data exists within $_SESSION - assing each property as a value of a same
        // property of current *_Scope object
        if (is_array($_SESSION['indi']['admin'][$section][$hash]))
            foreach ($_SESSION['indi']['admin'][$section][$hash] as $prop => $value)
                $this->$prop = $value;*/
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
    public function filter($alias) {

        // Try to decode json-string, stored in $this->filters
        $filterA =  json_decode($this->filters);

        // If decoding was successful, as decode result is an array
        if (is_array($filterA))

            // Try to find an $alias key within each $filterA array, and return it's value if such a key found
            foreach ($filterA as $filterI) if (key($filterI) == $alias) return current($filterI);
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

        if ($this->upperHash && $this->upperAix)
            Indi::trail($this->_level+1)->scope->apply(array('hash' => $this->upperHash, 'aix' => $this->upperAix));
    }
}
