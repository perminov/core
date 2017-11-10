<?php
class Notice_Row extends Indi_Db_Table_Row_Noeval {

    /**
     * Entry, that triggered the notice.
     * Used for building notification message's body, in NoticeGetter_Row::_notify()
     *
     * @var Indi_Db_Table_Row
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

    /**
     * Sync nested `noticeGetter` entries with keys, mentioned in `profileId` field
     *
     * @return int
     */
    public function save() {
        
        // Call parent
        $return = parent::save();

        // Sync keys, mentioned as comma-separated values in `profileId` prop, with entries, nested in `noticeGetter` table
        $this->keys2nested('profileId', 'noticeGetter');

        // Return
        return $return;
    }

    /**
     * Trigger the notice
     *
     * @param Indi_Db_Table_Row $row
     * @param int $diff
     */
    public function trigger(Indi_Db_Table_Row $row, $diff) {

        // Assign `row` prop, that will be visible in compiling context
        $this->row = $row;

        // Foreach getter, defined for current notice
        foreach ($this->nested('noticeGetter') as $noticeGetterR) {

            // Directly setup foreign data for `noticeId` key, to prevent it
            // from being pulled, as there is no need to do that
            $noticeGetterR->foreign('noticeId', $this);

            // Notify
            $noticeGetterR->notify($row, $diff);
        }
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
            Vk::send($vk, $subject . '<br>' . $msg);
        }
    }
}