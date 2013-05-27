<?php
class Indi_Controller{
    /**
     * Store $_POST variables got from Indi_Registry
     *
     * @var array
     */
    public $post;

    /**
     * Store $_GET variables got from Indi_Registry
     *
     * @var array
     */    
    public $get;

    /**
     * Store $_FILES
     *
     * @var array
     */
    public $files;
    
    /**
     * Store System configuration
     *
     * @var array
     */
    public $config;
    
    /**
     * Store info about call path
     *
     * @var Indi_Trail object
     */
    public $trail;
    
    /**
     * Store current section as object
     *
     * @var Section_Row object
     */
    public $section;
    
    /**
     * Store row found by identifier
     *
     * @var Indi_Db_Table_Row object
     */
    public $row;

    /**
     * Store rowset of entity, assotiated with current section
     *
     * @var Indi_Db_Table_Rowset object
     */
    public $rowset;
    
    /**
     * Store db adapter
     *
     * @var Indi_Db object
     */
    public $db;

    /**
     * Store current module name
     *
     * @var string
     */
    public $module;

    /**
     * Store module session
     *
     * @var Indi_Session_Namespace object
     */
    public $session;
    
    /**
     * Store current controller name
     * 
     * @var string
     */
    public $controller;
    
    /**
     * Store current action
     * 
     * @var string
     */
    public $action;
    
    /**
     * Store request row identifier
     * 
     * @var int
     */
    public $identifier;
    
    /**
     * Store default rows count to display on page
     *
     * @var int
     */
    public $limit = 100;

    public $rowsetCondition = null;
    
    public $order = null;
    
    public $only;
    
    public $specialParentCondition = '';

	public $params = array();

	public function __construct($params = array()) {
		// set up request variables
		$this->post = Indi_Registry::get('post');
		$this->get = Indi_Registry::get('get');
		$this->files = Indi_Registry::get('files');

		// set up db adapter
		$this->db = Indi_Db_Table::getDefaultAdapter();

		$this->controller = $params['section'];
		$this->action     = $params['action'];
		$this->module     = $params['module'];
		$this->identifier = $params['id'];

		// set up session
		$this->session = new Indi_Session_Namespace($this->module);

		// if action == index, set up page number
		if ($this->action == 'index') {
			$this->page     = $params['page'] ? $params['page'] : 1;
		}
		$this->view = new Indi_View();
		$config = Indi_Registry::get('config');

		$coreS = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . $_SERVER['STD'] . '/core/' . trim($config['view']->scriptPath, '/');
		$wwwS  = preg_replace('/core(\/application)/', 'www$1', $coreS);

		if(is_dir($wwwS)) {
			$this->view->setScriptPath($coreS . ($this->module != 'front' ? '/' . $this->module : ''));
			$this->view->addScriptPath($wwwS . ($this->module != 'front' ? '/' . $this->module : ''));
		} else {
			$this->view->setScriptPath($coreS . ($this->module != 'front' ? '/' . $this->module : ''));
		}

		$coreH = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . $_SERVER['STD'] . '/core/library';
		$wwwH  = preg_replace('/core(\/library)/', 'www$1', $coreH);

        $this->view->addHelperPath($coreH . '/Indi/View/Helper' . ($this->module != 'front' ? '/' . ucfirst($this->module) : ''), 'Indi_View_Helper_'. ($this->module != 'front' ? ucfirst($this->module) . '_' : ''));
        if (is_dir($wwwH)) $this->view->addHelperPath($wwwH . '/Project/View/Helper' . ($this->module != 'front' ? '/' . ucfirst($this->module) : ''), 'Project_View_Helper_'. ($this->module != 'front' ? ucfirst($this->module) . '_' : ''));

		Indi_Registry::set('view', $this->view);

		$this->params = $params;
		$this->action = $params['action'];
		$this->init();
        //d($this->view);
        //die();
	}

	/**
     * Initialize object
     * Called from {@link __construct()} as final step of object instantiation.
     *
     * @return void
     */
    public function init()
    {
	}

    public function modifyRowsetCondition($condition)
    {
        return $condition;
    }
	public function preIndexJson(){
	}
	public function _redirect($location){
		die('<script>window.parent.loadContent("' . $location . '");</script>');
	}
	public function visitors($clearOnly = false){
		$deleteBeforeStamp = mktime(date('H'), date('i')-5, date('s'), date('n'), date('j'), date('Y'));
		$deleteBeforeDate = date('Y-m-d H:i:s', $deleteBeforeStamp);
		$this->db->query('DELETE FROM `visitor` WHERE `lastActivity` < "' . $deleteBeforeDate . '" OR `title` = "' . $_COOKIE['PHPSESSID'] . '"');
		if (!$clearOnly) {
			if ($_SESSION['userId']) {
				$hidden = Misc::loadModel('User')->fetchRow('`id` = "' . $_SESSION['userId'] . '"')->hidden;
			} else {
				$hidden = 0;
			}
			$this->db->query('INSERT INTO `visitor` SET 
				`title` = "' . $_COOKIE['PHPSESSID'] . '", 
				`lastActivity` = "' . date('Y-m-d H:i:s') . '", 
				`userId` = "' . ($_SESSION['userId'] ? $_SESSION['userId'] : 0) . '",
				`hidden` = "' . $hidden . '"
			');
			$visitors = $this->db->query('SELECT * FROM `visitor`')->fetchAll();
			$info['logged'] = array();
			$info['loggedIds'] = array();
			$info['guests'] = 0;
			$info['hidden'] = 0;
			foreach ($visitors as $visitor) {
				if ($visitor['hidden']) {
					$info['hidden']++;
				} else if ($visitor['userId']) {
					$info['logged'][] = $visitor['userId'];
					$info['loggedIds'][] = $visitor['userId'];
				} else {
					$info['guests']++;
				}
			}
			$users = Misc::loadModel('User')->fetchAll('`id` IN ("' . implode('","', $info['logged']) . '")')->toArray();
			$logged = array();
			foreach ($users as $user) {
				$logged[] = '<a href="/users/details/id/' . $user['id'] . '/">' . $user['title'] . '</a>';
			}
			$info['logged'] = $logged;
			$info['total'] = count($visitors);
			return $info;
		}
	}
	public function dispatch() {

		header('Content-Type: text/html; charset=utf-8');
		$this->preDispatch();
		eval('$this->' . $this->params['action'] . 'Action();');
		$this->postDispatch();
	}
	public function preDispatch(){}
	public function postDispatch(){}

}