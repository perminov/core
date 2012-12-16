<?php
class Indi_Cache {
	public function update($modelName){
		$rs = Misc::loadModel($modelName)->fetchAll()->toArray();
		$fields = array_keys($rs[0]);
		foreach ($fields as $field) {
			foreach ($rs as $r) {
				$data[$field][] = $r[$field];
			}
		}
		ob_start();
		print_r($data);
		$php = ob_get_clean();
		if (!function_exists('dump2phpCallback')) {
			function dump2phpCallback($m){
				if (!is_numeric($m[1])) $m[1] = "'" . $m[1] . "'=>"; else $m[1] = '';
				if (!is_numeric($m[2]) && $m[2] != 'Array') $m[2] = "'" . preg_replace("/'/", "\'", $m[2]) . "'";
				return $m[1] . $m[2] . ',';
			}
		}
		$php = preg_replace_callback('/\[([^\]]+)\] => ([^\n]*)\n\s*/', 'dump2phpCallback', $php);
		$php = preg_replace('/(Array),\s*(\()\n\s*/', '$1$2', $php);
		$php = preg_replace('/,\)\n/', '),', $php);
		$php = preg_replace('/\),\s*\)$/', ")\n);", $php);
		$php = preg_replace('/[0-9]+=>Array\(\'id\'=>([0-9]+),/', "$1=>Array('id'=>$1,", $php);
		$php = '<?php $GLOBALS["cache"]["' . $modelName . '"] = ' . $php;
		$fp = fopen(self::fname($modelName), 'w');
		fwrite($fp, $php);
		fclose($fp);
//		d($php);
	}

	function fname($modelName) {
		return $_SERVER['DOCUMENT_ROOT'] . '/www/application/cache/' . $modelName . '.php';
	}

	function compare($modelName) {
		mt();
		$rs = Misc::loadModel($modelName)->fetchAll()->toArray();
		d('Получение списка ' . $modelName . ' из базы: ' . mt());
		include(self::fname($modelName));
		d('Получение списка ' . $modelName . ' из вертикального кэша: ' . mt());

		$ids = array_keys($GLOBALS['cache'][$modelName]);
		mt();

		foreach ($ids as $id) {
			$r = Misc::loadModel($modelName)->fetchRow('`id` = "' . $id . '"')->toArray();
		}
		d(count($ids) . ' операций поиска по идентификаторам в ' . $modelName . ' в базе: ' . mt());

		foreach ($ids as $id) {
			$r = $cache[$modelName][$id];
		}
		d(count($ids) . ' операций поиска по идентификаторам в ' . $modelName . ' в кэше: ' . mt());
	}

	public function fetcher($params) {
		return new Indi_Cache_Fetcher($params);
	}

	public function load() {
		require_once(self::fname('tables'));
		foreach ($GLOBALS['cache']['tables'] as $table) {
			$mname = ucfirst($table);
			$fname = Indi_Cache::fname($mname);
			if (file_exists($fname)) require_once($fname);
		}
	}
}
