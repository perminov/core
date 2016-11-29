<?php
class Admin_IndexController extends Indi_Controller_Admin {

    /**
     * @param $menu
     * @return mixed|void
     */
    public function adjustMenu(&$menu) {

        // Get array of ids of 1st-level sections
        $sectionIdA = array_column($menu, 'id');

        // If no 'Notice' entity found - return
        if (!Indi::model('Notice', true)) return;
        
        // Get notices
        $_noticeRs = Indi::model('Notice')->fetchAll(array(
            'FIND_IN_SET("' . Indi::admin()->profileId . '", `profileId`)',
            'CONCAT(",", `sectionId`, ",") REGEXP ",(' . im($sectionIdA, '|') . '),"',
            '`toggle` = "y"'
        ));

        // If no notices - return
        if (!$_noticeRs->count()) return;

        // Qtys array, containing quantities of rows, matched for each notice, per each section/menu-item
        $qtyA = array();

        // Foreach notice
        foreach ($_noticeRs as $_noticeR) {

            // Get qty
            $_noticeR->qty = Indi::db()->query('
                SELECT COUNT(`id`)
                FROM `' . Indi::model($_noticeR->entityId)->table().'`
                WHERE ' . $_noticeR->compiled('matchSql')
            )->fetchColumn();

            // Collect qtys for each sections
            foreach (ar($_noticeR->sectionId) as $sectionId)
                $qtyA[$sectionId][] = array(
                    'qty' => $_noticeR->qty ?: 0,
                    'id' => $_noticeR->id,
                    'bg' => $_noticeR->colorHex('bg')
                );
        }

        // Foreach menu item
        foreach ($menu as &$item) {

            // If $item relates to 0-level section, or is not linked to some entity - return
            if (!$qtyA[$item['id']]) continue;

            // Append each qty to menu item's title
            foreach ($qtyA[$item['id']] as $qtyI)
                $item['title'] .= '<span id="menu-qty-' . $qtyI['id']
                    . '" style="background: ' . $qtyI['bg'] . ';'
                    . ($qtyI['qty'] ? '' : 'display: none')
                    . '" class="menu-qty">' . $qtyI['qty'] . '</span>';
        }
    }
}