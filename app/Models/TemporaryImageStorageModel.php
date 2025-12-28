<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class TemporaryImageStorageModel extends Model
{
    use SoftDeletes;

    protected $table = 'temporary_image_storage';

    public $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected $fillable = [
        'image',
        'deleted_at',
        'deleted_by',
        'updated_at',
        'updated_by'
    ];

    protected $guarded = [
        'id'
    ];
}
