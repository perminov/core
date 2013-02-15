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
			$types = array('image' => 'gif,png,jpg', 'flash' => 'swf', 'video' => 'avi,mpg,mp4,3gp', 'file' => '');
			$info = pathinfo($file);
			foreach ($types as $type => $extensions) if (in_array($info['extension'], explode(',', $extensions))) break;
			$xhtml = '<field>';
			switch ($type) {
				case 'image':
					$uploaded = $this->view->image($entity, $id, $name, $copy, $silence) . '<br>';
					preg_match('/src="([^"]+)"/', $uploaded, $matches); $src = substr($matches[1], 0, strpos($matches[1], '?'));
					$abs = $_SERVER['DOCUMENT_ROOT'] . $src;
					$info = getimagesize($abs);
					if ($info[0] > $this->view->get['width'] + 8) {
						$uploaded = preg_replace('/src="/', 'width="' . ($this->view->get['width'] + 8).'" src="', $uploaded);
					}
					break;
				case 'flash':
					$uploaded = $this->view->flash($entity, $id, $name, $silence) . '<br>';
					break;
				case 'video':
					$uploaded = '<a href="' . $relative . $info['basename'] . '" target="_blank" title="Скачать">Видео</a> <a>в формате ' . $info['extension'] . '</a> &nbsp;';
					break;
				default:
					$uploaded = '<a href="/admin/auxillary/download/id/'.$id . '/field/' . $this->view->trail->getItem()->getFieldByAlias($name)->id . '/" title="Скачать">Файл</a> <a>в формате ' . $info['extension'] . '</a> &nbsp;';
					break;
			}

			$xhtml .= '<controls class="upload' . (!in_array($type, array('image','video'))?' no-file-yet':'') . '"  field="' . $name . '">';
			$xhtml .= $type == 'file' ? $uploaded : '';
			$xhtml .= '<input type="hidden" name="file-action[' . $name . ']" value="r"/>';
			$xhtml .= '<span class="radio checked" val="r" id="file-action-' . $name . '-r"><label id="file-action-' . $name . '-r-label">Оставить</label>&nbsp;</span>';
			$xhtml .= '<span class="radio" val="d" id="file-action-' . $name . '-d"><label id="file-action-' . $name . '-d-label">Удалить</label>&nbsp;</span>';
			$xhtml .= '<span class="radio" val="m" id="file-action-' . $name . '-m"></span><label id="file-action-' . $name . '-m-label"><a href="#" class="browse">Заменить</a></label>&nbsp;';
			$xhtml .= '<span class="selected" id="replace-by-'.$name.'">на</span> <span id="selected' . $name . '" class="selected-fname"></span>';
			$xhtml .= '<script>
				$("controls.upload[field='.$name.'] span.radio").click(function(){
					$(this).parent().find("span.radio").removeClass("checked");
					$(this).parent().find("input[name^=file-action]").val($(this).attr("val"));
					$(this).addClass("checked");
					if($(this).attr("val") == "m" && $(this).parents("controls.upload").find("input[id^=upload]").val() == "") {
						$(this).parents("controls.upload").find("input[id^=upload]").click();
					}
				});
				$("controls.upload[field='.$name.'] a.browse").click(function(){
					$(this).parents("controls.upload").find("input[id^=upload]").click();
					$(this).parents("controls.upload").find("span.radio").removeClass("checked");
					$(this).parents("controls.upload").find("input[name^=file-action]").val("m");
					$(this).parents("controls.upload").find("span.radio[val=m]").addClass("checked");
					return false;
				});
			</script>';
		} else {
			$xhtml .= '<controls class="upload no-file-yet" field="' . $name . '">';
			$xhtml .= '<input type="hidden" name="file-action[' . $name . ']" value="r"/>';
			$xhtml .= '<span class="radio checked" val="r" id="file-action-' . $name . '-r"><label id="file-action-' . $name . '-r-label">Отсутствует</label>&nbsp;</span>';
			$xhtml .= '<span class="radio" val="m" id="file-action-' . $name . '-m"></span><label id="file-action-' . $name . '-m-label"><a href="#" class="browse">Выбрать</a></label>&nbsp;';
			$xhtml .= '<span id="selected' . $name . '" class="selected-fname"></span>';
			$xhtml .= '<script>
				$("controls.upload[field='.$name.'] span.radio").click(function(){
					$(this).parent().find("span.radio").removeClass("checked");
					$(this).parent().find("input[name^=file-action]").val($(this).attr("val"));
					$(this).addClass("checked");
					if($(this).attr("val") == "m" && $(this).parents("controls.upload").find("input[id^=upload]").val() == "") {
						$(this).parents("controls.upload").find("input[id^=upload]").click();
					}
				});
				$("controls.upload[field='.$name.'] a.browse").click(function(){
					$(this).parents("controls.upload").find("input[id^=upload]").click();
					$(this).parents("controls.upload").find("span.radio").removeClass("checked");
					$(this).parents("controls.upload").find("input[name^=file-action]").val("m");
					$(this).parents("controls.upload").find("span.radio[val=m]").addClass("checked");
					return false;
				});
			</script>';
		}
		$xhtml .= '<input type="file" name="image[' . $name .']" id="upload' . $name . '" onchange="
			if ($(this).parent().hasClass(\'no-file-yet\')) {
				$(this).parent().find(\'span[class^=selected]\').css(\'display\', \'inline-block\');
			} else {
				$(this).parent().find(\'span[class^=selected]\').show();
			}
			$(this).parent().find(\'span.selected-fname\').text(this.value);
			if (this.value == \'\') {
				$(this).parent().find(\'span.selected-fname,span.selected\').hide();
				$(this).parent().find(\'span.radio[val=r]\').click();
			}
		"/>';
		$xhtml .= '</controls>';
		$xhtml .= $type == 'file' ? '' : $uploaded;
		$xhtml .= '<script>$(document).ready($("controls.upload").width($("td[id^=td-right]").first().width()))</script>';
		$xhtml .= '</field>';
        return $xhtml;
    }
}