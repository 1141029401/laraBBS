<?php

namespace App\Models;
use App\Models\Topic;
use App\Models\User;

class Reply extends Model
{
    protected $fillable = [ 'content' ];

    //关联帖子
    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    //关联用户
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
