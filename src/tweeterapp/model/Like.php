<?php
namespace tweeterapp\model;

class Like extends \Illuminate\Database\Eloquent\Model {
    protected $table = "like";
    protected $primaryKey = "id";
    public $timestamps = false;

    public function like($tweetId, $userId) {
        $like = new \tweeterapp\model\Like();
        $like->user_id = $userId;
        $like->tweet_id = $tweetId;
        $like->save();
    }
}