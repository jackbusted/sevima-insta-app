<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class ScoreModel extends Model
{
    use SoftDeletes;

    protected $table = 'scores';

    public $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected $fillable = ['user_id', 'schedule_id', 'score', 'admin_score', 'status', 'show_real_score', 'show_admin_score'];
}
