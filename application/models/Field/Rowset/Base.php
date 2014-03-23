<?php
class Field_Rowset_Base extends Indi_Db_Table_Rowset{
    /**
     * Set existing (original and redefined) params for each field in the current rowset
     *
     * @return Field_Rowset_Base
     */
    public function setParams() {
        foreach ($this as $r) {
            $this->_temporary[$r->id]['params'] = $r->getParams();
        }
        return $this;
    }

    /**
     * Setup a usage-friendly data about params for fields within current rowset, and afer that unset temporary data
     *
     * @return Field_Rowset_Base
     */
    public function params() {

        // Foreach row within current rowset
        foreach ($this as $row) {

            // Find overrided params
            foreach ($row->nested('param') as $param)
                $override[$param->possibleParamId] = (string) $param->value;

            // Find default params, and if for some default param was explicitly set (overrided) a value
            // - use it instead default value
            foreach ($row->foreign('elementId')->nested('possibleElementParam') as $possible)
                $this->_temporary[$row->id]['params'][$possible->alias] =
                    isset($override[$possible->id])
                        ? $override[$possible->id]
                        : $possible->defaultValue;
        }

        // After params was set, mean was set as key-value pairs, we do not need 'param' nested data, nested to
        // current rowset, and 'possibleElementParam' nested data, nested to foreign element rows, so we unset it
        //unset($this->_nested['param']);
        $this->nested('param', 'unset');
        foreach ($this->_foreign['elementId'] as $fieldId => $elementR)
            $this->_foreign['elementId'][$fieldId]->nested('possibleElementParam', 'unset');

        // Return rowset itself
        return $this;
    }
}