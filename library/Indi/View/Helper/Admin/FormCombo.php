<?php
class Indi_View_Helper_Admin_FormCombo extends Indi_View_Helper_Abstract{
    public function getField($name) {
        return $this->view->trail->getItem()->getFieldByAlias($name);
    }
    public function formCombo($name, $tableName = null){
        // Get field
        $field = $this->getField($name, $tableName);

        if ($field->storeRelationAbility == 'one') {

            // Declare $options array
            $options = array();

            // Get params
            $params = $field->getParams();

            // Get initial set of combo options
            $comboDataRs = $this->view->row->getComboData($name, null, $this->view->row->$name);

            // Get satellite
            if ($field->satellite) $satellite = $field->getForeignRowByForeignKey('satellite');

            // If 'optgroup' param is used
            if ($comboDataRs->optgroup) {
                $by = $comboDataRs->optgroup['by'];
            }

            // Detect key property for options
            $keyProperty = $comboDataRs->enumset ? 'alias' : 'id';

            // Setup primary data for options. Here we use '$o' name instead of '$comboDataR', because
            // it is much more convenient to use such name to deal with option row object while creating
            // a template in $params['template'] contents, if it is set, because php expressions are executed
            // in current context
            foreach ($comboDataRs as $o) {
                $system = $o->system();
                if ($by) $system = array_merge($system, array('group' => $o->$by));
                if($comboDataRs->enumset && $o->javascript)
                    $system = array_merge($system, array('js' => $o->javascript));

                $options[$o->$keyProperty] = array('title' => Misc::usubstr($o->title, 50), 'system' => $system);

                // Deal with optionTemplate param, if specified
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
            if ($comboDataRs->enumset) {
                $key = $this->view->row->$name ? $this->view->row->$name : key($options);
                $selected = array(
                    'title' => Misc::usubstr($options[$key]['title'], 50),
                    'value' => $key
                );
            } else {
                $selected = array(
                    'title' => Misc::usubstr($this->view->row->getForeignRowByForeignKey($name)->title, 50),
                    'value' => $this->view->row->$name ? $this->view->row->$name : $field->defaultValue
                );
            }

            // Set up html attributes for hidden input, if optionAttrs param was used
            if ($options[$selected['value']]['attrs']) {
                $attrs = array();
                foreach ($options[$selected['value']]['attrs'] as $k => $v) {
                    $attrs[] = $k . '="' . $v . '"';
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
                'js' => $field->javascript
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
            $pageUpDisabled = $this->view->row->$name ? 'false' : 'true';
        }
        ob_start();?>
<div class="combo-div">
    <img class="combo-trigger" id="<?=$name?>-trigger" src="/i/admin/trigger-system.png"/>
    <input class="combo-keyword" type="text" id="<?=$name?>-keyword" lookup="<?=$name?>" value="<?=$selected['title']?>" no-lookup="<?=$params['noLookup']?>"/>
    <input type="hidden" id="<?=$name?>" value="<?=$selected['value']?>" name="<?=$name?>"<?=$attrs?>/>
    <span class="combo-info" id="<?=$name?>-info" page-top="0" page-btm="0" fetch-mode="no-keyword" page-top-reached="<?=$pageUpDisabled?>" page-btm-reached="false" satellite="<?=$satellite->alias?>" changed="false">
            <span class="combo-count" id="<?=$name?>-count"></span>
            <span class="combo-of"><?=COMBO_OF?></span>
            <span class="combo-found" id="<?=$name?>-found"></span>
    </span>
</div>
    <?if (!$attribs['optionsOnly']) {?>
    <script>
    var comboOptions = comboOptions || {};
    comboOptions["<?=$name?>"] = (<?=$options?>);
    </script>
    <?} else {?>
        <?=$options?>
    <?}
    return ob_get_clean();
    }
}