<?php
session_start();

require_once 'vendor/autoload.php';
require_once 'src/mf/utils/ClassLoader.php';
$loader = new mf\utils\ClassLoader("src");
$loader->register();

$config = parse_ini_file("conf/config.ini");

$db = new Illuminate\Database\Capsule\Manager();
$db->addConnection($config); /* configuration avec nos paramètres */
$db->setAsGlobal();          /* visible de tout fichier */
$db->bootEloquent();         /* établir la connexion */

\tweeterapp\view\TweeterView::addStyleSheet('html/style.css');




$ctrl = new tweeterapp\control\TweeterController();

$router = new \mf\router\Router();

$router->addRoute('home',
                  '/home/',
                  '\tweeterapp\control\TweeterController',
                  'viewHome',
    \tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_NONE);

$router->addRoute('tweet',
                  '/tweet/',
                  '\tweeterapp\control\TweeterController',
                  'viewTweet',
    \tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_NONE);
                  
$router->addRoute('user',
                  '/user/',
                  '\tweeterapp\control\TweeterController',
                  'viewUserTweets',
    \tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_NONE);

$router->addRoute('post',
                  '/post/',
                  '\tweeterapp\control\TweeterController',
                  'viewFormNewTweet',
    \tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_USER);

$router->addRoute('send',
                  '/send/',
                  '\tweeterapp\control\TweeterController',
                  'sendInfos',
    \tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_USER);

$router->addRoute('signup',
                  '/signup/',
                  '\tweeterapp\control\TweeterController',
                  'viewFormSignUp',
    \tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_NONE);

$router->addRoute('checksignup',
                  '/checksignup/',
                  '\tweeterapp\control\TweeterAdminController',
                  'checkSignUp',
    \tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_NONE);

$router->addRoute('login',
                  '/login/',
                  '\tweeterapp\control\TweeterController',
                  'viewLogin',
    \tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_NONE);

$router->addRoute('checklogin',
                  '/checklogin/',
                  '\tweeterapp\control\TweeterAdminController',
                  'checkLogin',
    \tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_NONE);

$router->addRoute('logout',
                  '/logout/',
                  '\tweeterapp\control\TweeterAdminController',
                  'logout',
    \tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_USER);

$router->addRoute('following',
                  '/following/',
                  '\tweeterapp\control\TweeterAdminController',
                  'following',
    \tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_USER);

$router->addRoute('listUsersByFollow',
                  '/listusersbyfollow/',
                  '\tweeterapp\control\TweeterAdminController',
                  'listUsersByFollow',
    \tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_ADMIN);

$router->addRoute('listFollowers',
                  '/listfollowers/',
                  '\tweeterapp\control\TweeterAdminController',
                  'listFollowers',
    \tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_ADMIN);

$router->addRoute('listUsersByInfluence',
                  '/listusersbyinfluence/',
                  '\tweeterapp\control\TweeterAdminController',
                  'listUsersByInfluence',
    \tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_ADMIN);

$router->addRoute('like',
                  '/like/',
                  '\tweeterapp\control\TweeterAdminController',
                  'like',
    \tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_USER);

$router->addRoute('dislike',
                  '/dislike/',
                  '\tweeterapp\control\TweeterAdminController',
                  'dislike',
    \tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_USER);

$router->addRoute('follow',
                  '/follow/',
                  '\tweeterapp\control\TweeterAdminController',
                  'follow',
    \tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_USER);

$router->setDefaultRoute('/home/');

$router->run();