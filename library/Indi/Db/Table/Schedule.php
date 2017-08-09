<?php
class Indi_Db_Table_Schedule extends Indi_Db_Table {

    /**
     * Daily time. This can be used to setup working hours, for example since '10:00:00 until '20:00:00'.
     * If daily times are set, schedule will auto-create busy spaces within each separate 24h-hour period,
     * so, if take the above example, periods from 00:00:00 till 10:00:00 and from 20:00:00 till 00:00:00
     * will be set as busy spaces
     *
     * @var array
     */
    protected $_daily = array(
        'since' => false,
        'until' => false
    );

    /**
     * Set/get for $this->_daily
     */
    public function daily($arg1 = false, $arg2 = false) {

        // If $arg1 is either 'since' or 'until'
        if (in($arg1, 'since,until')) {

            // If $arg2 is also given
            if (func_get_args() == 2) {

                // Set daily bound
                $this->_daily[$arg1] = $arg2;

                // Return model itself
                return $this;

            // Else return current value of a daily bound, specified by $arg1
            } else return $this->_daily[$arg1];

        // Else
        } else {

            // Set 'since' and 'until' either as time or false
            if (func_num_args() > 0) $this->_daily['since'] = Indi::rexm('time', $arg1) ? $arg1 : false;
            if (func_num_args() > 1) $this->_daily['until'] = Indi::rexm('time', $arg2) ? $arg2 : false;

            // Return $this->_daily
            return $this->_daily;
        }
    }

    /**
     * Include `_daily` prop
     *
     * @return array
     */
    public function toArray() {

        // Call parent
        $array = parent::toArray();

        // Append `_daily`
        $array['daily'] = $this->_daily;

        // Return
        return $array;
    }
}