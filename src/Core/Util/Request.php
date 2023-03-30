<?php

namespace Core\Util;

/**
 * Classe Request
 */
Class Request {

    private $_controller;
    private $_method;
    private $_args;

    public function __construct($controller = null, $method = null, $args = null) {
        if ($controller == null) {
	        $parts = isset($_GET['url']) ? explode('/', $_GET['url']) : array();
            $parts = array_filter($parts);
            $this->_controller = ($c = array_shift($parts)) ? ucfirst($c) : 'Index';
            $this->_method = ($c = array_shift($parts)) ? $c : 'index';
            $this->_args = (isset($parts[0])) ? $parts : array();
        } else {
            $this->_controller = $controller;
            $this->_method = $method;
            $this->_args = $args;
        }
        
    }

    public function getController() {
        return $this->_controller;
    }

    public function getMethod() {
        return $this->_method;
    }

    public function getArgs() {
        return $this->_args;
    }

}
