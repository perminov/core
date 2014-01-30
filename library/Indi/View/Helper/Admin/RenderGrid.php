<?php
class Indi_View_Helper_Admin_RenderGrid extends Indi_View_Helper_Abstract
{
    public function renderGrid() {
        $comboFilters = array();
        foreach ($this->view->trail->getItem()->filters as $filter)
            if ($filter->foreign['fieldId']->relation || $filter->foreign['fieldId']->columnTypeId == 12)
                $comboFilters[] = $this->view->filterCombo($filter);

        ob_start();?><script>
            Indi.scope = top.Indi.scope = <?=json_encode($this->view->getScope())?>;
            Indi.trail.apply(<?=json_encode($this->view->trail->toArray())?>);
        </script><?

        if (count($comboFilters)){
            echo implode('', $comboFilters);
            ?><script>Indi.combo.filter = Indi.combo.filter || new Indi.proto.combo.filter(); Indi.combo.filter.run();</script><?
        }
        return ob_get_clean();
    }
}