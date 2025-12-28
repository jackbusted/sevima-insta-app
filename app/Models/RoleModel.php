<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class RoleModel extends Model
{
    use SoftDeletes;

    protected $table = 'roles';

    public $dates = ['created_at', 'updated_at'];

    protected $guarded = [];

    public function user()
    {
        return $this->hasMany(User::class);
    }
}
