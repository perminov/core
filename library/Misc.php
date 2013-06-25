<?php
if (!function_exists('lcfirst')) {
	function lcfirst($value){
		return strtolower(substr($value, 0, 1)) . substr($value, 1);
	}
}
function onlyme(){
	return $_SERVER['REMOTE_ADDR'] == '109.184.137.246';
}
function title2alias($title){
//Ë À Ì Â Í Ã Î Ä Ï Ç Ò È Ó É Ô Ê Õ Ö ê Ù ë Ú î Û ï Ü ô Ý õ â û ã ÿ ç
//E A I A I A I A I C O E O E O E O O e U e U i U i U o Y o a u a y c

	$s = array("а","б","в","г","д","е","ё","ж","з","и","й","к","л","м","н","о","п","р","с","т","у","ф","х","ц","ч","ш","щ","ъ","ы","ь","э","ю","я","№"," ","a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z","-","0","1","2","3","4","5","6","7","8","9","Ë","À","Ì","Â","Í","Ã","Î","Ä","Ï","Ç","Ò","È","Ó","É","Ô","Ê","Õ","Ö","ê","Ù","ë","Ú","î","Û","ï","Ü","ô","Ý","õ","â","û","ã","ÿ","ç");
	$r = array("a","b","v","g","d","e","yo","zh","z","i","i","k","l","m","n","o","p","r","c","t","u","f","h","ts","ch","sh","shh","","y","","e","yu","ya","#","-","a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z","-","0","1","2","3","4","5","6","7","8","9","e","a","i","a","i","a","i","a","i","c","o","e","o","e","o","e","o","o","e","u","e","u","i","u","i","u","o","u","o","a","u","a","y","c");
	$alias = '';
	$title = trim($title);
	$title = mb_strtolower($title, 'utf-8');
	for ($i = 0; $i < mb_strlen($title, 'utf-8'); $i++) {
		$c = mb_substr($title, $i, 1, 'utf-8');
		$index = array_search($c, $s);
		if ($index !== false) $alias = $alias . $r[$index];
	}
	$alias = preg_replace('/^\-+/', '', $alias);
	$alias = preg_replace('/\-+$/', '', $alias);
	$alias = preg_replace('/\-{2,}/', '-', $alias);
	return $alias;
}

/**
 * Displays or returns formatted view of a given value
 * $hr mean Hidden and/or Return
 *
 * Possible constructions of use
 *
 * 1.
 * call:   dump($value);
 * result: $value will be displayed as is
 *
 * 2.
 * call:   dump($value,1);
 * result: $value will be hidden displayed, mean that
 *         it can be viewed only as HTML-source
 *
 * 3.
 * call:   $dump=dump($value,2);
 * result: $value will be returned (! not displayed) as is.
 *
 * 4.
 * call:   $dump=dump($value,3);
 * result: $value will be returned, and if it then will need to be outputted
 *         this output can be viewed only as HTML-source
 *
 * @param mixed $value
 * @param int $hr
 * @return string
 */
function d($value, $hr=0){
    ob_start();
    echo '<pre' . ($hr == 1 || $hr == 3 ? ' style="display:none;"' : '') . '>';
    print_r($value);
    echo '</pre>';
    $dump = ob_get_clean();
    if ($hr < 2) {
        echo $dump;
    } else {
        return $dump;
    }
}
function filter($value){
	if (!is_array($value)) {
		$value = str_replace(array('"','>','<'), array('&quot;', '&gt;', '&lt;'), strip_tags($value));
	} else {
		foreach ($value as $k => $v) {
			$value[$k] = filter($v);
		}
	}
	return $value;
}
function i($value, $type = 'w', $file = 'debug.txt'){

    $s=pathinfo(__FILE__);$s=explode('/', preg_replace('!' . preg_quote($_SERVER['DOCUMENT_ROOT']) . '!', '', str_replace('\\','/', $s['dirname'])));$s=implode('/', array_slice($s, 0, count($s) - 2));
    $fp = fopen(rtrim($_SERVER['DOCUMENT_ROOT'] . $s . '/www', '\//') . '/' . $file, $type);
	ob_start();
	print_r($value);
	echo "\n";
	fwrite($fp, ob_get_clean());
	fclose($fp);
}
function getOccurDate($startDate, $endDate = ''){
	$russian = array('января','февраля','марта','апреля','мая','июня','июля','августа','сентября','октября','ноября','декабря');
	$english = array('january','february','march','april','may','june','july','august','september','october','november','december');
//	$startDate = date('Y-m-j', strtotime($start));
//	$endDate = date('Y-m-j', strtotime($end));
	$startParts = explode('-', $startDate);
	$endParts = explode('-', $endDate);
	if ($startDate == $endDate || $endDate == '0000-00-00') {
		return str_ireplace($english, $russian, date('j F', strtotime($startDate)));
	} else if ($startParts[0] == $endParts[0] && $startParts[1] == $endParts[1] && $startParts[2] != $endParts[2]) {
		return $startParts[2] . ' - ' . $endParts[2] . ' '. str_ireplace($english, $russian, date('F', strtotime($startDate)));
	} else if ($startParts[0] == $endParts[0] && $startParts[1] != $endParts[1]) {
		return $startParts[2] . str_ireplace($english, $russian, date(' F', strtotime($startDate))) . ' - ' . $endParts[2] . ' '. str_ireplace($english, $russian, date('F', strtotime($endDate)));
	} else {
		return str_ireplace($english, $russian, date('j F', strtotime($startDate)) . ' - ' . date('j F', strtotime($endDate)));
	}
}

function rDate($date, $format = 'd-M-Y'){
	$rus = array('янв','фев','мар','апр','май','июн','июл','авг','сен','окт','ноя','дек');
	$eng = array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
	if (Misc::number($date)) return str_replace($eng, $rus, date($format,strtotime($date))); else return '';
}
function pDate($date, $format = 'j M Y'){
	$rus = array('января','февраля','марта','апреля','мая','июня','июля','августа','сентября','октября','ноября','декабря');
	$eng = array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
	if (Misc::number($date)) return str_replace($eng, $rus, date($format,strtotime($date))); else return '';
}
function jDate($date, $format = 'j M Y'){
	$rus = array('января','февраля','марта','апреля','мая','июня','июля','августа','сентября','октября','ноября','декабря');
	$eng = array('January','February','March','April','May','June','July','August','September','October','November','December');
	if (Misc::number($date)) return str_replace($eng, $rus, date($format,strtotime($date))); else return '';
}
function tDate($date, $format = 'M Y'){
	$rus = array('Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь');
	$eng = array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
	if (Misc::number($date)) return str_replace($eng, $rus, date($format,strtotime($date))); else return '';
}
function dayOfWeek($date = '', $format = 'l'){
	if (!$date) $date = date('Y-m-d');
	$rus = array('Понедельник','Вторник','Среда','Четверг','Пятница','Суббота','Воскресенье');
	$eng = array('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday');
	if (Misc::number($date)) return str_replace($eng, $rus, date($format,strtotime($date))); else return '';
}
function dateDifference($d1, $d2){ 
    $d1 = (is_string($d1) ? strtotime($d1) : $d1);
    $d2 = (is_string($d2) ? strtotime($d2) : $d2);

    $diff_secs = abs($d1 + 60*60*24 - $d2);
    $base_year = min(date("Y", $d1), date("Y", $d2));

    $diff = mktime(0, 0, $diff_secs, 1, 1, $base_year);
    $info = array(
        "years" => date("Y", $diff) - $base_year,
//        "months_total" => (date("Y", $diff) - $base_year) * 12 + date("n", $diff) - 1,
        "months" => date("n", $diff) - 1,
//        "days_total" => floor($diff_secs / (3600 * 24)),
        "days" => date("j", $diff) - 1,
//        "hours_total" => floor($diff_secs / 3600),
//        "hours" => date("G", $diff),
//        "minutes_total" => floor($diff_secs / 60),
//        "minutes" => (int) date("i", $diff),
//        "seconds_total" => $diff_secs,
//        "seconds" => (int) date("s", $diff)
    );
	extract($info);
	if ($years) {
		if ($years == 1) {
			$ret = ' год';
		} else if ($years >=2 && $years <=4) {
			$ret = ' года';
		} else if ($years >=5 && $years <= 20) {
			$ret = ' лет';
		} else if ($years >= 21) {
			$last = substr($years, strlen($years) - 1, 1);
			if ($last == 1) {
				$ret = ' год';
			} else if ($last >=2 && $last <=4) {
				$ret = ' года';
			} else if (($last >= 5 && $last <=9) || $last == 0) {
				$ret = ' лет';
			}
		}
	}
	if ($years) $return = $years . $ret;

	if ($months) {
		if ($months == 1) {
			$ret = ' месяц';
		} else if ($months >=2 && $months <=4) {
			$ret = ' месяца';
		} else if ($months >=5 && $months <= 20) {
			$ret = ' месяцев';
		} else if ($months >= 21) {
			$last = substr($months, strlen($months) - 1, 1);
			if ($last == 1) {
				$ret = ' месяц';
			} else if ($last >=2 && $last <=4) {
				$ret = ' месяца';
			} else if (($last >= 5 && $last <=9) || $last == 0) {
				$ret = ' месяцев';
			}
		}
	}

	if ($months) $return .= ' ' . $months . $ret;

	if ($days) {
		if ($days == 1) {
			$ret = ' день';
		} else if ($days >=2 && $days <=4) {
			$ret = ' дня';
		} else if ($days >=5 && $days <= 20) {
			$ret = ' дней';
		} else if ($days >= 21) {
			$last = substr($days, strlen($days) - 1, 1);
			if ($last == 1) {
				$ret = ' день';
			} else if ($last >=2 && $last <=4) {
				$ret = ' дня';
			} else if (($last >= 5 && $last <=9) || $last == 0) {
				$ret = ' дней';
			}
		}
	}
	if (!$years && $days) $return .= ' ' . $days . $ret;
	return $return;
} 
function mtrim(&$val){$val = trim($val);}
function bylen($a, $b){return mb_strlen($a, 'utf-8') < mb_strlen($b, 'utf-8');}
function sortByRDC($a, $b) {
	$relevanceA = is_object($a) ? $a->relevance : $a['relevance'];
	$relevanceB = is_object($b) ? $b->relevance : $b['relevance'];
	$distanceA = is_object($a) ? $a->distance : $a['distance'];
	$distanceB = is_object($b) ? $b->distance : $b['distance'];
	$countA = is_object($a) ? $a->count : $a['count'];
	$countB = is_object($b) ? $b->count : $b['count'];
	$titleA = is_object($a) ? $a->title :$a['title'];
	$titleB = is_object($b) ? $b->title :$b['title'];
	if ($relevanceA == $relevanceB) {
		if ($distanceA == $distanceB) {
			if ($countA == $countB) {
				return $titleA > $titleB;
			} else {
				return $countA < $countB;
			}
		} else {
			return $distanceA > $distanceB;
		}
	} else {
		return $relevanceA < $relevanceB;
	}
}
function compare($S1,$S2,$limit)
{
  $S1=strtolower($S1);$S2=strtolower($S2);
  $n=strlen($S1);$m=strlen($S2);
////////////////////////
  if($S1==$S2)
	return 0;
  if($limit==null)
	$limit=9999998;
  if(abs($n-$m)>$limit)
    return 9999999;
  if(!$n)
	return $m;
  if(!$m)
	return $n;
////////////////////////	
  $dist=array_fill(0,2,array_fill(0,$m+1,0)); $ok=1; $current=1;
  for($i=1;$i<=$m;$i++)
  {	
	  $dist[0][$i]=$i;
  }
  for($i=1;$i<=$n;$i++)
  {
    $ok=0;
    $dist[$current][0]=$i;
    if($i-$limit>=1)
		$dist[$current][$i-$limit-1]=9999999;
    for($j=max($i-$limit,1);$j<=min($i+$limit,$m);$j++)
    {
        if(substr($S1,$i-1,1)==substr($S2,$j-1,1))
            $dist[$current][$j]=$dist[1-$current][$j-1];
        else
            $dist[$current][$j]=min($dist[1-$current][$j-1],$dist[1-$current][$j],$dist[$current][$j-1])+1;
        if($dist[$current][$j]<=$limit)
            $ok=1;
    }
    if($i+$limit<=$m)
    {
		$dist[$current][$i+$limit+1]=9999999;
	}
    if(!$ok)
    {
        return 9999999;
	}
    $current=1-$current;
  }
  return $dist[1-$current][$m];
}

function indexed($val){
	// ненужные слова
	$remove = 'г, о, оз, о ва, озеро, остров, острова, область, провинция, отель, спа, курорт, hotel, 
	SPA, resort, beach, suites, эмират, регион, провинция, велаят, провинции,край, земля, район, уезд,
	обл, ао, округ, и';
	$remove = explode(',', $remove);
	array_walk($remove, 'mtrim');
	usort($remove, 'bylen');
	//
	$indexed = mb_strtoupper($val, 'utf-8');
	$indexed = preg_replace('/[[:punct:]—]/ui', ' ', $indexed);
	$indexed = preg_replace('/^(' . implode('|', $remove) . ') /ui', ' ', $indexed);
	$indexed = preg_replace('/ (' . implode('|', $remove) . ') /ui', ' ', $indexed);
	$indexed = preg_replace('/ (' . implode('|', $remove) . ')$/ui', ' ', $indexed);
	$indexed = trim($indexed);
	$indexed = preg_replace('/ {2,}/ui', ' ', $indexed);
	return $indexed;
}

class Misc
{
    
    /**
     * Gets indent as &nbsp; multiplied on $count and on current $level
     *
     * @param int $level
     * @param int $count
     * @return string $indent
     */
    static function indent($level, $count = 5, $char = '&nbsp;')
    {
        for ($i = 0; $i < $count; $i++) $single .= $char;
        for ($i = 0; $i < $level; $i++) $indent .= $single;
        return $indent;
    }
    
    /**
     * Returns a number from a passed string in $string argument
     * For examplpe if passed string is 'abc123def456.34', function will
     * return '123456.34'. If you specify an $abs argument and set it 'true', dots if they are exists in a 
     * passed string - will be ignored, so the result will be '12345634' in example
     * ignored 
     *
     * @param string $string
     * @param bool $abs
     * @return int|float
     */
    function number($string, $abs = false)
    {
        $number = '';
        for ($i = 0; $i < strlen($string); $i++) {
            $letter = substr($string, $i, 1);
            if ($letter == ',') $letter = '.';
            if (is_numeric($letter) or ($letter == '.' && !$abs)) $number .= $letter;
        }
        return $number;
    }
    
	public function loadModel($modelClassName){
		// if file in which model is declared doesn't exists, so there will be performed emulation of it initialization
		$systemModelsDir1 = rtrim($_SERVER['DOCUMENT_ROOT'], '\\/') . $_SERVER['STD'] . '/www/application/models/';
		$systemModelsDir2 = rtrim($_SERVER['DOCUMENT_ROOT'], '\\/') . $_SERVER['STD'] . '/core/application/models/';
		$modelFileName = $modelClassName . '.php';
		$modelFilePath1 = $systemModelsDir1 . $modelFileName;
		$modelFilePath2 = $systemModelsDir2 . $modelFileName;
//		if ($modelClassName == 'Hotel') d(debug_backtrace());
		if (!in_array($modelClassName, get_declared_classes())){
			if (file_exists($modelFilePath1)) {
				if (!class_exists($modelClassName))
					require($modelFilePath1);
			} else if (file_exists($modelFilePath2)) {
				if (!class_exists($modelClassName))
					require($modelFilePath2);
			} else {

				$entityRow = Entity::getInstance()->fetchRow('`table` = "' . lcfirst($modelClassName) . '"');
				if ($entityRow) {
					$extends = $entityRow->extends ? $entityRow->extends : 'Indi_Db_Table';
					eval('class ' . $modelClassName . ' extends ' . $extends . '{}');
				} else {
					throw new Exception('Model is not in entities table');
				}
			}
		}
		$model = new $modelClassName();
		return $model;
	}
	public function usubstr($string, $length, $addTripleDot = true){
		if (mb_strlen($string, 'utf-8') > $length && $addTripleDot) $dots = '...';
		$string = mb_substr($string, 0, $length, 'utf-8') . $dots;
		return $string;
	}
	public function generateRandomSequence($length = 15, $useSpecialChars = false) {
		$chars = array(
			'a', 'b', 'c', 'd', 'e', 'f',
			'g', 'h', 'i', 'j', 'k', 'l',
			'm', 'n', 'o', 'p', 'r', 's',
			't', 'u', 'v', 'x', 'y', 'z',
			'A', 'B', 'C', 'D', 'E', 'F',
			'G', 'H', 'I', 'J', 'K', 'L',
			'M', 'N', 'O', 'P', 'R', 'S',
			'T', 'U', 'V', 'X', 'Y', 'Z',
			'1', '2', '3', '4', '5', '6',
			'7', '8', '9', '0'
		);
		if ($useSpecialChars) $chars = array_merge($chars, array(
			'.', ',', '(', ')', '[', ']',
			'!', '?', '&', '^', '%', '@', 
			'*', '$', '<', '>', '/', '|',
			'+', '-', '{', '}', '`', '~'
		));
	    // Генерируем пароль
	    $sequence = '';
	    for ($i = 0; $i < $length; $i++) {
	      // Вычисляем случайный индекс массива
	      $index = rand(0, count($chars) - 1);
	      $sequence .= $chars[$index];
	    }
	    return $sequence;
	}	
	public function addStyleForTables($html) {
		$tableStart = stripos($html, '<table', $tableEnd);
		$sa = new StripAttributes();
		while ($tableStart != false) {
			$tableEnd = stripos($html, '</table>', $tableStart);
			$table = substr($html, $tableStart, $tableEnd + 8 - $tableStart);
			$oldTables[] = $table;
			$tr = array();
			$trStart = stripos($table, '<tr', 0);
			while ($trStart != false) {
				$trEnd = stripos($table, '</tr>', $trStart);
				$tr[] = substr($table, $trStart, $trEnd + 5 - $trStart);
				$trStart = stripos($table, '<tr', $trEnd);
			}
			$newTable = array();
			$newTable[] = '<table>';
			for($i = 0; $i < count($tr); $i++){
				if($i == 0) {
					$newTable[] = '<thead>';
					$newTable[] = $sa->strip(strip_tags(str_ireplace(array('<td', '</td>'), array('<th', '</th>'), $tr[$i]), '<tr>,<th>'), 'class,colspan');
					$newTable[] = '</thead>';
				} else {
					if ($i == 1) $newTable[] = '<tbody>';
					$newTable[] = $sa->strip(strip_tags(str_ireplace(array('<tr','</p>'),array('<tr class="' . ($i%2?'odd':'even') . '"','</p><br>'), $tr[$i]),'<tr><td><br>'), 'class,colspan');
					if ($i == count($tr) - 1)  $newTable[] = '</tbody>';
				}
			}
			$newTable[] = '</table>';
			$newTable = implode("\n", $newTable);
			$newTables[] = $newTable;
			$tableStart = stripos($html, '<table', $tableEnd);
		}
		$html = str_ireplace($oldTables, $newTables, $html);
		return $html;
	}
	public function placeA($place, $singleTitle = true, $action = 'details', $urlAddition = '', $customTitle = '', $urlOnly = false){
		$keys = array('c' => 'cityId', 's' => 'subregionId', 'r' => 'regionId', 't' => 'countryId', 'd' => 'directionId');
		$urls = array('c' => 'cities', 's' => 'subregions', 'r' => 'regions', 't' => 'countries', 'd' => 'directions');
		$titles = array();
		if (is_object($place)) $place = $place->toArray();
		foreach ($keys as $type => $key) {
			if ($place['type'] == $type || $start) {
				if (!$start) $url = '/' . $urls[$type] . '/' . $action . '/id/' . $place[$key] . '/' . $urlAddition;
				if ($customTitle) {
					$titles[] = $customTitle;
				} else {
					if (is_object($place['foreign'][$key])) $place['foreign'][$key] = $place['foreign'][$key]->toArray();
					if (!in_array($place['foreign'][$key]['title'], $titles) && $place['foreign'][$key]['title']) $titles[] = $place['foreign'][$key]['title'];
					if (!$singleTitle) $start = true;
				}
			}
		}
		return $urlOnly ? $url : '<a href="' . $url . '">' . implode(', ', $titles) . '</a>';
	}
	function ucfirstUtf8($string, $e ='utf-8') { 
		if (function_exists('mb_strtoupper') && function_exists('mb_substr') && !empty($string)) { 
			$string = mb_strtolower($string, $e); 
			$upper = mb_strtoupper($string, $e); 
				preg_match('#(.)#us', $upper, $matches); 
				$string = $matches[1] . mb_substr($string, 1, mb_strlen($string, $e), $e); 
		} 
		else { 
			$string = ucfirst($string); 
		} 
		return $string; 
	} 
	function ucfirstWin($string, $e ='utf-8') { 
		if (function_exists('mb_strtoupper') && function_exists('mb_substr') && !empty($string)) { 
			$string = mb_strtolower($string, $e); 
			$upper = mb_strtoupper($string, $e); 
				preg_match('#(.)#s', $upper, $matches); 
				$string = $matches[1] . mb_substr($string, 1, mb_strlen($string, $e), $e); 
		} 
		else { 
			$string = ucfirst($string); 
		} 
		return $string; 
	} 
    /**
     * Calculate and return string width in pixels
     *
     * @param int $padding
     * @return int width
     */
    public function getStringWidth($string, $fontsize = 10, $padding = 2)
    {
		$info = imagettfbbox($fontsize, 0, 'data/fonts/arial.ttf', $string);
    	return $info[2] + $padding * 2;
    }
	public function uploadFails($title = 'Тема была создана, но'){
		if (count($uploadFails = Indi_Registry::get('uploadFails'))) {
			$m[] = $title . ' не загружены файлы:';
			$m[] = '<ul>';
			if (is_array($uploadFails['type'])) {
				$m[] = '<li>' . Misc::indent(1, 4) . 'Из-за несоответствия допустимым типам</li>';
				$m[] = '<ul>';for ($i = 0; $i < count($uploadFails['type']); $i++) $m[] = '<li>' . Misc::indent(2, 4) . '- ' . $uploadFails['type'][$i] . '</li>';$m[] = '</ul>';
			}
			if (is_array($uploadFails['maxsize'])) {
				$m[] = '<li>' . Misc::indent(1, 4) . 'Из-за превышения допустимого размера</li>';
				$m[] = '<ul>';for ($i = 0; $i < count($uploadFails['maxsize']); $i++) $m[] = '<li>' . Misc::indent(2, 4) . '- ' . $uploadFails['maxsize'][$i] . '</li>'; $m[] = '</ul>';
			}
			$m[] = '</ul><br>';
			return implode("\n", $m);
		}
		return '';
	}
	public function replaceUrls($text){
		$reg = "\b([\d\w\.\/\+\-\?\:]*)((ht|f)tp(s|)\:\/\/|[\d\d\d|\d\d]\.[\d\d\d|\d\d]\.|www\.|\.tv|\.ac|\.com|\.edu|\.gov|\.int|\.mil|\.net|\.org|\.biz|\.info|\.name|\.pro|\.museum|\.co|\.ru)([\d\w\.\/\%\+\-\=\&amp;\?\:\\\&quot;\'\,\|\~\;]*)\b";
		preg_match_all("/" . $reg . "/", $text, $matches);
		for ($i = 0; $i < count($matches[0]); $i++) {
			$text = str_replace($matches[0][$i], '<a href="' . $matches[0][$i] . '">' . $matches[0][$i] . '</a>', $text);
		}
		return $text;
	}
	public function mail($to, $from, $subj, $text, $files=null){
		$boundary = md5(uniqid(time()));
		$headers[] ="MIME-Version: 1.0";
		$headers[] ="Content-Type: multipart/mixed;boundary=\"$boundary\"; type=\"text/html;\"";
		$headers[] ="From: " . $from;
		$headers[] ="Reply-To: " . $from;
		$headers[] ="Return-Path: " . $from;
		$headers[] ="X-Mailer: PHP/" . phpversion();

		$multipart[]= "--" . $boundary;
		$multipart[]= "Content-Type: text/html; charset=utf-8";
		$multipart[]= "Content-Transfer-Encoding: Quot-Printed";
		$multipart[]= ""; // раздел между заголовками и телом html-части
		$multipart[]= $text;
		$multipart[]= "";

		if ((is_array($files))&&(!empty($files)))
			{
			foreach($files as $filename => $filecontent)
				{
				$multipart[]="--" . $boundary;
				$multipart[]= "Content-Type: application/octet-stream; name=\"" . $filename . "\"";
				$multipart[]= "Content-Transfer-Encoding: base64";
				$multipart[]= "Content-Disposition: attachment; filename=\"" . $filename . "\"";
				$multipart[]= "";
				$multipart[]= chunk_split(base64_encode($filecontent));
				}
			}

		$multipart[]= "--$boundary--";
		$multipart[]= "";
		$headers=implode("\r\n", $headers);
		$multipart=implode("\r\n", $multipart);
		if (mb_detect_encoding($subj, "UTF-8") == FALSE)
		$subj= mb_encode_mimeheader($subj,"UTF-8", "B", "\n");

		return mail($to, $subj, $multipart, $headers);
	}
	public function getMatchedTitle($row, $find){
		$title = $row->title;
		$synonyms = explode(',', $row->synonyms);
		array_walk($synonyms, trim);
		for ($i = 0; $i < count($synonyms); $i++) {
			if (preg_match('/'.$find.'/ui', $synonyms[$i])) {
				$title = $synonyms[$i];
				break;
			}
		}
		return array($title);
	}
	public static $db = null;
	public static function query($sql) {
		if (self::$db == null) {
			$config = parse_ini_file('application/config.ini', true);
			self::$db = mysql_connect($config['db']['host'], $config['db']['username'], $config['db']['password']) or die(mysql_error());
			mysql_select_db($config['db']['dbname'], self::$db) or die(mysql_error());
		}
		$res =  mysql_query($sql) or die(mysql_error() . ':' . $sql);
		return $res;
	}
	public function ini($file) {
		$config = array();
		if (is_file($file)) {
			$ini = file_get_contents($file);
			preg_match_all('/\[([^\]]+)\]\s([^\[]+)/', $ini, $blocks);
			for ($i = 0; $i < count($blocks[1]); $i++) {
				$lines = explode("\n", trim($blocks[2][$i]));
				for ($j = 0; $j < count($lines); $j++) {
					preg_match('/([^\s]+)+\s*=\s*([^\s]+)/', $lines[$j], $params);
					if ($params[1]) {
						if (! is_object($config[$blocks[1][$i]])) $config[$blocks[1][$i]] = new stdClass();
						$config[$blocks[1][$i]]->$params[1] = $params[2];
					}
				}
			}
			return $config;
		}
	}
    public static function ago($datetime, $postfix = 'назад')
    {
        $curr = time();
        $past = strtotime($datetime);
        $duration = $curr - $past;
        $container = array(
            'Y' => date('Y', $duration) - 1970,
            'n' => date('n', $duration) - 1,
            'j' => date('j', $duration) - 1,
            'G' => date('G', $duration) - 3,
            'i' => trim(date('i', $duration), '0'),
            's' => trim(date('s', $duration), '0')
        );
        $formats = array(
            'Y' => array(
                '0,11-19' => 'лет',
                '1'       => 'год',
                '2-4'     => 'года',
                '5-9'     => 'лет'
            ),
            'n' => array(
                '0,11-19' => 'месяцев',
                '1'       => 'месяц',
                '2-4'     => 'месяца',
                '5-9'     => 'месяцев'
            ),
            'j' => array(
                '0,11-19' => 'дней',
                '1'       => 'день',
                '2-4'     => 'дня',
                '5-9'     => 'дней'
            ),
            'G' => array(
                '0,11-19' => 'часов',
                '1'       => 'час',
                '2-4'     => 'часа',
                '5-9'     => 'часов'
            ),
            'i' => array(
                '0,11-19' => 'минут',
                '1'       => 'минута',
                '2-4'     => 'минуты',
                '5-9'     => 'минут'
            ),
            's' => array(
                '0,11-19' => 'секунд',
                '1'       => 'секунда',
                '2-4'     => 'секунды',
                '5-9'     => 'секунд'
            )
        );
        foreach ($container as $level => $difference)
        {
            if ($difference)
            {
                $part = $difference;
                $format = $formats[$level];
                foreach ($format as $digits => $lang)
                {
                    $cases = explode(',', $digits);
                    for ($k = 0; $k < count($cases); $k ++)
                    {
                        if (strpos($cases[$k], '-') === false)
                        {
                            if (preg_match('/'.$cases[$k].'$/', $part)) return $part.' '.$format[$digits].' '.$postfix;
                        }
                        else
                        {
                            $interval = explode('-', $cases[$k]);
                            for ($m = $interval[0]; $m <= $interval[1]; $m ++)
                            {
                                if (preg_match('/'.$m.'$/', $part)) return $part.' '.$format[$digits].' '.$postfix;
                            }
                        }
                    }
                }
                break;
            }
        }
        return 'только что';
    }

    public static function tbq($q = 2, $versions = '')
    {
        $versions = explode(',', $versions);
        $format = array(
            '0,11-19,5-9' => $versions[0],
            '1'       => $versions[1],
            '2-4'     => $versions[2]
        );
        foreach ($format as $digits => $lang)
        {
            $cases = explode(',', $digits);
            for ($k = 0; $k < count($cases); $k ++)
            {
                if (strpos($cases[$k], '-') === false)
                {
                    if (preg_match('/'.$cases[$k].'$/', $q)) return $q.' '.$format[$digits];
                }
                else
                {
                    $interval = explode('-', $cases[$k]);
                    for ($m = $interval[0]; $m <= $interval[1]; $m ++)
                    {
                        if (preg_match('/'.$m.'$/', $q)) return $q.' '.$format[$digits];
                    }
                }
            }
        }
    }
    /**
     * Color space conversion
     *
     * (H,S,L) -> (R,G,B)
     *
     * H ~ <0,360>
     * S,L ~ <0,1>
     * R,G,B ~ <0,255>
     *
     * @param array|int $hsl|$h
     * @param float $s
     * @param float $l
     * @return array ($r,$g,$b)
     */
    function hsl2rgb($hsl, $s=null, $l=null) {

        if (is_array($hsl) && sizeof($hsl) == 3) list($h, $s, $l) = $hsl;
        else $h=$hsl;


        if ($s == 0) {
            $r = $g = $b = round($l * 255);
        }
        else {
            if ($l <= 0.5) {
                $m2 = $l * ($s + 1);
            }
            else
            {
                $m2 = $l + $s - $l * $s;
            }
            $m1 = $l * 2 - $m2;
            $hue = $h / 360;

            $r = Misc::hsl2rgb_hue2rgb($m1, $m2, $hue + 1/3);
            $g = Misc::hsl2rgb_hue2rgb($m1, $m2, $hue);
            $b = Misc::hsl2rgb_hue2rgb($m1, $m2, $hue - 1/3);
        }
        return array($r, $g, $b);
    }

    function hsl2rgb_hue2rgb($m1, $m2, $hue) {
        if ($hue < 0) $hue += 1;
        else if ($hue > 1) $hue -= 1;

        if (6 * $hue < 1)
            $v = $m1 + ($m2 - $m1) * $hue * 6;
        else if (2 * $hue < 1)
            $v = $m2;
        else if (3 * $hue < 2)
            $v = $m1 + ($m2 - $m1) * (2/3 - $hue) * 6;
        else
            $v = $m1;

        return round(255 * $v);
    }
    function rgb2hex($r, $g, $b, $uppercase=false, $shorten=false)
    {
        // The output
        $out = "";

        // If shorten should be attempted, determine if it is even possible
        if ($shorten && ($r + $g + $b) % 17 !== 0) $shorten = false;

        // Red, green and blue as color
        foreach (array($r, $g, $b) as $c)
        {
            // The HEX equivalent
            $hex = base_convert($c, 10, 16);

            // If it should be shortened, and if it is possible, then
            // only grab the first HEX character
            if ($shorten) $out .= $hex[0];

            // Otherwise add the full HEX value (if the decimal color
            // is below 16 then we have to prepend a 0 to it)
            else $out .= ($c < 16) ? ("0".$hex) : $hex;
        }
        // Package and away we go!
        return $uppercase ? strtoupper($out) : $out;
    }
    function rgbPrependHue($rgb = ''){
        $rgb = preg_replace('/^#/', '', $rgb);
        $r = hexdec(substr($rgb, 0, 2));
        $g = hexdec(substr($rgb, 2, 2));
        $b = hexdec(substr($rgb, 4, 2));
        list($hue) = Indi_Image::rgb2hsl(array($r, $g, $b));
        return str_pad(round($hue*360), 3, '0', STR_PAD_LEFT) . '#' . $rgb;
    }

}
class Css{
	public $s,$f,$t,$r=array();
	public function __construct($s=''){$this->s=trim(str_replace('{','',$s));preg_match('/([#\.])([a-zA-Z][a-zA-Z0-9\-_]+)[ ,.:]/',$s,$m);$this->f=$m[2];$this->t=$m[1]=='#'?'id':'class';} 
	public function toString($nl=true){$s=array($this->s.' {');$s=array_merge($s,$this->r);$s[]='}'.($nl?"\n":'');return implode($nl?"\n":'',$s);} 
}
function ie8() {
	return preg_match('/MSIE 8/', $_SERVER['HTTP_USER_AGENT']);
}