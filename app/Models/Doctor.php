<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory;

    protected $fillable = [
        'bio',
        'hospital_name',
        'speciality_id',
        'user_id',
        'twitter',
        'linkedin',
        'experience',
    ];  

    // Creating a relationship between doctors and specialities
    public function speciality() {
        return $this->belongsTo(Specialities::class,'speciality_id');
    }

    // Creating a relationship between doctor amd users table
    public function doctorUser() {
        return $this->belongsTo(User::class,'user_id');
    }
}
