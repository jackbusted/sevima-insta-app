<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\QuestionModel;

class CategoryModel extends Model
{
    use SoftDeletes;

    protected $table = 'categories';

    public $dates = ['created_at', 'updated_at', 'deleted_at'];

    public function question()
    {
        return $this->hasMany(QuestionModel::class);
    }
}
