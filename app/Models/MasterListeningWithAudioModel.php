<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class MasterListeningWithAudioModel extends Model
{
    use SoftDeletes;

    protected $table = 'master_listening_with_audio_data';

    public $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected $fillable = [
        'story_audio_id',
        'schedule_id',
        'user_id',
        'user_name',
        'is_listened',
        'category_id',
    ];
}
