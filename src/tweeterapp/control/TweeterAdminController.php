<?php

namespace tweeterapp\control;

class TweeterAdminController extends \mf\control\AbstractController {
    public function __construct() {
        parent::__construct();
    }


    /* Méthode viewCheckSignUp
     *
     * Réalise la fonctionnalité de vérifier si le suername n'est pas déjà pris
     * Si non, enregistre le nouvel utilisateur
     * Si oui, retourne une exception
     *
     */

    public function checkSignUp() {

        try {
            if(!isset($this->request->post['pass']) || !isset($this->request->post['passVerif']))
                throw new \mf\auth\exception\AuthentificationException();

            if($this->request->post['pass'] !== $this->request->post['passVerif'])
                throw new \mf\auth\exception\AuthentificationException();

            $username = $this->request->post['username'];
            $fullname = $this->request->post['fullname'];
            $pass = $this->request->post['pass'];
            $level = \tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_USER;
            $controlAuth = new \tweeterapp\auth\TweeterAuthentification();

            $controlAuth->createUser($username, $pass, $fullname, $level);

            $tweets = \tweeterapp\model\Tweet::all();
            $view = new \tweeterapp\view\TweeterView($tweets);
            $renderHome = 'renderHome';
            $view->render($renderHome);

        }
        catch (\mf\auth\exception\AuthentificationException $e) {
                \mf\router\Router::executeRoute('signup');
        }
    }

    /* Méthode checkLogin
     *
     * Réalise la fonctionnalité de vérifier si le username et le mdp sont corrects
     * Si oui, connecte l'utilisateur
     * Si non, retourne une exception
     *
     */

    public function checkLogin() {
        $username = $this->request->post['username'];
        $password = $this->request->post['password'];
        $controlAuth = new \tweeterapp\auth\TweeterAuthentification();
        try {
            $controlAuth->loginUser($username, $password);
            $userConnected = \tweeterapp\model\User::select()->where('username', '=', $_SESSION['user_login'])->first();
            $view = new \tweeterapp\view\TweeterView($userConnected);
            $renderFollowing = 'renderFollowing';
            $view->render($renderFollowing);
        }
        catch (\mf\auth\exception\AuthentificationException $e) {
            \mf\router\Router::executeRoute('login');
        }
    }


    /* Méthode following
     *
     * Réaliase la fonctionnalité d'afficher la liste des gens que l'utilisateur suit
     *
     */

    public function following() {
        $userConnected = \tweeterapp\model\User::select()->where('username', '=', $_SESSION['user_login'])->first();
        $view = new \tweeterapp\view\TweeterView($userConnected);
        $renderFollowing = 'renderFollowing';
        $view->render($renderFollowing);
    }


    /* Méthode logout
     *
     * Réalise la fonctionnalité de déconnecter un utilisateur connecté
     *
     */

    public function logout() {
        unset($_SESSION['user_login']);
        $_SESSION['access_level'] = \mf\auth\AbstractAuthentification::ACCESS_LEVEL_NONE;
        $tweets = \tweeterapp\model\Tweet::all();
        $view = new \tweeterapp\view\TweeterView($tweets);
        $renderHome = 'renderHome';
        $view->render($renderHome);
    }

    public function listUsersByFollow() {
        $listUserByFollow = \tweeterapp\model\User::select()->orderBy('followers', 'desc')->get();
        $view = new \tweeterapp\view\TweeterView($listUserByFollow);
        $renderListUsersByFollow = 'renderListUsersByFollow';
        $view->render($renderListUsersByFollow);
    }

    public function listFollowers() {
        $webRequest = new \mf\utils\HttpRequest();
        $userId = $webRequest->get;

        $thisUser = \tweeterapp\model\User::select()->where('id', '=', $userId)->first();
        $view = new \tweeterapp\view\TweeterView($thisUser);
        $renderListFollowers = 'renderListFollowers';
        $view->render($renderListFollowers);
    }

    public function listUsersByInfluence() {
        $listUserByInfluence = \tweeterapp\model\User::select()->orderBy('followers', 'desc')->get();
        $view = new \tweeterapp\view\TweeterView($listUserByInfluence);
        $renderListUsersByInfluence = 'renderListUsersByInfluence';
        $view->render($renderListUsersByInfluence);
    }


    public function like() {
        $webRequest = new \mf\utils\HttpRequest();
        $tweet = $webRequest->get;
        $tweetId = $tweet['id'];
        $user = \tweeterapp\model\User::select()->where('username', '=', $_SESSION['user_login'])->first();
        $userId = $user['id'];
        $newLike = new \tweeterapp\model\Like();
        $newLike->like($tweetId, $userId);
        $updateTweet = new \tweeterapp\model\Tweet();
        $updateTweet->updateTweetLike($tweetId);
        $tweetToRender = \tweeterapp\model\Tweet::select()->where('id', '=', $tweetId)->first();
        $this->http_req->path_info = '/tweet/';
        $view = new \tweeterapp\view\TweeterView($tweetToRender);
        $renderViewTweet = 'renderViewTweet';
        $view->render($renderViewTweet);
    }

    public function dislike() {
        $webRequest = new \mf\utils\HttpRequest();
        $tweet = $webRequest->get;
        $tweetId = $tweet['id'];
        $user = \tweeterapp\model\User::select()->where('username', '=', $_SESSION['user_login'])->first();
        $userId = $user['id'];
        $newLike = new \tweeterapp\model\Like();
        $newLike->like($tweetId, $userId);
        $updateTweet = new \tweeterapp\model\Tweet();
        $updateTweet->updateTweetDislike($tweetId);
        $tweetToRender = \tweeterapp\model\Tweet::select()->where('id', '=', $tweetId)->first();
        $view = new \tweeterapp\view\TweeterView($tweetToRender);
        $renderViewTweet = 'renderViewTweet';
        $view->render($renderViewTweet);
    }

    public function follow() {
        $userConnected = \tweeterapp\model\User::select()->where('username', '=', $_SESSION['user_login'])->first();
        $checkIfNotFollowing = \tweeterapp\model\Follow::select()->where('follower', '=', $userConnected['id'])->first();
        if (count($checkIfNotFollowing) < 1) {
            $userId = $userConnected['id'];
            $webRequest = new \mf\utils\HttpRequest();
            $author = $webRequest->get;
            $authorId = $author['id'];
            $following = new \tweeterapp\model\Follow();
            $following->following($userId, $authorId);
            $followersUpdate = new \tweeterapp\model\User();
            $followersUpdate->followersUpdate($authorId);
            $allTweets = \tweeterapp\model\Tweet::select()->get();
            $view = new \tweeterapp\view\TweeterView($allTweets);
            $renderFollowing = 'renderFollowing';
            $view->render($renderFollowing);
        }
        else {
            $allTweets = \tweeterapp\model\Tweet::select()->get();
            $view = new \tweeterapp\view\TweeterView($allTweets);
            $renderFollowing = 'renderFollowing';
            $view->render($renderFollowing);
        }
    }
}