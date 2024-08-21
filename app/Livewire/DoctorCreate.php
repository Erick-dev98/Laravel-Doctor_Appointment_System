<?php

namespace App\Livewire;

use App\Models\Doctor;
use App\Models\Specialities;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class DoctorCreate extends Component
{

    public $name = '';
    public $email = '';
    public $bio = '';
    public $hospital_name = '';
    public $password = '';
    public $twitter = '';
    public $linkedin = '';
    public $speciality_id = '';
    public $experience = '';
    public $specialities;

    public function mount() {
        $this->specialities = Specialities::all();
    }

    public function register() {
        //validate the data that is coming from frontend
        $this->validate([
            'name' => 'required',
            'email' => 'required',
            'bio' => 'required',
            'hospital_name' => 'required',
            'password' => 'required|min:4',
            'speciality_id' => 'required',
            'twitter' => 'string',
            'linkedin' => 'string',
            'experience' => 'required',
        ]);

        // user table
        $user = new User;
        $user->name = $this->name;
        $user->email = $this->email;
        $user->role = 1;
        $user->password = Hash::make($this->password);
        $user->save();

        // doctors table
        $doctor = new Doctor;
        $doctor->bio = $this->bio;
        $doctor->hospital_name = $this->hospital_name;
        $doctor->speciality_id = $this->speciality_id;
        $doctor->user_id = $user->id;
        $doctor->experience = $this->experience;
        $doctor->twitter = $this->twitter;
        $doctor->linkedin = $this->linkedin;
        $doctor->save();

        session()->flash('message','Doctor Created Successfully');
        return $this->redirect('/admin/doctors', navigate: true);
    }

    public function render()
    {
        return view('livewire.doctor-create');
    }
}
