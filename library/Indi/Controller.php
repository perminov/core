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
		$this->post = Indi::registry('post');
		$this->get = Indi::registry('get');
		$this->files = Indi::registry('files');

		// set up db adapter
		$this->db = Indi::db();

		Indi::uri()->section = $params['section'];
		$this->action     = $params['action'];
		$this->module     = $params['module'];
		$this->identifier = $params['id'];

        // if action == index, set up page number
		if ($this->action == 'index') {
			$this->page     = $params['page'] ? $params['page'] : 1;
		}
		$this->view = new Indi_View();

		$coreS = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . STD . '/core/' . trim(Indi::ini('view')->scriptPath, '/');
		$wwwS  = preg_replace('/core(\/application)/', 'www$1', $coreS);

		if(is_dir($wwwS)) {
			$this->view->setScriptPath($coreS . ($this->module != 'front' ? '/' . $this->module : ''));
			$this->view->addScriptPath($wwwS . ($this->module != 'front' ? '/' . $this->module : ''));
		} else {
			$this->view->setScriptPath($coreS . ($this->module != 'front' ? '/' . $this->module : ''));
		}

		$coreH = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . STD . '/core/library';
		$wwwH  = preg_replace('/core(\/library)/', 'www$1', $coreH);

        $this->view->addHelperPath($coreH . '/Indi/View/Helper' . ($this->module != 'front' ? '/' . ucfirst($this->module) : ''), 'Indi_View_Helper_'. ($this->module != 'front' ? ucfirst($this->module) . '_' : ''));
        if (is_dir($wwwH)) $this->view->addHelperPath($wwwH . '/Project/View/Helper' . ($this->module != 'front' ? '/' . ucfirst($this->module) : ''), 'Project_View_Helper_'. ($this->module != 'front' ? ucfirst($this->module) . '_' : ''));

		Indi::registry('view', $this->view);

        $this->params = $params;
		$this->action = $params['action'];
	}

    public function adjustPrimaryWHERE($where)
    {
        return $where;
    }
	public function _redirect($location){
		die('<script>window.parent.Indi.load("' . $location . '");</script>');
	}

	public function dispatch() {
        header('Content-Type: text/html; charset=utf-8');
        $this->preDispatch();
        eval('$this->' . $this->params['action'] . 'Action();');
        $this->postDispatch();
    }

    public function preDispatch(){
    }
    public function postDispatch(){
    }
}