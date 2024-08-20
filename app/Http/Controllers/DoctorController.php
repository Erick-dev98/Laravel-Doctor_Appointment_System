<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DoctorController extends Controller
{
    //Function to load the doctor dashboard
    public function loadDoctorDashboard() 
    {
        return view('doctor.dashboard');
    }
}
