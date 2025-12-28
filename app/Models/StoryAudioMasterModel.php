<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class StoryAudioMasterModel extends Model
{
    use SoftDeletes;

    protected $table = 'story_audio_masters';

    public $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected $fillable = ['deleted_at', 'deleted_by', 'updated_at', 'updated_by', 'audio_name', 'audio_file'];

    public function getUpdatedAtAttribute($value)
    {
        return $this->asDateTime($value)->diffForHumans();
    }
}
