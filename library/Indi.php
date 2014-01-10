<?php
class Indi{

    /**
     * An internal static variable, will be used to store data, that should be accessible anywhere
     *
     * @var array
     */
    protected static $_registry = array();

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
}