<?php
class Admin_Row extends Indi_Db_Table_Row {

    /**
     * Function is redeclared for passwords encryption
     *
     * @return int
     */
    public function save(){

        // If password was changed
        if ($this->_modified['password']) {

            // Encrypt the password
            $this->_modified['password'] = Indi::db()->query('
                SELECT PASSWORD("' . $this->_modified['password'] . '")
            ')->fetchColumn(0);
        }

        // Standard save
        return parent::save();
    }

    /**
     * @return array|mixed
     */
    public function validate() {

        // Check 'vk' prop
        if (strlen($this->vk) && $this->isModified('vk')) {

            if (!preg_match('~^https://vk.com/([a-zA-Z0-9_\.]{3,})~', $this->vk, $m))
                $this->_mismatch['vk'] = 'Адрес страницы должен начинаться с https://vk.com/';

            else if (Indi::ini('vk')->enabled) {

                // Try to detect object type
                $response = Vk::type($m[1]);

                // If request was successful
                if ($response['success']) {

                    // Get result
                    $result = $response['json']['response'];

                    // If no result
                    if (!$result) $this->_mismatch['vk'] = 'Этой страницы ВКонтакте не существует';

                    // If result's type is not 'user'
                    else if ($result['type'] != 'user') $this->_mismatch['vk'] = 'Эта страница ВКонтакте не является страницей пользователя';

                    // Else setup custom mismatch message
                } else $this->_mismatch['vk'] = $response['msg'];
            }
        }

        // Call parent
        return $this->callParent();
    }
}