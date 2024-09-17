<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Teacher extends Model
{
    use HasFactory, HasApiTokens;

    protected $primaryKey = 'teacher_id';

    protected $fillable = [
        'teacher_id',
        'admin_id',
        'user_name',
        'password',
        'language',
        'stream',
        'contact',
    ];

    public function admins()
    {
        return $this->belongsTo(User::class, 'id');
    }

    public function DistrictDetails()
    {
        return $this->hasMany(District_detail::class, 'teacher_id');
    }

    public function Marks()
    {
        return $this->hasMany(Marks::class, 'teacher_id');
    }
}
