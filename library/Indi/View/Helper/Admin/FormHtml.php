<?php
class Indi_View_Helper_Admin_FormHtml extends Indi_View_Helper_Abstract
{
    public function formHtml($name, $value = null, $height = 300, $toolbar = 'Custom')
    {
        $toolbar = $toolbar ? $toolbar : 'Default';
        if ($value === null) {
            $value = $this->view->row->$name;
        }

		$field = $this->view->trail->getItem()->getFieldByAlias($name);
		$params = $field->getParams();

        $customParams = array('width','height','bodyClass','style','script','sourceStripper');
        foreach($customParams as $customParam) {
            if ($this->view->row->{$name . ucfirst($customParam)}) {
                $params[$customParam] = $this->view->row->{$name . ucfirst($customParam)};
            }
        }

        // Set up styles configuration for editor contents
        if ($params['style']) $CKconfig['style'] = $params['style'];
        $CKconfig['style'] .= 'body{max-width: auto;min-width: auto;width: auto;}';
        if ($params['contentsCss']) $CKconfig['contentsCss'] = preg_match('/^\[/', $params['contentsCss']) ? json_decode($params['contentsCss']) : $params['contentsCss'];
        if (is_array($CKconfig['contentsCss'])) {
            $CKconfig['contentsCss'] = array_merge($CKconfig['contentsCss'], array($CKconfig['style'] . ' body{max-width: auto;min-width: auto;width: auto;}'));
        } else {
            $CKconfig['contentsCss'] = array($CKconfig['contentsCss'], $CKconfig['style'] . ' body{max-width: auto;min-width: auto;width: auto;}');
        }
        if ($params['bodyClass']) $CKconfig['bodyClass'] = $params['bodyClass'];
        $CKconfig['uiColor'] = '#B8D1F7';

        // Set up editor size
        if ($params['width']) $CKconfig['width'] = $params['width'] + 52;
        if ($params['height']) $CKconfig['height'] = $params['height'];

        // Set up editor javascript
        if ($params['script']) $CKconfig['script'] = $params['script'];
        if ($params['contentsJs']) $CKconfig['contentsJs'] = preg_match('/^\[/', $params['contentsJs']) ? json_decode($params['contentsJs']) : $params['contentsJs'];
        if (is_array($CKconfig['contentsJs'])) {
            $CKconfig['contentsJs'] = array_merge($CKconfig['contentsJs'], array($CKconfig['script']));
        } else {
            $CKconfig['contentsJs'] = array($CKconfig['contentsJs'], $CKconfig['script']);
        }

        // Set up stripping some elements from html-code if Source button is toggled
        if ($params['sourceStripper']) $CKconfig['sourceStripper'] = $params['sourceStripper'];

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
        $CKconfig['language'] = Indi::registry('config')->view->lang;

        ob_start();?>
        <textarea id="<?=$name?>" name="<?=$name?>"><?=str_replace(array('<','>'), array('&lt;','&gt;'), $value)?></textarea>
        <script>
            CKFinder.setupCKEditor(null, '<?=STD?>/library/ckfinder/');
            var config = <?=json_encode($CKconfig)?>;

            config.toolbar = [
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
            config.enterMode = CKEDITOR.ENTER_BR;

            CKEDITOR.replace('<?=$name?>', config);$('#td-wide-<?=$name?>').css('padding-bottom', '1px');$('#tr-<?=$name?>').css('padding-bottom', '1px');
        </script>
        <? $xhtml = ob_get_clean();

        return $xhtml;
    }
}