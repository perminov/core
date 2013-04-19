<?php
class Indi_Auth{

	protected static $_alternate = null;
    /**
     * Store singleton instance
     *
     * @var Auth
     */
    protected static $_instance = null;

    /**
     * Id for index action
     *
     * @var int
     */
    protected $_indexActionId = null;
    
    /**
     * Object set up
     *
     */
    protected function __construct()
    {
        // set up index action id
		if (null === $this->_indexActionId) {
	        $action = Misc::loadModel('Action');
	        $this->_indexActionId = $action->fetchRow('`alias` = "index"')->id;
		}
    }

    /**
     * Singleton instance
     * 
     * @return Indi_Auth object
     */
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * Perform authentication
     *
     */
    public function auth(&$controller)
    {
		$this->controller = $controller;
		// check access if cms user is already logged in
        if ($_SESSION['admin']['id']) {
            $controller->admin = $_SESSION['admin'];
//			d($controller);
//			die();
			$this->identifier = $this->controller->identifier;
			$this->accessOk($controller->admin, $controller->controller, $controller->action);
			$controller->authComponent = $this;
		// otherwise, if it is an login attempt 
		} else {
            if ($controller->post['enter']) {
				$controller->post = filter($controller->post);
				if ($admin = $this->accessOk(null, 'index', null, $controller->post['email'], $controller->post['password'])) {
					$_SESSION['admin'] = $admin;
		            $controller->admin = $_SESSION['admin'];
					//header('Location: /' . ($GLOBALS['cmsOnlyMode'] ? '': 'admin/'));die();
					die(json_encode(array('ok' => true)));
				} else {
					$controller->view->assign('email', $controller->post['email']);
					$controller->view->assign('error', $authResult->getMessages());
                }
            }
            $controller->view->assign('project', $controller->config->project);
			/*$out = $controller->view->render('login.php');
			// perform hrefs adjustments in case if system used only as admin area
			$config = Indi_Registry::get('config');
			if($config['general']->standalone == 'true') {
				$out = preg_replace('/(src|href|background)=("|\')/', '$1=$2/admin', $out);
				$out = preg_replace('/\/admin\/admin\//', '/admin/', $out);
				$out = preg_replace('/\/adminjavascript/', 'javascript', $out);
			}*/
            $out = $this->controller->view->render('login.php');
            if ($_SERVER['STD']) {
                $out = preg_replace('/(<link[^>]+)(href)=("|\')\//', '$1$2=$3' . $_SERVER['STD'] . '/', $out);
                $out = preg_replace('/(<script[^>]+)(src)=("|\')\//', '$1$2=$3' . $_SERVER['STD'] . '/', $out);
                $out = preg_replace('/(<img[^>]+)(src)=("|\')\//', '$1$2=$3' . $_SERVER['STD'] . '/', $out);
                $out = preg_replace('/value: \'\/admin/', 'value: \'' . $_SERVER['STD'] . '/admin', $out);
            }
            die($out);


            die($out);
        }
    }
	public function accessOk($admin = null, $section = '', $action = '', $email = null, $password = null){
		$query = "
            SELECT 
                `a`.`toggle` = 'y' AS `adminToggle`,
                `p`.`id` AS `profileExists`,
                `p`.`toggle` = 'y' AS `profileToggle`,
                `s`.`id` > 0 AS `sectionExists`,
                `s`.`toggle` = 'y' AS `sectionToggle`,
                `ac`.`id` > 0 AS `actionExists`,
                `ac`.`toggle` = 'y' AS `actionToggle`,
                `sa`.`id` > 0 AS `section2actionExists`,
                `sa`.`toggle` = 'y' AS `section2actionToggle`,
                `p`.`title` AS `profile`,
                POSITION(CONCAT('\'', `p`.`id`, '\'') IN CONCAT('\'', REPLACE(`sa`.`profileIds`, ',', '\',\''), '\'')) > 0 AS `granted`,
				`s`.`sectionId` as `sectionId`
            FROM `admin` `a` 
               LEFT JOIN `profile` `p` ON (`p`.`id` = `a`.`profileId`)
               LEFT JOIN `section` `s` ON (`s`.`id` = (SELECT `id` FROM `section` WHERE `alias` = '" . $section . "'))
               LEFT JOIN `action` `ac` ON (`ac`.`id` = (SELECT `id` FROM `action` WHERE `alias`='" . $action . "'))
               LEFT JOIN `section2action` `sa` ON (`sa`.`actionId` = `ac`.`id` AND `sa`.`sectionId` = `s`.`id`)
            WHERE `a`.`id` = '" . $admin['id'] . "'
			";
		if (!$admin) {
			if (!$email) $this->logout('Введите пользователя');
			if (!$password) {
				$this->controller->view->assign('email', $email);
				$this->logout('Введите пароль');
			}
		}
		$query1 = "
			SELECT 
			  `a`.*, 
			  `a`.`password` = '" . $password . "' AS `passwordOk`, 
			  `a`.`toggle`='y' AS `adminToggle`,
			  `p`.`id` AS `profileExists`,
			  `p`.`toggle`='y' AS `profileToggle`,
              `p`.`title` AS `profile`,
			  COUNT(`sa`.`sectionId`) > 0 AS `atLeastOneSectionAccessible`
			FROM `admin` `a` 
			  LEFT JOIN `profile` `p` ON (`p`.`id` = `a`.`profileId`)
			  LEFT JOIN `section2action` `sa` ON (
				(
				`sa`.`profileIds` LIKE `a`.`profileId` OR
				`sa`.`profileIds` LIKE CONCAT(`a`.`profileId`, ',%') OR 
				`sa`.`profileIds` LIKE CONCAT('%,',`a`.`profileId`) OR 
				`sa`.`profileIds` LIKE CONCAT('%,',`a`.`profileId`, ',%')
				) AND `sa`.`actionId` = (SELECT `id` FROM `action` WHERE `alias`='index')
			  )
			WHERE `a`.`email` = '" . $email . "'
			GROUP BY `sa`.`sectionId`
				";
		if ($admin['alternate']) {
			$query = "
            SELECT 
                '1' AS `adminToggle`,
                `p`.`id` AS `profileExists`,
                `p`.`toggle` = 'y' AS `profileToggle`,
                `s`.`id` > 0 AS `sectionExists`,
                `s`.`toggle` = 'y' AS `sectionToggle`,
				`s`.`entityId`,
                `ac`.`id` > 0 AS `actionExists`,
                `ac`.`toggle` = 'y' AS `actionToggle`,
                `sa`.`id` > 0 AS `section2actionExists`,
                `sa`.`toggle` = 'y' AS `section2actionToggle`,
                `p`.`title` AS `profile`,
                POSITION(CONCAT('\'', `p`.`id`, '\'') IN CONCAT('\'', REPLACE(`sa`.`profileIds`, ',', '\',\''), '\'')) > 0 AS `granted`,
				`s`.`sectionId` as `sectionId`
            FROM `" . $admin['alternate'] . "` `a` 
               LEFT JOIN `profile` `p` ON (`p`.`id` = '" . $admin['profileId'] . "')
               LEFT JOIN `section` `s` ON (`s`.`id` = (SELECT `id` FROM `section` WHERE `alias` = '" . $section . "'))
               LEFT JOIN `action` `ac` ON (`ac`.`id` = (SELECT `id` FROM `action` WHERE `alias`='" . $action . "'))
               LEFT JOIN `section2action` `sa` ON (`sa`.`actionId` = `ac`.`id` AND `sa`.`sectionId` = `s`.`id`)
            WHERE `a`.`id` = '" . $admin['id'] . "'
			";
		}
		$info = $this->controller->db->query($admin ? $query : $query1)->fetchAll(); $info = $info[0];
        //d($query);
		if (!$info) {
			$logout = 'Ваш аккаунт не существует';
		} else if (!$admin && !$info['passwordOk']) {
			$logout = 'Неправильный пароль';
		} else if (!$info['adminToggle']) {
			$logout = 'Ваш аккаунт отключен';
		} else if (!$info['profileExists']) {
			$logout = 'Профиль Вашего аккаунта не существует';
		} else if (!$info['profileToggle']) {
			$logout = 'Профиль Вашего аккаунта отключен';
		} else if ($section != 'index') {
			if (!$info['sectionExists']) {
				$redirect = 'Этого раздела системы не существует';
			} else if (!$info['sectionToggle']) {
				$redirect = 'Этот раздел системы отключен';
			} else if (!$info['actionExists']) {
				$redirect = 'Это действие не существует';
			} else if (!$info['actionToggle']) {
				$redirect = 'Это действие отключено';
			} else if (!$info['section2actionExists']) {
				$redirect = 'Это действие в этом разделе не существует';
			} else if (!$info['section2actionToggle']) {
				$redirect = 'Это действие в этом разделе отключено';
			} else if (!$info['granted']){
				$redirect = 'У Вас нет прав на это действие в этом разделе';
			} else {
				$parentId = $info['sectionId'];
				do {
					$parent = Misc::loadModel('Section')->fetchRow('`id` = "' . $parentId . '"');
					$parentId = $parent->sectionId;
					if (!$parent) {
						break;
					} else if ($parent->toggle == 'n') {
						$redirect = 'Один из вышестоящих разделов для этого раздела отключен';
						break;
					}
				} while (true);

				if (!$redirect && $admin['alternate'] && $this->identifier) {
					$entity = Entity::getModelById($info['entityId']);
					$field = $admin['alternate']. 'Id';
					if ($entity->fieldExists($field))
					if ($action != 'index' && !($row = $entity->fetchRow('`id` = "' . $this->identifier . '" AND `' . $field. '` = "' . $admin['id'] . '"'))) {
						$redirect = 'Эта объект вам не принадлежит';
					}
				}
			}
		}
		if ($logout && !$admin['alternate']) $logout = $this->checkAlternate($admin, $section, $action, $email, $password, $logout, $info);
		if ($logout) {
	        if ($_SESSION['admin']['id']) {
		        unset($_SESSION['admin']);
				$this->controller->view->assign('error' , array($logout));
                $out = $this->controller->view->render('login.php');
                if ($_SERVER['STD']) {
                    $out = preg_replace('/(<link[^>]+)(href)=("|\')\//', '$1$2=$3' . $_SERVER['STD'] . '/', $out);
                    $out = preg_replace('/(<script[^>]+)(src)=("|\')\//', '$1$2=$3' . $_SERVER['STD'] . '/', $out);
                    $out = preg_replace('/(<img[^>]+)(src)=("|\')\//', '$1$2=$3' . $_SERVER['STD'] . '/', $out);
                    $out = preg_replace('/value: \'\/admin/', 'value: \'' . $_SERVER['STD'] . '/admin', $out);
                }
                die($out);
			} else {
				die(json_encode(array('error' => $logout)));
			}
		} else if ($redirect) {
			die($redirect);
		}
		if (isset($this->controller->get['check'])) {
			die('ok');
		} else {
			return $admin? true : $info;
		}
	}
	public function logout($message){
		if ($_SESSION['admin']['id']) {
			unset($_SESSION['admin']);
			$this->controller->view->assign('error' , array($message));
            $out = $this->controller->view->render('login.php');
            if ($_SERVER['STD']) {
                $out = preg_replace('/(<link[^>]+)(href)=("|\')\//', '$1$2=$3' . $_SERVER['STD'] . '/', $out);
                $out = preg_replace('/(<script[^>]+)(src)=("|\')\//', '$1$2=$3' . $_SERVER['STD'] . '/', $out);
                $out = preg_replace('/(<img[^>]+)(src)=("|\')\//', '$1$2=$3' . $_SERVER['STD'] . '/', $out);
                $out = preg_replace('/value: \'\/admin/', 'value: \'' . $_SERVER['STD'] . '/admin', $out);
            }
            die($out);
        } else {
			die(json_encode(array('error' => $message)));
		}
	}

    /**
     * Get actions of section that are accessbile for admin
     *
     * @return Indi_Db_Table_Rowset object of assotiated actions of current section
     */
    public function getActions($sectionId, $profileId)
    {
        $section2action = new Section2action();
        $section2actionArray = $section2action->fetchAll($query ='
			`sectionId` = "' . $sectionId . '" 
			AND `toggle` = "y" 
			AND POSITION(CONCAT("\'", "' . $profileId . '","\'") IN CONCAT("\'", REPLACE(`profileIds`, ",", "\',\'"), "\'")) > 0
			AND `actionId` IN (SELECT `id` FROM `action` WHERE `toggle` = "y")
		', 'move')->toArray();

		$actionIds = array();
		for ($i = 0; $i < count ($section2actionArray); $i++) $actionIds[] = $section2actionArray[$i]['actionId'];

		$action = new Action();
		$query = '`id` IN ("' . implode('","', $actionIds) . '")';		
		return $action->fetchAll($query, 'POSITION(CONCAT("\'", `id`, "\'") IN "\'' . implode("','", $actionIds) . '\'")');
    }

    /**
     * Get subsections of current section, that are accessible to profile that admin is assotiated to, 
     * specified by $profileId or false if no sections accessible
     *
     * @param int $profileId
     * @return Indi_Db_Table_Rowset object | null
     */
	public function getSections($sectionId, $profileId){
		$query = "
			SELECT 
			  GROUP_CONCAT(DISTINCT `s`.`id`) AS `accessibleSectionIds` 
			FROM 
			  `section` `s`,
			  `section2action` `sa`,
			  `admin` `a`,
			  `profile` `p`
			WHERE 1
			  AND `s`.`id` = `sa`.`sectionId`
			  AND `sa`.`actionId` = '" . $this->_indexActionId . "'
			  AND `sa`.`profileIds` LIKE '%" . $profileId . "%'
			  AND `s`.`sectionId` = '" . $sectionId . "'
			  AND `sa`.`toggle` = 'y'
			  AND `s`.`toggle` = 'y'
			  AND `a`.`toggle` = 'y'
			  AND `p`.`toggle` = 'y'
			  AND `a`.`profileId` = `p`.`id`
			  AND `p`.`id` = '1'
			ORDER BY `s`.`move`
		";
		$result = Indi_Db_Table::getDefaultAdapter()->query($query)->fetch();
		$accessibleSectionIds = $result['accessibleSectionIds'];
		$section = new Section();
		$where = $accessibleSectionIds ? '`id` IN (' . $accessibleSectionIds . ')' : '`id` IN ("")';
		$return = $section->fetchAll($where, 'move');
              if (!$this->controller->params['json'] && $sectionId == 32) {
//                  d($return->toArray());
              }
		return $return;
	}

    /**
     * Get cms left menu for admin with access level, specified by $profileId argument
     * If $profileId argument is not specified, the identfier of current logged in admin will be used
     * 
     * @param int|null $profileId
     * @return array left menu
     */
    public function getMenu($profileId = null)
    {
		$admin = $_SESSION['admin'];
		if (!$profileId) {
			$profileId = $admin['profileId'];
		}
		$section = new Section();
		$groups = $section->fetchAll('`sectionId` = "0" AND `toggle`="y"', 'move');
		foreach ($groups as $group) {
			if (!$admin['alternate']) {
    		$query = "
    			SELECT 
    			  GROUP_CONCAT(DISTINCT `s`.`id` ORDER BY `s`.`move`) AS `accessibleSectionIds` 
    			FROM 
    			  `section` `s`,
    			  `section2action` `sa`,
    			  `admin` `a`,
    			  `profile` `p`
    			WHERE 1
    			  AND `s`.`id` = `sa`.`sectionId`
    			  AND `sa`.`actionId` = '" . $this->_indexActionId . "'
				  AND POSITION(CONCAT('\'','" . $profileId . "', '\'') IN CONCAT('\'', REPLACE(`sa`.`profileIds`, ',', '\',\''), '\'')) > 0
    			  AND `s`.`sectionId` = '" . $group->id . "'
    			  AND `sa`.`toggle` = 'y'
    			  AND `s`.`toggle` = 'y'
    			  AND `a`.`toggle` = 'y'
    			  AND `p`.`toggle` = 'y'
    			  AND `a`.`profileId` = `p`.`id`
    			  AND `p`.`id` = '1'
    		";
			} else {
    		$query = "
    			SELECT 
    			  GROUP_CONCAT(DISTINCT `s`.`id` ORDER BY `s`.`move`) AS `accessibleSectionIds` 
    			FROM 
    			  `section` `s`,
    			  `section2action` `sa`,
    			  `profile` `p`
    			WHERE 1
    			  AND `s`.`id` = `sa`.`sectionId`
    			  AND `sa`.`actionId` = '" . $this->_indexActionId . "'
				  AND POSITION(CONCAT('\'','" . $profileId . "', '\'') IN CONCAT('\'', REPLACE(`sa`.`profileIds`, ',', '\',\''), '\'')) > 0
    			  AND `s`.`sectionId` = '" . $group->id . "'
    			  AND `sa`.`toggle` = 'y'
    			  AND `s`.`toggle` = 'y'
    			  AND `p`.`toggle` = 'y'
    			  AND `p`.`id` = '1'
    		";
			}
    		$result = Indi_Db_Table::getDefaultAdapter()->query($query)->fetch();
    		$accessibleSectionIds[$group->id] = $result['accessibleSectionIds'];
		}
		$menuIds = array();
		foreach ($accessibleSectionIds as $groupId => $sectionIds) {
			if ($sectionIds) {
				$menuIds[] = $groupId;
				$menuIds = array_merge($menuIds, explode(',', $sectionIds));
			}
		}
		$menuIds = implode(',', $menuIds);
		$where = $menuIds ? '`id` IN (' . $menuIds . ')' : '`id` IN ("")';
		$order = 'POSITION(CONCAT("\'", `id`, "\'") IN "\'' . implode("','", explode(',', $menuIds)) . '\'")';
		$return = $section->fetchAll($where, $order);
        return $return;
    }    
	public function checkAlternate($admin = null, $section = '', $action = '', $email = null, $password = null, $logout, &$info){
		$profiles = Misc::loadModel('Profile')->fetchAll('`entityId` != "0"');
		if (!$profiles->count()) return $logout;
		foreach ($profiles as $profile) {
			$entity = Entity::getModelById($profile->entityId);
			$row = $entity->fetchRow('`email` = "' . $email . '"');
			if ($row) {
				if ($profile->toggle == 'n') return 'Профиль Вашего аккаунта отключен';
				if ($row->password !== $password) return 'Неправильный пароль';

				if(!current($this->controller->db->query($query1 = "
					SELECT 
						COUNT(`sa`.`sectionId`) > 0 AS `atLeastOneSectionAccessible` 
					FROM 
						`section2action` `sa` 
					WHERE 
						FIND_IN_SET('" . $profile->id . "', `sa`.`profileIds`)
				")->fetch())) return 'Пока нет доступных вам разделов';
				$info = $row->toArray();
				$info['profileId'] = $profile->id;
				$info['toggle'] = 'y';
				$info['passwordOk'] = 1;
				$info['adminToggle'] = 1;
				$info['profileExists'] = 1;
				$info['profileToggle'] = 1;
				$info['atLeastOneSectionAccessible'] = 1;
				$info['alternate'] = $entity->info('name');
                $info['profile'] = $profile->title;
                return false;
			}
		}
		return $logout;
	}
}




