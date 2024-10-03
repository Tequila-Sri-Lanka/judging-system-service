<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $primaryKey = 'student_id';

    protected $fillable = [
        'student_id',
        'serial_no',
        'language',
        'district',
        'age',
        'image',
        'student_detail',
        'stream',
        'school',
        'studentName',
        'marking_status',
    ];

    public function Marks()
    {
        return $this->hasMany(Marks::class, 'student_id');
    }
}
