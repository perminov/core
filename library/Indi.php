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
    public static function registry($key, $value = null) {
        // If only $key param passed, the assigned registry value will be returned
        if (func_num_args() == 1) {
            return self::$_registry[$key];

        // Else a given value - agrument#2 - will be placed into registry under passed $key param
        } else if (func_num_args() == 2) {
            self::$_registry[$key] = $value;
        }
        return null;
    }

    /**
     * Loads the model by model's entity's id, or model class name
     *
     * @static
     * @param int|string $identifier
     * @return Indi_Db_Table object
     */
    public static function model($identifier) {
        if (preg_match('/^[0-9]+$/', $identifier)) {
            return Entity::getInstance()->getModelById($identifier);
        } else {
            return Misc::loadModel($identifier);
        }
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
        $columnA = $model->info('cols');

        // Determine title column name
        if ($titleColumn = current(array_intersect($columnA, array('title', '_title')))) {

            // Setup a new order for $idA
            $idA = Indi_Db_Table::getDefaultAdapter()->query('

                SELECT `id`
                FROM `' . $model->info('name') . '`
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
}