<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\QuestionModel;

class AnswerLineModel extends Model
{
    use SoftDeletes;

    protected $table = 'answer_lines';

    public $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected $fillable = [
        'question_id',
        'name', 
        'right_answer'
    ];

    protected $guarded = [
        'id'
    ];

    public function question()
    {
        return $this->belongsTo(QuestionModel::class);
    }
}
