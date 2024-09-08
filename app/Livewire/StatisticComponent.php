<?php

namespace App\Livewire;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Component;

class StatisticComponent extends Component
{
    public $users_count = 0;
    public $doctors_count = 0;
    public $patients_count = 0;
    public $appointments_count = 0;
    public $doctor_appointments_count = 0;
    public $upcoming_appointments_count = 0;
    public $complete_appointments_count = 0;
    public $last_week_appointments_count = 0;
    public $last_month_appointments_count = 0;
    public $last_week_users_count = 0;
    public $last_month_users_count = 0;
    public $last_week_doctor_count = 0;
    public $last_month_doctor_count = 0;
    public $last_week_patient_count = 0;
    public $last_month_patient_count = 0;

    public function mount()
    {
        $this->users_count = User::count();
        $this->doctors_count = Doctor::count();
        $this->patients_count = User::where('role', 0)->count();
        $this->appointments_count = Appointment::count();

        $user_doctor = auth()->user();
        if ($user_doctor && $user_doctor->role == 1) {
            $doctor = Doctor::where('user_id', $user_doctor->id)->first();
            $this->doctor_appointments_count = $doctor ? Appointment::where('doctor_id', $doctor->id)->count() : 0;

            // Pre-calculate last week and last month ranges once
            $lastWeekStart = Carbon::today()->subWeek();
            $lastMonthStart = Carbon::today()->subMonth();
            $today = Carbon::today();

            // Fetch doctor appointments
            $doctors_appointments = Appointment::where('doctor_id', $doctor->id)->get();

            foreach ($doctors_appointments as $value) {
                $appointmentDate = Carbon::parse($value->appointment_date);

                if ($appointmentDate->isAfter($today)) {
                    $this->upcoming_appointments_count++;
                }

                if ($appointmentDate->isBefore($today)) {
                    $this->complete_appointments_count++;
                }

                // Use the pre-calculated last week range
                if ($appointmentDate->isBetween($lastWeekStart, $today)) {
                    $this->last_week_appointments_count++;
                }

                // Use the pre-calculated last month range
                if ($appointmentDate->isBetween($lastMonthStart, $today)) {
                    $this->last_month_appointments_count++;
                }
            }
        }

        // Fetch all users 
        $all_users = User::all();

        foreach ($all_users as $value) {
            $userCreationDate = Carbon::parse($value->created_at);

            // Check if the user was created within the last week
            if ($userCreationDate->isBetween(Carbon::today()->subWeek(),Carbon::today())) {
                $this->last_week_users_count++;
            }

            // Check if the user was created within the last month
            if ($userCreationDate->isBetween(Carbon::today()->subMonth(),Carbon::today())) {
                $this->last_month_users_count++;
            }
        }

        // Fetch all doctors 
        $all_doctors = Doctor::all();

        foreach ($all_doctors as $value) {
            $userCreationDate = Carbon::parse($value->created_at);

            // Check if the user was created within the last week
            if ($userCreationDate->isBetween(Carbon::today()->subWeek(),Carbon::today())) {
                $this->last_week_doctor_count++;
            }

            // Check if the user was created within the last month
            if ($userCreationDate->isBetween(Carbon::today()->subMonth(),Carbon::today())) {
                $this->last_month_doctor_count++;
            }
        }

        // Fetch all patients 
        $all_patients = User::where('role',0)->get();

        foreach ($all_patients as $value) {
            $userCreationDate = Carbon::parse($value->created_at);

            // Check if the user was created within the last week
            if ($userCreationDate->isBetween(Carbon::today()->subWeek(),Carbon::today())) {
                $this->last_week_patient_count++;
            }

            // Check if the user was created within the last month
            if ($userCreationDate->isBetween(Carbon::today()->subMonth(),Carbon::today())) {
                $this->last_month_patient_count++;
            }
        }
    }

    public function render()
    {
        return view('livewire.statistic-component');
    }
}
