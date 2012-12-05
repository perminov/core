<?php
class Indi_Trail
{
    /**
     * Store array of Indi_Trail_Item objects
     *
     * @var array
     */
    public $items = array();
    
	/**
	 * Store request params
	 *
	 * @var array
	 */
	public $request = array();

    /**
     * Push a new Indi_Trail_Item object into a $this->items
     * array of these objects and return last pushed item
     *
     * @param int $sectionId
     * @param int $rowIdentifier = null
     * @param string $actionAlias = 'index'
     * @return Indi_Trail_Item object
     */
    public function addItem($sectionId, $rowIdentifier = null, $actionAlias = null, $trail = null, $_sectionId = null)
    {

        $this->items[] = new Indi_Trail_Item($sectionId, $rowIdentifier, $actionAlias, $trail, $_sectionId);
        return end($this->items);
    }
    
    /**
     * Get item from $this->items array, that have index
     * calculated as maximum index decremented by $stepsUp argument
     *
     * @return Indi_Trail_Item object
     */
    public function getItem($stepsUp = null)
    {    
        
        $index = $this->count() - 1 - ($stepsUp ? (int) $stepsUp : 0);
        if ($index >= 0 && $index < $this->count()) {
            return $this->items[$index];
        } else {
            return null;
        }
    }
    
    /**
     * Return count of elements in $this->items
     *
     * @return int
     */
    public function count()
    {
        return count($this->items);
    }
      
    /**
     * Get an array version of trail
     * 
     * @uses Indi_Trail_Item::toArray()
     * @return array
     */
    public function toArray()
    {
        $array = array();
        foreach($this->items as $item) {
            $array[] = $item->toArray();
        }
        end($this->items);
        return $array;
    }
	
	public function getRequestParam($paramName)
	{
		return $this->requestParams[$paramName];
	}
}