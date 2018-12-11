<?php
class Consider_Row extends Indi_Db_Table_Row {

    /**
     * Small hack, for spoofing prop name
     *
     * @param $where
     * @param $fieldR
     * @param $satelliteR
     * @param $satellite
     * @param null $noSatellite
     * @return mixed|void
     */
    protected function _comboDataSatelliteWHERE(&$where, $fieldR, $satelliteR, $satellite, $noSatellite = null) {

        //
        if ($satelliteR->alias == 'relation') $satelliteR->alias = 'entityId';

        // Call parent
        return $this->callParent();
    }
}