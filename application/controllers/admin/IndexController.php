<?php
class Admin_IndexController extends Indi_Controller_Admin {

    /**
     * @param $menu
     * @return mixed|void
     */
    public function adjustMenu(&$menu) {

        // If admin is not a teacher - return
        if (!Indi::admin()->alternate == 'teacher') return;

        // Find 'myTalks' menu item
        foreach ($menu as &$item) if ($item['alias'] == 'myTalks') {

            // Get number of messages, unread by teacher
            $urQty = Indi::db()->query('
                SELECT SUM(`teacherUnread`) FROM `talk` WHERE `teacherId` = "' . Indi::admin()->id . '"
            ')->fetchColumn();

            // Append it to menu item's title
            $item['title'] .= '<span id="urQty-' . $item['alias'] . '" class="urQty '
                . ($urQty ? '' : 'urQty-none') . '">' . $urQty . '</span>';
        }
    }
}