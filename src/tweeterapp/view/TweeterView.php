<?php

namespace tweeterapp\view;

class TweeterView extends \mf\view\AbstractView {

    

    /* Constructeur 
    *
    * Appelle le constructeur de la classe parent
    */
    public function __construct( $data ){
        parent::__construct($data);
    }
    
    public function linkBase() {
        return 'http://127.0.0.1:8080/projects/TweeTR/main.php';
    }

    public function imagesLinkBase() {
        return 'http://127.0.0.1:8080/projects/TweeTR';
    }

    /* Méthode renderHeader
     *
     *  Retourne le fragment HTML de l'entête (unique pour toutes les vues)
     */
    private function renderHeader(){
        if(!isset($_SESSION['user_login'])) {
            $headerHTML = '<h1>MiniTweeTr</h1>
            <br>
            <nav id="navbar">
            <a class="home" href="'.self::linkBase().'/home/" title="Home"><img alt="home" src="'.self::imagesLinkBase().'/html/images/home.png" width="32px"></a>
            <br>
            <a class="login" href="'.self::linkBase().'/login/" title="Login"><img alt="login" src="'.self::imagesLinkBase().'/html/images/login.png" width="32px"></a>
            <br>
            <a class="signup" href="'.self::linkBase().'/signup/" title="Sign Up"><img src="'.self::imagesLinkBase().'/html/images/signup.png" width="32px"></a>
            </nav>';
            return $headerHTML;
        }
        else {
            $headerHTML = '<h1>MiniTweeTr</h1>
            <br>
            <nav id="navbar">
            <a class="home" href="'.self::linkBase().'/home/" title="Home"><img alt="home" src="'.self::imagesLinkBase().'/html/images/home.png" width="32px"></a>
            <br>
            <a class="following" href="'.self::linkBase().'/following/" title="Following"><img alt="following" src="'.self::imagesLinkBase().'/html/images/following.png" width="32px"></a>
            <br>
            <a class="post" href="'.self::linkBase().'/post/" title="Post"><img alt="post" src="'.self::imagesLinkBase().'/html/images/post.png" width="32px"></a>
            <br>
            <a class="logout" href="'.self::linkBase().'/logout/" title="Logout"><img src="'.self::imagesLinkBase().'/html/images/logout.png" width="32px"></a>
            </nav>';
            return $headerHTML;
        }
    }
    
    /* Méthode renderFooter
     *
     * Retourne le fragment HTML du bas de la page (unique pour toutes les vues)
     */
    private function renderFooter(){
        return 'La super app créée en Licence Pro &copy;2018';
    }

    /* Méthode renderHome
     *
     * Vue de la fonctionalité afficher tous les Tweets. 
     *  
     */
    
    private function renderHome(){
        $renderTweets = '<br><h2>Latest Tweets</h2><br>';

        foreach ($this->data as $tweet) {
            $author = $tweet->author()->first();
            $renderTweets .= '<div class="tweet"><a href="'.self::linkBase().'/user/?id='.$author['id'].'">'.$author['fullname'].' (@'.$author['username'].') :</a><br><a href="'.self::linkBase().'/tweet/?id='.$tweet->id.'">'.$tweet->text.'<br><br> Ecrit le '.$tweet->created_at.'<br></a></div>';
        }
        
        $homeHTML = $renderTweets;
        return $homeHTML;
        /*
         * Retourne le fragment HTML qui affiche tous les Tweets. 
         *  
         * L'attribut $this->data contient un tableau d'objets tweet.
         * 
         */
    }
  
    /* Méthode renderUserTweets
     *
     * Vue de la fonctionalité afficher tout les Tweets d'un utilisateur donné. 
     * 
     */
     
    private function renderUserTweets(){
        if (count($this->data->followedBy()->get()) <= 1) {
            $renderUserTweets = '<b>'.$this->data->fullname.'</b><br><br>'.$this->data->username.'<br><br>'.count($this->data->followedBy()->get()).' follower';
        }
        else {
            $renderUserTweets = '<b>'.$this->data->fullname.'</b><br><br>'.$this->data->username.'<br><br>'.count($this->data->followedBy()->get()).' followers';
        }
        $userTweets = $this->data->tweets()->get();
        foreach($userTweets as $tweet) {
            $renderUserTweets .= '<div class="tweet"><a href="'.self::linkBase().'/tweet/?id='.$tweet['id'].'">'.$tweet['text'].'<br><br>Ecrit le '.$tweet['created_at'].'<br></a></div>';
        }

        $userTweetsHTML = $renderUserTweets;
        return $userTweetsHTML;
        /*
         * Retourne le fragment HTML pour afficher
         * tous les Tweets d'un utilisateur donné.
         *
         * L'attribut $this->data contient un objet User.
         *
         */

    }
  
    /* Méthode renderViewTweet 
     * 
     * Réalise la vue de la fonctionnalité affichage d'un tweet
     *
     */
    
    private function renderViewTweet(){
        if(!isset($_SESSION['user_login'])) {
            $author = $this->data->author()->first();

            $viewTweetHTML = '<div class="tweet"><a href="'.self::linkBase().'/user/?id=' . $author['id'] . '">' . $author['fullname'] . ' (@' . $author['username'] . ') :</a>
                          <br>
                          <a href="'.self::linkBase().'/tweet/?id=' . $this->data->id . '">' . $this->data->text . '
                          <br>
                          <br>
                          Ecrit le ' . $this->data->created_at . '
                          <br>
                          </a>
                          <hr>
                          <div><h1 class="score">' . $this->data->score . '</h1></div></div>';

            return $viewTweetHTML;

            /* Méthode renderViewTweet
             *
             * Retourne le fragment HTML qui réalise l'affichage d'un tweet
             * en particulié
             *
             * L'attribut $this->data contient un objet Tweet
             *
             */
        }
        else {
            $author = $this->data->author()->first();
            $connectedUser = \tweeterapp\model\User::select()->where('username', '=', $_SESSION['user_login'])->first();
            if ($author['id'] !== $connectedUser['id'] || $userAlreadyNote = \tweeterapp\model\Like::select()->where('user_id', '=', $connectedUser)) {
                $viewTweetHTML = '<div class="tweet"><a href="'.self::linkBase().'/user/?id=' . $author['id'] . '">' . $author['fullname'] . ' (@' . $author['username'] . ') :</a>
                          <br>
                          <a href="'.self::linkBase().'/tweet/?id=' . $this->data->id . '">' . $this->data->text . '
                          <br>
                          <br>
                          Ecrit le ' . $this->data->created_at . '
                          <br>
                          </a>
                          <hr>
                          <div><h1 class="tweet-control">' . $this->data->score . '</h1><a href="'.self::linkBase().'/like/?id='.$this->data->id.'" class="tweet-control"><img alt="Like" src="'.self::imagesLinkBase().'/html/images/like.png" width="32px"></a><a href="'.self::linkBase().'/dislike/?id='.$this->data->id.'" class="tweet-control"><img alt="Dislike" src="'.self::imagesLinkBase().'/html/images/dislike.png" width="32px"></a><a href="'.self::linkBase().'/follow/?id='.$author->id.'" class="tweet-control"><img alt="Follow" src="'.self::imagesLinkBase().'/html/images/follow.png" width="32px"></a></div></div>';
                return $viewTweetHTML;
            }
            else {
                $viewTweetHTML = '<div class="tweet"><a href="'.self::linkBase().'/user/?id=' . $author['id'] . '">' . $author['fullname'] . ' (@' . $author['username'] . ') :</a>
                          <br>
                          <a href="'.self::linkBase().'/tweet/?id=' . $this->data->id . '">' . $this->data->text . '
                          <br>
                          <br>
                          Ecrit le ' . $this->data->created_at . '
                          <br>
                          </a>
                          <hr>
                          <div class="tweet-control"><h1 class="score">' . $this->data->score . '</h1></div></div>';
                return $viewTweetHTML;
            }
        }
    }
    
    
    
    /* Méthode renderFormNewTweet
     *
     * Retourne le formulaire d'écriture d'un nouveau tweet
     *
     */
    
    private function renderFormNewTweet() {
        $formNewTweetHTML = '
        <div class="forms">
            <form method="POST" action="'.self::linkBase().'/send/">
                <textarea rows="5" cols="50" name="text" placeholder="Ecrivez votre tweet" maxlength="140"></textarea>
                <br>
                <input class="forms-button" type="submit" name="poster" value="Poster le tweet">
                <br>
            </form>
        </div>';
        return $formNewTweetHTML;
    }
    
    /* Méthode renderNewTweetSent
     *
     * Retourne la validation d'envoi du tweet
     *
     */
    
    private function renderNewTweetSent() {
        $newTweetSentHTML = '
        <div>
            Votre tweet a bien été envoyé.
        </div>
        <form action="'.self::linkBase().'/home/" method="POST">
            <input type="submit" name="home" value="Home">
        </form>
        ';
        return $newTweetSentHTML;
    }
    
    
    
    /* Méthode renderFormSignUp
     *
     * Retourne le formulaire d'inscription à TweeTR
     *
     */

    private function renderFormSignUp() {
        $formSignUpHTML = '
        <div class="forms">
            <form method="POST" action="'.self::linkBase().'/checksignup/">
                <label>Nom et prénom : </label><input type="text" class="forms-text" placeholder="Inscrivez votre nom et prénom" name="fullname" required>
                <br>
                <label>Pseudonyme : </label><input type="text" class="forms-text" placeholder="Inscrivez votre pseudo" name="username" required>
                <br>
                <label>Mot de passe : </label><input type="password" class="forms-text" name="pass" required>
                <br>
                <label>Retapez le mot de passe : </label><input type="password" class="forms-text" name="passVerif" required>
                <br>
                <input type="submit" class="forms-button" name="inscrire" value="S\'inscrire">
                <br>
            </form>
        </div>';
        return $formSignUpHTML;
    }

    private function renderSignUpDone() {
        $signUpDoneHTML = '
        <div>
            Votre compte a bien été créé.
            <form action="'.self::linkBase().'/home/" method="POST">
                <input type="submit" name="home" value="Home">
            </form>
        </div>';
        return $signUpDoneHTML;
    }

    private function renderSignUpErrorSignUp() {
        $signUpErrorHTML = '
        <div>
            Nom d\'utilisateur déjà utilisé ou mots de passe non identiques.
            <form action="'.self::linkBase().'/signup/" method="POST">
                <input type="submit" name="retour" value="Retour">
            </form>
        </div>';
        return $signUpErrorHTML;
    }

    private function renderFormLogin() {
        $formLoginHTML = '
        <div class="forms">
            <form action="'.self::linkBase().'/checklogin/" method="POST">
                <label>Pseudonyme : </label><input type="text" class="forms-text" name="username" placeholder="Entrez votre pseudonyme" required>
                <br>
                <label>Mot de passe : </label><input type="password" class="forms-text" name="password" required>
                <br>
                <input type="submit" name="login" value="Se connecter">
            </form>
        </div>';
        return $formLoginHTML;
    }

    private function renderSignUpErrorLogin() {
        $signUpErrorHTML = '
        <div>
            Nom d\'utilisateur ou mot de passe incorrect.
            <form action="'.self::linkBase().'/login/" method="POST">
                <input type="submit" name="retour" value="Retour">
            </form>
        </div>';
        return $signUpErrorHTML;
    }

    private function renderFollowing() {
        $followingHTML = '';
        $followeds = \tweeterapp\model\Follow::select()
            ->where('follower', '=', $this->data['id'])
            ->get();
        if(count($followeds) === 0) {
            $followingHTML = '<div>Vous ne suivez personne.</div>
                              <br>
                              <form action="'.self::linkBase().'/home/" method="post">
                                <input type="submit" name="home" value="Home">
                              </form>
                              </div>';
        }
        else {
            $followingHTML .= '<div>Vous suivez : <br>';
            foreach($followeds as $followed) {
                $followd = \tweeterapp\model\User::select()
                    ->where('id', '=', $followed['followee'])
                    ->first();
                $followingHTML .= '<br><div><a href="'.self::linkBase().'/user/?id='.$followd['id'].'">'.$followd['fullname'].'(@'.$followd['username'].')</a></div><br></a>';
            }
        }
        return $followingHTML;
    }

    private function renderListUsersByFollow() {
        $listUsersByFollowHTML = '<br>';
        foreach ($this->data as $user) {
            $listUsersByFollowHTML .= '<div class="tweet"><a href="'.self::linkBase().'/listfollowers/?id='.$user['id'].'">'.$user['fullname'].' (@'.$user['username'].') est suivi par '.$user['followers'].' personne(s)</a></div><br>';
        }
        return $listUsersByFollowHTML;
    }

    private function renderListFollowers() {
        $listFollowers = \tweeterapp\model\User::where('id', '=', $this->data['id'])->first();
        $listFollowerId = $listFollowers->followedBy()->get();
        if (count($listFollowerId) >= 1) {
            $listFollowersHTML = $this->data['fullname'].' (@'.$this->data['username'].') est suivi par : <br><br>';
            foreach ($listFollowerId as $follower) {
                $listFollowersHTML .= '<div><a href="http://127.0.0.1/projects/TweeTR/main.php/listfollowers/?id='.$follower['id'].'">'.$follower['fullname'].' (@'.$follower['username'].')</a></div>';
            }
        }
        else {
            $listFollowersHTML = $this->data['fullname'].' (@'.$this->data['username'].') n\'est suivi par personne';
        }
        return $listFollowersHTML;
    }

    private function renderListUsersByInfluence() {
        $listUsersByInfluence = '<br>';
        foreach ($this->data as $user) {
            $stillInflu = true;
            while ($stillInflu == true) {
                $listFollowers = \tweeterapp\model\User::where('id', '=', $user['id'])->first();
                $nbInflu = count($listFollowers->followedBy()->get());
                if (count($listFollowers->followedBy()->get()) > 0) {
                    foreach ($listFollowers->followedBy()->get() as $follower) {
                        if (count($follower->followedBy()->get()) > 0) {
                            $nbInflu += count($follower->followedBy()->get());
                        }
                        else {
                            $stillInflu = false;
                        }
                    }
                }
                else {
                    $stillInflu = false;
                }
            }
            $usersByInfluence[] = ['nbInflu' => $nbInflu, 'id' => $user['id']];
        }
        arsort($usersByInfluence);
        foreach ($usersByInfluence as $userInflu) {
            $thisUser = \tweeterapp\model\User::select()->where('id', '=', $userInflu['id'])->first();
            $listUsersByInfluence .= '<div class="tweet"><a href="'.self::linkBase().'/listfollowers/?id='.$thisUser['id'].'">La sphère d\'influence de '.$thisUser['fullname'].' (@'.$thisUser['username'].') est de '.$userInflu['nbInflu'].'</a></div><br>';
        }

        return $listUsersByInfluence;
    }




    /* Méthode renderBody
     *
     * Retourne le fragment HTML de la balise <body>. Elle est appelée
     * par la méthode héritée render.
     *
     */
    
    protected function renderBody($selector=null){
        $bodyHTML = '<header class="theme-backcolor1">'.$this->renderHeader().'</header>
                     <section class="theme-backcolor2">'.$this->$selector().'</section>
                     <footer class="theme-backcolor1">'.$this->renderFooter().'</footer>';
        return $bodyHTML;
        /*
         * voir la classe AbstractView
         * 
         */
        
    }
}
