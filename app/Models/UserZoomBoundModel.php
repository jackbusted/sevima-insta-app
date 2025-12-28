<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class UserZoomBoundModel extends Model
{
    use SoftDeletes;

    protected $table = 'user_zoom_bound';

    public $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected $fillable = ['deleted_at', 'deleted_by', 'updated_at', 'updated_by', 'user_id', 'email', 'first_name', 'last_name'];
}
