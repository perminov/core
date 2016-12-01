<?php
class Notice_Row extends Indi_Db_Table_Row_Noeval {

    /**
     * @var null
     */
    public $row = null;

    /**
     * Constructor
     *
     * @param array $config
     */
    public function __construct(array $config = array()) {

        // Explicitly set table name
        $config['table'] = 'notice';

        // Call parent
        parent::__construct($config);
    }

    public function save() {
        
        // Call parent
        $return = parent::save();

        // Sync keys, mentioned as comma-sepaarted values in `profileId` prop, with entries, nested in `noticeGetter` table
        $this->keys2nested('profileId', 'noticeGetter');

        // Return
        return $return;
    }

    /**
     * Increase counter
     *
     * @param $dir
     * @param $row
     */
    public function counter($dir, $row) {

        // Get recipients
        $to = array();
        foreach ($this->nested('noticeGetter') as $noticeGetterR) {
            $to[$noticeGetterR->profileId] = $noticeGetterR->ar($row);
            $ws[$noticeGetterR->profileId] = strlen($noticeGetterR->criteria)
                ? array_column($to[$noticeGetterR->profileId], 'id')
                : true;
        }

        // Assign `row` prop, that will be visible in compiling context
        $this->row = $row;

        // Unset previously compiled criteria
        unset($this->_compiled['tpl' . ucfirst($dir) . 'Body']);

        // Get header and body
        $header = $this->{'tpl' . ucfirst($dir) . 'Header'};
        $body = $this->compiled('tpl' . ucfirst($dir) . 'Body');

        // Do it using websockets
        Indi::ws($msg = array(
            'type' => 'notice',
            'mode' => 'menu-qty',
            'noticeId' => $this->id,
            'diff' => $dir == 'up' ? 1 : -1,
            'row' => $row->id,
            'to' => $ws,
            'msg' => array(
                'header' => $header,
                'body' => $body
            )
        ));

        // Send notices by email
        $this->_mail($to, $header, $body);

        // Send notices by VK API
        if (Indi::ini('vk')->enabled) $this->_vk($to, $header, $body);
    }

    private function _mail($to, $subject, $body) {

        // If $body arg is empty - return
        if (!$body) return;

        // Foreach notice getter
        foreach ($this->nested('noticeGetter') as $noticeGetterR) {

            // If notice getter should not receive emails - skip
            if ($noticeGetterR->mail == 'n') continue;

            // Init mailer
            $mailer = Indi::mailer();
            $mailer->Subject = $subject;
            $mailer->Body = $body;

            // Add each valid email address to BCC
            foreach(array_column($to[$noticeGetterR->profileId], 'email') as $email)
                if (Indi::rexm('email', $email) && $atLeastOne = true)
                    $mailer->addBCC($email);

            // If at least one valid email found - send notices by email
            if ($atLeastOne) $mailer->send();
        }
    }

    private function _vk($to, $subject, $body) {

        // If $body arg is empty - return
        if (!$body) return;

        // VK uid collection
        $vkA = array();

        // Foreach notice getter
        foreach ($this->nested('noticeGetter') as $noticeGetterR) {

            // If notice getter should not receive emails - skip
            if ($noticeGetterR->vk == 'n') continue;

            // Add each valid VK page address to $vkA array as a key (to prevent duplicates)
            foreach(array_column($to[$noticeGetterR->profileId], 'vk') as $i => $vk)
                if ($vk = Indi::rexm('vk', $vk))
                    $vkA[$vk[1]] = $to[$noticeGetterR->profileId][$i]['title'];
        }

        // If no valid VK pages found - return
        if (!$vkA) return;

        // Foreach found
        foreach ($vkA as $vk => $title) {

            // Build msg
            $msg = $title ? $msg = $title . ', ' . mb_lcfirst($body) : $body;

            // Send
            Vk::send($vk, '<strong>' . $subject . '</strong><br>' . $msg);
        }
    }
}