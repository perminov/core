<?php
/**
 * Controller for auxillary abilities
 *
 */
class Admin_AuxillaryController extends Indi_Controller
{
    public function preDispatch(){
        eval('$this->' . Indi::uri('action') . 'Action();');
    }
    /**
     * Provide ability to use yahoo color picker
     *
     */
    public function colorpickerAction()
    {
		$p = STD . '/js/admin/';
		$name = Indi::uri()->name;
        $out = '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<!-- Dependencies -->
<script type="text/javascript" src="' . $p .'colorpicker/yahoo/utilities/utilities.js" ></script>
<script type="text/javascript" src="' . $p .'colorpicker/yahoo/slider/slider-min.js" ></script>
<!-- Color Picker source files for CSS and JavaScript -->
<link rel="stylesheet" type="text/css" href="' . $p .'colorpicker/yahoo/colorpicker/assets/skins/sam/colorpicker.css"> 
<script type="text/javascript" src="' . $p .'colorpicker/yahoo/colorpicker/colorpicker-beta-min.js" ></script>
<style>
.yui-picker-thumb img{
position: relative;
top: -4px;
}
</style>
</head>
<body class="yui-skin-sam" style="background-color: white;">
<div id="container"></div>
<script>
<!--
function strip_tags( str ){
    return str.replace(/<\/?[^>]+>/gi, "");
}
function $(id){
    return document.getElementById(id);
}
    // set up "input" variable to point at text field, assotiated with color picker
    var input = top.frames["form-frame"].document.getElementById("' . $name . '" + "Input");
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
    hexSummary = input.value ? input.value : "#FFFFF2";
    hexSummary = hexSummary.replace("#","");
    var dec = new Array();
    dec[0] = new Number("0x" + hexSummary.substr(0, 2)).toString(10);
    dec[1] = new Number("0x" + hexSummary.substr(2, 2)).toString(10);
    dec[2] = new Number("0x" + hexSummary.substr(4, 2)).toString(10);

    //set current value:
    picker.setValue(dec, false);

    function dec2hex(n){
        n = parseInt(n); var c = "ABCDEF";
        var b = n / 16; var r = n % 16; b = b-(r/16);
        b = ((b>=0) && (b<=9)) ? b : c.charAt(b-10);
        return ((r>=0) && (r<=9)) ? b+""+r : b+""+c.charAt(r-10);
    }

    // define function that change value in text field, assotiated with color picker
    var onRgbChange = function(o) {
        input.value = "#" + dec2hex(o.newValue[0]) + dec2hex(o.newValue[1]) + dec2hex(o.newValue[1]);
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
	public function downloadAction(){
		Indi::uri()->id = (int) Indi::uri()->id;
		Indi::uri()->field = (int) Indi::uri()->field;
		if (!Indi::uri()->id || !Indi::uri()->field) {
			die('Ошибка входных данных');
		} else {
			$fieldR = Indi::model('Field')->fetchRow('`id` = "' . Indi::uri()->field . '"');
			$params = $fieldR->getParams();
			if ($fieldR) {
				$entityR = Indi::model('Entity')->fetchRow('`id` = "' . $fieldR->entityId . '"');
				if ($entityR) {
					$itemR = Indi::model($entityR->id)->fetchRow('`id` = "' . Indi::uri()->id . '"');
					if ($itemR) {
						$pattern  = $itemR->id . ($fieldR->alias ? '_' . $fieldR->alias : '') . '.*';
						$relative = '/' . trim(Indi::ini()->upload->path, '/') . '/' . $entityR->table  . '/';
						$absolute = rtrim($_SERVER['DOCUMENT_ROOT'], '\\/') . STD . $relative;
						$file = glob($absolute . $pattern); $file = $file[0];
						$info = pathinfo($file);
						if (file_exists($file)) {
							$title = ($params['prependEntityTitle'] != 'false' ? $entityR->title . ' ' : '') . $itemR->title . ($params['appendFieldTitle'] != 'false' ? ' - ' . $fieldR->title : '')  . '.' . $info['extension'];
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

    public function updateAction() {
        die('ok');
    }
}
