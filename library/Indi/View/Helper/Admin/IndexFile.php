<?php
class Indi_View_Helper_Admin_IndexFile {
    public function indexFile($name = null, $copy = null, $silence = true, $entity = null, $id = null)
    {
        static $index = null;

        $xhtml = '';
        
        $entity = $entity ? $entity : Indi::trail()->model->table();
        $id = $id ? $id : Indi::view()->row->id;

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
		$relative = '/' . trim(Indi::ini()->upload->path, '/') . '/' . $entity  . '/';
		$absolute = rtrim($_SERVER['DOCUMENT_ROOT'], '\\/') . $relative;
		$file = glob($absolute . $pattern); $file = $file[0];
		if ($file) {
			$types = array('image' => 'gif,png,jpg,jpeg,bmp', 'flash' => 'swf', 'video' => 'avi,mpg,mp4,3gp', 'other' => '');
			$info = pathinfo($file);
			foreach ($types as $type => $extensions) if (in_array($info['extension'], explode(',', $extensions))) break;
			switch ($type) {
				case 'image':
					$xhtml = Indi::view()->img($entity, $id, $name, $copy, $silence) . '<br>';
					break;
				case 'flash':
					$xhtml = Indi::view()->swf($entity, $id, $name, $silence) . '<br>';
					break;
				case 'video':
					$xhtml = 'Video in format ' . $info['extension'] . ' - <a href="' . $relative . $info['basename'] . '" target="_blank">Download</a> ,';
					break;
				default:
					$xhtml = 'File in format ' . $info['extension'] . ' - <a href="' . $relative . $info['basename'] . '" target="_blank">Download</a> ,';
					break;
			}
		}
        return $xhtml;
    }
}