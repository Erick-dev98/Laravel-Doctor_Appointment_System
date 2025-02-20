<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Specialities;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function loadDoctorBySpeciality($speciality_id)
    {
        $id = $speciality_id;
        $speciality = Specialities::find($id);

        return view('patient.doctor-by-speciality', compact('id','speciality'));
    }
    public function loadMyAppointments()
    {
        return view('patient.my-appointments');
    }

    public function loadArticles()
    {
        return view('patient.articles');
    }

    public function loadBookingPage($id)
    {
        // Calling the relationship speciality and doctorUser
        $doctor = Doctor::with('speciality','doctorUser')->where('id',$id)->first();
        // Now let's load the details of the doctor
        return view('patient.booking-page',compact('doctor'));
    }

    public function loadAllDoctors(){
        return view('patient.all-doctors');
    }
}
