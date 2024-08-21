<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function loadDoctorBySpeciality($speciality_id) 
    {
        $id = $speciality_id;
        return view('patient.doctor-by-speciality',compact('id'));
    }
}
