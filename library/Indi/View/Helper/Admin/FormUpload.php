<?php
class Indi_View_Helper_Admin_FormUpload
{
    public function formUpload($name = null, $copy = null, $silence = true, $entity = null, $id = null)
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
		$absolute = rtrim($_SERVER['DOCUMENT_ROOT'] . STD, '\\/') . $relative;
		$file = glob($absolute . $pattern); $file = $file[0];
		if ($file) {
			$types = array('image' => 'gif,png,jpg,jpeg', 'flash' => 'swf', 'video' => 'avi,mpg,mp4,3gp', 'file' => '');
			$info = pathinfo($file);
			foreach ($types as $type => $extensions) if (in_array($info['extension'], explode(',', $extensions))) break;
			$xhtml = '<div>';
			switch ($type) {
				case 'image':
					$uploaded = Indi::view()->row->img($name) . '<br>';
					preg_match('/src="([^"]+)"/', $uploaded, $matches); $src = substr($matches[1], 0, strpos($matches[1], '?'));
					$abs = $_SERVER['DOCUMENT_ROOT'] . $src;
                    $info = getimagesize($abs);
                    if (STD) $uploaded = preg_replace('~src="' . preg_quote(STD) . '~', 'src="', $uploaded);
                    if ($info[0] > Indi::get('width') + 8) {
						$uploaded = preg_replace('/src="/', 'width="' . (Indi::get('width') + 8).'" src="', $uploaded);
					}
                    break;
				case 'flash':
					$uploaded = Indi::view()->swf($entity, $id, $name, $silence) . '<br>';
                    preg_match('/src="([^"]+)"/', $uploaded, $matches); $src = substr($matches[1], 0, strpos($matches[1], '?'));$src = $matches[1];
                    $abs = $_SERVER['DOCUMENT_ROOT'] . $src;
                    $info = getflashsize($abs);
                    if (STD) $uploaded = preg_replace('~src="' . preg_quote(STD) . '~', 'src="', $uploaded);
                    if ($info[0] > Indi::get('width') + 8) {
                        $width = Indi::get('width') + 8;
                        $height = ceil($width/$info[0] * $info[1]);
                        $uploaded = preg_replace('/src="/', 'width="' . $width .'" height="' . $height . '" wmode="opaque" src="', $uploaded);
                    } else {
                        $uploaded = preg_replace('/src="/', $info[3] . ' wmode="opaque" src="', $uploaded);
                    }
					break;
				case 'video':
					$uploaded = '<a href="' . $relative . $info['basename'] . '" target="_blank" title="Скачать">Видео</a> <a>в формате ' . $info['extension'] . '</a> &nbsp;';
					break;
				default:
					$uploaded = '<a href="/admin/auxillary/download/id/'.$id . '/field/' . Indi::trail()->model->fields($name)->id . '/" title="Скачать">Файл</a> <a>в формате ' . $info['extension'] . '</a> &nbsp;';
					break;
			}

			$xhtml .= '<span class="upload' . (!in_array($type, array('image', 'video', 'flash'))?' no-file-yet':'') . '"  field="' . $name . '">';
			$xhtml .= $type == 'file' ? $uploaded : '';
			$xhtml .= '<input type="hidden" name="' . $name . '" value="r" id="' . $name .'"/>';
			$xhtml .= '<span class="radio checked" val="r" id="file-action-' . $name . '-r"><label id="file-action-' . $name . '-r-label">' . FORM_UPLOAD_REMAIN . '</label>&nbsp;</span>';
			$xhtml .= '<span class="radio" val="d" id="file-action-' . $name . '-d"><label id="file-action-' . $name . '-d-label">' . FORM_UPLOAD_DELETE . '</label>&nbsp;</span>';
			$xhtml .= '<span class="radio" val="m" id="file-action-' . $name . '-m"></span><label id="file-action-' . $name . '-m-label"><a href="#" class="browse">' . FORM_UPLOAD_REPLACE . '</a></label>&nbsp;';
			$xhtml .= '<span class="selected" id="replace-by-'.$name.'">' . FORM_UPLOAD_REPLACE_WITH . '</span> <span id="selected' . $name . '" class="selected-fname"></span>';
            $xhtml .= '<a href="' . $src . '" target="_blank" class="original">' . FORM_UPLOAD_ORIGINAL . '</a>';
            $xhtml .= '<script>
				$("span.upload[field='.$name.'] span.radio").click(function(){
					$(this).parent().find("span.radio").removeClass("checked");
					$(this).parent().find("#' . $name . '").val($(this).attr("val"));
					$(this).addClass("checked");
					if($(this).attr("val") == "m" && $(this).parents("span.upload").find("input[id^=upload]").val() == "") {
						$(this).parents("span.upload").find("input[id^=upload]").click();
					}
				});
				$("span.upload[field='.$name.'] a.browse").click(function(){
					$(this).parents("span.upload").find("input[id^=upload]").click();
					$(this).parents("span.upload").find("span.radio").removeClass("checked");
					$(this).parents("span.upload").find("#' . $name . '").val("m");
					$(this).parents("span.upload").find("span.radio[val=m]").addClass("checked");
					return false;
				});
			</script>';
		} else {
			$xhtml .= '<span class="upload no-file-yet" field="' . $name . '">';
			$xhtml .= '<input type="hidden" name="' . $name . '" value="r" id="' . $name . '"/>';
			$xhtml .= '<span class="radio checked" val="r" id="file-action-' . $name . '-r"><label id="file-action-' . $name . '-r-label">' . FORM_UPLOAD_NO . '</label>&nbsp;</span>';
			$xhtml .= '<span class="radio" val="m" id="file-action-' . $name . '-m"></span><label id="file-action-' . $name . '-m-label"><a href="#" class="browse">' . FORM_UPLOAD_BROWSE . '</a></label>&nbsp;';
			$xhtml .= '<span id="selected' . $name . '" class="selected-fname"></span>';
			$xhtml .= '<script>
				$("span.upload[field='.$name.'] span.radio").click(function(){
					$(this).parent().find("span.radio").removeClass("checked");
					$(this).parent().find("#' . $name . '").val($(this).attr("val"));
					$(this).addClass("checked");
					if($(this).attr("val") == "m" && $(this).parents("span.upload").find("input[id^=upload]").val() == "") {
						$(this).parents("span.upload").find("input[id^=upload]").click();
					}
				});
				$("span.upload[field='.$name.'] a.browse").click(function(){
					$(this).parents("span.upload").find("input[id^=upload]").click();
					$(this).parents("span.upload").find("span.radio").removeClass("checked");
					$(this).parents("span.upload").find("#' . $name . '").val("m");
					$(this).parents("span.upload").find("span.radio[val=m]").addClass("checked");
					return false;
				});
			</script>';
		}
		$xhtml .= '<input type="file" name="' . $name .'" id="upload' . $name . '" onchange="
			if ($(this).parent().hasClass(\'no-file-yet\')) {
				$(this).parent().find(\'span[class^=selected]\').show();
			} else {
				$(this).parent().find(\'span[class^=selected]\').show();
			}
			var text = this.value.length > 40 ? \'...\' + this.value.substr(this.value.length - 40) : this.value;
			$(this).parent().find(\'span.selected-fname\').text(text);
			if (this.value == \'\') {
				$(this).parent().find(\'span.selected-fname,span.selected\').hide();
				$(this).parent().find(\'span.radio[val=r]\').click();
			}
		"/>';
		$xhtml .= '</span>';
		$xhtml .= $type == 'file' ? '' : $uploaded;
		$xhtml .= '<script>$(document).ready($("span.upload").width($("td[id^=td-right]").first().width()))</script>';
		$xhtml .= '</div>';
        return $xhtml;
    }
}
