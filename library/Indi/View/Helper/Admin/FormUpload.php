<?php
class Indi_View_Helper_Admin_FormUpload extends Indi_View_Helper_FormElement
{
    public function formUpload($name = null, $copy = null, $silence = true, $entity = null, $id = null)
    {
        static $index = null;

        $xhtml = '';
        
        $entity = $entity ? $entity : $this->view->entity->table;
        $id = $id ? $id : $this->view->row->id;

        if ($name === null) {
            if ($index !== null) {
                $index++;
                $name = $index;
            } else {
                $index = 1;
            }
        }
		// pattern and paths
		$pattern  = $id . ($name ? '_' . $name : '') . ($copy ? ',' . $copy : '') . '.*';
		$config = Indi_Registry::get('config');
		$relative = '/' . trim($config['upload']->uploadPath, '/') . '/' . $entity  . '/';
		$absolute = rtrim($_SERVER['DOCUMENT_ROOT'], '\\/') . $relative;
		$file = glob($absolute . $pattern); $file = $file[0];
		if ($file) {
			// get info about mime type
//			$finfo = finfo_open(FILEINFO_MIME);
//			$info = explode(';', finfo_file($finfo, $file));
//			$mime = $info[0];
//			list($type, $exaction) = explode('/', $mime);
			$types = array('image' => 'gif,png,jpg', 'flash' => 'swf', 'video' => 'avi,mpg,mp4,3gp');
			$info = pathinfo($file);
			foreach ($types as $type => $extensions) if (in_array($info['extension'], explode(',', $extensions))) break;
			$xhtml = '<field>';
			switch ($type) {
				case 'image':
					$uploaded = $this->view->image($entity, $id, $name, $copy, $silence) . '<br>';
					break;
				case 'flash':
					$uploaded = $this->view->flash($entity, $id, $name, $silence) . '<br>';
					break;
				case 'video':
					$uploaded = 'Video in format ' . $info['extension'] . ' - <a href="' . $relative . $info['basename'] . '" target="_blank">Download</a> ,';
					break;
				default:
					$uploaded = 'File in format ' . $info['extension'] . ' - <a href="' . $relative . $info['basename'] . '" target="_blank">Download</a> ,';
					break;
			}

			$xhtml .= '<controls class="upload">';
			$xhtml .= '<input type="hidden" name="image[]" value="' . $name . '" id="image' . $name . '">';
			$xhtml .= '<span class="checkbox" style="display: inline;"><label for="image' . $name . '">Удалить</label></span>';
			$xhtml .= '<js>$("span.checkbox").click(function(){
				if ($(this).parents("field").find("input[type=hidden]").attr("checked") == "checked") {
					$(this).parents("field").find("input[type=hidden]").removeAttr("checked").attr("disabled", "disabled");
					$(this).removeClass("checked");
				} else {
					$(this).parents("field").find("input").attr("checked", "checked").removeAttr("disabled");
					$(this).addClass("checked");
				}
			})</js>';
		} else {
			$xhtml .= '<controls>';
		}
        $xhtml .= '<input type="file" name="image[' . $name .']"/>';
		$xhtml .= '</controls>';
		$xhtml .= $uploaded;
		$xhtml .= '</field>';
        return $xhtml;
    }
}