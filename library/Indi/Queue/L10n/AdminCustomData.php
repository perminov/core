<?php
class Indi_Queue_L10n_AdminCustomData extends Indi_Queue_L10n_AdminUi {

    /**
     * Create queue chunks
     *
     * @param array $params
     */
    public function chunk(array $params) {

        // Create `queueTask` entry
        $queueTaskR = Indi::model('QueueTask')->createRow(array(
            'title' => array_pop(explode('_', get_class($this))),
            'params' => json_encode($params),
            'queueState' => $params['toggle'] == 'n' ? 'noneed' : 'waiting'
        ), true);

        // Save `queueTask` entries
        $queueTaskR->save();

        // Foreach `entity` entry, having `system` = "n" (e.g. project's custom entities)
        // Foreach `field` entry, having `l10n` = "y" - append chunk
        foreach (Indi::model('Entity')->fetchAll('`system` = "n"') as $entityR)
            foreach ($entityR->nested('field', ['where' => '`l10n` = "y"']) as $fieldR)
                $this->appendChunk($queueTaskR, $entityR, $fieldR);
    }
}