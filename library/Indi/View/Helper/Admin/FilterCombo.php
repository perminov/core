<?php
class Indi_View_Helper_Admin_FilterCombo extends Indi_View_Helper_Admin_FormCombo{
    /**
     * This var is used for html elements css class names building
     * @var string
     */
    public $type = 'filter';

    /**
     * Builds the combo for grid filter
     *
     * @param Search_Row $filter
     * @return string
     */
    public function filterCombo(Search_Row $filter){
        // Here we create a shared *_Row object, that will be used by all filters, that are presented in current grid.
        // We need it bacause of a satellites. If we define a default value for some combo, and that combo is a satellite
        // for another combo - another combo's initial data will depend on satellite value, so the shared row is the place
        // there dependent combo can get that value.
        if (!$this->view->filtersSharedRow) $this->view->filtersSharedRow = $this->view->trail->getItem()->model->createRow();

        $this->filter = $filter;
        $this->where = $this->filter->filter;
        $this->ignoreTemplate = $this->filter->ignoreTemplate;
        ob_start(); echo parent::formCombo($filter->foreign['fieldId']->alias); return ob_get_clean();
    }


    public function getField() {
        return $this->filter->foreign['fieldId'];
    }

    public function noSatellite() {
        if ($satelliteFieldId = $this->getField()->satellite) {
            $availableFilterA = $this->view->trail->getItem()->filters->toArray();
            foreach ($availableFilterA as $availableFilterI) {
                if ($availableFilterI['fieldId'] == $satelliteFieldId) {
                    return false;
                }
            }
            return true;
        } else {
            return true;
        }
    }

    /**
     * Setup row object for combo
     *
     * @return Indi_Db_Table_Row
     */
    public function getRow(){
        return $this->view->filtersSharedRow;
    }

    /**
     * Filter combos have different behaviour, related to deal with default values
     *
     * @return string
     */
    public function getDefaultValue() {
        $gotFromScope = $this->view->getScope('filters', $this->field->alias);

        if ($gotFromScope || ($this->field->columnTypeId == 12 && $gotFromScope != '')) {
            if ($this->field->storeRelationAbility == 'many')
                $gotFromScope = implode(',', $gotFromScope);
            $this->filter->defaultValue = $this->view->filtersSharedRow->{$this->field->alias} = $gotFromScope;
            return $gotFromScope;
        }
        if ($this->filter->defaultValue || ($this->field->columnTypeId == 12 && $this->filter->defaultValue != '')) {
            Indi::$cmpTpl = $this->filter->defaultValue; eval(Indi::$cmpRun); $this->filter->defaultValue = Indi::$cmpOut;
            $this->view->filtersSharedRow->{$this->field->alias} = $this->filter->defaultValue;
            return $this->filter->defaultValue;
        } else {
            return '';
        }
    }

    /**
     * Calculates width for combo. Takes in attention:
     * 1. Indents (if combo is dealing with tree of options)
     * 2. Color-boxes
     * 3. Titles width
     * 4. .i-combo-info visual availability. If can be visible - additional space will be reserved.
     *
     * @return int
     */
    public function getWidth() {
        if (!$this->titleMaxLength) $this->titleMaxLength = 20;
        return ($this->titleMaxIndent ? $this->titleMaxIndent * 3 : 0) +
                ($this->comboDataRs->optgroup ? 15 : 0) +
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
        ?><div style="width: <?=$this->getWidth()?>px;" max="<?=$this->titleMaxLength?>" class="i-combo i-combo-<?=$this->type?> i-combo-<?=$this->type?>-single x-form-text" id="i-section-<?=$this->view->trail->getItem()->section->alias?>-action-index-filter-<?=$this->name?>-combo"><?
            ?><div class="i-combo-trigger x-form-trigger" id="<?=$this->name?>-trigger"></div><?
            ?><div class="i-combo-single"><?
                $this->selected = $this->detectColor($this->selected, true); echo $this->selected['box'];
                ?><input class="i-combo-keyword" style="width: <?=$this->getKeywordFieldWidth()?>px;" id="<?=$this->name?>-keyword"<?=$this->selected['style']?> type="text" lookup="<?=$this->name?>" value="<?=str_replace('"', '&quot;', $this->selected['input'] ? $this->selected['input'] : $this->selected['title']);?>" no-lookup="<?=$this->params['noLookup']?>"/><?
                ?><input type="hidden" id="<?=$this->name?>" value="<?=$this->selected['value']?>" name="<?=$this->name?>"<?=$this->attrs?> boolean="<?=$this->field->columnTypeId==12 ? 'true' : 'false'?>"/><?
                ?><span class="i-combo-info" id="<?=$this->name?>-info" page-top="0" page-btm="0" fetch-mode="no-keyword" page-top-reached="<?=$this->pageUpDisabled?>" page-btm-reached="false" satellite="<?=$this->noSatellite() ? '' : $this->satellite->alias?>" changed="false"><?
                    ?><span class="i-combo-count" id="<?=$this->name?>-count"></span><?
                    ?><span class="i-combo-of"><?=COMBO_OF?></span><?
                    ?><span class="i-combo-found" id="<?=$this->name?>-found"></span><?
                ?></span><?
            ?></div><?
        ?></div><?
        return ob_get_clean();
    }

    /**
     * Template for mutiple-value combo
     */
    public function formComboMultiple() {
        ob_start();
        ?><div style="width: <?=ceil(($this->getWidth()-20)*1.5)?>px;" class="i-combo i-combo-<?=$this->type?> x-form-text" id="i-section-<?=$this->view->trail->getItem()->section->alias?>-action-index-filter-<?=$this->name?>-combo"><?
            ?><img class="i-combo-trigger" id="<?=$this->name?>-trigger" src="/i/admin/trigger-system.png"/><?
            ?><div class="i-combo-multiple" style="width: 200% !important;"><?
                foreach($this->comboDataRs->selected as $selectedR) {
                    $item = $this->detectColor(array('title' => $selectedR->title));
                    ?><span class="i-combo-selected-item" selected-id="<?=$selectedR->{$this->keyProperty}?>"<?=$item['style']?>><?
                        ?><?=Misc::usubstr($item['title'], 50)?><?
                        ?><span class="i-combo-selected-item-delete"></span><?
                        ?></span><?
                }
                ?><input class="i-combo-keyword" type="text" id="<?=$this->name?>-keyword" lookup="<?=$this->name?>" value="" no-lookup="<?=$this->params['noLookup']?>"/><?
                ?><input type="hidden" id="<?=$this->name?>" value="<?=$this->selected['value']?>" name="<?=$this->name?>"<?=$this->attrs?>/><?
                ?><span class="i-combo-info i-combo-info-multiple" id="<?=$this->name?>-info" page-top="0" page-btm="0" fetch-mode="no-keyword" page-top-reached="<?=$this->pageUpDisabled?>" page-btm-reached="false" satellite="<?=$this->noSatellite() ? '' : $this->satellite->alias?>" changed="false"><?
                    ?><span class="i-combo-count" id="<?=$this->name?>-count"></span><?
                    ?><span class="i-combo-of"><?=COMBO_OF?></span><?
                    ?><span class="i-combo-found" id="<?=$this->name?>-found"></span><?
                ?></span><?
            ?></div><?
        ?></div><?
        return ob_get_clean();
    }

}
