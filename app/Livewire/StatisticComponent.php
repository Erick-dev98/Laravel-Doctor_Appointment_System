<?php

namespace App\Livewire;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\User;
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
        } else {
            $this->doctor_appointments_count = 0;
        }
    }

    public function render()
    {
        return view('livewire.statistic-component');
    }
}
