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
                ? array_keys($to[$noticeGetterR->profileId])
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

        // Send mail
        $this->mail($to, $header, $body);
    }

    public function mail($to, $subject, $body) {

        // Foreach notice getter
        foreach ($this->nested('noticeGetter') as $noticeGetterR) {

            // If notice getter should not receive emails - skip
            if ($noticeGetterR->mail == 'n') continue;

            // Init mailer
            $mailer = Indi::mailer();
            $mailer->Subject = $subject;
            $mailer->Body = $body;

            // Add each valid email address to BCC
            foreach($to[$noticeGetterR->profileId] as $email)
                if (Indi::rexm('email', $email))
                    $mailer->addBCC($email);

            // Send
            i($noticeGetterR->foreign('profileId')->title, 'a');
            i($mailer->getBccAddresses(), 'a');
            i($mailer->Body, 'a');
            //$mailer->send();
        }
    }
}