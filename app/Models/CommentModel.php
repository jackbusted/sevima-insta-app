<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class CommentModel extends Model
{
    use SoftDeletes;

    protected $table = 'comments';

    public $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected $fillable = [
        'post_id',
        'user_id',
        'parent_id',
        'content', 
        'deleted_at',
        'deleted_by',
        'updated_at',
        'updated_by',
    ];

    protected $guarded = [
        'id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
