<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class ListeningDataModel extends Model
{
    use SoftDeletes;

    protected $table = 'listening_data';

    public $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected $fillable = [
        'question_id',
        'title',
        'user_id',
        'user_name'
    ];
}
