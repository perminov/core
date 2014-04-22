<?php
class Indi_Uri_Base {

    public function dispatch(){

        $this->preDispatch();

        $params = Indi::uri();

        $sectionR = Indi::model('Section')->fetchRow('`alias` = "' . $params['section'] . '"');

        $this->trailingSlash();

        $controllerClass = 'Admin_' . ucfirst($params['section']) . 'Controller';

        if (!class_exists($controllerClass)) {

            $extendClass = $sectionR ? $sectionR->extends : 'Project_Controller_Admin';

            if (!class_exists($extendClass)) $extendClass = 'Indi_Controller_Admin';

            eval('class ' . ucfirst($controllerClass) . ' extends ' . $extendClass . '{}');
        }

        $controller = new $controllerClass($params);

        $controller->dispatch();
    }

    public function preDispatch() {
        $this->no3w();
        $this->setCookieDomain();
        session_start();
    }

    public function setCookieDomain(){
        $domain = Indi::ini()->general->domain;
        if (strpos($domain, '.') !== false) ini_set('session.cookie_domain', '.' . $domain);
        if (STD) ini_set('session.cookie_path', STD);
    }

    public function no3w(){
        if (strpos($_SERVER['HTTP_HOST'], 'www') !== false) {
            header('HTTP/1.1 301 Moved Permanently');
            die(header('Location: http://' . str_replace('www.', '', $_SERVER['HTTP_HOST']) . $_SERVER['REQUEST_URI']));
        }
    }

    public function trailingSlash(){
        if ($_SERVER['REQEST_URI'] != '/' && !preg_match('~/$~', $_SERVER['REQUEST_URI']) && !preg_match('/\?/', $_SERVER['REQUEST_URI'])) {
            header('HTTP/1.1 301 Moved Permanently');
            header('Location: ' . $_SERVER['REQUEST_URI'] . '/');
            die();
        }
    }
}