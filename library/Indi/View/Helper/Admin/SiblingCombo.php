<?php
class Indi_View_Helper_Admin_SiblingCombo extends Indi_View_Helper_Admin_FormCombo{
    public $type = 'sibling';
    public $context = 'top.window';
    public function siblingCombo(){

        $order = Indi::view()->getScope('ORDER');
        $this->comboDataOrderDirection = array_pop(explode(' ', $order));
        $this->comboDataOrderColumn = trim(preg_replace('/ASC|DESC/', '', $order), ' `');
        if (preg_match('/\(/', $order)) $this->comboDataOffset = Indi::uri('aix') - 1;

        ob_start();
        ?><div style="display: none;"><div id="i-action-form-topbar-nav-to-sibling-combo-wrapper"><?
        echo parent::formCombo('i-action-form-topbar-nav-to-sibling-id');
        ?></div></div><?
        return ob_get_clean();
    }

    public function getSelected() {

        // If current row does not exist, combo will use field's default value as selected value
        if ($this->getRow()->id) {
            $selected = $this->getRow()->id;
        }

        return $selected;
    }



    public function getField($name, $tableName) {
        $pseudoFieldR = Indi::model('Field')->createRow();
        $pseudoFieldR->entityId = Indi::trail()->section->entityId;
        $pseudoFieldR->alias = $name;
        $pseudoFieldR->storeRelationAbility = 'one';
        $pseudoFieldR->elementId = 23;
        $pseudoFieldR->columnTypeId = 3;
        //$pseudoFieldR->defaultValue = Indi::trail()->row->id;
        $pseudoFieldR->relation = Indi::trail()->section->entityId;
        $pseudoFieldR->dependency = 'u';
        $pseudoFieldR->satellite = 0;
        $pseudoFieldR->filter = Indi::view()->getScope('WHERE');

        return $pseudoFieldR;//Indi::model('Field')->fetchRow('`id` = "19"');
    }

    public static function createPseudoFieldR($name, $entityId, $filter) {
        $pseudoFieldR = Indi::model('Field')->createRow();
        $pseudoFieldR->entityId = $entityId;
        $pseudoFieldR->alias = $name;
        $pseudoFieldR->storeRelationAbility = 'one';
        $pseudoFieldR->elementId = 23;
        $pseudoFieldR->columnTypeId = 3;
        //$pseudoFieldR->defaultValue = Indi::trail()->row->id;
        $pseudoFieldR->relation = $entityId;
        $pseudoFieldR->dependency = 'u';
        $pseudoFieldR->satellite = 0;
        $pseudoFieldR->filter = $filter;
        return $pseudoFieldR;
    }

    public function getRow(){
        return Indi::trail()->row;
    }

    /**
     * Calculates width for combo. Takes in attention:
     * 1. Indents (if combo is deaing with tree of options)
     * 2. Color-boxes
     * 3. Titles width
     * 4. .i-combo-info visual availability. If can be visible - additional space will be reserved.
     *
     * @return int
     */
    public function getWidth() {
        if (!$this->titleMaxLength) $this->titleMaxLength = 20;

        return ($this->titleMaxIndent ? $this->titleMaxIndent * 3 : 0) +
            ($this->hasColorBox ? 15 : 0) +
            ceil($this->titleMaxLength * 6.5) +
            ($this->params['noLookup'] == 'true' || $this->comboDataRs->enumset ? 0 : 30) +
            20;
    }

    /**
     * Calculates width for keyword input field.
     *
     * @return int
     */
    public function getKeywordFieldWidth() {
        return $this->getWidth() - ($this->params['noLookup'] == 'true' || $this->comboDataRs->enumset ? 0 : 30);

    }

    /**
     * Template for single-value combo
     */
    public function formComboSingle(){
        ob_start();
        ?><div class="i-combo i-combo-<?=$this->type?> i-combo-<?=$this->type?>-single" id="<?=$this->name?>-combo" style="width: <?=$this->getWidth()?>px;" max="<?=$this->titleMaxLength?>"><?
            ?><div class="i-combo-single x-form-text"><?
            ?><table class="i-combo-table"><tr><?
                ?><td class="i-combo-color-box-cell"><?
                    ?><div class="i-combo-color-box-div"><?
                        $this->selected = Indi_View_Helper_Admin_FormCombo::detectColor($this->selected); echo $this->selected['box'];
                    ?></div><?
                ?></td><?
                ?><td class="i-combo-keyword-cell"><?
                    ?><div class="i-combo-keyword-div"><?
                        ?><input class="i-combo-keyword" id="<?=$this->name?>-keyword"<?=$this->selected['style']?> type="text" lookup="<?=$this->name?>" value="<?=str_replace('"', '&quot;', $this->selected['input'] ? $this->selected['input'] : $this->selected['title'])?>" no-lookup="<?=$this->params['noLookup']?>"/><?
                        ?><input type="hidden" id="<?=$this->name?>" value="<?=$this->selected['value']?>" name="<?=$this->name?>"<?=$this->attrs?>/><?
                    ?></div><?
                ?></td><?
                ?><td class="i-combo-info-cell"><?
                    ?><div class="i-combo-info-div"><?
                        ?><table class="i-combo-info" id="<?=$this->name?>-info" page-top="0" page-btm="0" fetch-mode="no-keyword" page-top-reached="<?=$this->pageUpDisabled?>" page-btm-reached="false" satellite="<?=$this->noSatellite() ? '' : $this->satellite->alias?>" changed="false"><tr><?
                            ?><td><span class="i-combo-count" id="<?=$this->name?>-count"></span></td><?
                            ?><td><span class="i-combo-of"><?=I_COMBO_OF?></span></td><?
                            ?><td><span class="i-combo-found" id="<?=$this->name?>-found"></span></td><?
                        ?></tr></table><?
                    ?></div><?
                ?></td><?
                ?><td class="i-combo-trigger-cell"><?
                    ?><div class="i-combo-trigger x-form-trigger" id="<?=$this->name?>-trigger"></div><?
                ?></td><?
            ?></tr></table><?
            ?></div><?
        ?></div><?
        return ob_get_clean();
    }

}