<?php
class Indi_View_Helper_Admin_RenderGrid {
    public function renderGrid() {
        foreach (Indi::trail()->filters as $filter) {
            if ($filter->foreign('fieldId')->relation || $filter->foreign('fieldId')->columnTypeId == 12)
                Indi::view()->filterCombo($filter, 'extjs');
        }
        ob_start();
        ?><script>Indi.trail(true).apply(<?=json_encode(Indi::trail(true)->toArray())?>);</script><?
        return ob_get_clean();
    }
}