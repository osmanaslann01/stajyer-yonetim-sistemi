<?php


class Router
{

    private $routes = [];


    public function get($url, $action)
    {
        $this->routes['GET'][$url] = $action;
    }


    public function post($url, $action)
    {
        $this->routes['POST'][$url] = $action;
    }



    public function dispatch()
    {

        $url = $_GET['url'] ?? '';

        $method = $_SERVER['REQUEST_METHOD'];



        if(!isset($this->routes[$method][$url]))
        {
            die("Route bulunamadı: ".$url);
        }



        $action = $this->routes[$method][$url];



        if(is_string($action) && strpos($action,'@') !== false)
        {

            list($controller,$method)
            =
            explode('@',$action);



            $controllerFile =
            BASE_PATH .
            "/app/controllers/"
            .$controller
            .".php";



            require_once $controllerFile;



            $controllerObject =
            new $controller();



            $controllerObject->$method();



        }


        elseif(is_callable($action))
        {

            call_user_func($action);

        }


    }


}