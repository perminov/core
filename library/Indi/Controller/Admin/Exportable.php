<?php
class Indi_Controller_Admin_Exportable extends Indi_Controller_Admin {

    /**
     * Flush creation expression for selected entries, to be applied on another project running on Indi Engine
     */
    public function exportAction() {

        // Declare array of ids of entries, that should be exported, and push main entry's id as first item
        $toBeExportedIdA[] = $this->row->id;

        // If 'others' param exists in $_POST, and it's not empty
        if ($otherIdA = ar(Indi::post()->others)) {

            // Unset invalid values
            foreach ($otherIdA as $i => $otherIdI) if (!(int) $otherIdI) unset($otherIdA[$i]);

            // If $otherIdA array is still not empty append it's item into $toBeExportedIdA array
            if ($otherIdA) $toBeExportedIdA = array_merge($toBeExportedIdA, $otherIdA);
        }

        // Fetch rows that should be moved
        $toBeExportedRs = t()->model->fetchAll(
            array('`id` IN (' . im($toBeExportedIdA) . ')', Indi::trail()->scope->WHERE),
            t()->scope->ORDER
        );

        // For each row get export expression
        $php = array(); foreach ($toBeExportedRs as $toBeExportedR) $php []= $toBeExportedR->export();

        // Apply new index
        $this->setScopeRow(false, null, $toBeExportedRs->column('id'));

        // Flush
        jtextarea(true, im($php, "\n/*----------------*/\n"));
    }
}