<?php
/**
 * Indi Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Indi
 * @package    IndiProjectView
 * @copyright  Copyright (c) 2005-2010 Indi Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: View.php 20096 2010-01-06 02:05:09Z bkarwin $
 */


/**
 * Abstract master class for extension.
 */
require_once 'Indi/View/Abstract.php';

/**
 * Concrete class for handling view scripts.
 *
 * @category   Indi
 * @package    IndiProjectView
 * @copyright  Copyright (c) 2005-2010 Indi Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Indi_View extends Indi_View_Abstract
{
    /**
     * Whether or not to use streams to mimic short tags
     * @var bool
     */
    private $_useViewStream = false;

    /**
     * Whether or not to use stream wrapper if short_open_tag is false
     * @var bool
     */
    private $_useStreamWrapper = false;

    /**
     * Constructor
     *
     * Register Indi_View_Stream stream wrapper if short tags are disabled.
     *
     * @param  array $config
     * @return void
     */
    public function __construct($config = array())
    {
        $this->_useViewStream = (bool) ini_get('short_open_tag') ? false : true;
        if ($this->_useViewStream) {
            if (!in_array('zend.view', stream_get_wrappers())) {
                require_once 'Indi/View/Stream.php';
                stream_wrapper_register('zend.view', 'Indi_View_Stream');
            }
        }

        if (array_key_exists('useStreamWrapper', $config)) {
            $this->setUseStreamWrapper($config['useStreamWrapper']);
        }

        parent::__construct($config);
    }

    /**
     * Set flag indicating if stream wrapper should be used if short_open_tag is off
     *
     * @param  bool $flag
     * @return Indi_View
     */
    public function setUseStreamWrapper($flag)
    {
        $this->_useStreamWrapper = (bool) $flag;
        return $this;
    }

    /**
     * Should the stream wrapper be used if short_open_tag is off?
     *
     * @return bool
     */
    public function useStreamWrapper()
    {
        return $this->_useStreamWrapper;
    }

    /**
     * Includes the view script in a scope with only public $this variables.
     *
     * @param string The view script to execute.
     */
    protected function _run()
    {
        if ($this->_useViewStream && $this->useStreamWrapper()) {
            include 'zend.view://' . func_get_arg(0);
        } else {
            include func_get_arg(0);
        }
    }

	public function prepareForJs($helper){
		$html = $this->$helper();
		$html = explode("\r\n", trim($html));
		for($i = 0; $i < count($html); $i++) $html[$i] = str_replace("'", "\'",$html[$i]);
//		return implode(" + ", $html);
		return implode("", $html);
	}
}
