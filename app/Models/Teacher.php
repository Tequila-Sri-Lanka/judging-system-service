<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $primaryKey = 'teacher_id';

    protected $fillable = [
        'admin_id',
        'user_name',
        'password',
        'language',
    ];

    public function admins()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    public function DistrictDetails()
    {
        return $this->hasMany(District_detail::class, 'district_detail_id');
    }
}
