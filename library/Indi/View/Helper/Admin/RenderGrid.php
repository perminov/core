<?php
class Indi_View_Helper_Admin_RenderGrid {
    public function renderGrid() {
        $comboFilters = array();
        foreach (Indi::trail()->filters as $filter) {
            if ($filter->foreign('fieldId')->relation || $filter->foreign('fieldId')->columnTypeId == 12)
                $comboFilters[] = Indi::view()->filterCombo($filter);
        }
        ob_start();?><script>
            Indi.trail.apply(<?=json_encode(Indi::trail(true)->toArray())?>);
        </script><?
        if (count($comboFilters)){
            echo '<span style="display: none;">' . implode('', $comboFilters) . '</span>';
            /*?><script>Indi.combo.filter = Indi.combo.filter || new Indi.proto.combo.filter(); Indi.combo.filter.run();</script><?*/
        }
        return ob_get_clean();
    }
}