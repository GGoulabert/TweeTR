<?php
namespace tweeterapp\model;

class Follow extends \Illuminate\Database\Eloquent\Model {
    protected $table = "follow";
    protected $primaryKey = "id";
    public $timestamps = false;

    public function following($userId, $authorId) {
        $followAction = new \tweeterapp\model\Follow();
        $followAction->follower = $userId;
        $followAction->followee = $authorId;
        $followAction->save();
    }
}