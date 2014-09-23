<?php
class Indi_View_Helper_Admin_FormUpload {
    public function formUpload($name = null) {
        if ($abs = Indi::trail()->row->abs($name))
            Indi::trail()->row->view($name, Indi::trail()->row->file($name));
    }
}
