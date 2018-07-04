<?php
class Month extends Indi_Db_Table {

    /**
     * Classname for row
     *
     * @var string
     */
    public $_rowClass = 'Month_Row';

    /**
     * @var string
     */
    public $comboDataOrder = 'month';

    /**
     * Return an instance of Month_Row class, representing current month within current year
     *
     * @param int $shift Shift in months, can be negative
     * @return Month_Row|null
     */
    public function now($shift = 0) {

        // Build date in 'Y-m-d' format, using $shift argument, if given
        $date = date('Y-m-d', $shift ? mktime(0, 0, 0, date('m') + $shift, date('d'), date('Y')) : time());

        // Extract 4-digit year and 2-digit month from a current date
        list($y, $m) = explode('-', $date);

        // If there is no such a year entry found
        if (!$yearR = Indi::model('Year')->fetchRow('`title` = "' . $y . '"')) {

            // Create it
            $yearR = Indi::model('Year')->createRow()->assign(array('title' => $y));
            $yearR->save();
        }


        // If there is no such a month entry found
        if (!$monthR = $this->fetchRow('`month` = "' . $m . '" AND `yearId` = "' . $yearR->id . '"')) {

            // Create it
            $monthR = $this->createRow()->assign(array('month' => $m, 'yearId' => $yearR->id));
            $monthR->save();
        }

        // Return month row
        return $monthR;
    }

    /**
     * Return Month_Row instance, representing previous month
     *
     * @return Month_Row|null
     */
    public function was() {
        return $this->now(-1);
    }

    /**
     * Get an entry within `month` table, that represents current (or given by $date arg) month
     * as an instance of stdClass object. If such entry does not yet exists - it will be created
     *
     * @static
     * @param $date string Date in php 'Y-m-d' format
     * @return stdClass
     */
    public static function o($date = null) {

        // Extract 4-digit year and 2-digit month from a current date
        list($y, $m) = explode('-', $date ?: date('Y-m-d'));

        // Get year
        $yearO = Year::o($y);

        // If current month not exists within `month` table
        if (!$monthI = Indi::db()->query('
            SELECT * FROM `month` WHERE `month` = "' . $m . '" AND `yearId` = "' . $yearO->id . '"
        ')->fetch()) {

            // Build month title
            $title = Indi::db()->query('
                SELECT `es`.`title`
                FROM `enumset` `es`, `field` `f`, `entity` `e`
                WHERE 1
                  AND `es`.`alias` = "'. $m . '"
                  AND `f`.`id` = `es`.`fieldId`
                  AND `f`.`alias` = "month"
                  AND `f`.`entityId` = `e`.`id`
                  AND `e`.`table` = "month"
              ')->fetchColumn() . ' ' . $yearO->title;

            // Create month entry
            Indi::db()->query('
                INSERT INTO `month`
                SET
                  `month` = "' . $m . '",
                  `title` = "' . $title . '",
                  `yearId` = "'. $yearO->id . '"
            ');

            // Get it's id
            $monthI = array(
                'id' => Indi::db()->getPDO()->lastInsertId(),
                'yearId' => $yearO->id,
                'month' => $m,
                'title' => $title
            );
        }

        // Return month entry as an instance of stdClass object
        return (object) $monthI;
    }
}