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
			switch ($type) {
				case 'image':
					$xhtml = $this->view->image($entity, $id, $name, $copy, $silence) . '<br>';
					break;
				case 'flash':
					$xhtml = $this->view->flash($entity, $id, $name, $silence) . '<br>';
					break;
				case 'video':
					$xhtml = 'Video in format ' . $info['extension'] . ' - <a href="' . $relative . $info['basename'] . '" target="_blank">Download</a> ,';
					break;
				default:
					$xhtml = 'File in format ' . $info['extension'] . ' - <a href="' . $relative . $info['basename'] . '" target="_blank">Download</a> ,';
					break;
			}
			$xhtml .= '<input type="checkbox" name="image[]" value="' . $name . '" id="image' . $name . '" style="width:13px; height: 13px; position: relative; top: 3px;"/><label for="image' . $name . '">delete</label><br>';
		}
        $xhtml .= '<input type="file" name="image[' . $name .']"/>';
        return $xhtml;
    }
}