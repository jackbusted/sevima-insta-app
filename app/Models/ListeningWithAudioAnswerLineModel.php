<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class ListeningWithAudioAnswerLineModel extends Model
{
    use SoftDeletes;

    protected $table = 'listening_with_audio_answer_lines';

    public $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected $fillable = [
        'listening_question_id',
        'name',
        'right_answer',
        'is_answered',
    ];
}
