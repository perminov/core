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
    public function filterCombo($filter, $mode = 'default') {
        // Here we create a shared *_Row object, that will be used by all filters, that are presented in current grid.
        // We need it bacause of a satellites. If we define a default value for some combo, and that combo is a satellite
        // for another combo - another combo's initial data will depend on satellite value, so the shared row is the place
        // there dependent combo can get that value.
        $this->filter = $filter;

        $this->getRow()->{$this->getField()->alias} = null;

        $this->where = $this->filter->filter;
        $this->ignoreTemplate = $this->filter->ignoreTemplate;
        ob_start(); echo parent::formCombo($filter->foreign('fieldId')->alias, null, $mode); return ob_get_clean();
    }

    public function getSelected() {
        return $this->getDefaultValue();
    }

    public function getField() {
        return Indi::trail()->fields->select($this->filter->fieldId)->at(0);
    }

    /**
     * Detect whether or not WHERE clause part, related especially to current field's satellite value
     * should be involved in the process of fetching data for current filter-combo
     *
     * @return bool
     */
    public function noSatellite() {

        // If current field has a satellite, we'll try to find satellite's value in several places
        if ($satelliteFieldId = $this->getField()->satellite) {

            // Clone filters rowset, as iterating through same rowset will give an error
            $filters = clone Indi::trail()->filters;

            // Lookup satellite within the available filters. If found - use it
            $availableFilterA = $filters->toArray();
            foreach ($availableFilterA as $availableFilterI)
                if ($availableFilterI['fieldId'] == $satelliteFieldId)
                    return false;

            // Lookup satellite within the filterSharedRow's props, that might hav been set up by
            // trail items connections logic. If found - use it
            $satelliteFieldAlias = $this->getField()->foreign('satellite')->alias;
            if (array_key_exists($satelliteFieldAlias, $this->getRow()->modified())) return false;

            // If current cms user is an alternate, and if there is corresponding column-field within current entity structure. If found - use it
            if (Indi::admin()->alternate && in($aid = Indi::admin()->alternate . 'Id', Indi::trail()->model->fields(null, 'columns')))
                return false;

            // No satellite should be used
            return true;

        // No satellite should be used
        } else return true;
    }

    /**
     * Setup row object for combo
     *
     * @return Indi_Db_Table_Row
     */
    public function getRow(){
        return Indi::trail()->filtersSharedRow;
    }

    /**
     * Filter combos have different behaviour, related to deal with default values
     *
     * @return mixed|string
     */
    public function getDefaultValue() {
        $gotFromScope = Indi::trail()->scope->filter($this->field->alias);

        if ($gotFromScope || ($this->field->columnTypeId == 12 && $gotFromScope != '')) {
            if ($this->field->storeRelationAbility == 'many')
                if(is_array($gotFromScope))
                    $gotFromScope = implode(',', $gotFromScope);
            $this->filter->defaultValue = $this->getRow()->{$this->field->alias} = $gotFromScope;
            return $gotFromScope;
        }
        if (strlen($this->filter->defaultValue) || ($this->field->columnTypeId == 12 && $this->filter->defaultValue != '')) {
            Indi::$cmpTpl = $this->filter->defaultValue; eval(Indi::$cmpRun); $this->filter->defaultValue = Indi::cmpOut();
            $this->getRow()->{$this->field->alias} = $this->filter->defaultValue;
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
                ($this->params['noLookup'] == 'true' || $this->comboDataRs->enumset ? 10 : 30) +
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
        ?><div id="i-section-<?=Indi::trail()->section->alias?>-action-index-filter-<?=$this->name?>-combo" class="i-combo i-combo-<?=$this->type?> i-combo-<?=$this->type?>-single" style="width: <?=$this->getWidth()?>px;" max="<?=$this->titleMaxLength?>"><?
            ?><div class="i-combo-single x-form-text"><?
            ?><table class="i-combo-table"><tr><?
                ?><td class="i-combo-color-box-cell"><?
                    ?><div class="i-combo-color-box-div"><?
                        $this->selected = Indi_View_Helper_Admin_FormCombo::detectColor($this->selected); echo $this->selected['box'];
                    ?></div><?
                ?></td><?
                ?><td class="i-combo-keyword-cell"><?
                    ?><div class="i-combo-keyword-div"><?
                        ?><input class="i-combo-keyword" id="<?=$this->name?>-keyword"<?=$this->selected['style']?> type="text" lookup="<?=$this->name?>" value="<?=str_replace('"', '&quot;', $this->selected['input'] ? $this->selected['input'] : $this->selected['title']);?>" no-lookup="<?=$this->params['noLookup']?>"/><?
                        ?><input type="hidden" id="<?=$this->name?>" value="<?=$this->selected['value']?>" name="<?=$this->name?>"<?=$this->attrs?> boolean="<?=$this->field->columnTypeId==12 ? 'true' : 'false'?>"/><?
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

    /**
     * Template for mutiple-value combo
     */
    public function formComboMultiple() {
        ob_start();
        ?><div class="i-combo i-combo-<?=$this->type?>" id="i-section-<?=Indi::trail()->section->alias?>-action-index-filter-<?=$this->name?>-combo" style="width: <?=ceil(($this->getWidth()-20)*1.5)?>px;"><?
            ?><div class="i-combo-multiple x-form-text"><?
                foreach($this->comboDataRs->selected as $selectedR) {
                    $item = Indi_View_Helper_Admin_FormCombo::detectColor(array('title' => $selectedR->title));
                    ?><span class="i-combo-selected-item" selected-id="<?=$selectedR->{$this->keyProperty}?>"<?=$item['style'] ? $item['style'] : ($item['font'] ? ' style="' . $item['font'] . '"' : '')?>><?
                        ?><?=$item['box'] . usubstr($item['title'], 50)?><?
                        ?><span class="i-combo-selected-item-delete"></span><?
                        ?></span><?
                }
                ?><span class="i-combo-table-wrapper" id="<?=$this->name?>-table-wrapper"><table class="i-combo-table"><tr><?
                    ?><td class="i-combo-keyword-cell"><?
                        ?><div class="i-combo-keyword-div"><?
                            ?><input class="i-combo-keyword" type="text" id="<?=$this->name?>-keyword" lookup="<?=$this->name?>" value="" no-lookup="<?=$this->params['noLookup']?>"/><?
                            ?><input type="hidden" id="<?=$this->name?>" value="<?=$this->selected['value']?>" name="<?=$this->name?>"<?=$this->attrs?>/><?
                        ?></div><?
                    ?></td><?
                    ?><td class="i-combo-info-cell"><?
                        ?><div class="i-combo-info-div"><?
                            ?><table class="i-combo-info i-combo-info-multiple" id="<?=$this->name?>-info" page-top="0" page-btm="0" fetch-mode="no-keyword" page-top-reached="<?=$this->pageUpDisabled?>" page-btm-reached="false" satellite="<?=$this->noSatellite() ? '' : $this->satellite->alias?>" changed="false"><tr><?
                                ?><td><span class="i-combo-count" id="<?=$this->name?>-count"></span></td><?
                                ?><td><span class="i-combo-of"><?=I_COMBO_OF?></span></td><?
                                ?><td><span class="i-combo-found" id="<?=$this->name?>-found"></span></td><?
                            ?></tr></table><?
                        ?></div><?
                    ?></td><?
                    ?><td class="i-combo-trigger-cell"><?
                        ?><div class="i-combo-trigger x-form-trigger" id="<?=$this->name?>-trigger"></div><?
                    ?></td><?
                ?></tr></table></span><?
                ?><div style="clear: both;"></div><?
            ?></div><?
        ?></div><?
        return ob_get_clean();
    }

}
