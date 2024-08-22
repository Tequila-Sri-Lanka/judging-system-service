<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    use HasFactory;

    protected $primaryKey = 'admin_id';

    protected $fillable = [
        'user_name',
        'password',
    ];

    public function teachers()
    {
        return $this->hasMany(Teacher::class, 'admin_id');
    }
}


