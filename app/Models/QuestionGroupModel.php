<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class QuestionGroupModel extends Model
{
    use SoftDeletes;

    protected $table = 'question_groups';

    public $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected $fillable = [
        'name',
        'active',
    ];

    protected $guarded = [
        'id'
    ];

    public function getUpdatedAtAttribute($value)
    {
        return $this->asDateTime($value)->diffForHumans();
    }
}
