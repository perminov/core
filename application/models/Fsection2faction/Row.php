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
        return $this->foreign('factionId')->title;
    }
    public function getInfoAboutDependentCountsToBeGot(){
    	return Indi::model('DependentCount')->fetchAll('`fsection2factionId` = "' . $this->id . '"');
    }
    public function getInfoAboutDependentRowsetsToBeGot(){
    	return Indi::model('DependentRowset')->fetchAll('`fsection2factionId` = "' . $this->id . '"');
    }	
    public function getInfoAboutForeignRowsToBeGot(){
    	return Indi::model('JoinFk')->fetchAll('`fsection2factionId` = "' . $this->id . '"');
    }
	public function getInfoAboutIndependentCountsToBeGot(){
		return Indi::model('IndependentRowset')->fetchAll('`fsection2factionId` = "' . $this->id . '"');
	}
}