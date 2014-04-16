<?php
class Indi_Uri {
    public $staticpageAdditionalWHERE = array();

	public function dispatch($params = array()){
        $this->preDispatch();

        $params = Indi::uri();

        if ($params['module'] != 'admin') {
            $controllerClassName = ucfirst($params['section']) . 'Controller';
            if (!class_exists($controllerClassName)) {
                $fsectionM = Indi::model('Fsection');
                $fsectionA = $fsectionM->fetchAll('`alias` IN ("' . $params['section'] . '", "static")', 'FIND_IN_SET(`alias`, "' . $params['section'] . ',static")')->toArray();
                if ($fsectionA[0]['alias'] == 'static') {
                    $where = array_merge(
                        array('`alias` IN ("' . $params['section'] . '", "404")', '`toggle` = "y"'),
                        $this->staticpageAdditionalWHERE
                    );
                    $staticA = Indi::model('Staticpage')->fetchAll($where, 'FIND_IN_SET(`alias`, "' . $params['section'] . ',404")')->toArray();
                    $params['section'] = 'static';
                    $params['action'] = 'details';
                    $params['id'] = $staticA[0]['id'];
				    if ($staticA[0]['alias'] == '404') {
					    $notFound = true;
				    }
                } else if (!Indi::model('Faction')->fetchRow('`alias` IN ("' . str_replace('"', '\"', $params['action']) . '")')) {
                    $where = array_merge(
                        array('`alias` IN ("404")', '`toggle` = "y"'),
                        $this->staticpageAdditionalWHERE
                    );
                    $staticA = Indi::model('Staticpage')->fetchAll($where, 'FIND_IN_SET(`alias`, "404")')->toArray();
                    $params['section'] = 'static';
                    $params['action'] = 'details';
                    $params['id'] = $staticA[0]['id'];
				    if ($staticA[0]['alias'] == '404') {
					    $notFound = true;
				    }
                }
            }
        } else {
            $sectionM = Indi::model('Section');
            $sectionR = $sectionM->fetchRow('`alias` = "' . $params['section'] . '"');
            if ($sectionR) $sectionA = $sectionR->toArray();
        }

        if ($notFound) {
            header('HTTP/1.1 404 Not Found');
        } else {
            $this->trailingSlash();
        }

        $controllerClassName = ($params['module'] == 'front' ? '' : ucfirst($params['module']) . '_') . ucfirst($params['section']) . 'Controller';

        if (!class_exists($controllerClassName)) {

			if ($params['module'] == 'admin') {
				if ($sectionA) {
					$extendClass = $sectionA['extends'];
				} else {
					$extendClass = 'Project_Controller_Admin';
				}
                if (!class_exists($extendClass)) $extendClass = 'Indi_Controller_Admin';
            } else {
                $extendClass = 'Project_Controller_Front';
                if ($fsectionA[0]['extends']) $extendClass = implode('_', array($extendClass, $fsectionA[0]['extends']));
				if (!class_exists($extendClass)) $extendClass = preg_replace('/^Project/', 'Indi', $extendClass);
			}
            eval('class ' . ucfirst($controllerClassName) . ' extends ' . $extendClass . '{}');
		}

        $controller = new $controllerClassName($params);
        $controller->dispatch();
    }

	public function preDispatch() {
		$this->no3w();
		$this->setCookieDomain();
        session_start();
        $this->checkSeoUrlsMode();
    }

	public function no3w(){
		if (strpos($_SERVER['HTTP_HOST'], 'www') !== false) {
            header('HTTP/1.1 301 Moved Permanently');
            die(header('Location: http://' . str_replace('www.', '', $_SERVER['HTTP_HOST']) . $_SERVER['REQUEST_URI']));
        }
	}

    public function trailingSlash(){
        if ($_SERVER['REQEST_URI'] != '/' && !preg_match('/\/$/', $_SERVER['REQUEST_URI']) && !preg_match('/\?/', $_SERVER['REQUEST_URI'])) {
            header('HTTP/1.1 301 Moved Permanently');
            header('Location: ' . $_SERVER['REQUEST_URI'] . '/');
            die();
        }    
    }
	public function setCookieDomain(){
		$domain = Indi::ini()->general->domain;
		if (strpos($domain, '.') !== false) ini_set('session.cookie_domain', '.' . $domain);
        if (STD) ini_set('session.cookie_path', STD);
	}

	public function checkSeoUrlsMode(){
		list($first) = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
		if ($first != 'admin') {
			$this->adjustUriIfSubdomain();
			$GLOBALS['enableSeoUrls'] = current(Indi::db()->query('SELECT `value` FROM `fconfig` WHERE `alias` = "enableSeoUrls"')->fetch());
            $GLOBALS['INITIAL_URI'] = $_SERVER['REQUEST_URI'];
            if ($GLOBALS['enableSeoUrls'] == 'true') {
                $_SERVER['REQUEST_URI'] = $this->seo2sys($_SERVER['REQUEST_URI']);
            }
		}
	}

	public function adjustUriIfSubdomain(){
		$db = Indi::db();
		$subdomains = $db->query('SELECT `fs`.`alias` FROM `subdomain` `sd`, `fsection` `fs` WHERE `sd`.`fsectionId` = `fs`.`id`')->fetchAll();
		$subdomainsArray = array(); foreach ($subdomains as $sd) $subdomainsArray[] = $sd['alias'];
		if ($_SERVER['HTTP_HOST'] != Indi::ini()->general->domain) {
			$subdomain = str_replace('.'.Indi::ini()->general->domain, '',$_SERVER['HTTP_HOST']);
			if (in_array($subdomain, $subdomainsArray)) {
				$_SERVER['REQUEST_URI'] = '/' . $subdomain . $_SERVER['REQUEST_URI'];
			}
			Indi::registry('subdomain', $subdomain);
		}
		Indi::registry('subdomains', $subdomainsArray);
	}

	public function seo2sys($seo){
		$db = Indi::db();
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
					if (!in_array($parts[$i]['entityId'], array_keys($models))) $models[$parts[$i]['entityId']] = Indi::model($parts[$i]['entityId']);
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
							$where = ' AND `' . $models[$parts[$i]['entityId']]->name() . 'Id` = ' . $component->id;
							$alias = $aim[$i+($parts[0]['alias'] ? 2 : 3) - $shift];
						}
						$where = ' AND `' . $models[$parts[$i]['entityId']]->name() . 'Id` = ' . $component->id;
						$alias = $aim[$i+($parts[0]['alias'] ? 2 : 3) - $shift];
					} else if ($component = $models[$parts[$i]['entityId']]->fetchRow('`alias` = ""' . $where)) {
						$where = ' AND `' . $models[$parts[$i]['entityId']]->name() . 'Id` = ' . $component->id;
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

	public static function sys2seo($sys, $cr = false, $reg = ''){
		preg_match_all($reg ? $reg: '/(href|url)="([0-9a-z\/#]+)"/', $sys, $matches);
		$uri = $matches[2];
		$db = Indi::db();
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
				if (!in_array($r[$concat1][$i]['entityId'], array_keys($models))) $models[$r[$concat1][$i]['entityId']] = Indi::model($r[$concat1][$i]['entityId']);
			}
			$continue = false;
			for ($i = count($r[$concat1])-1; $i >= 0; $i--) {
				if ($r[$concat1][$i]['prefix'] == $prefix || $continue) {
					if ($components = $models[$r[$concat1][$i]['entityId']]->fetchAll('`id` IN ("' . implode('","', $ids) . '")')) {
						if ($i > 0) {
							$key = $models[$r[$concat1][$i-1]['entityId']]->name() . 'Id';
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
}