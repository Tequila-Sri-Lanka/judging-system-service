<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $primaryKey = 'teacher_id';

    protected $fillable = [
        'teacher_id',
        'id',
        'user_name',
        'password',
        'language',
        'stream',
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
