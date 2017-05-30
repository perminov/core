<?php
class Enumset extends Indi_Db_Table {

    /**
     * Classname for row
     *
     * @var string
     */
    protected $_rowClass = 'Enumset_Row';

    /**
     * Here we override parent's l10n() method, as enumset-model has it's special way of handling translations
     *
     * @param $data
     * @return array
     */
    public function l10n($data) {

        // Pick localized value of `title` prop, if detected that raw value contain localized values
        if (preg_match('/^{"[a-z_A-Z]{2,5}":/', $data['title']))
            $data['title'] = json_decode($data['title'])->{Indi::ini('lang')->admin};

        // Return data
        return $data;
    }
}