<?php
class Indi_View_Helper_Admin_FormCombo {

    /**
     * This var is used for html elements css class names building
     * @var string
     */
    public $type = 'form';

    /**
     * Additional WHERE clause, that will be used while options data retrieving
     *
     * @var null
     */
    public $where = null;

    /**
     * Context of combo initialization javascript execution
     *
     * @var string
     */
    public $context = 'window';

    /**
     * Whether or not at least one option have color box. This property is used in *_FilterCombo helper
     *
     * @var bool
     */
    public $hasColorBox = false;

    /**
     * Setup row object for combo
     *
     * @return Indi_Db_Table_Row
     */
    public function getRow(){
        return Indi::view()->row;
    }

    /**
     * Default value determining is extracted from raw code to seperate function for inherited classes
     * being able to setup a different logic for dealing with default values
     *
     * @return mixed
     */
    public function getDefaultValue() {
        return $this->field->compiled('defaultValue');
    }

    /**
     * Setup field
     *
     * @param $name
     * @return Field_Row
     */
    public function getField($name) {
        return Indi::trail()->model->fields($name);
    }

    /**
     * Function to determime whether current field has a satellite, and if even have - does tha satellite exist in the same
     * form (it may, for example, be switched off). This function currently returns false, but same function in inherited
     * class *_FormFilter makes a check
     *
     * @return bool
     */
    public function noSatellite() {
        if ($satelliteFieldId = $this->field->satellite) {
            return false;
        } else {
            return true;
        }
    }

    public function getSelected() {

        // If current row does not exist, combo will use field's default value as selected value
        if ($this->getRow()->id || strlen($this->getRow()->{$this->name})) {
            $selected = $this->getRow()->{$this->name};
        } else {
            $selected = $this->getDefaultValue();
        }

        return $selected;
    }

    /**
     * Builds the combo
     *
     * @param $name
     * @param null $tableName
     * @return string
     */
    public function formCombo($name, $tableName = null) {

        // Set name
        $this->name = $name;

        // Get field
        $this->field = $this->getField($name, $tableName);

        // Get params
        $params = $this->field->params;

        // Get selected
        $selected = $this->getSelected();

        // Declare $options array
        $options = array();

        // Get initial set of combo options
        $comboDataRs = $this->getRow()->getComboData($name, null, $selected, null, null,
            $this->where, $this->noSatellite(), $this->field, $this->comboDataOrderColumn,
            $this->comboDataOrderDirection, $this->comboDataOffset);

        // Get title column
        $titleColumn = $comboDataRs->titleColumn;

        // Get satellite
        if ($this->field->satellite) $satellite = $this->field->foreign('satellite');

        // If 'optgroup' param is used
        if ($comboDataRs->optgroup) $by = $comboDataRs->optgroup['by'];

        // Detect key property for options
        $keyProperty = $comboDataRs->enumset ? 'alias' : 'id';

        // Option title maxlength
        $this->titleMaxLength = 0;

        // Option title maxlength
        $this->titleMaxIndent = 0;

        // Setup primary data for options. Here we use '$o' name instead of '$comboDataR', because
        // it is much more convenient to use such name to deal with option row object while creating
        // a template in $params['template'] contents, if it is set, because php expressions are executed
        // in current context
        foreach ($comboDataRs as $o) {

            // Get initial array of system properties of an option
            $system = $o->system();

            // Set group identifier for an option
            if ($by) $system = array_merge($system, array('group' => $o->$by));

            // Set javascript handler on option select event, if needed
            if($comboDataRs->enumset && $o->javascript)
                $system = array_merge($system, array('js' => $o->javascript));

            // Here we are trying to detect, does $o->title have tag with color definition, for example
            // <span style="color: red">Some option title</span> or <font color=lime>Some option title</font>, etc.
            // We should do that because such tags existance may cause a dom errors while performing usubstr()
            $info = self::detectColor(array('title' => $o->$titleColumn, 'value' => $o->$keyProperty));

            // If color was detected as a box, append $system['boxColor'] property
            if ($info['box']) $system['boxColor'] = $info['color'];

            // Setup primary option data
            $options[$o->$keyProperty] = array('title' => usubstr($info['title'], 50), 'system' => $system);

            // If color was detected, and it has box-type, we remember this fact
            if ($info['box']) $this->hasColorBox = true;

            // Update maximum option title length, if it exceeds previous maximum
            $noHtmlSpecialChars = preg_replace('/&[a-z]*;/', ' ',$options[$o->$keyProperty]['title']);
            if (mb_strlen($noHtmlSpecialChars, 'utf-8') > $this->titleMaxLength)
                $this->titleMaxLength = mb_strlen($noHtmlSpecialChars, 'utf-8');

            // Update maximum option title indent, if it exceeds previous maximum
            if ($comboDataRs->model()->treeColumn()) {
                $indent = mb_strlen(preg_replace('/&nbsp;/', ' ', $options[$o->$keyProperty]['system']['indent']), 'utf-8');
                if ($indent > $this->titleMaxIndent) $this->titleMaxIndent = $indent;
            }

            // If color was found, we remember it for that option
            if ($info['style']) $options[$o->$keyProperty]['system']['color'] = $info['color'];

            // Current context does not have a $this->ignoreTemplate member, but inherited class *_FilterCombo does.
            // so option height that is applied to form combo will not be applied to filter combo, unless $this->ignoreTemplate
            // in *_FilterCombo is set to false
            if ($this->field->params['optionTemplate'] && !$this->ignoreTemplate) {
                Indi::$cmpTpl = $this->field->params['optionTemplate']; eval(Indi::$cmpRun); $options[$o->$keyProperty]['option'] = Indi::cmpOut();
            }

            // Deal with optionAttrs, if specified.
            if ($comboDataRs->optionAttrs) {
                for ($i = 0; $i < count($comboDataRs->optionAttrs); $i++) {
                    $options[$o->$keyProperty]['attrs'][$comboDataRs->optionAttrs[$i]] = $o->{$comboDataRs->optionAttrs[$i]};
                }
            }
        }

        // If current field column type is ENUM or SET, and current row have no selected value, we use first
        // option to get default info about what title should be displayed in input keyword field and what value
        // should have hidden field
        if ($this->field->storeRelationAbility == 'one') {

            // Setup a key
            if (($this->getRow()->id && !$comboDataRs->enumset) || !is_null($this->getRow()->$name)) {

                if (preg_match('/Sibling/', get_class($this))) {
                    $key = $this->getRow()->id;
                } else {
                    $key = $this->getRow()->$name;
                }
            } else if ($comboDataRs->enumset && $this->type == 'form') {
                $key = key($options);
            } else {
                $key = $this->getDefaultValue();
            }

            // Setup an info about selected value
            $selected = array(
                'title' => $options[$key]['title'],
                'value' => $key
            );

            if ($options[$key]['system']['boxColor']) $selected['boxColor'] = $options[$key]['system']['boxColor'];

            // Setup css color property for input, if original title of selected value contained a color definition
            if ($options[$selected['value']]['system']['color'])
                $selected['style'] =  ' style="color: ' . $options[$selected['value']]['system']['color'] . ';"';

            // Set up html attributes for hidden input, if optionAttrs param was used
            if ($options[$selected['value']]['attrs']) {
                $attrs = array();
                foreach ($options[$selected['value']]['attrs'] as $k => $v) {
                    $attrs[] = $k . '="' . $v . '"';
                }
                $attrs = ' ' . implode(' ', $attrs);
            }

        // Else if combo is mulptiple
        } else if ($this->field->storeRelationAbility == 'many') {
            // Set value for hidden input
            $selected = array('value' => $selected);

            // Set up html attributes for hidden input, if optionAttrs param was used
            $exploded = explode(',', $selected['value']);
            $attrs = array();
            for ($i = 0; $i < count($exploded); $i++) {
                if ($options[$exploded[$i]]['attrs']) {
                    foreach ($options[$exploded[$i]]['attrs'] as $k => $v) {
                        $attrs[] = $k . '-' . $exploded[$i] . '="' . $v . '"';
                    }
                }
            }
            $attrs = ' ' . implode(' ', $attrs);

        // Else if combo is boolean
        } else if ($this->field->storeRelationAbility == 'none' && $this->field->columnTypeId == 12) {

            // Setup a key
            if ($this->getRow()->$name) {
                $key = $this->getRow()->$name;
            } else if ($comboDataRs->enumset && $this->type == 'form') {
                $key = key($options);
            } else {
                $key = $this->getDefaultValue();
            }

            // Setup an info about selected value
            $selected = array(
                'title' => $options[$key]['title'],
                'value' => $key
            );
        }

        // Prepare options data
        $options = array(
            'ids' => array_keys($options),
            'data' => array_values($options),
            'found' => $comboDataRs->found(),
            'page' => $comboDataRs->page(),
            'enumset' => $comboDataRs->enumset,
            'js' => $this->field->javascript,
            'titleMaxLength' => $this->titleMaxLength
        );

        // Setup tree flag in entity has a tree structure
        if ($comboDataRs->model()->treeColumn()) $options['tree'] = true;

        // Setup groups for options
        if ($comboDataRs->optgroup) $options['optgroup'] = $comboDataRs->optgroup;

        // Setup option height. Current context does not have a $this->ignoreTemplate member,but inherited class *_FilterCombo
        // does, so option height that is applied to form combo will not be applied to filter combo, unless $this->ignoreTemplate
        // in *_FilterCombo is set to false
        $options['optionHeight'] = $params['optionHeight'] && !$this->ignoreTemplate ? $params['optionHeight'] : 14;

        // Setup groups for options
        if ($comboDataRs->optionAttrs) $options['attrs'] = $comboDataRs->optionAttrs;

        // Encode that set in json format
        $options = json_encode($options);

        // If combo-field of current row currently does not have a value, we should disable paging up
        $this->pageUpDisabled = $this->getRow()->$name ? 'false' : 'true';

        // Assign local variables to public class variables
        $vars = array('name', 'selected', 'params', 'attrs', 'pageUpDisabled', 'satellite', 'comboDataRs', 'keyProperty');
        foreach ($vars as $var) $this->$var = $$var;

        ob_start();

        if ($this->field->storeRelationAbility == 'one' ||
            ($this->field->storeRelationAbility == 'none' && $this->field->columnTypeId == 12)) {
            echo $this->formComboSingle();
        } else if ($this->field->storeRelationAbility == 'many') {
            echo $this->formComboMultiple();
        }

        // Init combo store data
        ?><script>Indi.ready(function(){<?=$this->context?>.Indi.combo.<?=$this->type?>.store['<?=$this->name?>'] = (<?=$options?>)}, 'combo.<?=$this->type?>', <?=$this->context?>);</script><?

        return ob_get_clean();
    }

    /**
     * Template for single-value combo
     */
    public function formComboSingle(){
		ob_start();
        ?><div class="i-combo i-combo-<?=$this->type?> i-combo-focus"><?
            ?><div class="i-combo-single"><?
                ?><table class="i-combo-table"><tr><?
                    ?><td class="i-combo-color-box-cell"><?
                        ?><div class="i-combo-color-box-div"><?
                        $this->selected = self::detectColor($this->selected); echo $this->selected['box'];
                        ?></div><?
                    ?></td><?
                    ?><td class="i-combo-keyword-cell"><?
                        ?><div class="i-combo-keyword-div"><?
                            ?><input class="i-combo-keyword" id="<?=$this->name?>-keyword"<?=$this->selected['style']?> type="text" lookup="<?=$this->name?>" value="<?=str_replace('"', '&quot;', $this->selected['input'] ? $this->selected['input'] : $this->selected['title']);?>" no-lookup="<?=$this->params['noLookup']?>" placeholder="<?=$this->params['placeholder']?>"/><?
                            ?><input type="hidden" id="<?=$this->name?>" value="<?=$this->selected['value']?>" name="<?=$this->name?>"<?=$this->attrs?>/><?
                        ?></div><?
                    ?></td><?
                    ?><td class="i-combo-info-cell"><?
                        ?><div class="i-combo-info-div"><?
                            ?><table class="i-combo-info" id="<?=$this->name?>-info" page-top="0" page-btm="0" fetch-mode="no-keyword" page-top-reached="<?=$this->pageUpDisabled?>" page-btm-reached="false" satellite="<?=$this->satellite->alias?>" changed="false"><tr><?
                                ?><td><span class="i-combo-count" id="<?=$this->name?>-count"></span></td><?
                                ?><td><span class="i-combo-of"><?=COMBO_OF?></span></td><?
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
        ?><div class="i-combo i-combo-<?=$this->type?> i-combo-focus"><?
            ?><div class="i-combo-multiple"><?
            foreach($this->comboDataRs->selected as $selectedR) {
                $item = self::detectColor(array('title' => $selectedR->title));
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
                            ?><table class="i-combo-info i-combo-info-multiple" id="<?=$this->name?>-info" page-top="0" page-btm="0" fetch-mode="no-keyword" page-top-reached="<?=$this->pageUpDisabled?>" page-btm-reached="false" satellite="<?=$this->satellite->alias?>" changed="false"><tr><?
                                ?><td><span class="i-combo-count" id="<?=$this->name?>-count"></span></td><?
                                ?><td><span class="i-combo-of"><?=COMBO_OF?></span></td><?
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

    /**
     * Check if option contain a color definition, and if so, extract that color, build color box (in some cases) and
     * prepare some auxillary data
     *
     * @param $option
     * @return array
     */
    public static function detectColor($option) {

        // Color detection in different places within one certain option
        ($v = preg_match('/^[0-9]{3}(#[0-9a-fA-F]{6})$/', $option['value'], $color)) ||
        ($t = preg_match('/^[0-9]{3}(#[0-9a-fA-F]{6})$/', $option['title'], $color)) ||
        ($s = preg_match('/color[:=][ ]*[\'"]{0,1}([#a-zA-Z0-9]+)/i', $option['title'], $color)) ||
        ($b = preg_match('/^<span class="i-color-box" style="background: ([#0-9a-zA-Z]{3,20});[^"]*"[^>]*>/', $option['title'], $color));

        // If color was detected somewhere
        if ($v || $t || $s || $b || $option['boxColor']) {

            // Setup color
            $option['color'] = $color[1] ? $color[1] : $option['boxColor'];

            // If color was detected in 'title' property, and found as 'color' or 'background' css property
            // - strip tags from title
            if ($s || $b) $option['title'] = strip_tags($option['title']);

            // If color was detected in 'title' property as 'color' css property
            if ($s) {

                // If there is no 'style' property within $option variable -
                // set it as 'color' and 'font' css properties specification
                if (!$option['style']) $option['style'] = ' style="color: ' . $option['color'] . '; ' . $option['font'] . '"';

            // Else if color was not detected in 'title' property as 'color' css property - we assume that color should
            // be represented as a color-box
            } else {

                // If color was detected in 'value' property of $option variable - set 'input' property as a purified color
                if ($t) $option['input'] = $option['color'];

                // Setup color box
                $option['box'] = '<span class="i-combo-color-box" style="background: ' . $option['color'] . '; margin-right: 3px;"></span>';
            }
        }

        // Append font specification, as there might be case when fonts specifications, mentioned in css files - do not
        // applied at the moment of indi.combo.js maintenance, because indi.combo.js may run earlier than css files
        // loaded and this may cause wrong calculation of widths of selected options in multiple-combos, as by default
        // options's text is in 'Times New Roman' font, which have letter widths, differerent from other fonts
        $option['font'] = ' font-family: tahoma, arial, verdana, sans-serif;';

        return $option;
    }
}