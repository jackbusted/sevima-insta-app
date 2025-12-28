<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class PaymentModel extends Model
{
    use SoftDeletes;

    protected $table = 'payments';

    public $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected $fillable = ['deleted_at', 'deleted_by', 'updated_at', 'updated_by', 'status', 'reason'];

    public function getUpdatedAtAttribute($value)
    {
        return $this->asDateTime($value)->diffForHumans();
    }
}
