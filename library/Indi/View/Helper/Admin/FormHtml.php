<?php
class Indi_View_Helper_Admin_FormHtml {
    public function formHtml($name, $value = null, $height = 300, $toolbar = 'Custom')
    {
        $toolbar = $toolbar ? $toolbar : 'Default';
        if ($value === null) {
            $value = Indi::view()->row->$name;
        }

		$field = Indi::trail()->model->fields($name);

        $customParams = array('width','height','bodyClass','style','script','sourceStripper');
        foreach($customParams as $customParam) {
            if (Indi::view()->row->{$name . ucfirst($customParam)}) {
                $field->params[$customParam] = Indi::view()->row->{$name . ucfirst($customParam)};
            }
        }

        // Set up styles configuration for editor contents
        if ($field->params['style']) $CKconfig['style'] = $field->params['style'];
        $CKconfig['style'] .= 'body{max-width: auto;min-width: auto;width: auto;}';
        if ($field->params['contentsCss']) $CKconfig['contentsCss'] = preg_match('/^\[/', $field->params['contentsCss']) ? json_decode($field->params['contentsCss']) : $field->params['contentsCss'];
        if (is_array($CKconfig['contentsCss'])) {
            $CKconfig['contentsCss'] = array_merge($CKconfig['contentsCss'], array($CKconfig['style'] . ' body{max-width: auto;min-width: auto;width: auto;}'));
        } else {
            $CKconfig['contentsCss'] = array($CKconfig['contentsCss'], $CKconfig['style'] . ' body{max-width: auto;min-width: auto;width: auto;}');
        }
        if ($field->params['bodyClass']) $CKconfig['bodyClass'] = $field->params['bodyClass'];
        $CKconfig['uiColor'] = '#B8D1F7';

        // Set up editor size
        $CKconfig['width'] = $field->params['width'] ? $field->params['width'] + 52 : 'auto';
        if ($field->params['height']) $CKconfig['height'] = $field->params['height'];

        // Set up editor javascript
        if ($field->params['script']) $CKconfig['script'] = $field->params['script'];
        if ($field->params['contentsJs']) $CKconfig['contentsJs'] = preg_match('/^\[/', $field->params['contentsJs']) ? json_decode($field->params['contentsJs']) : $field->params['contentsJs'];
        if (is_array($CKconfig['contentsJs'])) {
            $CKconfig['contentsJs'] = array_merge($CKconfig['contentsJs'], array($CKconfig['script']));
        } else {
            $CKconfig['contentsJs'] = array($CKconfig['contentsJs'], $CKconfig['script']);
        }

        // Set up stripping some elements from html-code if Source button is toggled
        if ($field->params['sourceStripper']) $CKconfig['sourceStripper'] = $field->params['sourceStripper'];

        // take in attention of STD
        if (is_array($CKconfig['contentsCss'])) {
            for ($i = 0; $i < count($CKconfig['contentsCss']); $i++) {
                if (preg_match('/^\/.*\.css$/', $CKconfig['contentsCss'][$i])) {
                    $CKconfig['contentsCss'][$i] = STD . $CKconfig['contentsCss'][$i];
                }
            }
        }
        if (is_array($CKconfig['contentsJs'])) {
            for ($i = 0; $i < count($CKconfig['contentsJs']); $i++) {
                if (preg_match('/^\/.*\.js$/', $CKconfig['contentsJs'][$i])) {
                    $CKconfig['contentsJs'][$i] = STD . $CKconfig['contentsJs'][$i];
                }
            }
        }
        $CKconfig['language'] = Indi::ini('view')->lang;

        ob_start();?>
        <div id="i-section-<?=Indi::trail()->section->alias?>-action-<?=Indi::trail()->action->alias?>-row-<?=Indi::trail()->row->id?>-field-<?=$name?>-html">
        <textarea id="<?=$name?>" name="<?=$name?>"><?=str_replace(array('<','>'), array('&lt;','&gt;'), $value)?></textarea>
        <script>
            CKFinder.setupCKEditor(null, '<?=STD?>/library/ckfinder/');
            <?$ns = 'i-section-' . Indi::trail()->section->alias . '-action-' . Indi::trail()->action->alias . '-row-' . Indi::trail()->row->id . '-field-' . $name . '-html'?>
            window['<?=$ns.'-config'?>'] = <?=json_encode($CKconfig)?>;

            window['<?=$ns.'-config'?>'].toolbar = [
                {items: ['Source', 'Preview'] },
                {items: [ 'Paste', 'PasteText', 'PasteFromWord', 'Table'] },
                {items: [ 'Image', 'Flash', 'oembed','Link', 'Unlink'] },
                {items: [ 'Bold', 'Italic', 'Underline'] },
                {items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent'] },
                {items: ['JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'] },
                {items: ['Format'] },
                {items: ['Font'] },
                {items: ['FontSize' ] },
                {items: [ 'TextColor', 'BGColor', '-', 'Blockquote', 'CreateDiv' ] },
                {items: [ 'Maximize', 'ShowBlocks', 'Find', '-', 'RemoveFormat'  ] }
            ];
            window['<?=$ns.'-config'?>'].enterMode = CKEDITOR.ENTER_BR;
            window['<?=$ns.'-config'?>'].bodyId = '<?=$ns?>';

            CKEDITOR.replace('<?=$name?>', window['<?=$ns.'-config'?>']);$('#td-wide-<?=$name?>').css('padding-bottom', '1px');$('#tr-<?=$name?>').css('padding-bottom', '1px');
        </script>
        </div>
        <? $xhtml = ob_get_clean();

        return $xhtml;
    }
}