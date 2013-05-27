<?php
abstract class Indi_Image
{
    /**
     * Get upload path from registry
     *
     * @return string
     */
    public function getUploadPath()
    {
		$config = Indi_Registry::get('config');
        return trim($config['upload']->uploadPath, '\\/');
    }
    
    /**
     * Delete images checked on cms edit screens
     *
     * @param string $entity
     * @param int $id
     * @throws Exception
     */
    public function deleteEntityImagesIfChecked($entity = null, $id = null)
    {
		// entity and identfier by default set up as values, got from
        // current controller
        $entity = $entity ? $entity : $this->section->getForeignRowByForeignKey('entityId')->table;
        $id = $id ? $id : $this->identifier;
        
        // get upload path from config
        $uploadPath = self::getUploadPath();
        
        // absolute upload path  in filesystem
        $absolute = $_SERVER['DOCUMENT_ROOT'] . $_SERVER['STD'] .  '/' . $uploadPath . '/' . $entity . '/';
        // get images names that are checked to be deleted
        $post = Indi_Registry::get('post');
		$imagesToDelete = array();
        //$imagesToDelete = $post['image'];
		if (is_array($post['file-action'])) foreach ($post['file-action'] as $file => $action) if ($action == 'd') $imagesToDelete[] = $file;
		for ($i = 0; $i < count($imagesToDelete); $i++) {
			// all resized copies are to be deleted too
            $files = array();
            if (in_array($imagesToDelete[$i], array ('','1'))) {
                $pattern = $id . ',*.*';
                $files = glob($absolute . $pattern);
                $pattern = $id . '.*';
                $files = array_merge($files, glob($absolute . $pattern));
            }
            $files = array_merge(glob($absolute . $id . (!in_array($imagesToDelete[$i], array('','1')) ? '_' . $imagesToDelete[$i] : '') . '*.*'), $files);
			$files = array_unique($files);
			for ($j = 0; $j < count($files); $j++) {
                try {
                    unlink($files[$j]);
                } catch (Exception $e) {
                    throw new Exception($e->__toString());
                }
            }
        }
    }

    /**
     * Upload images
     *
     * @param string $entity
     * @uses self::getUploadPath()
     * @param int $id
     */
    public function uploadEntityImagesIfBrowsed($entity = null, $id = null, $requirements = array())
    {
        // entity and identfier by default set up as values, got from
        // current controller
        $entity = $entity ? $entity : $this->section->getForeignRowByForeignKey('entityId')->table;
        $id = $id ? $id : $this->identifier;

        // get upload path from config
        $uploadPath = self::getUploadPath();
        
        // absolute upload path  in filesystem
        $absolute = $_SERVER['DOCUMENT_ROOT'] . $_SERVER['STD'] . '/' . $uploadPath . '/' . $entity . '/';

		if (!$requirements) {
			$requirements = array('type' => 'image', 'maxsize' => 1024 * 1024 * 5);
		}
		//d($requirements);
        $files = Indi_Registry::get('files');
        $images = $files['image'];
		$failInfo = array();

		$post = Indi_Registry::get('post');
		$imagesToUpload = array();
		if (is_array($post['file-action'])) foreach ($post['file-action'] as $file => $action) if ($action == 'm') $imagesToUpload[] = $file;
		if (count($imagesToUpload)) foreach ($images['tmp_name'] as $name => $tmp) {
            if (in_array($name, $imagesToUpload) && $images['error'][$name] == 0) {
				if ($requirements) {
					if ($requirements['type']) {
						$userFile = $images['name'][$name];
						$info = explode('/', Indi_Image::m_content_type($userFile));
						if ($info[0] != $requirements['type']) {
							$failInfo['type'][] = $images['name'][$name];
							continue;
						}
					}
					if ($images['size'][$name] > $requirements['maxsize']) {
						$failInfo['maxsize'][] = $images['name'][$name];
						continue;
					}
					if ($requirements['ext']) {
						$userFile = $images['name'][$name];
						$info = pathinfo($userFile);
						if (!in_array($info['extension'], explode(',', $requirements['ext']))) {
							$failInfo['type'][] = $images['name'][$name];
							continue;
						}
					}
				}
                try {
					// check if entity images directory is exists
					if (!is_dir($absolute)) {
						self::createEntityImagesDir($absolute);
					}
					// check if entity images directory is writable
					if (!is_writable($absolute)) {
						throw new Exception('Directory "' . $absolute . '" is not writable. Please change permitions.');
					}
                    self::deletePreviousEntityImage($name);
                    $info = pathinfo($images['name'][$name]);
                    $dst = $absolute . $id . (!in_array($name, array('0','1')) ? '_' . $name : '') . '.' . strtolower($info['extension']);
					
			        if(!move_uploaded_file($tmp, $dst)) copy($tmp, $dst);
					$entityId = $this->trail ? $this->trail->getItem()->fields[0]->entityId : $this->section->entityId;
					$entityId = Misc::loadModel('Entity')->fetchRow('`table` = "' . $entity . '"')->id;
					$copies = Misc::loadModel('Resize')->fetchAll('`fieldId` = (SELECT `id` FROM `field` WHERE `alias`="' . $name . '" AND `entityId`="' . $entityId . '")')->toArray();
					for ($i = 0; $i < count($copies); $i++) {
						switch($copies[$i]['proportions']){
							case 'o': // original
								$size = getimagesize($dst);
								$size = $size[0] . 'x' . $size[1];
								break;
							case 'c':
								$size = $copies[$i]['masterDimensionValue'] . 'x' . $copies[$i]['slaveDimensionValue'];
								break;
							case 'p':
								$size = array($copies[$i]['masterDimensionValue'], $copies[$i]['slaveDimensionValue']);
								if ($copies[$i]['masterDimensionAlias'] == 'height'){
									$size = array_reverse($size);
								}
								if($copies[$i]['slaveDimensionLimitation']) $size[1] .= 'M';
								$size = implode('x', $size);
								break;
							default:
								$size = '';
								break;
						}
	                    Indi_Image::resize($dst, $copies[$i]['alias'], $size);
					}
                } catch (Exception $e) {
                    throw new Exception($e->__toString());
                }
            }
        }
		if ($failInfo) Indi_Registry::set('uploadFails', $failInfo);
    }

    /**
     * Performs attempt to create an entity images dir
     *
     * @param string $dir
     * @throws Exception
     */
    public function createEntityImagesDir($dir)
    {    
        if (!mkdir($dir, 0777)) {
			die('Cannot create dir ' . $dir);
            throw new Exception($e->__toString());
        }
    }

    public function deletePreviousEntityImage($name)
    {
        // entity and identfier by default set up as values, got from
        // current controller
        $entity = $entity ? $entity : $this->section->foreignRows->entityId->table;
        $id = $id ? $id : $this->identifier;

        // get upload path from config
        $uploadPath = self::getUploadPath();
        
        // absolute upload path  in filesystem
        $absolute = rtrim($_SERVER['DOCUMENT_ROOT'], '\\/') . $_SERVER['STD'] . '/' . $uploadPath . '/' . $entity . '/';
        
        // all resized copies are to be deleted too
        $files = array();
        if ($name == '1') {
            $pattern = $id . ',*.*';
            $files = glob($absolute . $pattern);
            $pattern = $id . '.*';
            $files = array_merge($files, glob($absolute . $pattern));
        }
//		d($files);
        $files = @array_unique(array_merge(glob($absolute . $id . '_' . $name . '*.*'), $files));
		for ($j = 0; $j < count($files); $j++) {
            try {
                unlink($files[$j]);
            } catch (Exception $e) {
                throw new Exception($e->__toString());
            }
        }
    }

    public function rgb2hsl($rgb)
    {
        $varR = $rgb[0] / 255; //Where RGB values = 0 ? 255
        $varG = $rgb[1] / 255;
        $varB = $rgb[2] / 255;
    
        $varMin = min($varR, $varG, $varB );    //Min. value of RGB
        $varMax = max($varR, $varG, $varB );    //Max. value of RGB
        $delMax = $varMax - $varMin;             //Delta RGB value
    
        $l = ($varMax + $varMin) / 2;
    
        if ($delMax == 0) {    //This is a gray, no chroma...
            $H = 0;         //HSL results = 0 ? 1
            $S = 0;
        } else {            //Chromatic data...
            if ($l < 0.5 ) {
                $s = $delMax / ($varMax + $varMin);
            } else {
                $s = $delMax / (2 - $varMax - $varMin);
            }
    
            $delR = ((($varMax - $varR) / 6) + ($delMax / 2)) / $delMax;
            $delG = ((($varMax - $varG) / 6) + ($delMax / 2)) / $delMax;
            $delB = ((($varMax - $varB) / 6) + ($delMax / 2)) / $delMax;
    
            if ($varR == $varMax){
                $h = $delB - $delG;
            } else if ($varG == $varMax) {
                $h = (1 / 3) + $delR - $delB;
            } else if ($varB == $varMax) {
                $h = (2 / 3) + $delG - $delR;
            }
            if ($h < 0) {
                $h++;
            }
            if ($h > 1) {
                $h--;
            }
        }
        return array($h, $s, $l);
    }
    public function hsl2rgb($hsl)
    {
        $h = $hsl[0];
        $s = $hsl[1];
        $l = $hsl[2];
    
        if ($s == 0) {        //HSL values = 0 ? 1
            $r = $l * 255;  //RGB results = 0 ? 255
            $g = $l * 255;
            $b = $l * 255;
        } else {
            if ($l < 0.5) {
                $var2 = $l * (1 + $s);
            } else {
                $var2 = ($l + $s)-($s * $l);
            }
            $var1 = 2 * $l - $var2;
            $r = round(255 * self::hue2rgb($var1, $var2, $h + (1/3))); 
            $g = round(255 * self::hue2rgb($var1, $var2, $h));
            $b = round(255 * self::hue2rgb($var1, $var2, $h - (1/3)));
            
        }
        return  array($r, $g, $b);
    }
    public function hue2rgb($v1, $v2, $vH)
    {
        if ($vH < 0) {
            $vH++;
        }
        if ($vH > 1) {
            $vH--;
        }
        
        if ((6 * $vH) < 1) {
            return ($v1 + ($v2 - $v1) * 6 * $vH);
        }
        if ((2 * $vH) < 1) {
            return ($v2);
        }
        if ((3 * $vH) < 2) {
            return ($v1 + ($v2 - $v1) * ((2 / 3) - $vH) * 6);
        }
        return ($v1);
    }
    public function rgb2gray($rgb)
    {
        $val = round((0.299 * $rgb[0]) + (0.587 * $rgb[1]) + (0.114 * $rgb[2]));
        return array($val, $val, $val);
    }
    
    public function setColor($oldFile, $color = '#CCCCCC', $newFile = null)
    {
        list ($r, $g, $b) = self::hex2decRGB($color);
        
        $oldImage =imagecreatefrompng($oldFile);
//		imagealphablending($oldImage, false);
//		imagesavealpha($oldImage, true);

        $oldX = imagesx($oldImage);
        $oldY = imagesy($oldImage);
    
        $newImage = imagecreatetruecolor($oldX, $oldY);
//		imagealphablending($newImage, false);
//		imagesavealpha($newImage, true);


        for ($x = 0; $x < $oldX; $x++) {
            for ($y = 0; $y < $oldY; $y++) {
                // original color
                $_rgb = ImageColorAt($oldImage, $x, $y);
                
                // if pixel color is NOT white, and NOT LIKE white at all
                if ($_rgb < 15523000) {
                    // split 24 bit color in RGB values
                    $rgb[0] = ($_rgb >> 16) & 0xFF;
                    $rgb[1] = ($_rgb >> 8) & 0xFF;
                    $rgb[2] = $_rgb & 0xFF;
        
                    // convert to grayscale
                    $rgb = self::rgb2gray($rgb);
        
                    // convert to HSL
                    $hsl = self::rgb2hsl($rgb);
                    
                    // set requied color
                    $toHsl = self::rgb2hsl(array($r, $g, $b));
        
                    // change hue, saturation and ligthness values from pixel
                    $hsl[0] = $toHsl[0];
                    $hsl[1] = $toHsl[1];
                    $hsl[2] = $hsl[2] + ($toHsl[2] - 0.5);
                    
                    // convert from hsl to rgb
                    $rgb = self::hsl2rgb($hsl);
        
                    // merge from RGB to 24 bit
                    $_rgb = intval(($rgb[2]) + ($rgb[1] << 8) + ($rgb[0] << 16));
        
                }
                // draw pixel in new image
//                imagesetpixel($newImage, $x, $y, $_rgb);
                $r1 = ($_rgb >> 16) & 0xFF;
                $g1 = ($_rgb >> 8) & 0xFF;
                $b1 = $_rgb & 0xFF;				
				$c = imagecolorallocatealpha($newImage, $r1, $g1, $b1, 0);
				imagesetpixel($newImage, $x, $y, $c);
            }
        }
        if ($newFile) {
            imagepng($newImage, $newFile);
        } else {
            header('Content-type: image/png');
            imagepng($newImage, null);
        }
    }

    public function createImageFromText($imageIndex, $text = 'No title', $size = 13, $textColor = null, $bgColor = null, $font = 'comicbd.ttf')
    {
        // text box info determinig for new pic
        $textBoxInfo = imagettfbbox($size, 0, $font, $text);

        // default text color value
        if (!$textColor) {
            $textColor = $this->row->color;
            if (!$textColor) {
                $textColor = '#000000';
            }
        }
        
        // default background color value
        if (!$bgColor) {
            $bgColor = '#FFFFFF';
        }

        // text color as array of r,g,b in decimal format
        $textColorRGBarray = self::hex2decRGB($textColor);

        // background color as array of r,g,b in decimal format
        $bgColorRGBarray = self::hex2decRGB($bgColor);
        
        // image dimensions
        $width  = $textBoxInfo[2] + 1;
        $height = abs($textBoxInfo[5]-$textBoxInfo[1]) + 2;
        
        // image resource
        $im = imagecreatetruecolor($width, $height);
        
        // text color for image resource
        $resourceTextColor = imagecolorallocate($im, $textColorRGBarray[0], $textColorRGBarray[1], $textColorRGBarray[2]);
        
        // background color for image resource
        $resourceBgColor = imagecolorallocate($im, $bgColorRGBarray[0], $bgColorRGBarray[1], $bgColorRGBarray[2]);
        
        // fill image with background color
        imagefill($im, 0, 0, $resourceBgColor);
        
        // write text on image
        imagettftext($im, $size, 0, 0, $height-$textBoxInfo[1] - 2, $resourceTextColor, $font, $text);
        
        // get file name
        $file = self::getNewImageFileName($imageIndex);

        imagepng($im, $file);
    }

    
    function getNewImageFileName($imageName = '', $id = null, $extension = 'png')
    {
        // entity and identfier by default set up as values, got from
        // current controller
        $entity = $entity ? $entity : $this->section->foreignRows->entityId->class;
        $id = $id ? $id : $this->identifier;
        
        // get upload path from config
        $uploadPath = self::getUploadPath();
        
        // absolute upload path  in filesystem
        $absolute = trim($_SERVER['DOCUMENT_ROOT'], '\\/') . '/' . $uploadPath . '/' . $entity . '/';

        // check if entity images directory is exists
        if (!is_dir($absolute)) {
            self::createEntityImagesDir($absolute);
        }        

        // check if entity images directory is writable
        if (!is_writable($absolute)) {
            throw new Exception('Directory "' . $absolute . '" is not writable. Please change permitions.');
        }
        
        $fileName = $absolute . $id . (!in_array($imageName, array('0','1')) ? '_' . $imageName : '') . '.' . $extension;
        
        return $fileName;
    }

    public function hex2decRGB($color)
    {
        $color = str_replace(array('\\','#'), array('', ''), $color);
        return array(hexdec(substr($color, 0, 2)), hexdec(substr($color, 2, 2)), hexdec(substr($color, 4, 2)));
    }
    public function image($entity, $id, $key = null, $copy = null, $silence = true, $width = null, $height = null)
    {
        if ($id) {
			$config = Indi_Registry::get('config');
            $uploadPath = $config['upload']->uploadPath;
            
            $relative = '/' . trim($uploadPath, '\\/') . '/' . $entity . '/';
            $absolute = $_SERVER['DOCUMENT_ROOT'] . $_SERVER['STD'] . $relative;
//            dump($id);

            $name = $id . ($key !== null && !empty($key) ? '_' . $key : '') . ($copy != null ? ',' . $copy : '');
            // as we do not store filename in database, so we should use glob() function to
            // deterimine file extension
            if (is_dir($absolute)) {
                if (is_writable($absolute)) {
                    $path = $absolute . $name . '.*';
					$file = glob($path);
					$fileBack = $file;
					for ($i = 0; $i < count($fileBack); $i++) {
						$info = pathinfo($fileBack[$i]);
						if (!$info['extension']) unset($file[$i]);
					}
                    if (count($file)) {
                        $pathinfo = pathinfo($file[0]);
						$mtime = substr(filemtime($file[0]), -3);
						if ($width || $height) {
							if ($width) {
								$size = ' width="' . $width . '"';
							}
							if ($height) {
								$size .= ' height="' . $height . '"';
							}
						} else {
							$size = '';
						}
                        $xhtml = '<img src="' . $_SERVER['STD'] . $relative . $name . '.' .$pathinfo['extension'] . '?'.$mtime.'" ' . $size . ' alt="" />';
                    } else if (!$silence) {
                        $xhtml = 'Cannot find any file on pattern ' . $path;
                    }
                } elseif (!$silence) {
                    $xhtml = $absolute . ' is not writable';
                }
            } else if (!$silence) {
                $xhtml = $absolute . ' is not a directory';
            }
    
            return $xhtml;
        }
    }
    /**
     * Resizes image specified by $image (full path) to $size dimensions
     * with a given quality $quality (1-100)
     *
     * usage resize('/path/to/some/image/1.jpg','small','100x50')
     * will create 1_small.jpg with size 100x50 px
     * otherwise, if 'small' set to '', 1.jpg will be resized to 100x50
     *
     * @param string $image
     * @param string $postfix
     * @param string $size
     * @param int $quality
     * @return bool
     */
    function resize($image, $postfix = '', $size = '', $quality = 100)
    {
        $type = explode('/', Indi_Image::m_content_type($image));
        if ($type[0] != 'image') return false ;
        $types = array('jpeg' => 'jpeg', 'pjpeg' => 'jpeg', 'jpg' => 'jpeg', 'gif' => 'gif', 'png' => 'png',
                       'x-png' => 'png');
        eval('$oldim = imagecreatefrom'.$types[$type[1]].'($image);');
        $oldim_w = imagesx($oldim);
        $oldim_h = imagesy($oldim);
        $size = $size ? $size : implode('x', array($oldim_w, $oldim_h));
        $dims = explode('x', $size);
        if (@ereg('M', $size)) {
            $max_w = Misc::number($dims[0]);
            $max_h = Misc::number($dims[1]);
        } else {
            if (@ereg('m', $dims[0])) $max_w = Misc::number($dims[0]);
            if (!@ereg('m', $dims[0]) && @ereg('m', $dims[1])) $newim_w = Misc::number($dims[0]);
    
            if (@ereg('m', $dims[1])) $max_h = Misc::number($dims[1]);
            if (!@ereg('m', $dims[1]) && @ereg('m', $dims[0])) $newim_h = Misc::number($dims[1]);
        }
    
        $prop = $oldim_w / $oldim_h;
    
        if (!$max_w&&!$max_h) {
            $newim_w = $dims[0];
            $newim_h = $dims[1];
        } else if (!$max_h) {
            $newim_w = $newim_h * $prop;
            if ($newim_w > $max_w) {
                $newim_w = $max_w;
                $newim_h = $newim_w / $prop;
            }
        } else if (!$max_w) {
            $newim_h = $newim_w / $prop;
            if ($newim_h > $max_h) {
                $newim_h = $max_h;
                $newim_w = $newim_h * $prop;
            }
        } else {
            $newim_w = $max_w;
            $newim_h = $newim_w / $prop;
            if ($newim_h > $max_h) {
                $newim_h = $max_h;
                $newim_w = $newim_h * $prop;
            }
            if ($newim_w > $max_w) {
                $newim_w = $max_w;
                $newim_h = $newim_w / $prop;
            }
        }
        $newim_w = ceil($newim_w);
        $newim_h = ceil($newim_h);

		if (!$newim_h && $newim_w) {
			$newim_h = ceil($newim_w / $prop);
		} else if ($newim_h && !$newim_w) {
			$newim_w = ceil($newim_h * $prop);
		}
    
//		echo $prop . '<br>';
//		echo $postfix . '-'. $size . ':' . $newim_w . 'x' . $newim_h . '<br>';

        $newim = imagecreatetruecolor($newim_w, $newim_h);

        if(in_array($types[$type[1]], array('png', 'gif'))){
            imagecolortransparent($newim, imagecolorallocatealpha($newim, 0, 0, 0, 127));
            imagealphablending($newim, false);
            imagesavealpha($newim, true);
        }

        imagecopyresampled($newim, $oldim, 0, 0, 0, 0, $newim_w, $newim_h, $oldim_w, $oldim_h);
    
		if ($types[$type[1]] == 'png') {
//			$quality = floor($quality/11.2);
			$quality = 11-$quality/10;
		}
        $info = pathinfo($image);
        $info['extension'] = strtolower($info['extension']);
        if (@eregi('f', $size)) {
            ob_start();
            header("Content-Type: image/" . $info['extension']);
            eval('$save=image' . $types[$type[1]] . '($newim,\'\',\$quality);');
            $raw = ob_get_contents();
            ob_clean();
            return $raw;
        } else {
            $newfile = $info['dirname'] . '/' . substr($info['basename'], 0, strrpos($info['basename'], '.'))
            . ($postfix ? ',' . $postfix : '') . '.' . $info['extension'];
            eval('$save=image' . $types[$type[1]] . '($newim,"' . $newfile . '","' . $quality . '");');
        }
        return $save ? true : false; 
    }
    
    /**
     * this function is used in resize function and it was defined
     * because it was deprecated in internal list of php functions
     *
     * return mime content type only for images
     *
     * @param string $file
     * @return string
     */
    function m_content_type($file)
    {
        $info=pathinfo($file);
        $info['extension'] = strtolower($info['extension']);
        $types=array('jpeg'=>'jpeg','pjpeg'=>'jpeg','jpg'=>'jpeg','gif'=>'gif','png'=>'png','x-png'=>'png');
        return in_array($info['extension'],array_keys($types))?'image/'.$info['extension']:'';
    }
    public function flash($entity, $id, $key = null, $silence = true)
    {
        if ($id) {
			$config = Indi_Registry::get('config');
            $uploadPath = $config['upload']->uploadPath;
            
            $relative = '/' . trim($uploadPath, '\\/') . '/' . $entity . '/';
            $absolute = $_SERVER['DOCUMENT_ROOT'] . $_SERVER['STD'] . $relative;
            
            $name = $id . ($key !== null && !empty($key) ? '_' . $key : '');
            // as we do not store filename in database, so we should use glob() function to
            // deterimine file extension
            if (is_dir($absolute)) {
                if (is_writable($absolute)) {
                    $path = $absolute . $name . '.*';
					$file = glob($path);
                    if (count($file)) {
                        $pathinfo = pathinfo($file[0]);
                        $xhtml = '<embed src="' . $relative . $name . '.' .$pathinfo['extension'] . '" width="200"/>';
                    } else if (!$silence) {
                        $xhtml = 'Cannot find any file on pattern ' . $path;
                    }
                } elseif (!$silence) {
                    $xhtml = $absolute . ' is not writable';
                }
            } else if (!$silence) {
                $xhtml = $absolute . ' is not a directory';
            }
    
            return $xhtml;
        }
    }
    public function getEntityImageByUrl($url, $entity, $id, $name, $requirements = array())
    {
        // get upload path from config
        $uploadPath = self::getUploadPath();
        
        // absolute upload path in filesystem
        $absolute = rtrim($_SERVER['DOCUMENT_ROOT'], '\\/') . $_SERVER['STD'] . '/' . $uploadPath . '/' . $entity . '/';

		if ($requirements['type']) {
			$info = explode('/', Indi_Image::m_content_type($url));
			if ($info[0] != $requirements['type']) {
				return;
			}
		}
		$info = pathinfo($url);
		if (!$info['extension']) {
			$fp = fopen($url , 'r'); while(!feof($fp)) $data .= fgets($fp, 1000); fclose($fp);
			if (preg_match('/Location: (.*)\\n/', $data, $matches)) {
				self::getEntityImageByUrl($matches[1], $entity, $id, $name, $requirements);
				return;
			}
			$url = tempnam(sys_get_temp_dir(), "image");
			$fp = fopen($url, 'wb');
			fwrite($fp, $data);
			fclose($fp);
			$tmpUsage = true;
			$info['extension'] = 'jpg';
		}
		if (!$images['error'][$name]) {
			// check if entity images directory is exists
			if (!is_dir($absolute)) {
				self::createEntityImagesDir($absolute);
			}
			// check if entity images directory is writable
			if (!is_writable($absolute)) {
				throw new Exception('Directory "' . $absolute . '" is not writable. Please change permitions.');
			}
//			self::deletePreviousEntityImage($name);
			$dst = $absolute . $id . (!in_array($name, array('0','1')) ? '_' . $name : '') . '.' . strtolower($info['extension']);
			
			copy($url, $dst);
			if ($tmpUsage) unlink($url);
			$entityId = Misc::loadModel('Entity')->fetchRow('`table` = "' . $entity . '"')->id;
			$copies = Misc::loadModel('Resize')->fetchAll('`fieldId` = (SELECT `id` FROM `field` WHERE `alias`="' . $name . '" AND `entityId`="' . $entityId . '")')->toArray();
			for ($i = 0; $i < count($copies); $i++) {
				switch($copies[$i]['proportions']){
					case 'o': // original
						$size = getimagesize($dst);
						$size = $size[0] . 'x' . $size[1];
						break;
					case 'c':
						$size = $copies[$i]['masterDimensionValue'] . 'x' . $copies[$i]['slaveDimensionValue'];
						break;
					case 'p':
						$size = array($copies[$i]['masterDimensionValue'], $copies[$i]['slaveDimensionValue']);
						if ($copies[$i]['masterDimensionAlias'] == 'height'){
							$size = array_reverse($size);
						}
						if($copies[$i]['slaveDimensionLimitation']) $size[1] .= 'M';
						$size = implode('x', $size);
						break;
					default:
						$size = '';
						break;
				}
				Indi_Image::resize($dst, $copies[$i]['alias'], $size);
			}
		}
    }
}