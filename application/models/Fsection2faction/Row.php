<?php
class Fsection2faction_Row extends Indi_Db_Table_Row
{
    /**
     * Get title for fsection2faction row
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getForeignRowByForeignKey('factionId')->title;
    }
    public function getInfoAboutDependentCountsToBeGot(){
    	return Misc::loadModel('DependentCount')->fetchAll('`fsection2factionId` = "' . $this->id . '"');
    }
    public function getInfoAboutDependentRowsetsToBeGot(){
    	return Misc::loadModel('DependentRowset')->fetchAll('`fsection2factionId` = "' . $this->id . '"');
    }	
    public function getInfoAboutForeignRowsToBeGot(){
    	return Misc::loadModel('JoinFk')->fetchAll('`fsection2factionId` = "' . $this->id . '"');
    }
	public function getInfoAboutIndependentCountsToBeGot(){
		return Misc::loadModel('IndependentRowset')->fetchAll('`fsection2factionId` = "' . $this->id . '"');
	}
}