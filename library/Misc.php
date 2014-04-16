<?php
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
if (!function_exists('lcfirst')) {
    function lcfirst($value){
        return strtolower(substr($value, 0, 1)) . substr($value, 1);
    }
}
function filter($value){
	if (is_string($value)) {
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

	public function usubstr($string, $length, $dots = true){
        $dots = mb_strlen($string, 'utf-8') > $length && $dots ? '..' : '';
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
		$multipart[]= "";
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

    public static function tbq($q = 2, $versions = '', $showNumber = true)
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
                    if (preg_match('/'.$cases[$k].'$/', $q)) return ($showNumber ? $q . ' ' : '') . $format[$digits];
                }
                else
                {
                    $interval = explode('-', $cases[$k]);
                    for ($m = $interval[0]; $m <= $interval[1]; $m ++)
                    {
                        if (preg_match('/'.$m.'$/', $q)) return ($showNumber ? $q . ' ' : '') . $format[$digits];
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
        list($hue) = self::rgb2hsl(array($r, $g, $b));
        return str_pad(round($hue*360), 3, '0', STR_PAD_LEFT) . '#' . $rgb;
    }
    public function rgb2hsl($rgb) {
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
}
function ie8() {
	return preg_match('/MSIE 8/', $_SERVER['HTTP_USER_AGENT']);
}
function ipad() {
	return preg_match('/iPad/', $_SERVER['HTTP_USER_AGENT']);
}
function getflashsize($path){
    class blob_data_as_file_stream {

        private static $blob_data_position = 0;
        public static $blob_data_stream = '';

        public static function stream_open($path,$mode,$options,&$opened_path){
            self::$blob_data_position = 0;
            return true;
        }

        public static function stream_seek($seek_offset,$seek_whence){
            $blob_data_length = strlen(self::$blob_data_stream);
            switch ($seek_whence) {
                case SEEK_SET:
                    $new_blob_data_position = $seek_offset;
                    break;
                case SEEK_CUR:
                    $new_blob_data_position = self::$blob_data_position+$seek_offset;
                    break;
                case SEEK_END:
                    $new_blob_data_position = $blob_data_length+$seek_offset;
                    break;
                default:
                    return false;
            }
            if (($new_blob_data_position >= 0) AND ($new_blob_data_position <= $blob_data_length)){
                self::$blob_data_position = $new_blob_data_position;
                return true;
            }else{
                return false;
            }
        }

        public static function stream_tell(){
            return self::$blob_data_position;
        }

        public static function stream_read($read_buffer_size){
            $read_data = substr(self::$blob_data_stream,self::$blob_data_position,$read_buffer_size);
            self::$blob_data_position += strlen($read_data);
            return $read_data;
        }

        public static function stream_write($write_data){
            $write_data_length=strlen($write_data);
            self::$blob_data_stream = substr(self::$blob_data_stream,0,self::$blob_data_position).
                $write_data.substr(self::$blob_data_stream,self::$blob_data_position+=$write_data_length);
            return $write_data_length;
        }

        public static function stream_eof(){
            return self::$blob_data_position >= strlen(self::$blob_data_stream);
        }

    }

    // Register stream wrapper
    stream_wrapper_register("FlashStream", "blob_data_as_file_stream");

    // Store file contents to the data stream
    blob_data_as_file_stream::$blob_data_stream = file_get_contents($path);

    //Run getimagesize() on the data stream
    return @getimagesize('FlashStream://');
}