<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class ReadingAnswerLineModel extends Model
{
    use SoftDeletes;

    protected $table = 'reading_answer_lines';

    public $dates = ['created_at', 'updated_at', 'deleted_at'];
}
