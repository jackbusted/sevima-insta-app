<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class CommentLikeModel extends Model
{
    use SoftDeletes;

    protected $table = 'comment_likes';

    public $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected $fillable = [
        'comment_id',
        'user_id',
        'deleted_at',
        'deleted_by',
        'updated_at',
        'updated_by',
    ];

    protected $guarded = [
        'id'
    ];
}
