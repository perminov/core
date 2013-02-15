<?php
/**
 * Controller for auxillary abilities
 *
 */
class Admin_AuxillaryController extends Indi_Controller
{
    /**
     * Provide ability to use yahoo color picker
     *
     */
    public function colorpickerAction()
    {
		$p = '/js/admin/';
		$name = $this->params['name'];
        $out = '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<script type="text/javascript" src="' . $p .'index.js" ></script>
<!-- Dependencies --> 
<script type="text/javascript" src="' . $p .'colorpicker/yahoo/utilities/utilities.js" ></script>
<script type="text/javascript" src="' . $p .'colorpicker/yahoo/slider/slider-min.js" ></script>
<!-- Color Picker source files for CSS and JavaScript -->
<link rel="stylesheet" type="text/css" href="' . $p .'colorpicker/yahoo/colorpicker/assets/skins/sam/colorpicker.css"> 
<script type="text/javascript" src="' . $p .'colorpicker/yahoo/colorpicker/colorpicker-beta-min.js" ></script>
</head>
<body class="yui-skin-sam" style="background-color: white;">
<div id="container"></div>
<script>
<!--
    // set up "input" variable to point at text field, assotiated with color picker
    var input = top.window.document.getElementById("' . $name . '" + "Input");
    var input = input ? input : new Object();
    // init yahoo color picker
    var picker = new YAHOO.widget.ColorPicker("container", {
        images: {
            PICKER_THUMB: "' . $p .'colorpicker/yahoo/colorpicker/assets/picker_thumb.png",
            HUE_THUMB: "' . $p .'colorpicker/yahoo/colorpicker/assets/hue_thumb.png"
        }
    });

    //get current or default value from hex 2 dec
    var hexSummary = "";
    hexSummary = input.value ? input.value : "#FFFFFF";
    hexSummary = hexSummary.replace("#","");
    var dec = new Array();
    dec[0] = new Number("0x" + hexSummary.substr(0, 2)).toString(10);
    dec[1] = new Number("0x" + hexSummary.substr(2, 2)).toString(10);
    dec[2] = new Number("0x" + hexSummary.substr(4, 2)).toString(10);

    //set current value:
    picker.setValue(dec, false); 
    
    // define function that change value in text field, assotiated with color picker
    var onRgbChange = function(o) {
        input.value = "#" + $("yui-picker-hex-summary").innerText;
    }

    //subscribe to the rgbChange event;
    picker.on("rgbChange", onRgbChange);

    // disable viewing other color picker staff, that is not nessary
    $("yui-picker-websafe-swatch").style.display = "none";
    $("yui-picker-controls").style.display = "none";
    $("yui-picker-swatch").style.display = "none";
-->    
</script>
</body>
</html>';
        die($out);
    }
	public function arrowAction(){
		if ($this->params['color'] == 'red') {
			if ($this->params['number']) {
				$png = rtrim($_SERVER['DOCUMENT_ROOT'] . '/www', '\\/') . '/i/map-arrow-shadow-red.png';
			} else {
				$png = rtrim($_SERVER['DOCUMENT_ROOT'] . '/www', '\\/') . '/i/map-arrow-shadow-red-offset.png';
			}
		} else {
			$png = rtrim($_SERVER['DOCUMENT_ROOT'] . '/www', '\\/') . '/i/map-arrow-shadow-orange.png';
		}
		$calibri = rtrim($_SERVER['DOCUMENT_ROOT'] . '/www', '\\/') . '/data/fonts/calibrib.ttf';
		header("Content-type: image/png"); 
		$string = $this->params['number'];
		$im     = imagecreatefrompng($png); 
		imagealphablending($im, true);
		imagesavealpha($im, true);
		$white = imagecolorallocate($im, 255, 255, 255); 
//		$px     = (imagesx($im) - 7.5 * strlen($string)) / 2; 
		$px     = (26  - 7.5 * strlen($string)) / 2; 
		if (Misc::number($string)) imagettftext($im, 10, 0, $px, 18, $white, $calibri, $string);
		imagepng($im); 
		imagedestroy($im); 
		die();
	}
	
	public function autocompleteAction(){
		$json = array();
		$limit = 10;
		if ($this->post['id'] && $this->post['value']) {
			$field = Misc::loadModel('Field')->fetchRow('`id` = "' . (int) $this->post['id'] . '"');
			$params = $field->getParams();
			if ($field->relation && $model = Entity::getModelById($field->relation)) {
				$rs = $model->fetchAll('`title` LIKE "%' .  strip_tags($this->post['value']) .'%"', null, $limit);
				$fields = $field->getTable()->getFieldsByEntityId($field->relation)->toArray();
				$foreign = array();
				$local = array();
				$data = array();
				if (trim($params['appendPattern'])) {
					// Detect foreign keys
					preg_match_all('/[a-zA-Z0-9]+\.[a-zA-Z0-9]+/', $params['appendPattern'], $matches);
					$tforeign = array();
					for ($i = 0; $i < count($matches[0]); $i++) {
						$pair = explode('.', $matches[0][$i]);
						$tforeign[$pair[0]] = $pair[1];
					}
					for ($i = 0; $i < count($fields); $i++) {
						if ($fields[$i]['relation'] && in_array($fields[$i]['alias'], array_keys($tforeign))) {
							$foreign[$fields[$i]['alias']] = $tforeign[$fields[$i]['alias']];
						} else {
							if (preg_match('/[^\.]' . $fields[$i]['alias'] . '/', $params['appendPattern'])) {
								$local[] = $fields[$i]['alias'];
							}
						}
					}
					if (count($foreign)) $rs->setForeignRowsByForeignKeys(implode(',', array_keys($foreign)));
				}
				if (trim($params['additionalData'])) {
					$tdataFields = explode(',', $params['additionalData']);
					for ($i = 0; $i < count($tdataFields); $i++) $tdataFields[$i] = trim($tdataFields[$i]);
					for ($i = 0; $i < count($fields); $i++) {
						if (in_array($fields[$i]['alias'], $tdataFields)) {
							$data[] = $fields[$i]['alias'];
						}
					}
				}
				$options = array();
				foreach($rs as $r) {
					$options[$r->id]['text'] = $r->getTitle();
					$additional = $params['appendPattern'];
					foreach ($foreign as $foreignKey => $foreignEntityField) {
						$additional = str_replace($foreignKey . '.' . $foreignEntityField, $r->foreign[$foreignKey][$foreignEntityField], $additional);
					}
					for ($i = 0; $i < count($local); $i++) {
						$additional = str_replace($local[$i], $r->{$local[$i]}, $additional);
					}
					$options[$r->id]['text'] .= $additional;
					for ($i = 0; $i < count($data); $i++) {
						$options[$r->id]['data'][$data[$i]] = $r->{$data[$i]};
					}
				}
				$json = array('general' => $model->info('name').'Id', 'options' => $options);
				die(json_encode($json));
			}
		}
		die(json_encode($json));
	}
	public function downloadAction(){
		$this->params['id'] = (int) $this->params['id'];
		$this->params['field'] = (int) $this->params['field'];
		if (!$this->params['id'] || !$this->params['field']) {
			die('Ошибка входных данных');
		} else {
			$fieldR = Misc::loadModel('Field')->fetchRow('`id` = "' . $this->params['field'] . '"');
			if ($fieldR) {
				$entityR = Misc::loadModel('Entity')->fetchRow('`id` = "' . $fieldR->entityId . '"');
				if ($entityR) {
					$itemR = Entity::getModelById($entityR->id)->fetchRow('`id` = "' . $this->params['id'] . '"');
					if ($itemR) {
						$pattern  = $itemR->id . ($fieldR->alias ? '_' . $fieldR->alias : '') . '.*';
						$config = Indi_Registry::get('config');
						$relative = '/' . trim($config['upload']->uploadPath, '/') . '/' . $entityR->table  . '/';
						$absolute = rtrim($_SERVER['DOCUMENT_ROOT'], '\\/') . $relative;
						$file = glob($absolute . $pattern); $file = $file[0];
						$info = pathinfo($file);
						if (file_exists($file)) {
							$title = $entityR->title . ' ' . $itemR->getTitle() . ' - ' . $fieldR->title  . '.' . $info['extension'];
							$title = str_replace('+', '%20', urlencode($title));
							header('Content-Type: application/octet-stream');
							header('Content-Disposition: attachment; filename="' . $title . '";');
							readfile($file);
							die();
						} else die("Ошибка: файл не существует");
					} else die("Ошибка: запись не существует");
				} else die("Ошибка: сущность не существует");
			} else die("Ошибка: поле не существует");
		}
		die("Ok");
	}
}