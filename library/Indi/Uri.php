<?php
class Indi_Uri {
	public function dispatch(){
		$this->preDispatch();

		$uri = parse_url('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
		$uri = explode('/', trim($uri['path'], '/'));

		$params['module'] = 'front';
		$params['section'] = 'index';
		$params['action'] = 'index';

		if ($uri[0] == 'admin') {
			$params['module'] = 'admin';
			array_shift($uri);
		}

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

		if ($params['module'] != 'admin') {
			$fsectionM = Misc::loadModel('Fsection');
			$fsectionA = $fsectionM->fetchAll('`alias` IN ("' . $params['section'] . '", "static")', 'FIND_IN_SET(`alias`, "' . $params['section'] . ',static")')->toArray();
			if ($fsectionA[0]['alias'] == 'static') {
				$staticA = Misc::loadModel('Staticpage')->fetchAll('`alias` IN ("' . $params['section'] . '", "404") AND `toggle` = "y"')->toArray();
				$params['section'] = 'static';
				$params['action'] = 'details';
				$params['id'] = $staticA[0]['id'];
			}
		}

		$controllerClassName = ($params['module'] == 'front' ? '' : $params['module'] . '_') . ucfirst($params['section']) . 'Controller';
		if (!class_exists($controllerClassName)) {
			eval('class ' . ucfirst($controllerClassName) . ' extends Indi_Controller_' . ($params['module'] == 'front' ? 'Front' : 'Admin') . '{}');
		}

		$controller = new $controllerClassName($params);
		$controller->dispatch();
	}

	public function preDispatch() {
		$this->no3w();
		$this->setCookieDomain();
		$this->startSession();
		$this->checkSeoUrlsMode();
	}

	public function no3w(){
		if (strpos($_SERVER['HTTP_HOST'], 'www') !== false) die(header('Location: http://' . str_replace('www.', '', $_SERVER['HTTP_HOST']) . $_SERVER['REQUEST_URI']));
	}

	public function setCookieDomain(){
		$config = Indi_Registry::get('config');
		$domain = $config['general']->domain;
		if (strpos($domain, '.') !== false) ini_set('session.cookie_domain', '.' . $domain);
	}

	public function startSession() {
		$post = Indi_Registry::get('post');
		if (isset($post['sessid'])) session_id($post['sessid']);
		Indi_Session::start();
	}

	public function checkSeoUrlsMode(){
		list($first) = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
		if ($first != 'admin') {
			$this->setDbCacheUsageIfNeed();
			$this->adjustUriIfSubdomain();
			$GLOBALS['enableSeoUrls'] = current(Indi_Db_Table::getDefaultAdapter()->query('SELECT `value` FROM `fconfig` WHERE `alias` = "enableSeoUrls"')->fetch());
			if ($GLOBALS['enableSeoUrls'] == 'true') $_SERVER['REQUEST_URI'] = $this->seo2sys($_SERVER['REQUEST_URI']);
		}
	}

	public function adjustUriIfSubdomain(){
		$config = Indi_Registry::get('config');
		$db = Indi_Db_Table::getDefaultAdapter();
		$subdomains = $db->query('SELECT `fs`.`alias` FROM `subdomain` `sd`, `fsection` `fs` WHERE `sd`.`fsectionId` = `fs`.`id`')->fetchAll();
		foreach ($subdomains as $sd) $subdomainsArray[] = $sd['alias'];
		if ($_SERVER['HTTP_HOST'] != $config['general']->domain) {
			$subdomain = str_replace('.'.$config['general']->domain, '',$_SERVER['HTTP_HOST']);
			if (in_array($subdomain, $subdomainsArray)) {
				$_SERVER['REQUEST_URI'] = '/' . $subdomain . $_SERVER['REQUEST_URI'];
			}
		}
		Indi_Registry::set('subdomains', $subdomainsArray);
		Indi_Registry::set('subdomain', $subdomain);
	}

	public function setDbCacheUsageIfNeed(){
		$useCache = Indi_Db_Table::getDefaultAdapter()->query('SELECT `value` FROM `fconfig` WHERE `alias` = "useCache"')->fetch();
		Indi_Db::$useCache = !(!is_array($useCache) || current($useCache) != 'true');
		if (Indi_Db::$useCache) Indi_Cache::load();
	}

	public function seo2sys($seo){
		$db = Indi_Db_Table::getDefaultAdapter();
		$url = parse_url($seo);
		$aim = explode('/', trim($url['path'], '/'));
		if ($aim[count($aim)-1] == 'noseo') return $seo;
		if (count($aim) > 1) {
			$sql = '
			SELECT
			  `sa`.`id`
			FROM
			  `fsection` `s`,
			  `faction` `a`,
			  `fsection2faction` `sa`
			WHERE 1
			  AND `sa`.`fsectionId` = `s`.`id`
			  AND `s`.`alias` = "' . $aim[0] . '"
			  AND `sa`.`factionId` = `a`.`id`
			  AND IF(`sa`.`rename`,`sa`.`alias` = "' . $aim[1] . '", `a`.`alias` = "' . $aim[1] . '")';

			$r = $db->query($sql)->fetchAll();
			$saId = $r[0]['id'];
			$sql = '
			SELECT
			  `u`.*,
			  `sa`.`blink`,
			  `sa`.`rename`,
			  `sa`.`alias` AS `renameBy`,
			  `a`.`alias` AS `originalAlias`
			FROM
			  `url` `u`,
			  `fsection` `s`,
			  `faction` `a`,
			  `fsection2faction` `sa`
			WHERE 1
			  AND `u`.`fsection2factionId` = `sa`.`id`
			  AND `sa`.`fsectionId` = `s`.`id`
			  AND `s`.`alias` = "' . $aim[0] . '"
			  AND `sa`.`factionId` = `a`.`id`
			  AND ' . ($saId ? '`sa`.`id` = "' . $saId . '"' : 'IF(`sa`.`rename`,`sa`.`alias` = "' . $aim[1] . '", `a`.`alias` = "' . $aim[1] . '")') . '
			ORDER BY `u`.`move`';
			$parts = $db->query($sql)->fetchAll();
			if (count($parts) == 0 && !$saId) {
				$sql = '
				SELECT
				  `u`.*,
				  `sa`.`blink`,
				  `a`.`alias`
				FROM
				  `url` `u`,
				  `fsection` `s`,
				  `faction` `a`,
				  `fsection2faction` `sa`
				WHERE 1
				  AND `u`.`fsection2factionId` = `sa`.`id`
				  AND `sa`.`fsectionId` = `s`.`id`
				  AND `s`.`alias` = "' . $aim[0] . '"
				  AND `sa`.`factionId` = `a`.`id`
				  AND `sa`.`blink` = "1"
				ORDER BY `u`.`move`';
				$parts = $db->query($sql)->fetchAll();
			}
			if (count($parts) == 0) {
				$sys = $seo;
			} else {
				$models = array();
				$sys = array($aim[0]);
				$sys[] = $parts[0]['alias'] ? $parts[0]['alias'] : ($parts[0]['rename'] ? $parts[0]['originalAlias'] : $aim[1]);
				$alias = $parts[0]['alias'] ? $aim[1] : $aim[2];
				for ($i = 0; $i < count($parts); $i++) {
					if (!in_array($parts[$i]['entityId'], array_keys($models))) $models[$parts[$i]['entityId']] = Entity::getInstance()->getModelById($parts[$i]['entityId']);
				}
				$where = '';
				$lastId = 0;
				$shift = 0;
				for ($i = 0; $i < count($parts); $i++) {
					if (isset($aim[$i - 1 + ($parts[0]['alias'] ? 2 : 3) - $shift]) && $component = $models[$parts[$i]['entityId']]->fetchRow('`alias` = "' . $alias . '"' . $where)) {
//					echo '`alias` = "' . $alias . '"' . $where . '<br>' . "\n";
						$lastId = $component->id;

						if ($i == ($parts[0]['alias'] && !$parts[0]['blink'] ? count($parts) : count($parts) - 1)) {
//					if ($i == ($parts[0]['alias'] ? count($parts) : count($parts) - 1)) {
							$sys[] = $parts[$i]['prefix'] . '/' . $component->id;
							break;
						} else if ($i > 0){
							$where = ' AND `' . $models[$parts[$i]['entityId']]->info('name') . 'Id` = ' . $component->id;
							$alias = $aim[$i+($parts[0]['alias'] ? 2 : 3) - $shift];
						}
						$where = ' AND `' . $models[$parts[$i]['entityId']]->info('name') . 'Id` = ' . $component->id;
						$alias = $aim[$i+($parts[0]['alias'] ? 2 : 3) - $shift];
					} else if ($component = $models[$parts[$i]['entityId']]->fetchRow('`alias` = ""' . $where)) {
						$where = ' AND `' . $models[$parts[$i]['entityId']]->info('name') . 'Id` = ' . $component->id;
						$shift++;
						$alias = $aim[$i+($parts[0]['alias'] ? 2 : 3) - $shift];
					} else if (!$alias) {
						$sys[] = $parts[$i-1]['prefix'] . '/' . $lastId;
						break;
					}

				}
				for ($i = ($parts[0]['alias'] ? 0 : 1)+ 1 + count($parts); $i < count($aim); $i++) $sys[] = $aim[$i];
				$sys = '/' . implode('/', $sys) . '/';
				if ($url['query']) $sys .= '?' . $url['query'];
				if ($url['fragment']) $sys .= '#' . $url['fragment'];
			}
		} else {
			$sys = $seo;
		}
		return $sys;
	}

	public static function sys2seo($sys, $cr = false){
		preg_match_all('/(href|url)="([0-9a-z\/#]+)"/', $sys, $matches);
		$uri = $matches[2];
		$db = Indi_Db_Table::getDefaultAdapter();
		$furi = array();
		for ($i = 0; $i < count($uri); $i++) if (count(explode('/', trim($uri[$i], '/'))) > 1) $furi[] = $uri[$i]; $uri = $furi; $furi = array();

		$groups = array();
		for ($i = 0; $i < count($uri); $i++) {
			list($empty, $section, $action, $prefix) = explode('/', $uri[$i]);
			$group = '/' . $section . '/' . $action . '/';
			if (!in_array($group, $groups)) $groups[] = $group;
		}

		$sql = '
			SELECT
			  CONCAT("/", `s`.`alias`, "/", `a`.`alias`, "/") AS `concat`,
			  `u`.*,
			  `sa`.`blink`,
			  `sa`.`rename`,
			  `sa`.`alias`
			FROM
			  `url` `u`,
			  `fsection` `s`,
			  `faction` `a`,
			  `fsection2faction` `sa`
			WHERE 1
			  AND `u`.`fsection2factionId` = `sa`.`id`
			  AND `sa`.`fsectionId` = `s`.`id`
			  AND CONCAT("/", `s`.`alias`, "/", `a`.`alias`, "/") IN ("' . implode('","', $groups) . '")
			  AND `sa`.`factionId` = `a`.`id`
			ORDER BY `s`.`id`,`a`.`id`,`u`.`move`
			';
		$rs = $db->query($sql)->fetchAll();
		$found = array();
		$r = array();
		for ($i = 0; $i < count($rs); $i++) {
			if (!in_array($rs[$i]['concat'], $found)) $found[] = $rs[$i]['concat'];
			$r[$rs[$i]['concat']][] = $rs[$i];
		}
		for ($i = 0; $i < count($uri); $i++) {
			list($empty, $section, $action, $prefix) = explode('/', $uri[$i]);
			$group = '/' . $section . '/' . $action . '/';
			if (in_array($group, $found)) $furi[] = $uri[$i];
		}
		$uri = $furi; $furi = array();
		$groupped = array();
		for ($i = 0; $i < count($uri); $i++) {
			list($empty, $section, $action, $prefix) = explode('/', $uri[$i]);
			$group = '/' . $section . '/' . $action . '/' . $prefix . '/';
			$groupped[$group]['search'][] = $uri[$i];
			$groupped[$group]['upper'][] = $uri[$i];
		}

		foreach ($groupped as $concat => $group) {
			for ($i = 0; $i < count($group['search']); $i++) {
				list($empty, $section, $action, $prefix) = explode('/', $group['search'][$i]);
				$concat2 = '/' . $section . '/' . $action . '/';
				$groupped[$concat]['replace'][$i] = '/' . $section . '/' . ($r[$concat2][0]['blink'] ? '' : ($r[$concat2][0]['rename'] ? $r[$concat2][0]['alias'] : $action) . '/');
			}
		}

		foreach ($groupped as $concat => $group) {
			list($empty1,$section1, $action1, $prefix1) = explode('/', $concat);
			$concat1 = '/' . $section1 . '/' . $action1 . '/';
			$ids = array();
			for ($i = 0; $i < count($group['search']); $i++) {
				list($empty, $section, $action, $prefix, $id) = explode('/', $group['search'][$i]);
				$ids[] = $id;
			}
			$models = array();
			for ($i = count($r[$concat1])-1; $i >= 0; $i--) {
				if (!in_array($r[$concat1][$i]['entityId'], array_keys($models))) $models[$r[$concat1][$i]['entityId']] = Entity::getInstance()->getModelById($r[$concat1][$i]['entityId']);
			}
			$continue = false;
			for ($i = count($r[$concat1])-1; $i >= 0; $i--) {
				if ($r[$concat1][$i]['prefix'] == $prefix || $continue) {
					if ($components = $models[$r[$concat1][$i]['entityId']]->fetchAll('`id` IN ("' . implode('","', $ids) . '")')) {
						if ($i > 0) {
							$key = $models[$r[$concat1][$i-1]['entityId']]->info('name') . 'Id';
						}
						$ids = array();
						foreach ($components as $component) {
							if ($cr) return array($r[$concat1][$i]['entityId'] => $component);
							for ($j = 0; $j < count($groupped[$concat]['upper']); $j++) {
								$item = explode('/', $groupped[$concat]['upper'][$j]);
								if ($item[4] == $component->id) {
									if ($i > 0) {
										$item[4] = $component->{$key};
										$groupped[$concat]['upper'][$j] = implode('/', $item);
									}
									if (strlen($component->alias))
										$groupped[$concat]['revert'][$j][] = $component->alias;
								}
							}
							if ($i > 0) {
								$ids[] = $component->{$key};
							}
						}
					}
					$continue = true;
				}
			}
			foreach($group['search'] as $key => $value) {
				$reverted = @array_reverse($groupped[$concat]['revert'][$key]);
				$item = explode('/', $group['search'][$key]);
				for ($i = 5; $i < count($item); $i++) $reverted[] = $item[$i];
				$groupped[$concat]['replace'][$key] .= implode('/', $reverted);
			}
			$sys = str_replace($groupped[$concat]['search'], $groupped[$concat]['replace'], $sys);
		}
		return $sys;
	}

	public static function contextRows($sys){
		preg_match_all('/[0-9a-z\/#]+/', $sys, $matches);
		$uri = $matches[0][0];
		$db = Indi_Db_Table::getDefaultAdapter();
		$furi = ''; if (count(explode('/', trim($uri, '/'))) > 1) $furi = $uri; $uri = $furi; $furi = '';
		list($empty, $section, $action, $prefix) = explode('/', $uri);
		$group = '/' . $section . '/' . $action . '/';

		$sql = '
			SELECT
			  CONCAT("/", `s`.`alias`, "/", `a`.`alias`, "/") AS `concat`,
			  `u`.*,
			  `sa`.`blink`
			FROM
			  `url` `u`,
			  `fsection` `s`,
			  `faction` `a`,
			  `fsection2faction` `sa`
			WHERE 1
			  AND `u`.`fsection2factionId` = `sa`.`id`
			  AND `sa`.`fsectionId` = `s`.`id`
			  AND CONCAT("/", `s`.`alias`, "/", `a`.`alias`, "/") = "' . $group . '"
			  AND `sa`.`factionId` = `a`.`id`
			ORDER BY `s`.`id`,`a`.`id`,`u`.`move`
			';
		$rs = $db->query($sql)->fetchAll();
		$found = array();
		$r = array();
		for ($i = 0; $i < count($rs); $i++) {
			if (!in_array($rs[$i]['concat'], $found)) $found[] = $rs[$i]['concat'];
			$r[] = $rs[$i];
		}

		list($empty, $section, $action, $prefix) = explode('/', $uri);
		$group = '/' . $section . '/' . $action . '/';
		if (in_array($group, $found)) $furi = $uri;
		$uri = $furi; $furi = '';

		$groupped = array();
		list($empty, $section, $action, $prefix) = explode('/', $uri);
		$group = '/' . $section . '/' . $action . '/' . $prefix . '/';
		$search = $uri;
		$upper = $uri;


		list($empty, $section, $action, $prefix) = explode('/', $search);
		$replace = '/' . $section . '/' . ($r[0]['blink'] ? '' : $action . '/');
		list($empty1,$section1, $action1, $prefix1) = explode('/', $group);
		$id = 0;
		list($empty, $section, $action, $prefix, $id) = explode('/', $search);
		$models = array();
		for ($i = count($r)-1; $i >= 0; $i--) {
			if (!in_array($r[$i]['entityId'], array_keys($models))) $models[$r[$i]['entityId']] = Entity::getInstance()->getModelById($r[$i]['entityId']);
		}
		$continue = false;
		$contextRows = array();
		for ($i = count($r)-1; $i >= 0; $i--) {
			if ($r[$i]['prefix'] == $prefix || $continue) {
				if ($component = $models[$r[$i]['entityId']]->fetchRow('`id` = "' . $id . '"')) {
					$contextRows[$r[$i]['entityId']] = $component;
					return $contextRows;
				}
				$continue = true;
			}
		}
		return $contextRows;
	}

	public static function rawResponse($host, $path, $https = false, $post = ''){
		$host = str_replace('http', '', $host);
		$fp = pfsockopen(($https ? 'ssl://' : '') . $host, $https ? 443 : 80, $errno, $errstr, 30);
		if (!$fp) echo "$host - $errstr - ($errno)<br />\n";  else {
			$headers = ((is_array($post) && count($post) || strlen($post)) ? 'POST' : 'GET') . ' ' . str_replace(' ', '%20', $path)." HTTP/1.1\r\n";
			$headers .= "Host: $host\r\n";
			$headers .= "Cache-Control: max-age=0\r\n";
			$headers .= "User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/535.7 (KHTML, like Gecko) Chrome/16.0.912.75 Safari/535.7\r\n";
			$headers .= "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8\r\n";
			$headers .= "Accept-Language: ru-RU,ru;q=0.8,en-US;q=0.6,en;q=0.4\r\n";
			$headers .= "Accept-Charset: windows-1251,utf-8;q=0.7,*;q=0.3\r\n";
			$headers .= "Connection: Close\r\n";
			if ((is_array($post) && count($post)) || mb_strlen($post, 'utf-8')) {
				$p = is_array($post) && count($post) ? http_build_query($post) : $post;
				$headers .= "Content-Type: application/x-www-form-urlencoded\r\n";
				$headers .= "Content-Length: " . mb_strlen($p, 'utf-8') . "\r\n\r\n";
				$headers .= $p."\r\n\r\n";
			} else $headers .= "\r\n";
			fwrite($fp, $headers);
			while (!feof($fp)) {
				$raw .= fgets($fp, 1024 * 1024);
			}
			fclose($fp);
			//	i($headers, 'a');
			//	i($raw, 'a');
			list($headers, $html) = explode("\r\n\r\n", $raw);
			if (preg_match('/^HTTP\/1\.1 30(1|2|3)/', $headers) && preg_match('/Location: (.*)/ui', $headers, $location)) {
				$u = parse_url($location[1]);
				$location = (strpos($location[1], '://') === false ? 'http' . ($https?'s':'') . '://'.$u['host'] : '') . $location[1];
				//	i($location, 'a');
				Indi_Uri::setupSessionIdIfExistsInLocation($location);
				$u = parse_url($location);
				$u['query'] = trim($u['query'], '_');
				return Indi_Uri::rawResponse($u['host'], $u['path'] . ($u['query'] ? '?' . $u['query'] : '') . ($u['fragment'] ? '#' . $u['fragment'] : ''), $u['scheme'] == 'https');
			}
		}
		return $raw;
	}

	public static function setupSessionIdIfExistsInLocation($location) {
	}
}