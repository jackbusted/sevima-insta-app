<?php

namespace App\Models;

use App\Models\User;
use App\Models\PostLikeModel;
use App\Models\CommentModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class PostModel extends Model
{
    use SoftDeletes;

    protected $table = 'posts';

    public $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected $fillable = [
        'user_id',
        'caption',
        'image',
        'visibility',
        'deleted_at',
        'deleted_by',
        'updated_at',
        'updated_by',
    ];

    protected $guarded = [
        'id'
    ];

    public function getUpdatedAtAttribute($value)
    {
        return $this->asDateTime($value)->diffForHumans();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function likes()
    {
        return $this->hasMany(PostLikeModel::class, 'post_id');
    }

    public function comments()
    {
        return $this->hasMany(CommentModel::class, 'post_id');
    }

    // apakah post ini sudah dilike user
    public function isLikedBy($userId)
    {
        return $this->likes->where('user_id', $userId)->count() > 0;
    }
}
