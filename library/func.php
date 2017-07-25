<?php
/**
 * Autoloader function. Here we provide an ability for classes to be loaded from 'coref', if they are used in admin module,
 * so all classes located in coref/application/controller/admin, and coref/Indi/Controller/Admin will be loaded if need
 *
 * @param $class
 */
function autoloader($class) {

    // If $class - is a controller name, convert the first letter to lowercase
    if (preg_match('/Admin_([a-zA-z]*Controller)$/', $class, $c)) $class = lcfirst($class);

    // Get the filename, by replacing '_' to '/' in $class, and appending '.php'
    $cf = str_replace('_', '/', $class) . '.php';

    // If file inclusion failed
    if (!@include_once($cf)) {

        // Check if we are in 'admin' module
        if (COM || preg_match('~^' . preg_quote(STD, '~') . '/admin\b~', URI)) {

            // If $class is a library class for admin module controllers
            if (preg_match('/^Indi_Controller_Admin_([a-zA-Z]*)$/', $class, $l))

                // Prepend an appropriate dir to filename
                $cf = '../coref/library/Indi/Controller/Admin/' . str_replace('_', '/', $l[1]) . '.php';

            // Else if $class is an admin module controller
            else if (is_array($c) && count($c)) {

                // Prepend an appropriate dir to filename
                $cf = '../coref/application/controllers/admin/' . str_replace('_', '/', $c[1]) . '.php';

            // Else if $class is some other class, we assume it's a model class
            } else $cf = '../coref/application/models/' . $cf;

            // Include class file
            @include_once($cf);
        }
    }
}

/**
 * Custom handler for php errors, except E_NOTICE and E_DEPRECATED
 *
 * @param null $type
 * @param null $message
 * @param null $file
 * @param null $line
 * @return mixed
 */
function ehandler($type = null, $message = null, $file = null, $line = null) {

    // If arguments are given, we assume that we are here because of
    // a set_error_handler() usage, e.g current error is not a fatal error
    if (func_num_args()) {

        // If current error is not in a list of ignored errors - return
        if(!(error_reporting() & $type)) return;

    // Else if argument are not given, we assume that we are here because
    // of a register_shutdown_function() usage, e.g current error is a fatal error
    } else {

        // Get the fatal error
        $error = error_get_last();

        //if ($error !== null && $error["type"] != E_NOTICE && $error["type"] != E_DEPRECATED) extract($error);
        if ($error === null || in($error['type'], array(E_NOTICE, E_DEPRECATED))) return;

        // Extract error info
        extract($error);
    }

    // Flush json-encoded error info, wrapped by <error> tag
    echo jerror($type, $message, $file, $line);
}

/**
 * Build and return a string, containing json-encoded error info, wrapped with
 * '<error>' tag, for error to be easy pickable with javascript
 *
 * @param $errno
 * @param $errstr
 * @param $errfile
 * @param $errline
 * @return string
 */
function jerror($errno, $errstr, $errfile, $errline) {

    // Build an array, containing error information
    $error = array(
        'code' => $errno,
        'text' => $errstr,
        'file' => $errfile,
        'line' => $errline,
        'trace' => array_slice(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), 2)
    );

    // Log this error if logging of 'jerror's is turned On
    if (Indi::logging('jerror')) Indi::log('jerror', $error);

    // Send HTTP 500 code
    if (!headers_sent()) header('HTTP/1.1 500 Internal Server Error');

    // Return that info via json encode, wrapped with '<error>' tag, for error to be easy pickable with javascript
    return '<error>' . json_encode($error) . '</error>';
}

/**
 * Displays formatted view of a given value
 *
 * @param mixed $value
 * @return null
 */
function d($value) {

    // Wrap the $value with the '<pre>' tag, and write it to the output
    echo '<pre>'; print_r($value); echo '</pre>';
}

/**
 * Write the contents of $value to a file - 'debug.txt' by default, located in the 'www' folder of the document root
 *
 * @param $value
 * @param string $type
 * @param string $file
 */
function i($value, $type = 'w', $file = 'debug.txt') {

    // Get the document root, with trimmed right trailing slash
    $doc = rtrim($_SERVER['DOCUMENT_ROOT'], '\\/');

    // Get the array of directory branches, from current directory and up to the document root (non-inclusive)
    $dir = explode('/', substr(str_replace('\\', '/', __DIR__), strlen($doc)));

    // Get the STD path, if project run not from the document root, but from some-level subdirectory of document root
    $std = implode('/', array_slice($dir, 0, count($dir) -2));

    // Get the absolute path of a file, that will be used for writing data to
    $abs = $doc . $std. '/www/' . $file;

    // Renew the $dir, where we assume that output file is/will be located
    // Here we do not use existing $dir value, because $file arg can be
    // not only 'someOutputFile.txt', for example, but 'someSubDir/someOutputFile.txt' also
    // e.g. it can contain additional (deeper) directory specification
    $dir = Indi::dir(pathinfo($abs, PATHINFO_DIRNAME) . '/');

    // If $dir is not a directory name
    if (!Indi::rexm('dir', $dir)) {

        // Backup logging mode for 'jerror'
        $mode = Indi::logging('jerror');

        // Disable logging for 'jerror'
        Indi::logging('jerror', false);

        // Flush error, containing message describing dir info: whether is ex
        echo jerror(2, $dir, __FILE__, __LINE__);

        // Revert back logging for 'jerror'
        Indi::logging('jerror', $mode);

    // Else if $abs file exists but not writable
    } else if (file_exists($abs) && !is_writable($abs)) {

        // Backup logging mode for 'jerror'
        $mode = Indi::logging('jerror');

        // Disable logging for 'jerror'
        Indi::logging('jerror', false);

        // Flush error message, saying that destination file is not writable
        echo jerror(2, $abs . ' is not writable', __FILE__, __LINE__);

        // Revert back logging for 'jerror'
        Indi::logging('jerror', $mode);

    // Else
    } else {

        // Write the data
        $fp = fopen($abs, $type); fwrite($fp, print_r($value, true) . "\n"); fclose($fp);
    }
}

/**
 * Gets indent as '&nbsp;' multiplied on $count and on current $level
 *
 * @param $level
 * @param int $count
 * @param string $char
 * @return string
 */
function indent($level, $count = 5, $char = '&nbsp;') {

    // Init $single and $indent variables with empty values
    $indent = '';

    // Build the indent
    for ($i = 0; $i < $count; $i++) for ($j = 0; $j < $level; $j++) $indent .= $char;

    // Return the indent
    return $indent;
}

/**
 * Trim the given string by the $length characters, assuming that string is in utf8 encoding, and append dots '..'
 * as an indicator of that string was trimmed
 *
 * @param $string
 * @param $length
 * @param bool $dots
 * @return string
 */
function usubstr($string, $length, $dots = true) {

    // If $dots argument is true, and length of $string argument
    // is greater that the value of $length argument set $dots as '..'
    $dots = mb_strlen($string, 'utf-8') > $length && $dots ? '…' : '';

    // Trim the $string by the $length characters, add dots, if need, and return the result string
    return mb_substr($string, 0, $length, 'utf-8') . $dots;
}

/**
 * Get the string representation of the period, between dates
 *
 * @param string|int $date1 Can be formatted date or unix-timestamp
 * @param string|int|null $date2 Can be formatted date or unix-timestamp. If not given, time() will be used instead
 * @param string $mode Can be 'ago' or 'left'
 * @param bool $exact If passed non-false, return value will be exact
 * @return string
 */
function ago($date1, $date2 = null, $mode = 'ago', $exact = false) {

    // Convert $date1 and $date2 dates to unix-timestamps
    $date1 = is_numeric($date1) ? $date1 : strtotime($date1);
    $date2 = $date2 ? (is_numeric($date2) ? $date2 : strtotime($date2)) : time();

    // If $curr date and $past
    if ($date1 == $date2) return '';

    // Setup $sign depend on whether $date2 is greater than $date1 and $mode is 'left' or 'ago'
    if ($mode == 'left') {
        $sign = $date2 < $date1 ? '' : '-';
    } else if (!$mode || $mode == 'ago') {
        $sign = $date2 > $date1 ? '' : '-';
    }

    // Get the difference between them in seconds
    $duration = max($date1, $date2) - min($date1, $date2);

    // Build an array of difference levels and their values
    $levelA = array(
        'Y' => date('Y', $duration) - 1970,
        'n' => date('n', $duration) - 1,
        'j' => date('j', $duration) - 1,
        'G' => date('G', $duration) - 3,
        'i' => ltrim(date('i', $duration), '0'),
        's' => ltrim(date('s', $duration), '0')
    );

    // Build an array of difference levels quantity spelling, depends on their values
    $tbqA = array(
        'Y' => 'лет,год,года',
        'n' => 'месяцев,месяц,месяца',
        'j' => 'дней,день,дня',
        'G' => 'часов,час,часа',
        'i' => 'минут,минута,минуты',
        's' => 'секунд,секунда,секунды'
    );

    // If $exact arg is true
    if ($exact) {

        // Start building exact value
        $exact = $sign;

        // Build exact value
        foreach ($levelA as $levelK => $levelV) if ((int) $levelV) $exact .=  tbq($levelV, $tbqA[$levelK]) . ' ';

        // Return exact value
        return trim($exact);
    }

    // Foreach difference level, check if it is has non-zero value and return correct spelling
    foreach ($levelA as $levelK => $levelV) if ((int) $levelV) return $sign . tbq($levelV, $tbqA[$levelK]);
}

/**
 * Add the measure version to a given quantity $q
 *
 * @param int $q
 * @param string $versions012
 * @param bool $showNumber
 * @return string
 */
function tbq($q = 2, $versions012 = '', $showNumber = true) {

    // Distribute quantity measure spell versions
    list($formatA['2-4'], $formatA['1'], $formatA['0,11-19,5-9']) = array_reverse(ar($versions012));

    // Foreach format
    foreach ($formatA as $formatK => $formatV) {

        // Extract the intervals from format key
        $spanA = explode(',', $formatK);

        // Foreach interval
        for ($k = 0; $k < count($spanA); $k++) {

            // If current interval is actually not interval, e.g it constits from only one digit
            if (strpos($spanA[$k], '-') === false) {

                // If quantity count ends with that digit
                if (preg_match('/' . $spanA[$k] . '$/', $q))

                    // Return the quantity (if $showNumber argument is true), with appended spell version
                    return ($showNumber ? $q . ' ' : '') . $formatV;

            // Else current interval really is an inteval
            } else {

                // Get the start and end digits of that interval
                $interval = explode('-', $spanA[$k]);

                // Foreach digit within start and end interval digits
                for ($m = $interval[0]; $m <= $interval[1]; $m ++) {

                    // If quantity count ends with that digit
                    if (preg_match('/' . $m . '$/', $q))

                        // Return the quantity (if $showNumber argument is true), with appended spell version
                        return ($showNumber ? $q . ' ' : '') . $formatV;
                }
            }
        }
    }
}

/**
 * Does the exact same as getimagesize one, but for flash files
 *
 * @param $path
 * @return array|bool|int|string
 */
function getflashsize($path) {

    // Special class for use as flash stream wrapper. This code was got somethere on the internet,
    // so i feel too lazy to write a proper comments
    if (!class_exists('blob_data_as_file_stream')) {class blob_data_as_file_stream {private static $blob_data_position=0;
    public static $blob_data_stream=''; public static function stream_open($path,$mode,$options,&$opened_path){self::
    $blob_data_position=0;return true;}public static function stream_seek($seek_offset,$seek_whence){$blob_data_length
    =strlen(self::$blob_data_stream);switch($seek_whence){case SEEK_SET:$new_blob_data_position=$seek_offset;break;case
    SEEK_CUR:$new_blob_data_position=self::$blob_data_position+$seek_offset;break;case SEEK_END:$new_blob_data_position=
    $blob_data_length+$seek_offset;break;default:return false;}if(($new_blob_data_position>=0)AND($new_blob_data_position
    <=$blob_data_length)){self::$blob_data_position=$new_blob_data_position;return true;}else{return false;}}public static
    function stream_tell(){return self::$blob_data_position;}public static function stream_read($read_buffer_size){$read_data=
    substr(self::$blob_data_stream,self::$blob_data_position,$read_buffer_size);self::$blob_data_position+=strlen(
    $read_data);return $read_data;}public static function stream_write($write_data){$write_data_length=strlen($write_data);
    self::$blob_data_stream=substr(self::$blob_data_stream,0,self::$blob_data_position).$write_data.substr(
    self::$blob_data_stream,self::$blob_data_position+=$write_data_length);return $write_data_length;}public static
    function stream_eof(){return self::$blob_data_position >= strlen(self::$blob_data_stream);}}}

    // Register stream wrapper
    @stream_wrapper_register('FlashStream', 'blob_data_as_file_stream');

    // Store file contents to the data stream
    blob_data_as_file_stream::$blob_data_stream = file_get_contents(preg_replace('/(\?.*)*/', '', $path));

    //Run getimagesize() on the data stream
    return @getimagesize('FlashStream://');
}

/**
 * Detect if user agent is <user agent key>. Currently supported keys are 'ie8' and 'ipad' only.
 *
 * @param $uaK
 * @return bool
 */
function ua($uaK) {

    // Get the user agent string from environment
    $ua = $_SERVER['HTTP_USER_AGENT'];

    // Declare the array of keys and their identifiers
    $uaA = array('ie8' => 'MSIE 8', 'ipad' => 'iPad');

    // Detect
    return preg_match('/' . $uaA[$uaK] . '/', $ua) ? true : false;
}

/**
 * Convert color from 'rgb' format to 'hsl' format, and return converted as array
 *
 * @param $rgb
 * @return array
 */
function rgb2hsl($rgb) {

    // This code was got somethere on the internet, so i feel too lazy to write a proper comments
    $varR=$rgb[0]/255;$varG=$rgb[1]/255;$varB=$rgb[2]/255;$varMin=min($varR,$varG,$varB);$varMax=max($varR,$varG,$varB);
    $delMax=$varMax-$varMin;$l=($varMax+$varMin)/2;if($delMax==0){$H=0;$S = 0;}else{if($l<0.5){$s=$delMax/($varMax+$varMin);
    }else{$s=$delMax/(2-$varMax-$varMin);}$delR=((($varMax-$varR)/6)+($delMax/2))/$delMax;$delG=((($varMax-$varG)/6)+($delMax
    /2))/$delMax;$delB=((($varMax-$varB)/6)+($delMax/2))/$delMax;if($varR==$varMax){$h=$delB-$delG;}else if($varG==$varMax)
    {$h=(1/3)+$delR-$delB;}else if($varB==$varMax){$h=(2/3)+$delG-$delR;}if($h<0){$h++;}if($h>1){$h--;}}return array($h,$s,$l);
}

/**
 * Append a hue number to a $rgb color in format 'rrggbb', so the result color will look like 'hue#rrggbb'
 *
 * @param string $rgb
 * @return string
 */
function hrgb($rgb = '') {

    // Strip the '#' sign from the beginning of $rgb agrument
    $rgb = preg_replace('/^#/', '', $rgb);

    // Convert red, green and blue values from hex to decimals
    $r = hexdec(substr($rgb, 0, 2));
    $g = hexdec(substr($rgb, 2, 2));
    $b = hexdec(substr($rgb, 4, 2));

    // Get the hue value
    list($hue) = rgb2hsl(array($r, $g, $b));

    // Append the hue value to a color and return it
    return str_pad(round($hue*360), 3, '0', STR_PAD_LEFT) . '#' . $rgb;
}

/**
 * Generate a sequence, consisting of random characters
 *
 * @param int $length
 * @param string $charTypes
 * @return string
 */
function grs($length = 15, $charTypes = 'an') {

    // Set of characters
    $chars = array();

    // Strip unsupported values from $charTypes arg
    $charTypes = preg_replace('/[^ans]/', '', $charTypes);

    // If $charTypes arg was given, but it does not contain supported values, reset it's value to default
    if (!$charTypes) $charTypes = 'an';

    // If $charTypes arg contains 'a' letter, include alpha-characters in the chars list
    if (preg_match('/a/', $charTypes)) $chars = array_merge($chars, array(
        'a', 'b', 'c', 'd', 'e', 'f',
        'g', 'h', 'i', 'j', 'k', 'l',
        'm', 'n', 'o', 'p', 'q', 'r',
        's', 't', 'u', 'v', 'w', 'x',
        'y', 'z', 'A', 'B', 'C', 'D',
        'E', 'F', 'G', 'H', 'I', 'J',
        'K', 'L', 'M', 'N', 'O', 'P',
        'Q', 'R', 'S', 'T', 'U', 'V',
        'W', 'X', 'Y', 'Z'
    ));

    // If $charTypes arg contains 'a' letter, include numeric-characters in the chars list
    if (preg_match('/n/', $charTypes)) $chars = array_merge($chars, array(
        '1', '2', '3', '4', '5', '6', '7', '8', '9', '0'
    ));

    // If $charTypes arg contains 's' letter, include special-characters in the chars list
    if (preg_match('/s/', $charTypes)) $chars = array_merge($chars, array(
        '.', ',', '(', ')', '[', ']',
        '!', '?', '&', '^', '%', '@',
        '*', '$', '<', '>', '/', '|',
        '+', '-', '{', '}', '`', '~'
    ));

    // Generate
    $s = ''; for ($i = 0; $i < $length; $i++) $s .= $chars[rand(0, count($chars) - 1)];

    // Return sequence
    return $s;
}

/**
 * Build a localized date
 *
 * @param string $format
 * @param string $date
 * @param string|array $when
 * @return string
 */
function ldate($format, $date = '', $when = '') {

    // If $date arg not given - assume it is a current datetime
    if (!$date) $date = date('Y-m-d H:i:s');
    else if (preg_match('/^[0-9]{8,11}$/', $date)) $date = date('Y-m-d H:i:s', $date);

    // If strftime's format syntax is used
    if (preg_match('/%/', $format)) {

        // Format date
        $formatted = strftime($format, strtotime($date));

        // Return
        return mb_strtolower($_SERVER['WINDIR'] ? iconv('windows-1251', 'UTF-8', $formatted) : $formatted, 'utf-8');

    // Else
    } else {

        // Get localized date
        $date = ldate(Indi::date2strftime($format), $date);

        // Force Russian-style month name endings
        if (in('month', $when)) foreach (array('ь' => 'я', 'т' => 'та', 'й' => 'я') as $s => $r) {
            $date = preg_replace('/([а-яА-Я]{2,})' . $s . '\b/u', '$1' . $r, $date);
            $date = preg_replace('/' . $s . '(\s)/u', $r . '$1', $date);
            $date = preg_replace('/' . $s . '$/u', $r, $date);
        }

        // Force Russian-style weekday name endings, suitable for version, spelling-compatible for question 'When?'
        if (in('weekday', $when))
            foreach (array('а' => 'у') as $s => $r) {
                $date = preg_replace('/' . $s . '\b/u', $r, $date);
                $date = preg_replace('/' . $s . '(\s)/u', $r . '$1', $date);
                $date = preg_replace('/' . $s . '$/u', $r, $date);
            }

        // Return
        return $date;
    }
}

/**
 * Provide php's lcfirst() function declaration, as it's usefult, but not available in PHP versions < 5.3.0
 */
if (!function_exists('lcfirst')) {
	function lcfirst($string) {
		return strtolower(substr($string, 0, 1)) . substr($string, 1);
	}
}

/**
 * Provide non-native-existing, but usefult mb_lcfirst() function declaration
 */
if (!function_exists('mb_lcfirst')) {
    function mb_lcfirst($str, $u = 'utf-8') {
        return mb_strtolower(mb_substr($str, 0, 1, $u), $u) . mb_substr($str, 1, mb_strlen($str, $u) - 1, $u);
    }
}

/**
 * Provide php's array_column() function declaration, as it's useful, but not available in PHP versions < 5.5.0
 */
if (!function_exists('array_column')) {
    function array_column(array $array, $column_key, $index_key = null) {
        $column = array();
        foreach ($array as $item) {
            if ($index_key) {
                $column[$item[$index_key]] = $item[$column_key];
            } else {
                $column[] = $item[$column_key];
            }
        }
        return $column;
    }
}

/**
 * Provide php's http_parse_headers() function declaration, as it's useful,
 * but available only as a part if special PECL extension, that may be not installed
 */
if (!function_exists('http_parse_headers')) {
    function http_parse_headers($raw){
        return parsepairs($raw, ':');
    }
}

/**
 * 
 *
 */
function parsepairs($raw, $delimiter = ':'){
    $headers = array(); $key = '';
    foreach(explode("\n", $raw) as $h) {
        $h = explode($delimiter, $h, 2);
        if (isset($h[1])){
            if (!isset($headers[$h[0]])) $headers[$h[0]] = trim($h[1]);
            else if (is_array($headers[$h[0]])) $headers[$h[0]] = array_merge($headers[$h[0]], array(trim($h[1])));
            else $headers[$h[0]] = array_merge(array($headers[$h[0]]), array(trim($h[1])));
            $key = $h[0];
        } else {
            if (substr($h[0], 0, 1) == "\t") $headers[$key] .= "\r\n\t".trim($h[0]);
            else if (!$key) $headers[0] = trim($h[0]);trim($h[0]);
        }
    }
    return $headers;
}

/**
 * Provide php's apache_request_headers() function declaration, as it's useful,
 * but available only in case if PHP is running as an Apache module. Function
 * implementation initially got from stackoverflow.com
 */
if (!function_exists('apache_request_headers')) {
    function apache_request_headers() {
        
        // Cased headers
        $casedHeaderA = array(

            // HTTP
            'Dasl'             => 'DASL',
            'Dav'              => 'DAV',
            'Etag'             => 'ETag',
            'Mime-Version'     => 'MIME-Version',
            'Slug'             => 'SLUG',
            'Te'               => 'TE',
            'Www-Authenticate' => 'WWW-Authenticate',

            // MIME
            'Content-Md5'      => 'Content-MD5',
            'Content-Id'       => 'Content-ID',
            'Content-Features' => 'Content-features',
        );
        
        // Headers array
        $httpHeaderA = array();

        // Pick headers info from $_SERVER
        foreach($_SERVER as $k => $v) {

            // Make sure we $k is header name
            if('HTTP_' !== substr($k, 0, 5)) continue;
            
            // Trim 'HTTP_'
            $k = strtolower(substr($k, 5));

            // If header name contains '_'
            if (0 < substr_count($k, '_')) {

                // Split by '_'
                $kA = explode('_', $k);

                // Call 'ucfirst' on each item within $kA
                $kA = array_map('ucfirst', $kA);

                // Implode by '-'
                $k = implode('-', $kA);

            // Else call 'ucfirst' on $k
            } else $k = ucfirst($k);

            // Replace key name if needed
            if (array_key_exists($k, $casedHeaderA)) $k = $casedHeaderA[$k];

            // Push into $httpHeaderA
            $httpHeaderA[$k] = $v;
        }
        
        // Return
        return $httpHeaderA;
    }
}


/**
 * Shortcut for in_array() function, but takes $array argument not only as array, but as a string also.
 * In that case $array argument will be converted to array by splitting by comma.
 *
 * @param $item
 * @param $array
 * @return boolean
 */
function in($item, $array) {

    // If $array arg is bool or is null - set $strict flag as true
    $strict = is_bool($array) || is_null($array);

    // Normalize $array arg
    $array = ar($array);

    // Return
    return in_array($item, $array, $strict);
}

/**
 * Shortcut for implode() function, but with the reversed order of arguments
 *
 * @param $array
 * @param string $separator
 * @return string
 */
function im(array $array, $separator = ',') {
    return implode($separator, $array);
}

/**
 * Comma-separeted values to array converter
 *
 * @param $items
 * @param $allowEmpty - If $items arg is an empty string, function will return an array containing that empty string
 *                      as a first item, rather than returning empty array
 * @return array
 */
function ar($items, $allowEmpty = false) {

    // If $items arg is already an array - return it as is
    if (is_array($items)) return $items;

    // Else if $items arg is strict null - return array containing that null as a first item
    if ($items === null) return $allowEmpty ? array(null) : array();

    // Else if $items arg is a boolean value - return array containing that boolean value as a first item
    if (is_bool($items)) return array($items);

    // Else if $items arg is an object we either return result of toArray() call on that object,
    // or return result, got by php's native '(array)' cast-prefix expression, depending whether
    // or not $items object has 'toArray()' method
    if (is_object($items)) return in_array('toArray', get_class_methods($items)) ? $items->toArray(): (array) $items;

    // Else we assume $items is a string and return an array by comma-exploding $items arg
    if (is_string($items)) {

        // If $items is an empty string - return empty array
        if (!strlen($items) && !$allowEmpty) return array();

        // Explode $items arg by comma
        foreach ($items = explode(',', $items) as $i => $item) {

            // Convert strings 'null', 'true' and 'false' items to their proper types
            if ($item == 'null') $items[$i] = null;
            if ($item == 'true') $items[$i] = true;
            if ($item == 'false') $items[$i] = false;
        }

        // Return normalized $items
        return $items;
    }

    // Else return array, containing $items arg as a single item
    return array($items);
}

/**
 * Remove one or more items from the array
 *
 * @param mixed $array
 * @param mixed $unset
 * @param bool $strict
 * @param bool $preserveKeys
 * @return array
 */
function un($array, $unset, $strict = true, $preserveKeys = false) {

    // Convert $array into an array in case if it is a comma-separated string
    $array = ar($array);

    // Convert $unset
    $unset = ar($unset);

    // Find all keys of a values, that should be removed from array, and remove them
    foreach ($unset as $unsetI)
        foreach (array_keys($array, $unsetI, $strict) as $key)
            unset($array[$key]);

    // Return filtered array
    return $preserveKeys ? $array : array_values($array);
}

/**
 * Convert number to string representation
 */
function num2str($num, $iunit = true, $dunit = true) {
    if(!function_exists('num2str_')){function num2str_($n,$f1,$f2,$f5){$n=abs(intval($n))%100;if($n>10&&$n<20)return$f5;
    $n=$n%10;if($n>1&&$n<5)return$f2;if($n==1)return $f1;return $f5;}}
    $nul='ноль';$ten=array(array('','один','два','три','четыре','пять','шесть','семь','восемь','девять'),array('','одна',
    'две','три','четыре','пять','шесть','семь','восемь','девять'),);$a20=array('десять','одиннадцать','двенадцать','тринадцать',
    'четырнадцать','пятнадцать','шестнадцать','семнадцать','восемнадцать','девятнадцать');$tens=array(2=>'двадцать','тридцать',
    'сорок','пятьдесят','шестьдесят','семьдесят','восемьдесят','девяносто');$hundred=array('','сто','двести','триста',
    'четыреста','пятьсот','шестьсот','семьсот','восемьсот','девятьсот');$unit=array(array('копейка','копейки','копеек',1),
    array('рубль','рубля','рублей',0),array('тысяча','тысячи','тысяч',1),array('миллион','миллиона','миллионов',0),array(
    'миллиард','милиарда','миллиардов',0),);list($rub,$kop)=explode('.',sprintf("%015.2f",floatval($num)));$out=array();
    if(intval($rub)>0){foreach(str_split($rub,3)as$uk=>$v){if(!intval($v))continue;$uk=sizeof($unit)-$uk-1;$gender=$unit
    [$uk][3];list($i1,$i2,$i3)=array_map('intval',str_split($v,1));$out[]=$hundred[$i1];if($i2>1)$out[]=$tens[$i2].' '.
    $ten[$gender][$i3];else$out[]=$i2>0?$a20[$i3]:$ten[$gender][$i3];if($uk>1)$out[]=num2str_($v,$unit[$uk][0],$unit[$uk][1],
    $unit[$uk][2]);}}else$out[]=$nul;if($iunit)$out[]=num2str_(intval($rub), $unit[1][0],$unit[1][1],$unit[1][2]);if($dunit)
    $out[]=$kop.' '.num2str_($kop,$unit[0][0],$unit[0][1],$unit[0][2]);return trim(preg_replace('/ {2,}/',' ',join(' ',$out)));
}

/**
 * Flush the json-encoded message, containing `status` property, and other optional properties
 *
 * @param $success
 * @param mixed $msg1
 * @param mixed $msg2
 * @param bool $die
 */
function jflush($success, $msg1 = null, $msg2 = null, $die = true) {

    // Start building data for flushing
    $flush = is_array($success) && array_key_exists('success', $success) ? $success : array('success' => $success);

    // Deal with first data-argument
    if (func_num_args() > 1 && func_get_arg(1) != null)
        $mrg1 = is_object($msg1)
            ? (in('toArray', get_class_methods($msg1)) ? $msg1->toArray() : (array) $msg1)
            : (is_array($msg1) ? $msg1 : array('msg' => $msg1));

    // Deal with second data-argument
    if (func_num_args() > 2 && func_get_arg(2) != null)
        $mrg2 = is_object($msg2)
            ? (in('toArray', get_class_methods($msg2)) ? $msg2->toArray() : (array) $msg2)
            : (is_array($msg2) ? $msg2 : array('msg' => $msg2));

    // Merge the additional data to the $flush array
    if ($mrg1) $flush = array_merge($flush, $mrg1);
    if ($mrg2) $flush = array_merge($flush, $mrg2);

    // If headers were not already sent - flush an error message
    if (!headers_sent()) header('Content-Type: application/json');

    // Check if redirect should be performed
    $redir = func_num_args() == 4 ? is_string($die) && Indi::rex('url', $die) : ($_ = Indi::$jfr) && $die = $_;

    // Log this error if logging of 'jerror's is turned On
    if (Indi::logging('jflush') || $redir) Indi::log('jflush', $flush);

    // Send HTTP 400 or 200 status code
    if ($flush['success'] === false && !headers_sent()) header('HTTP/1.1 400 Bad Request');
    if ($flush['success'] === true && !headers_sent()) header('HTTP/1.1 200 OK');

    // If $die arg is an url - do not flush data
    if (!$redir) echo json_encode($flush);

    // Exit if need
    if ($redir) die(header('Location: ' . $die)); else if ($die) iexit();
}

/**
 * Flush mismatch errors messages. This can be useful instead of jflush(false, 'Some error message'),
 * in cases when you want 'Some error message' to appear as a certain field's error message.
 *
 * Example:
 * if (!preg_match($emailRegexPattern, $_POST['email'])) mflush('email', 'Invalid email format');
 *
 * @param string $field
 * @param string $msg
 */
function mflush($field, $msg = '') {

    // Mismatches array
    $mismatch = array();

    // If $field arg is a string - add $msg into $mismatch array using $field arg as a key
    if (is_string($field) && $msg) $mismatch[$field] = $msg;

    // Else if $field arg is an array - assume that it is an array containing
    // mismatch error messages for more than 1 field
    else if (is_array($field)) $mismatch = $field;

    // Flush
    jflush(false, array('mismatch' => array(
        'direct' => true,
        'errors' => $mismatch
    )));
}
/**
 * Flush the json-encoded message, containing `status` property, and other optional properties, especially for confirm
 *
 * @param string $msg
 */
function jconfirm($msg) {

    // Start building data for flushing
    $flush = array('confirm' => true, 'msg' => $msg);

    // If headers were not already sent - flush an error message
    if (!headers_sent()) header('Content-Type: application/json');

    // Here we send HTTP/1.1 400 Bad Request to prevent success handler from being fired
    if (!headers_sent()) header('HTTP/1.1 400 Bad Request');

    // Flush
    iexit(json_encode($flush));
}

/**
 * Normalize the price-value
 *
 * @param float|int $price
 * @param bool $formatted
 * @return float|string
 */
function price($price, $formatted = false) {
    return decimal($price, 2, $formatted);
}

/**
 * Normalize the decimal value to the specified precision
 *
 * @param float|int $value
 * @param int $precision
 * @param bool $formatted
 * @return float|string
 */
function decimal($value, $precision = 2, $formatted = false) {

    // Get the normalizer value
    $normalizer = pow(10, $precision);

    // Get price
    $float = round($value * $normalizer) / $normalizer;

    // Return that price as float value or as formatted string
    return $formatted ? number_format($float, $precision, '.', ' ') : $float;
}

/**
 * Converts passed string to it's url equivalent
 *
 * @param $title
 * @return string
 */
function alias($title){

    // Symbols
    $s = array('а','б','в','г','д','е','ё','ж','з','и','й','к','л','м','н','о','п','р','с','т','у','ф','х','ц','ч','ш','щ',
        'ъ','ы','ь','э','ю','я','№',' ','a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s',
        't','u','v','w','x','y','z','-','0','1','2','3','4','5','6','7','8','9','Ë','À','Ì','Â','Í','Ã','Î','Ä','Ï',
        'Ç','Ò','È','Ó','É','Ô','Ê','Õ','Ö','ê','Ù','ë','Ú','î','Û','ï','Ü','ô','Ý','õ','â','û','ã','ÿ','ç','&', '/', '_');

    // Replacements
    $r = array('a','b','v','g','d','e','yo','zh','z','i','i','k','l','m','n','o','p','r','s','t','u','f','h','c','ch','sh','shh',
        '','y','','e','yu','ya','','-','a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s',
        't','u','v','w','x','y','z','-','0','1','2','3','4','5','6','7','8','9','e','a','i','a','i','a','i','a','i',
        'c','o','e','o','e','o','e','o','o','e','u','e','u','i','u','i','u','o','u','o','a','u','a','y','c','-and-', '-', '_');

    // Declare variable for alias
    $alias = '';

    // Convert passed title to loweк case and trim whitespaces
    $title = trim(mb_strtolower($title, 'utf-8'));

    // Find a replacement for each char of title and append it to alias
    for ($i = 0; $i < mb_strlen($title, 'utf-8'); $i++) {
        $c = mb_substr($title, $i, 1, 'utf-8');
        if (($j = array_search($c, $s)) !== false) $alias .= $r[$j];
    }

    // Strip '-' symbols from alias beginning, ending and replace multiple '-' symbol occurence with single occurence
    $alias = preg_replace('/^\-+/', '', $alias);
    $alias = preg_replace('/\-+$/', '', $alias);
    $alias = preg_replace('/\-{2,}/', '-', $alias);

    // Got as we need
    return $alias;
}

/**
 * @param $msg
 */
function iexit($msg = null) {

    // Send all DELETE queries to an special email address, for debugging
    Indi::mailDELETE();

    // Exit
    exit($msg);
}

/**
 * Get the sign of a number
 *
 * @param $n
 * @return int
 */
function sign($n) {
    return (int) ($n > 0) - (int) ($n < 0);
}

/**
 * Convert size in bytes to string representation
 *
 * @param $size
 * @return string
 */
function size2str($size) {

    // Postixes
    $postfix = array('b', 'kb', 'mb', 'gb', 'tb', 'pb');

    // Pow
    $pow = (int) floor(strlen($size)/3);

    // Return
    return (floor(($size/pow(1024, $pow))*100)/100) . $postfix[$pow];
}

/**
 * Wrap all urls with <a href="..">
 * Code got from: http://stackoverflow.com/questions/1188129/replace-urls-in-text-with-html-links
 *
 * Testing text: <<<EOD

Here are some URLs:
stackoverflow.com/questions/1188129/pregreplace-to-detect-html-php
Here's the answer: http://www.google.com/search?rls=en&q=42&ie=utf-8&oe=utf-8&hl=en. What was the question?
A quick look at http://en.wikipedia.org/wiki/URI_scheme#Generic_syntax is helpful.
There is no place like 127.0.0.1! Except maybe http://news.bbc.co.uk/1/hi/england/surrey/8168892.stm?
Ports: 192.168.0.1:8080, https://example.net:1234/.
Beware of Greeks bringing internationalized top-level domains: xn--hxajbheg2az3al.xn--jxalpdlp.
And remember.Nobody is perfect.

<script>alert('Remember kids: Say no to XSS-attacks! Always HTML escape untrusted input!');</script>
EOD;

 *
 * @param $text
 * @return string
 */
function url2a($text) {

    // Regexps
    $rexProtocol = '(https?://)?';
    $rexDomain   = '((?:[-a-zA-Z0-9]{1,63}\.)+[-a-zA-Z0-9]{2,63}|(?:[0-9]{1,3}\.){3}[0-9]{1,3})';
    $rexPort     = '(:[0-9]{1,5})?';
    $rexPath     = '(/[!$-/0-9:;=@_\':;!a-zA-Z\x7f-\xff]*?)?';
    $rexQuery    = '(\?[!$-/0-9:;=@_\':;!a-zA-Z\x7f-\xff]+?)?';
    $rexFragment = '(#[!$-/0-9:;=@_\':;!a-zA-Z\x7f-\xff]+?)?';

    // Valid top-level domains
    $validTlds = array_fill_keys(explode(' ', '.aero .asia .biz .cat .com .coop .edu .gov .info .int .jobs .mil .mobi '
        . '.museum .name .net .org .pro .tel .travel .ac .ad .ae .af .ag .ai .al .am .an .ao .aq .ar .as .at .au .aw '
        . '.ax .az .ba .bb .bd .be .bf .bg .bh .bi .bj .bm .bn .bo .br .bs .bt .bv .bw .by .bz .ca .cc .cd .cf .cg '
        . '.ch .ci .ck .cl .cm .cn .co .cr .cu .cv .cx .cy .cz .de .dj .dk .dm .do .dz .ec .ee .eg .er .es .et .eu '
        . '.fi .fj .fk .fm .fo .fr .ga .gb .gd .ge .gf .gg .gh .gi .gl .gm .gn .gp .gq .gr .gs .gt .gu .gw .gy .hk '
        . '.hm .hn .hr .ht .hu .id .ie .il .im .in .io .iq .ir .is .it .je .jm .jo .jp .ke .kg .kh .ki .km .kn .kp '
        . '.kr .kw .ky .kz .la .lb .lc .li .lk .lr .ls .lt .lu .lv .ly .ma .mc .md .me .mg .mh .mk .ml .mm .mn .mo '
        . '.mp .mq .mr .ms .mt .mu .mv .mw .mx .my .mz .na .nc .ne .nf .ng .ni .nl .no .np .nr .nu .nz .om .pa .pe '
        . '.pf .pg .ph .pk .pl .pm .pn .pr .ps .pt .pw .py .qa .re .ro .rs .ru .rw .sa .sb .sc .sd .se .sg .sh .si '
        . '.sj .sk .sl .sm .sn .so .sr .st .su .sv .sy .sz .tc .td .tf .tg .th .tj .tk .tl .tm .tn .to .tp .tr .tt '
        . '.tv .tw .tz .ua .ug .uk .us .uy .uz .va .vc .ve .vg .vi .vn .vu .wf .ws .ye .yt .yu .za .zm .zw '
        . '.xn--0zwm56d .xn--11b5bs3a9aj6g .xn--80akhbyknj4f .xn--9t4b11yi5a .xn--deba0ad .xn--g6w251d '
        . '.xn--hgbk6aj7f53bba .xn--hlcj6aya9esc7a .xn--jxalpdlp .xn--kgbechtv .xn--zckzah .arpa'), true);

    // Start output buffering
    ob_start();

    // Position
    $position = 0;

    // Split given $text by urls
    while (preg_match("{\\b$rexProtocol$rexDomain$rexPort$rexPath$rexQuery$rexFragment(?=[?.!,;:\"]?(\s|$))}",
        $text, $match, PREG_OFFSET_CAPTURE, $position)) {

        // Extract $url and $urlPosition from match
        list($url, $urlPosition) = $match[0];

        // Print the text leading up to the URL.
        print(htmlspecialchars(substr($text, $position, $urlPosition - $position)));

        // Pick domain, port and path from matches
        $domain = $match[2][0];
        $port   = $match[3][0];
        $path   = $match[4][0];

        // Get top-level domain
        $tld = strtolower(strrchr($domain, '.'));

        // Check if the TLD is valid - or that $domain is an IP address.
        if (preg_match('{\.[0-9]{1,3}}', $tld) || isset($validTlds[$tld])) {

            // Prepend http:// if no protocol specified
            $completeUrl = $match[1][0] ? $url : 'http://' . $url;

            // Print the hyperlink.
            printf('<a href="%s">%s</a>', htmlspecialchars($completeUrl), htmlspecialchars("$domain$port$path"));

        // Else if not a valid URL.
        } else print(htmlspecialchars($url));

        // Continue text parsing from after the URL.
        $position = $urlPosition + strlen($url);
    }

    // Print the remainder of the text.
    print(htmlspecialchars(substr($text, $position)));

    // Return
    return ob_get_clean();
}

/**
 * Try to detect phone number within the given string
 * and if detected, return it in +7 (123) 456-78-90 format
 *
 * If nothing detected - return empty string
 * If multiple phone numbers detected - return first one
 *
 * @param $str
 * @return mixed|string
 */
function phone($str) {
    $parts = preg_split('/[,;\/б]/', $str);
    $phone = array_shift($parts);
    $phone = preg_replace('/^[^0-9+()]+/', '', $phone);
    $phone = array_shift(explode(' +7', $phone));
    $phone = preg_replace('/([0-9])[ -]([0-9])/', '$1$2', $phone);
    $phone = array_shift(explode('. ', $phone));
    $phone = array_shift(preg_split('/ [а-яА-Я]/', $phone));
    $phone = array_shift(explode('||', $phone));
    $phone = preg_replace('/\) 8/', ')8', $phone);
    $phone = array_shift(explode(' 8', $phone));
    $phone = preg_replace('/\) ([0-9])/', ')$1', $phone);
    $phone = array_shift(preg_split('/ \([а-яА-Я]/', $phone));
    $phone = preg_replace('/- /', '-', $phone);
    $phone = preg_replace('/([0-9])-([0-9])/', '$1$2', $phone);
    $phone = preg_replace('/\)-/', ')', $phone);
    $phone = array_shift(preg_split('/\.[а-яА-Я]/', $phone));
    $phone = preg_replace('/-[а-яА-Я]+$/', '', $phone);
    $phone = rtrim($phone, ' -(');
    $phone = preg_replace('/[ ()-]/', '', $phone);
    $phone = preg_replace('/831831/', '831', $phone);
    if (strlen($phone) == 7) $phone = '+7831' . $phone;
    else if (strlen($phone) == 11 && preg_match('/^8/', $phone)) $phone = preg_replace('/^8/', '+7', $phone);
    else if (strlen($phone) == 10 && preg_match('/^83/', $phone)) $phone = '+7' . $phone;
    else if (strlen($phone) == 11 && preg_match('/^7/', $phone)) $phone = '+' . $phone;
    else if (strlen($phone) == 10 && preg_match('/^9/', $phone)) $phone = '+7' . $phone;
    else if (strlen($phone) == 10 && preg_match('/^495/', $phone)) $phone = '+7' . $phone;
    else if (strlen($phone) == 8 && preg_match('/^257/', $phone)) $phone = '+78' . $phone;
    else if (strlen($phone) == 8 && preg_match('/^23/', $phone)) $phone = '+783' . $phone;
    else if (strlen($phone) == 10 && preg_match('/^383/', $phone)) $phone = '+7' . $phone;
    else if (strlen($phone) == 10 && preg_match('/^093/', $phone)) $phone = '+7493' . preg_replace('/^093/', '', $phone);
    else if (strlen($phone) == 10 && preg_match('/^343/', $phone)) $phone = '+7' . $phone;
    else if (strlen($phone) == 12 && preg_match('/^\+7/', $phone)) $phone = $phone;
    else $phone = '';
    if ($phone) $phone = preg_replace('/(\+7)([0-9]{3})([0-9]{3})([0-9]{2})([0-9]{2})/', '$1 ($2) $3-$4-$5', $phone);
    return $phone;
}

/**
 * Build a string representation of a date and time in special format
 *
 * @param $date
 * @param string $time
 * @return string
 */
function when($date, $time = '') {
    $when = array(); $when_ = '';

    // Detect yesterday/today/tomorrow/etc part
    if ($date == date('Y-m-d', time() - 60 * 60 * 24 * 2)) $when_ = 'позавчера';
    else if ($date == date('Y-m-d', time() - 60 * 60 * 24)) $when_ = 'вчера';
    else if ($date == date('Y-m-d')) $when_ = 'сегодня';
    else if ($date == date('Y-m-d', time() + 60 * 60 * 24)) $when_ = 'завтра';
    else if ($date == date('Y-m-d', time() + 60 * 60 * 24 * 2)) $when_ = 'послезавтра';
    if ($when_) $when[] = $when_ . ',';

    // Append date
    $when[] = date('N', strtotime($date)) == 2 ? 'во' : 'в';
    $when[] = ldate('l d F', $date, 'month,weekday');

    // Append time
    if ($time) $when[] = 'в ' . $time;

    // Return
    return im($when, ' ');
}

/**
 * Create plain PHP associative array from XML.
 *
 * Example usage:
 *   $xmlNode = simplexml_load_file('example.xml');
 *   $arrayData = xml2ar($xmlNode);
 *   echo json_encode($arrayData);
 *
 * @param SimpleXMLElement $xml The root node
 * @param array $options Associative array of options
 * @return array
 * @link http://outlandishideas.co.uk/blog/2012/08/xml-to-json/ More info
 * @author Tamlyn Rhodes <http://tamlyn.org>
 * @license http://creativecommons.org/publicdomain/mark/1.0/ Public Domain
 */
function xml2ar($xml, $options = array()) {
    $defaults = array(
        'namespaceSeparator' => ':',//you may want this to be something other than a colon
        'attributePrefix' => '@',   //to distinguish between attributes and nodes with the same name
        'alwaysArray' => array(),   //array of xml tag names which should always become arrays
        'autoArray' => true,        //only create arrays for tags which appear more than once
        'textContent' => '$',       //key used for the text content of elements
        'autoText' => true,         //skip textContent key if node has no attributes or child nodes
        'keySearch' => false,       //optional search and replace on tag and attribute names
        'keyReplace' => false       //replace values for above search values (as passed to str_replace())
    );
    $options = array_merge($defaults, $options);
    $namespaces = $xml->getDocNamespaces();
    $namespaces[''] = null; //add base (empty) namespace

    //get attributes from all namespaces
    $attributesArray = array();
    foreach ($namespaces as $prefix => $namespace) {
        foreach ($xml->attributes($namespace) as $attributeName => $attribute) {
            //replace characters in attribute name
            if ($options['keySearch']) $attributeName =
                str_replace($options['keySearch'], $options['keyReplace'], $attributeName);
            $attributeKey = $options['attributePrefix']
                . ($prefix ? $prefix . $options['namespaceSeparator'] : '')
                . $attributeName;
            $attributesArray[$attributeKey] = (string)$attribute;
        }
    }

    //get child nodes from all namespaces
    $tagsArray = array();
    foreach ($namespaces as $prefix => $namespace) {
        foreach ($xml->children($namespace) as $childXml) {
            //recurse into child nodes
            $childArray = xml2ar($childXml, $options);
            list($childTagName, $childProperties) = each($childArray);

            //replace characters in tag name
            if ($options['keySearch']) $childTagName =
                str_replace($options['keySearch'], $options['keyReplace'], $childTagName);
            //add namespace prefix, if any
            if ($prefix) $childTagName = $prefix . $options['namespaceSeparator'] . $childTagName;

            if (!isset($tagsArray[$childTagName])) {
                //only entry with this key
                //test if tags of this type should always be arrays, no matter the element count
                $tagsArray[$childTagName] =
                    in_array($childTagName, $options['alwaysArray']) || !$options['autoArray']
                        ? array($childProperties) : $childProperties;
            } elseif (
                is_array($tagsArray[$childTagName]) && array_keys($tagsArray[$childTagName])
                === range(0, count($tagsArray[$childTagName]) - 1)
            ) {
                //key already exists and is integer indexed array
                $tagsArray[$childTagName][] = $childProperties;
            } else {
                //key exists so convert to integer indexed array with previous value in position 0
                $tagsArray[$childTagName] = array($tagsArray[$childTagName], $childProperties);
            }
        }
    }

    //get text content of node
    $textContentArray = array();
    $plainText = trim((string)$xml);
    if ($plainText !== '') $textContentArray[$options['textContent']] = $plainText;

    //stick it all together
    $propertiesArray = !$options['autoText'] || $attributesArray || $tagsArray || ($plainText === '')
        ? array_merge($attributesArray, $tagsArray, $textContentArray) : $plainText;

    //return node as array
    return array(
        $xml->getName() => $propertiesArray
    );
}

/**
 * Check props, stored in $data arg to match rules, given in $ruleA arg
 * and return array of *_Row objects, collected for props, that have 'key' rule
 *
 * @param $ruleA
 * @param $data
 * @return array
 */
function jcheck($ruleA, $data) {

    // Declare $rowA array
    $rowA = array();

    // Foreach prop having mismatch rules
    foreach ($ruleA as $props => $rule) foreach (ar($props) as $prop) {

        // Shortcut to $data[$prop]
        $value = $data[$prop];

        // If prop is required, but has empty/null/zero value - flush error
        if ($rule['req'] && !$value) jflush(false, sprintf(I_JCHECK_REQ, $prop));

        // If prop's value should match certain regular expression, but it does not - flush error
        if ($rule['rex'] && !Indi::rexm($rule['rex'], $value)) jflush(false, sprintf(I_JCHECK_REG, $value, $prop));

        // If prop's value should be an identifier of an existing object, but such object not found - flush error
        if ($value && $rule['key'] && !$rowA[$prop] = Indi::model($rule['key'])->fetchRow('`id` = "' . $value . '"'))
            jflush(false, sprintf(I_JCHECK_KEY, $rule['key'], $value));
    }

    // Return *_Row objects, collected for props, that have 'key' rule
    return $rowA;
}
