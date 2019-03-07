<?php
class Month_Row extends Indi_Db_Table_Row {

    /**
     * @return int
     */
    public function save(){

        // Build title
        $this->title = $this->foreign('month')->title . ' ' . $this->foreign('yearId')->title;

        // Standard save
        return parent::save();
    }

    /**
     * 'Ym' here mean date('Y-m')
     *
     * @return string
     */
    public function Ym() {
        return $this->foreign('yearId')->title . '-' . $this->month;
    }

    /**
     * Get count of a certain weekdays within the current month
     *
     * @param string $wd
     * @return mixed
     */
    public function wdQty($wd = 'Mon') {

        // Pick 4-digit year from `title`
        list($empty, $y) = explode(' ', $this->title);

        // Get timestamp, related to current month's first day
        $time = strtotime($y . '-' . $this->month . '-01');

        // Weekdays quantity counter
        $qty = 0; $jumpNextWeek = count(ar($wd)) == 1;

        // While we a not jumped to the next month
        while (date('m', $time) == $this->month) {

            // If current timestamp points to weekday same as $wd arg
            if (in(date('D', $time), $wd)) {

                // Increase $qty counter
                $qty ++;

                // Jump to next week
                $time += 60 * 60 * 24 * ($jumpNextWeek ? 7 : 1);

            // Else jump to next day
            } else $time += 60 * 60 * 24;
        }

        // Return
        return $qty;
    }
}