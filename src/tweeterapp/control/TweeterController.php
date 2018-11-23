<?php

namespace tweeterapp\control;

/* Classe TweeterController :
 *  
 * Réalise les algorithmes des fonctionnalités suivantes: 
 *
 *  - afficher la liste des Tweets 
 *  - afficher un Tweet
 *  - afficher les tweet d'un utilisateur 
 *  - afficher la le formulaire pour poster un Tweet
 *  - afficher la liste des utilisateurs suivis 
 *  - évaluer un Tweet
 *  - suivre un utilisateur
 *   
 */

use mf\utils\httpRequest;

class TweeterController extends \mf\control\AbstractController {


    /* Constructeur :
     * 
     * Appelle le constructeur parent
     *
     * c.f. la classe \mf\control\AbstractController
     * 
     */
    
    public function __construct(){
        parent::__construct();
    }


    /* Méthode viewHome : 
     * 
     * Réalise la fonctionnalité : afficher la liste de Tweet
     * 
     */
    
    public function viewHome(){
        $tweets = \tweeterapp\model\Tweet::select()->orderBy('created_at', 'desc')->get();
        $view = new \tweeterapp\view\TweeterView($tweets);
        $renderHome = 'renderHome';
        $view->render($renderHome);
        
        /* Algorithme :
         *  
         *  1 Récupérer tout les tweet en utilisant le modèle Tweet
         *  2 Parcourir le résultat 
         *      afficher le text du tweet, l'auteur et la date de création
         *  3 Retourner un block HTML qui met en forme la liste
         * 
         */

    }


    /* Méthode viewTweet : 
     *  
     * Réalise la fonctionnalité afficher un Tweet
     *
     */
    
    public function viewTweet(){
        $webRequest = new \mf\utils\HttpRequest();
        $tweetId = $webRequest->get;
        
        $tweet = \tweeterapp\model\Tweet::select()->where('id', '=', $tweetId)->first();
        $view = new \tweeterapp\view\TweeterView($tweet);
        $renderViewTweet = 'renderViewTweet';
        $view->render($renderViewTweet);

        /* Algorithme : 
         *  
         *  1 L'identifiant du Tweet en question est passé en paramètre (id) 
         *      d'une requête GET 
         *  2 Récupérer le Tweet depuis le modèle Tweet
         *  3 Afficher toutes les informations du tweet 
         *      (text, auteur, date, score)
         *  4 Retourner un block HTML qui met en forme le Tweet
         * 
         *  Erreurs possibles : (*** Ã  implanter ultérieurement ***)
         *    - pas de paramètre dans la requête
         *    - le paramètre passé ne correspond pas a un identifiant existant
         *    - le paramètre passé n'est pas un entier 
         * 
         */

    }


    /* Méthode viewUserTweets :
     *
     * Réalise la fonctionnalité afficher les tweet d'un utilisateur
     *
     */
    
    public function viewUserTweets(){
        $webRequest = new \mf\utils\HttpRequest();
        $userId = $webRequest->get;
        
        $user = \tweeterapp\model\User::select()->where('id', '=', $userId)->first();
//        $view = new \tweeterapp\view\TweeterView($user);
//        echo $view->renderUserTweets();
        $view = new \tweeterapp\view\TweeterView($user);
        $renderUserTweets = 'renderUserTweets';
        $view->render($renderUserTweets);
        
        /*
         *
         *  1 L'identifiant de l'utilisateur en question est passé en 
         *      paramètre (id) d'une requête GET 
         *  2 Récupérer l'utilisateur et ses Tweets depuis le modèle 
         *      Tweet et User
         *  3 Afficher les informations de l'utilisateur 
         *      (non, login, nombre de suiveurs) 
         *  4 Afficher ses Tweets (text, auteur, date)
         *  5 Retourner un block HTML qui met en forme la liste
         *
         *  Erreurs possibles : (*** Ã  implanter ultérieurement ***)
         *    - pas de paramètre dans la requête
         *    - le paramètre passé ne correspond pas a un identifiant existant
         *    - le paramètre passé n'est pas un entier 
         * 
         */
        
    }
    
    
    /* Méthode viewFormNewTweet
     * 
     * Réalise la fonctionnalité afficher le formulaire de nouveau tweet
     *
     */
    
    public function viewFormNewTweet() {
        $view = new \tweeterapp\view\TweeterView(null);
        $renderFormNewTweet = 'renderFormNewTweet';
        $view->render($renderFormNewTweet);
    }
    
    
    
    /* Méthode sendInfos
     *
     * Réalise la fonctionnalité envoyer les infos du tweet à la BDD
     *
     */
    
    public function sendInfos() {
        $newTweet = new \tweeterapp\model\Tweet();
        $newTweet->text = filter_var($_POST['text'], FILTER_SANITIZE_STRING);
        $authorId = \tweeterapp\model\User::select()->where('username', 'like', $_SESSION['user_login'])->first();
        $newTweet->author = $authorId->id;
        $newTweet->save();
        $view = new \tweeterapp\view\TweeterView(null);
        $renderNewTweetSent = 'renderNewTweetSent';
        $view->render($renderNewTweetSent);
    }



    /* Méthode viewFormSignUp
     *
     * Réalise la fonctionnalité afficher le formulaire d'inscription
     *
     */

    public function viewFormSignUp() {
        $view = new \tweeterapp\view\TweeterView(null);
        $renderFormSignUp = 'renderFormSignUp';
        $view->render($renderFormSignUp);
    }

    public function viewLogin() {
        $view = new \tweeterapp\view\TweeterView(null);
        $renderFormLogin = 'renderFormLogin';
        $view->render($renderFormLogin);
    }
}