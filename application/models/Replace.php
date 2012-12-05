<?php
class Replace extends Indi_Db_Table
{
	/**
	 * Store singleton instance
	 *
	 * @var Territorytypes
	 */
	protected static $_instance = null;

	/**
	 * Store rowset
	 *
	 * @var Locationtypes_Rowset
	 */
	public $rowset = null;	
	
	/**
     * Singleton instance
     * 
     * @return Replaces
     */
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
    
    public function getRowset()
    {
		if (null === $this->rowset) {
    		$this->rowset = $this->fetchAll(null, array('transformId ASC', 'LENGTH(`title`) DESC'));
		}
		return $this->rowset;    	
    }

    public function getArray(){
		$this->getRowset();
    	if ($this->rowset) {
    		$array = $this->rowset->toArray();
    		$final = array();
    		foreach ( $array as $item) {
    			$final[$item['id']] = $item;
    		}
    		return $final;
    	}
    }

	public function	version($string, $transformId = '0'){
		$rowset = $this->getArray();
		foreach ($rowset as $row) {
			if ($row['transformId'] == $transformId) {
				$pattern = '/'.$row['title'].'$/u';
				if (preg_match($pattern,$string)) {
					return preg_replace($pattern, $row['with'], $string);
				}					
			}
		}
		return $string;
	}
}