<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class ListeningWithAudioQuestionModel extends Model
{
    use SoftDeletes;

    protected $table = 'listening_with_audio_question_data';

    public $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected $fillable = [
        'master_audio_listening_id',
        'question_id',
        'is_listened',
    ];
}
