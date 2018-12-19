<?php
namespace mf\router;

class Router extends AbstractRouter {
    
    public function __construct() {
        parent::__construct();
    }
    
    public function run() {
        $url = $this->http_req->path_info;
        $access_level = new \mf\auth\Authentification;
        if(isset($url) && $access_level->checkAccessRight(self::$routes[$url][2])) {
            if (array_key_exists($url, self::$routes)) {
                $curRoute = new self::$routes[$url][0];
                $methoToUse = self::$routes[$url][1];
                $curRoute->$methoToUse();
            } else {
                $defRoute = self::$aliases['default'];
                $curRoute = new self::$routes[$defRoute][0];
                $methoToUse = self::$routes[$defRoute][1];
                $curRoute->$methoToUse();
            }
        }
        else {
            $defRoute = self::$aliases['default'];
            $curRoute = new self::$routes[$defRoute][0];
            $methoToUse = self::$routes[$defRoute][1];
            $curRoute->$methoToUse();
        }
    }
    
    public function urlFor($route_name, $param_list=[]) {
        $finalURL = $route_name.self::$routes[$route_name][0];
        if(!empty($param_list)) {
            $finalURL .= '?';
            foreach($param_list as $key => $val) {
                $finalURL .= $key.'='.$val.'&';
            }
        }
        return $finalURL;
    }

    public function setDefaultRoute($url) {
        self::$aliases['default'] = $url;
    }
    
    public function addRoute($name, $url, $ctrl, $mth, $lvl) {
        self::$routes[$url] = [$ctrl, $mth, $lvl];
        
        self::$aliases[$name] = $url;
    }
    
    public static function executeRoute($alias) {
        $newRoute = self::$routes[$alias][0];
        $methoToUse = self::$routes[$alias][1];
        $curRoute = new $newRoute;
        $curRoute->$methoToUse();
    }
}