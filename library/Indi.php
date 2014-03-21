<?php
class Indi{

    /**
     * An internal static variable, will be used to store data, that should be accessible anywhere
     *
     * @var array
     */
    protected static $_registry = array();

    /**
     * An internal static variable, will be used to store data, got from `staticblock` table 
	 * as an assotiative array  and that should be accessible anywhere
     *
     * @var array|null
     */
    protected static $_blockA = null;

    /**
     * Compilation template
     *
     * @var string
     */
    public static $cmpTpl = '';

    /**
     * Compilation result/output
     *
     * @var string
     */
    public static $cmpOut = '';

    /**
     * Regular expressions patterns for common usage
     *
     * @var array
     */
    protected static $_rex = array(
        'email' => '/^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/',
        'date' => '/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$/',
        'hrgb' => '/^[0-9]{3}#([0-9a-fA-F]{6})$/'
    );

    /**
     * Array of HTML colors
     *
     * @var array
     */
    public static $colorNameA = array(
        'aliceblue'=>'F0F8FF',
        'antiquewhite'=>'FAEBD7',
        'aqua'=>'00FFFF',
        'aquamarine'=>'7FFFD4',
        'azure'=>'F0FFFF',
        'beige'=>'F5F5DC',
        'bisque'=>'FFE4C4',
        'black'=>'000000',
        'blanchedalmond '=>'FFEBCD',
        'blue'=>'0000FF',
        'blueviolet'=>'8A2BE2',
        'brown'=>'A52A2A',
        'burlywood'=>'DEB887',
        'cadetblue'=>'5F9EA0',
        'chartreuse'=>'7FFF00',
        'chocolate'=>'D2691E',
        'coral'=>'FF7F50',
        'cornflowerblue'=>'6495ED',
        'cornsilk'=>'FFF8DC',
        'crimson'=>'DC143C',
        'cyan'=>'00FFFF',
        'darkblue'=>'00008B',
        'darkcyan'=>'008B8B',
        'darkgoldenrod'=>'B8860B',
        'darkgray'=>'A9A9A9',
        'darkgreen'=>'006400',
        'darkgrey'=>'A9A9A9',
        'darkkhaki'=>'BDB76B',
        'darkmagenta'=>'8B008B',
        'darkolivegreen'=>'556B2F',
        'darkorange'=>'FF8C00',
        'darkorchid'=>'9932CC',
        'darkred'=>'8B0000',
        'darksalmon'=>'E9967A',
        'darkseagreen'=>'8FBC8F',
        'darkslateblue'=>'483D8B',
        'darkslategray'=>'2F4F4F',
        'darkslategrey'=>'2F4F4F',
        'darkturquoise'=>'00CED1',
        'darkviolet'=>'9400D3',
        'deeppink'=>'FF1493',
        'deepskyblue'=>'00BFFF',
        'dimgray'=>'696969',
        'dimgrey'=>'696969',
        'dodgerblue'=>'1E90FF',
        'firebrick'=>'B22222',
        'floralwhite'=>'FFFAF0',
        'forestgreen'=>'228B22',
        'fuchsia'=>'FF00FF',
        'gainsboro'=>'DCDCDC',
        'ghostwhite'=>'F8F8FF',
        'gold'=>'FFD700',
        'goldenrod'=>'DAA520',
        'gray'=>'808080',
        'green'=>'008000',
        'greenyellow'=>'ADFF2F',
        'grey'=>'808080',
        'honeydew'=>'F0FFF0',
        'hotpink'=>'FF69B4',
        'indianred'=>'CD5C5C',
        'indigo'=>'4B0082',
        'ivory'=>'FFFFF0',
        'khaki'=>'F0E68C',
        'lavender'=>'E6E6FA',
        'lavenderblush'=>'FFF0F5',
        'lawngreen'=>'7CFC00',
        'lemonchiffon'=>'FFFACD',
        'lightblue'=>'ADD8E6',
        'lightcoral'=>'F08080',
        'lightcyan'=>'E0FFFF',
        'lightgoldenrodyellow'=>'FAFAD2',
        'lightgray'=>'D3D3D3',
        'lightgreen'=>'90EE90',
        'lightgrey'=>'D3D3D3',
        'lightpink'=>'FFB6C1',
        'lightsalmon'=>'FFA07A',
        'lightseagreen'=>'20B2AA',
        'lightskyblue'=>'87CEFA',
        'lightslategray'=>'778899',
        'lightslategrey'=>'778899',
        'lightsteelblue'=>'B0C4DE',
        'lightyellow'=>'FFFFE0',
        'lime'=>'00FF00',
        'limegreen'=>'32CD32',
        'linen'=>'FAF0E6',
        'magenta'=>'FF00FF',
        'maroon'=>'800000',
        'mediumaquamarine'=>'66CDAA',
        'mediumblue'=>'0000CD',
        'mediumorchid'=>'BA55D3',
        'mediumpurple'=>'9370D0',
        'mediumseagreen'=>'3CB371',
        'mediumslateblue'=>'7B68EE',
        'mediumspringgreen'=>'00FA9A',
        'mediumturquoise'=>'48D1CC',
        'mediumvioletred'=>'C71585',
        'midnightblue'=>'191970',
        'mintcream'=>'F5FFFA',
        'mistyrose'=>'FFE4E1',
        'moccasin'=>'FFE4B5',
        'navajowhite'=>'FFDEAD',
        'navy'=>'000080',
        'oldlace'=>'FDF5E6',
        'olive'=>'808000',
        'olivedrab'=>'6B8E23',
        'orange'=>'FFA500',
        'orangered'=>'FF4500',
        'orchid'=>'DA70D6',
        'palegoldenrod'=>'EEE8AA',
        'palegreen'=>'98FB98',
        'paleturquoise'=>'AFEEEE',
        'palevioletred'=>'DB7093',
        'papayawhip'=>'FFEFD5',
        'peachpuff'=>'FFDAB9',
        'peru'=>'CD853F',
        'pink'=>'FFC0CB',
        'plum'=>'DDA0DD',
        'powderblue'=>'B0E0E6',
        'purple'=>'800080',
        'red'=>'FF0000',
        'rosybrown'=>'BC8F8F',
        'royalblue'=>'4169E1',
        'saddlebrown'=>'8B4513',
        'salmon'=>'FA8072',
        'sandybrown'=>'F4A460',
        'seagreen'=>'2E8B57',
        'seashell'=>'FFF5EE',
        'sienna'=>'A0522D',
        'silver'=>'C0C0C0',
        'skyblue'=>'87CEEB',
        'slateblue'=>'6A5ACD',
        'slategray'=>'708090',
        'slategrey'=>'708090',
        'snow'=>'FFFAFA',
        'springgreen'=>'00FF7F',
        'steelblue'=>'4682B4',
        'tan'=>'D2B48C',
        'teal'=>'008080',
        'thistle'=>'D8BFD8',
        'tomato'=>'FF6347',
        'turquoise'=>'40E0D0',
        'violet'=>'EE82EE',
        'wheat'=>'F5DEB3',
        'white'=>'FFFFFF',
        'whitesmoke'=>'F5F5F5',
        'yellow'=>'FFFF00',
        'yellowgreen'=>'9ACD32'
    );

    /**
     * Compilation function source code, that will be passed to eval() function. Usage:
     * // 1. Setup a template for compiling
     * Indi::$cmpTpl = 'Hello <?=$user->firstName?>';
     * // 2. Call eval() within a scope, where $user object was defined. After eval() is finished, Indi::$cmpTpl is set to ''
     * eval(Indi::$cmpRun);
     * // 3. Get a compilation result
     * $compilationResult = Indi::$cmpOut;
     *
     * @var string
     */
    public static $cmpRun = '
        if (preg_match(\'/<\?|\?>/\', Indi::$cmpTpl)) {
            $iterator = \'i\' . md5(microtime());
            $php = preg_split(\'/(<\?|\?>)/\', Indi::$cmpTpl, -1, PREG_SPLIT_DELIM_CAPTURE);
            Indi::$cmpOut = \'\';
            for ($$iterator = 0; $$iterator < count($php); $$iterator++) {
                if ($php[$$iterator] == \'<?\') {
                    $php[$$iterator+1] = preg_replace(\'/^=/\', \' echo \', $php[$$iterator+1]) . \';\';
                    ob_start(); eval($php[$$iterator+1]); Indi::$cmpOut .= ob_get_clean();
                    $$iterator += 2;
                } else {
                    Indi::$cmpOut .= $php[$$iterator];
                }
            }
        } else if (preg_match(\'/(\$|::)/\', Indi::$cmpTpl)) {
            if (preg_match(\'/^\\\'/\', trim(Indi::$cmpTpl))) {
                Indi::$cmpTpl = ltrim(Indi::$cmpTpl, "\' ");
                if (preg_match(\'/\\\'$/\', trim(Indi::$cmpTpl)))
                    Indi::$cmpTpl = rtrim(Indi::$cmpTpl, "\' ");
                eval(\'Indi::$cmpOut = \\\'\' . Indi::$cmpTpl . \'\\\';\');
            } else {
                eval(\'Indi::$cmpOut = \\\'\' . Indi::$cmpTpl . \'\\\';\');
            }
        } else {
            Indi::$cmpOut = Indi::$cmpTpl;
        }
        Indi::$cmpTpl = \'\';
        ';

    /**
     * Compiles a given template. This function should be called only in case if there is no context variables mentioned
     * in template, because otherwise there will be a fatal error with messages like 'Using $this when not in object
     * context' or 'Call to a member function somefunc() on a non-object'
     *
     * @static
     * @param $tpl
     * @return string
     */
    public static function cmp($tpl){
        $out = '';
        if (preg_match('/<\?|\?>/', $tpl)) {
            $php = preg_split('/(<\?|\?>)/', $tpl, -1, PREG_SPLIT_DELIM_CAPTURE);
            for ($i = 0; $i < count($php); $i++) {
                if ($php[$i] == '<?') {
                    $php[$i+1] = preg_replace('/^=/', ' echo ', $php[$i+1]) . ';';
                    ob_start(); eval($php[$i+1]); $out .= ob_get_clean();
                    $i += 2;
                } else {
                    $out .= $php[$i];
                }
            }
        } else if (preg_match('/(\$|::)/', $tpl)) {
            eval('$out = \'' . $tpl . '\';');
        } else {
            $out = $tpl;
        }

        return $out;
    }

    /**
     * Function is similar as jQuery .attr() function.
     * If only $key param is passed, the assigned value will be returned.
     * Otherwise, if $value param is also passed, this value will be placed in self::$_registry under $key key
     *
     * @static
     * @param $key
     * @param $value
     * @return mixed
     */
    public static function registry($key = null, $value = null) {
        // If only $key param passed, the assigned registry value will be returned
        if (func_num_args() == 1) return self::$_registry[$key];

        // Else if $value argument was given - it will be placed into registry under passed $key param.
        // If $value argument is an array, it will be converted to a new instance of ArrayObject class,
        // with setting ArrayObject::ARRAY_AS_PROPS flag for that newly created instance properties
        // to be also accessible as if they were an array elements
        else if (func_num_args() == 2)
            return self::$_registry[$key] = is_array($value)
                ? new ArrayObject($value, ArrayObject::ARRAY_AS_PROPS)
                : $value;

        // Else if no arguments passed, return the whole registry
        else if (func_num_args() == 0) return self::$_registry;
    }

    /**
     * Shortcut for Indi_Db::model() function
     * Loads the model by model's entity's id, or model class name
     *
     * @static
     * @param int|string $identifier
     * @return Indi_Db_Table object
     */
    public static function model($identifier) {

        // Call same method within Indi_Db object
        return Indi_Db::model($identifier);
    }

    /**
     * Shortcut for Indi_Db::factory() function
     * Returns an singleton instance of Indi_Db
     * If an argument is presented, it will be passed to Indi_Db::factory() method, for, in it's turn,
     * usage as PDO connection properties
     *
     * @static
     * @return Indi_Db object
     */
    public static function db() {

        // Call 'factory' method of Indi_Db class, with first argument, if given. Otherwise just Indi_Db instance
        // will be returned, with no PDO configuration setup
        return Indi_Db::factory(func_num_args() ? func_get_arg(0) : null);
    }

    /**
     * Set or get values of all uri params or single param. If there is no value for 'uri' key in registry yet, setup it
     *
     * @static
     * @param null $key
     * @param null $value
     * @return mixed|null
     */
    public static function uri($key = null, $value = null){

        // If there is no value for 'uri' key in registry yet, we setup it
        if (is_null(Indi::store('uri'))) {

            // If project located in some subfolder of $_SERVER['DOCUMENT_ROOT'] instead of directly in it
            // we strip mention of that subfolder from $_SERVER['REQUEST_URI']
            if (STD) $_SERVER['REQUEST_URI'] = preg_replace('!^' . STD . '!', '', $_SERVER['REQUEST_URI']);

            // If 'cms-only' mode is turned on, we prepend $_SERVER['REQUEST_URI'] with '/admin'
            if (COM) $_SERVER['REQUEST_URI'] = '/admin' . $_SERVER['REQUEST_URI'];

            // Build the full url by prepending protocol and hostname, and parse it by parse_url() function
            $uri = parse_url('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);

            // Trim '/' from 'path' item, got by parse_url() usage, and explode it by '/'
            $uri = explode('/', trim($uri['path'], '/'));

            // Set default params
            $params['module'] = 'front';
            $params['section'] = 'index';
            $params['action'] = 'index';

            // If first chunk of $uri is 'admin', we set 'module' param as 'admin' and drop that chunk from $uri
            if ($uri[0] == 'admin') {
                $params['module'] = $uri[0];
                array_shift($uri);
            }

            // Setup all other params
            for ($i = 0; $i < count($uri); $i++) {
                if ($i == 0 && $uri[$i]) {
                    $params['section'] = $uri[$i];
                } else if ($i == 1) {
                    $params['action'] = $uri[$i];
                } else if (count($uri) > $i) {
                    if ($uri[$i]) {
                        $params[$uri[$i]] = $uri[$i + 1];
                        $i++;
                    }
                }
            }

            // Push $key array in registry under 'uri' key
            Indi::store('uri', $params);
        }

        // If $param argument is null or not given, return value, stored under 'uri' key in registry
        if (is_null($key)) return Indi::store('uri');

        // Else if $param argument is not null, we assume it is a key within data, stored under 'uri' key in registry
        // and return value of $key key
        if (func_num_args() == 1)
            if (is_array($key) || is_object($key)) {
                return Indi::store('uri', $key);
            } else {
                return Indi::registry('uri')->$key;
            }

        // Else if $value argument is given, we assign it to $key key within data, stored under 'uri' key in registry
        if (func_num_args() == 2) return Indi::registry('uri')->$key = $value;
    }


    /**
     * Implode and compress files, mentioned in $files argument, under filename, constructed with usage of $alias
     * argument. This function is a part of performance improvement policy, which consists of:
     * 1. Imploding and gz-compressing all 'js' files in one, and all 'css' files in one, under filenames, ending with
     *    '.gz.css' and 'gz.css'
     * 2. Adding a .htaccess directive for additional header 'Content-Encoding: gzip' for such files
     *
     * This policy is modified version of static content compression idea, and the difference is that 'gz.css' and
     * 'gz.js' files are ALREADY gzipped, so we-server is not forced to compress them each time it receceives a request,
     * so it just flush it as is, but with special 'Content-Encoding' header, for them to be decompressed at client-side
     *
     * @param array $files
     * @param string $alias
     * @return int
     */
    public function implode($files = array(), $alias = '') {

        // Get the type of files, here we assume that all files in $files argument have same type
        preg_match('/\.(css|js)$/', $files[0], $ext); $ext = $ext[1];

        // Get the subdir name, relative to webroot
        $rel = '/' . $ext;

        // We set $refresh as false by default
        $refresh = false;

        // Get filename of file, containing modification times for all files that are compiled
        $mtime = DOC . STD . '/core/' . $rel . '/admin/indi.all' . ($alias ? '.' . $alias : '') . '.mtime';

        // If this file does not exists, we set $refresh as true
        if (!file_exists($mtime)) {
            $refresh = true;

        // Else
        } else {

            // Get 'mtime' file contents and convert is to json
            $json = json_decode(file_get_contents($mtime), true);

            // If $json is not an array, or is empty array, of files, mentioned in it do not match files in $files arg
            if (!is_array($json) || !count($json) || count(array_diff($files, array_keys($json)))
                || count(array_diff(array_keys($json), $files)))

                // We set $refresh as true
                $refresh = true;

            // Else we do a final check:
            else

                // If modification time  of at least one file in $files argument, is not equal to time,
                // stored in $json for that file, we set $refresh as true
                for ($i = 0; $i < count($files); $i++)
                    if (filemtime(DOC . STD . '/core' . $files[$i]) != $json[$files[$i]]) {
                        $refresh = true;
                        break;
                    }
        }

        // If after all these checks we discovered that compilation should be refreshed
        if ($refresh) {

            // Empty $json array
            $json = array();

            // Start output buffering
            ob_start();

            // Foreach file in $files argument
            for ($i = 0; $i < count($files); $i++) {

                // Get full file name
                $file = DOC . STD . '/core' . $files[$i];

                // Collect info about that file's modification time
                $json[$files[$i]] = filemtime($file);

                // Echo that file contents
                readfile($file);

                // Echo ';' if we deal with javascript files. Also flush double newline
                echo ($ext == 'js' ? ';' : '') . "\n\n";
            }

            // Refresh 'mtime' file for current compilation
            $fp = fopen($mtime, 'w'); fwrite($fp, json_encode($json)); fclose($fp);

            // Get output
            $txt = ob_get_clean();

            // If we currently deal with 'css' files
            if ($ext == 'css') {

                // Replace extjs relative paths to paths, relative to web root, instead of relative to folder, where
                // extjs is located
                $txt = preg_replace('!url\(\'\.\./\.\./resources!', 'url(\'/library/extjs4/resources', $txt);

                // Remove comments from css
                $txt = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $txt);

                // Remove tabs, excessive spaces and newlines
                $txt = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '   '), '', $txt);
            }

            // Compress compilation
            $txt = gzencode($txt, 9);

            // Refresh compilation file
            $fp = fopen(DOC . STD . '/core' . $rel . '/admin/indi.all' . ($alias ? '.' . $alias : '') . '.gz.' . $ext, 'w');
            fwrite($fp, $txt);
            fclose($fp);
        }

        // Return modification time for 'mtime' file
        return filemtime($mtime);
    }

    /**
     * This function does similar as Indi::registry() function, but is additionally able to set/get subkeys values for
     * data, stored in registry, if that data is of types 'array' or 'object'. Function was created to avoid almost
     * same coding for Indi::get(), Indi::post() and Indi::files() functions, so now these function use this function
     * instead of consisting of almost same code.
     *
     * @static
     * @param null $key
     * @param null $arg1
     * @param null $arg2
     * @return mixed|null
     */
    public static function store($key = null, $arg1 = null, $arg2 = null) {

        // If no $key argument was given - return whole registry
        if (is_null($key)) return Indi::registry();

        // If $key argument is not null and $arg1 argument is null - we get the value, stored in registry
        // under $key key, and return it
        if (is_null($arg1)) return Indi::registry($key);

        // Else if only $key and $arg1 arguments is passed, and they both are not null
        if (func_num_args() == 2)

            // We check is $arg1 an array or an object, and if so
            if (is_array($arg1) || is_object($arg1)) {

                // Set a value ($arg1) for $key key in registry, because the fact that $arg1 is array/object mean that
                // it is not a key, as arrays and objects are not allowed to be used as array keys or object properties
                return Indi::registry($key, $arg1);

            // Else if $arg1 argument is not an array or object, we assume that it is a subkey, so we return it's value
            } else return Indi::store($key)->$arg1;

        // Else if three arguments passed, we assume that they are key, subkey and value, so we set a value, got from
        // third argument under a subkey (second argument), under a $key key in registry and after that return that value
        else if (func_num_args() == 3) return Indi::store($key)->$arg1 = $arg2;
    }

    /**
     * Set or gets $_GET params as single param or as whole array, converted to instance of ArrayObject class.
     * Usage:
     * 1.Indi::get();               //   ArrayObject (
     *                              //       [param1] => value1
     *                              //       [param2] => value2
     *                              //   )
     * 2.Indi::get()->param1        //   value1
     * 3.Indi::get()->param1 = 1234 //   1234
     * 4.Indi::get()->param1        //   1234
     * 5.Indi::get('param1')        //   1234
     * 6.Indi::get('param1', 12345) //   12345
     * 7.Indi::get('param1')        //   12345
     * 8.$myGetCopy = Indi::get();  //   ArrayObject (
     *                              //       [param1] => 12345
     *                              //       [param2] => value2
     *                              //   )
     * 9.$myGetCopy['param1']       //   12345
     * 10. $myGetCopy->param1       //   12345
     *
     * For initial (and further, if need) setting, use Indi::get($_GET)
     *
     * @static
     * @param null $arg1
     * @param null $arg2
     * @return mixed
     */
    public static function get($arg1 = null, $arg2 = null) {
        return func_num_args() == 1 ? Indi::store('get', $arg1) : Indi::store('get', $arg1, $arg2);
    }

    /**
     * Set or gets $_POST params as single param or as whole array, converted to instance of ArrayObject class.
     * Usage - same as for Indi::get() function
     *
     * @static
     * @param null $arg1
     * @param null $arg2
     * @return mixed
     */
    public static function post($arg1 = null, $arg2 = null) {
        return func_num_args() == 1 ? Indi::store('post', $arg1) : Indi::store('post', $arg1, $arg2);
    }

    /**
     * Set or gets $_FILES params as single param or as whole array, converted to instance of ArrayObject class.
     * Usage - same as for Indi::get() function
     *
     * @static
     * @param null $arg1
     * @param null $arg2
     * @return mixed
     */
    public static function files($arg1 = null, $arg2 = null) {
        return func_num_args() == 1 ? Indi::store('files', $arg1) : Indi::store('files', $arg1, $arg2);
    }

    /**
     * Setup a proper order of elements in $setA array, depending on their titles
     *
     * @static
     * @param $entityId
     * @param $idA
     * @param string $dir
     * @return array
     */
    public static function order($entityId, $idA, $dir = 'ASC'){
        // Load the model
        $model = Indi::model($entityId);

        // Get the columns list
        $columnA = $model->fields(null, 'cols');

        // Determine title column name
        if ($titleColumn = current(array_intersect($columnA, array('title', '_title')))) {

            // Setup a new order for $idA
            $idA = Indi::db()->query('

                SELECT `id`
                FROM `' . $model->name() . '`
                WHERE `id` IN (' . implode(',', $idA) . ')
                ORDER BY `' . $titleColumn . '` ' . $dir . '

            ')->fetchAll(PDO::FETCH_COLUMN);
        }

        // Return reordered ids
        return $idA;
    }
	
    /**
     * Return an array containing defined constants
     *
     * @static
     * @param string $category
     * @param boolean $json
     * @return array|json
     */
	public static function constants($category = 'user', $json = false) {
		$constants = get_defined_constants(true);
		$constants = $category ? $constants[$category] : $constants;
		return $json ? json_encode($constants) : $constants;
	}

    /**
     * Converts an html color name to a hex color value
     *
     * @static
     * @param $color
     * @return string
     */
    public static function hexColor($color) {

        // Remove the spaces, and leading '#', if presented
        $color = ltrim(trim($color), '#');

        // If $color is a hex color in format 'rrggbb', we return it as is
        if (preg_match('/^([a-fA-F0-9]{6})$/', $color, $match)) {
            return $match[1];

        // Else if $color is a hex color, but in format 'rgb' we convert it to 'rrggbb' format
        } else if (preg_match('/^([a-fA-F0-9])([a-fA-F0-9])([a-fA-F0-9])$/', $color, $match)) {
            $hex = ''; for ($i = 1; $i < 4; $i++) $hex .= $match[$i] . $match[$i]; return $hex;

        // Else we'll try to find a match within self::$colorNameA array, containing 147 standard HTML color names
        } else {

            // Convert color name to lowercase
            $color = strtolower($color);

            // If found, return it, with '#' prefix, else return empty string
            return ($hex = self::$colorNameA[$color]) ? '#' . $hex : '';
        }
    }

	/**
	 * Fetch rowset from `staticblock` table and return it as an assotiative array with aliases as keys.
	 * Rows in `staticblock` table store some text phrases and settings, so function provide and ability to
	 * access it from anywhere. Rowset fetch will be only done at first function call.
	 *
     * @param string $key
     * @param string $default A value, that will be returned if $key will not be found in self::$_blockA array
	 * @return array
	 */
	public static function blocks($key = null, $default = null){
		// If self::$_blockA is null at the moment, we fetch it from `staticblock` table
		if (self::$_blockA === null) {

			// Setup self::$_blockA as an empty array at first
			self::$_blockA = array();

			// Fetch rowset
            $staticBlockRs = Indi::model('Staticblock')->fetchAll('`toggle` = "y"');
			
			// Setup values in self::$_blockA array under certain keys
            foreach ($staticBlockRs as $staticBlockR) {
                self::$_blockA[$staticBlockR->alias] = $staticBlockR->{'details' . ucfirst($staticBlockR->type)};
                if ($staticBlockR->type == 'textarea') self::$_blockA[$staticBlockR->alias] = nl2br(self::$_blockA[$staticBlockR->alias]);
            }
		}

		// If $key argument was specified, we return a certain value, or all array otherwise
		return $key == null ? self::$_blockA : (array_key_exists($key, self::$_blockA) ? self::$_blockA[$key] : $default);
	}

    /**
     * Parses the given ini file, and create a stdClass object with recognized properties
     *
     * @static
     * @param $file
     * @return stdClass
     */

    /**
     * Parses ini file given by $arg argument, convert it from array to ArrayObject and save into the registry
     * If $arg agrument does not end with '.ini', it will be interpreted as a key, so it's value will be returned
     * If $arg argument is not given or null, the whole ini ArrayObject object, that represents ini file contents
     * will be returned
     *
     * @static
     * @param null $arg
     * @return mixed|null
     */
    public static function ini($arg = null) {

        // If $arg argument is a path end with '.ini', and file with that path exists
        if (preg_match('/\.ini$/', $arg) && is_file($arg)) {

            // Parse ini file
            $ini = parse_ini_file($arg, true);

            // Convert sections from type 'array' to type 'ArrayObject'
            foreach ($ini as $sectionName => $sectionParamA)
                $ini[$sectionName] = new ArrayObject($sectionParamA, ArrayObject::ARRAY_AS_PROPS);

            // Save into the registry
            return Indi::registry('ini', $ini);
        }

        // Else if $arg argument is a string, we assume that it is a key, so we return it's value
        else if (is_string($arg)) return Indi::store('ini')->$arg;

        // Else we return the whole ini object
        else if (!$arg) return Indi::store('ini');
    }

    /**
     * Return regular expressions pattern, stored within $this->_rex property under $alias key
     *
     * @param $alias
     * @return null
     */
    public static function rex($alias){
        return $alias ? self::$_rex[$alias] : null;
    }

    public static function trail($arg = null) {

        // If $arg argument is an array, we assume that it's a route stack, so we create a new trail object and store
        // it into the registry
        if (is_array($arg)) {
            $class = 'Indi_Trail_' . ucfirst(Indi::uri()->module);
            return Indi::registry('trail', new $class($arg));
        }

        // Else if $arg argument is boolean 'true', we return the whole trail object
        else if ($arg === true) return Indi::registry('trail');

        // Else if $arg argument is not set, we return current trail item object
        else if ($arg == null) return Indi::registry('trail')->item();

        // Else if $arg argument is integer, we return item, that is at index, shifted from the last index by $arg number
        else if (is_int($arg)) return Indi::registry('trail')->item($arg);
    }
}