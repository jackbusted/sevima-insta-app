<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\CategoryModel;
use App\Models\AnswerLineModel;

class QuestionModel extends Model
{
    use SoftDeletes;

    protected $table = 'questions';

    public $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected $fillable = [
        'title', 
        'category_id',
        'email',
        'audio',
        'image',
        'question_words',
        'story_audio_id',
        'group_id',
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

    public function category()
    {
        return $this->belongsTo(CategoryModel::class);
    }

    public function answerLine()
    {
        return $this->hasMany(AnswerLineModel::class);
    }
}
