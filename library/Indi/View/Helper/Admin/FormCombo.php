<?php
class Indi_View_Helper_Admin_FormCombo extends Indi_View_Helper_Abstract{
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
     * Setup row object for combo
     *
     * @return Indi_Db_Table_Row
     */
    public function getRow(){
        return $this->view->row;
    }

    /**
     * Default value determining is extracted from raw code to seperate function for inherited classes
     * being able to setup a different logic for dealing with default values
     *
     * @return mixed
     */
    public function getDefaultValue() {
        return $this->field->defaultValue;
    }

    public function getField($name) {
        return $this->view->trail->getItem()->getFieldByAlias($name);
    }

    /**
     * Builds the combo
     *
     * @param Search_Row $filter
     * @return string
     */
    public function formCombo($name, $tableName){
        // Get field
        $this->field = $this->getField($name, $tableName);

        // Declare $options array
        $options = array();

        // Get params
        $params = $this->field->getParams();

        // If current row does not exist, combo will use field's default value as selected value
        if ($this->getRow()->id) {
            $selected = $this->getRow()->$name;
        } else {
            $selected = $this->getDefaultValue();
        }

        // Get initial set of combo options
        $comboDataRs = $this->getRow()->getComboData($name, null, $selected, null, null, $this->where);

        // Get satellite
        if ($this->field->satellite) $satellite = $this->field->getForeignRowByForeignKey('satellite');

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
            // We should do that because such tags existance may cause a dom errors while performing Misc::usubstr()
            $info = $this->detectColor(array('title' => $o->title, 'value' => $o->$keyProperty));
            $options[$o->$keyProperty] = array('title' => Misc::usubstr($info['title'], 50), 'system' => $system);

            // If color was detected, and it has box-type, we remember this fact
            if ($info['box']) $this->hasColorBox = true;

            // Update maximum option title length, if it exceeds previous maximum
            $noHtmlSpecialChars = preg_replace('/&[a-z]*;/', ' ',$options[$o->$keyProperty]['title']);
            if (mb_strlen($noHtmlSpecialChars, 'utf-8') > $this->titleMaxLength)
                $this->titleMaxLength = mb_strlen($noHtmlSpecialChars, 'utf-8');

            // Update maximum option title indent, if it exceeds previous maximum
            if ($comboDataRs->treeColumn) {
                $indent = mb_strlen(preg_replace('/&nbsp;/', ' ', $options[$o->$keyProperty]['system']['indent']), 'utf-8');
                if ($indent > $this->titleMaxIndent) $this->titleMaxIndent = $indent;
            }

            // If color was found, we remember it for that option
            if ($info['style']) $options[$o->$keyProperty]['system']['color'] = $info['color'];

            if ($params['optionTemplate']) {
                $php = preg_split('/(<\?|\?>)/', $params['optionTemplate'], -1, PREG_SPLIT_DELIM_CAPTURE);
                $out = '';
                for ($i = 0; $i < count($php); $i++) {
                    if ($php[$i] == '<?') {
                        $php[$i+1] = preg_replace('/^=/', ' echo ', $php[$i+1]) . ';';
                        ob_start(); eval($php[$i+1]); $out .= ob_get_clean();
                        $i += 2;
                    } else {
                        $out .= $php[$i];
                    }
                }
                $options[$o->$keyProperty]['option'] = $out;
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
        }


        // Prepare options data
        $options = array(
            'ids' => array_keys($options),
            'data' => array_values($options),
            'found' => $comboDataRs->foundRows,
            'page' => $comboDataRs->page,
            'enumset' => $comboDataRs->enumset,
            'js' => $this->field->javascript
        );

        // Setup tree flag in entity has a tree structure
        if ($comboDataRs->treeColumn) $options['tree'] = true;

        // Setup groups for options
        if ($comboDataRs->optgroup) $options['optgroup'] = $comboDataRs->optgroup;

        // Setup option height
        $options['optionHeight'] = $params['optionHeight'] ? $params['optionHeight'] : 14;

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

        if ($this->field->storeRelationAbility == 'one') {
            echo $this->formComboSingle();
        } else if ($this->field->storeRelationAbility == 'many') {
            echo $this->formComboMultiple();
        }

        // Init combo store data
        ?><script>Indi.ready(function(){Indi.combo.<?=$this->type?>.store['<?=$this->name?>'] = (<?=$options?>)}, 'combo.<?=$this->type?>');</script><?

        return ob_get_clean();
    }

    /**
     * Template for single-value combo
     */
    public function formComboSingle(){
        ob_start();
        ?><div class="i-combo i-combo-<?=$this->type?>"><?
        ?><div class="i-combo-trigger x-form-trigger x-form-trigger-over" id="<?=$this->name?>-trigger"></div><?
        ?><div class="i-combo-single"><?
            $this->selected = $this->detectColor($this->selected, true); echo $this->selected['box'];
            ?><input class="i-combo-keyword" id="<?=$this->name?>-keyword"<?=$this->selected['style']?> type="text" lookup="<?=$this->name?>" value="<?=$this->selected['input'] ? $this->selected['input'] : $this->selected['title']?>" no-lookup="<?=$this->params['noLookup']?>"/><?
            ?><input type="hidden" id="<?=$this->name?>" value="<?=$this->selected['value']?>" name="<?=$this->name?>"<?=$this->attrs?>/><?
            ?><span class="i-combo-info" id="<?=$this->name?>-info" page-top="0" page-btm="0" fetch-mode="no-keyword" page-top-reached="<?=$this->pageUpDisabled?>" page-btm-reached="false" satellite="<?=$this->satellite->alias?>" changed="false"><?
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
        ?><div class="i-combo i-combo-form"><?
        ?><img class="i-combo-trigger" id="<?=$this->name?>-trigger" src="/i/admin/trigger-system.png"/><?
        ?><div class="i-combo-multiple"><?
            foreach($this->comboDataRs->selected as $selectedR) {
                $item = $this->detectColor(array('title' => $selectedR->title));
                ?><span class="i-combo-selected-item" selected-id="<?=$selectedR->{$this->keyProperty}?>"<?=$item['style']?>><?
                    ?><?=Misc::usubstr($item['title'], 50)?><?
                    ?><span class="i-combo-selected-item-delete"></span><?
                ?></span><?
            }
            ?><input class="i-combo-keyword" type="text" id="<?=$this->name?>-keyword" lookup="<?=$this->name?>" value="" no-lookup="<?=$this->params['noLookup']?>"/><?
            ?><input type="hidden" id="<?=$this->name?>" value="<?=$this->selected['value']?>" name="<?=$this->name?>"<?=$this->attrs?>/><?
            ?><span class="i-combo-info i-combo-info-multiple" id="<?=$this->name?>-info" page-top="0" page-btm="0" fetch-mode="no-keyword" page-top-reached="<?=$this->pageUpDisabled?>" page-btm-reached="false" satellite="<?=$this->satellite->alias?>" changed="false"><?
                ?><span class="i-combo-count" id="<?=$this->name?>-count"></span><?
                ?><span class="i-combo-of"><?=COMBO_OF?></span><?
                ?><span class="i-combo-found" id="<?=$this->name?>-found"></span><?
                ?></span><?
            ?></div><?
        ?></div><?
        return ob_get_clean();
    }

    /**
     * Check if option contain a color definition, and if so, extract that color, build color box (in some cases) and
     * prepare some auxillary data
     *
     * @param $selected
     * @return array
     */
    public function detectColor($selected) {

        ($v = preg_match('/^[0-9]{3}(#[0-9a-fA-F]{6})$/', $selected['value'], $color)) ||
        ($t = preg_match('/^[0-9]{3}(#[0-9a-fA-F]{6})$/', $selected['title'], $color)) ||
        ($s = preg_match('/color[:=][ ]*[\'"]{0,1}([#a-zA-Z0-9]+)/i', $selected['title'], $color));

        if ($v || $t || $s) {
            $selected['color'] = $color[1];
            if ($s) {
                $selected['title'] = strip_tags($selected['title']);
                if (!$selected['style'])
                    $selected['style'] = ' style="color: ' . $selected['color'] . '"'; //++
            } else {
                if ($t) $selected['input'] = $color[1];
                $selected['box'] = '<span class="i-combo-color-box" style="background: ' . $color[1] . '"></span>';
            }
        }

        return $selected;
    }
}