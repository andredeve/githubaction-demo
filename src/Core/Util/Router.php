<?php

namespace Core\Util;

use App\Controller\IndexController;
use ReflectionClass;

Class Router {

    public static function route(Request $request) {
        
        $controller_class = "\App\Controller\\" . $request->getController() . 'Controller';
        $method = $request->getMethod();
        $args = $request->getArgs();
        $controllerFile = APP_PATH . 'src/App/Controller/' . $request->getController() . 'Controller.php';
        if (is_readable($controllerFile) && !(new ReflectionClass($controller_class))->isAbstract()) {
            $controller_class = new $controller_class();
            $method = (is_callable(array($controller_class, $method))) ? $method : 'error404';
            if (!empty($args)) {
                call_user_func_array(array($controller_class, $method), $args);
            } else {
                call_user_func(array($controller_class, $method));
            }
        } else {
            (new IndexController())->error404();
        }
        return;
    }

}
