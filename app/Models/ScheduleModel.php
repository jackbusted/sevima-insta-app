<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class ScheduleModel extends Model
{
    use SoftDeletes;

    protected $table = 'schedules';

    public $dates = ['open_date', 'created_at', 'updated_at', 'deleted_at'];

    protected $fillable = ['deleted_at', 'deleted_by', 'updated_at', 'updated_by', 'status', 'meeting_url', 'join_url', 'meeting_id', 'name'];
}
