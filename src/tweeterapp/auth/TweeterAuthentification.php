<?php

namespace tweeterapp\auth;

use mf\auth\AuthentificationException;

class TweeterAuthentification extends \mf\auth\Authentification {

    /*
     * Classe TweeterAuthentification qui définie les méthodes qui dépendent
     * de l'application (liée à la manipulation du modèle User)
     *
     */

    /* niveaux d'accès de TweeterApp
     *
     * Le niveau USER correspond a un utilisateur inscrit avec un compte
     * Le niveau ADMIN est un plus haut niveau (non utilisé ici)
     *
     * Ne pas oublier le niveau NONE un utilisateur non inscrit est hérité
     * depuis AbstractAuthentification
     */
    const ACCESS_LEVEL_USER  = 100;
    const ACCESS_LEVEL_ADMIN = 200;

    /* constructeur */
    public function __construct(){
        parent::__construct();
    }

    /* La méthode createUser
     *
     *  Permet la création d'un nouvel utilisateur de l'application
     *
     *
     * @param : $username : le nom d'utilisateur choisi
     * @param : $pass : le mot de passe choisi
     * @param : $fullname : le nom complet
     * @param : $level : le niveaux d'accès (par défaut ACCESS_LEVEL_USER)
     *
     * Algorithme :
     *
     *  Si un utilisateur avec le même nom d'utilisateur existe déjà en BD
     *     - soulever une exception
     *  Sinon
     *     - créer un nouvel modèle User avec les valeurs en paramètre
     *       ATTENTION : Le mot de passe ne doit pas Ãªtre enregistré en clair.
     *
     */

    public function createUser($username, $pass, $fullname,
                               $level=self::ACCESS_LEVEL_USER) {
        $matchUsername = \tweeterapp\model\User::select()
            ->where('username', 'like', $username)
            ->first();
        if(!is_null($matchUsername)) {
            throw new \mf\auth\exception\AuthentificationException();
        }
        else {
            $newUser = new \tweeterapp\model\User();
            $newUser->fullname = filter_var($fullname, FILTER_SANITIZE_STRING);
            $newUser->username = filter_var($username, FILTER_SANITIZE_STRING);
            $newUser->password = \mf\auth\Authentification::hashPassword($pass);
            $newUser->level = $level;
            $newUser->followers = 0;
            self::updateSession($username, $level);
            $newUser->save();
        }
    }

    /* La méthode loginUser
     *
     * permet de connecter un utilisateur qui a fourni son nom d'utilisateur
     * et son mot de passe (depuis un formulaire de connexion)
     *
     * @param : $username : le nom d'utilisateur
     * @param : $password : le mot de passe tapé sur le formulaire
     *
     * Algorithme :
     *
     *  - Récupérer l'utilisateur avec l'identifiant $username depuis la BD
     *  - Si aucun de trouvé
     *      - soulever une exception
     *  - sinon
     *      - réaliser l'authentification et la connexion
     *
     */

    public function loginUser($username, $password){
        $matchUser = \tweeterapp\model\User::select()
            ->where('username', 'like', $username)
            ->first();
        if(is_null($matchUser)) {
            throw  new \mf\auth\exception\AuthentificationException();
        }

        if($this->verifyPassword($password, $matchUser->password) === false) {
            throw  new \mf\auth\exception\AuthentificationException();
        }

        $this->login($username, $matchUser->password, $password, $matchUser->level);
    }

}
