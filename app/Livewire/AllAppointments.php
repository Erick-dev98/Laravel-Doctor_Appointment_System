<?php

namespace App\Livewire;

use App\Mail\AppointmentCancelled;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Livewire\WithPagination;

class AllAppointments extends Component
{
    use WithPagination;
    public $perPage = 5;
    public $search = '';

    public function cancel($id)
    {
        $cancelled_by_details = auth()->user();
        $appointment = Appointment::find($id);

        //Get patient details
        $patient = User::find($appointment->patient_id);
        //Get doctor details
        $doctor = Doctor::find($appointment->doctor_id);

        // sleep(3);

        // Create a payload before deleting the data
        // Retrieve all admins' emails where the role is 2
        $adminEmails = User::where('role', 2)->pluck('email')->toArray();
        $appointmentEmailData = [
            'date' => $appointment->appointment_date,
            'time' => Carbon::parse($appointment->appointment_time)->format('H:i A'),
            'location' => '123 Medical Street, Health City',
            'patient_name' => $patient->name,
            'patient_email' => $patient->email,
            'doctor_name' => $doctor->doctorUser->name,
            'doctor_email' => $doctor->doctorUser->email,
            'admin_email' => $adminEmails,
            // 'appointment_type' => $this->appointment_type == 0 ? 'on-site' : 'live consultation',
            'doctor_specialization' => $doctor->speciality->speciality_name,
            'cancelled_by' => $cancelled_by_details->name,
            'role' => $cancelled_by_details->role,
        ];
        // dd($appointmentEmailData);
        $this->sendAppointmentNotification($appointmentEmailData);

        $appointment->delete();

        session()->flash('message', 'Appointment cancelled successfully');
        if (auth()->user()->role == 0) {
            return $this->redirect('/my/appointments', navigate: true);
        }

        if (auth()->user()->role == 2) {
            return $this->redirect('/admin/appointments', navigate: true);
        }

        if (auth()->user()->role == 1) {
            return $this->redirect('/doctor/appointments', navigate: true);
        }
    }

    public function sendAppointmentNotification($appointmentData)
    {
        // Send to Admin
        $appointmentData['recipient_name'] = 'Admin Admin';
        $appointmentData['recipient_role'] = 'admin';
        // Check if there are multiple admin emails and send to each one
        if (is_array($appointmentData['admin_email'])) {
            foreach ($appointmentData['admin_email'] as $adminEmail) {
                Mail::to($adminEmail)->send(new AppointmentCancelled($appointmentData));
            }
        } else {
            // If it's a single email, send as usual
            Mail::to($appointmentData['admin_email'])->send(new AppointmentCancelled($appointmentData));
        }

        // Send to Doctor
        $appointmentData['recipient_name'] = $appointmentData['doctor_name'];
        $appointmentData['recipient_role'] = 'doctor';
        Mail::to($appointmentData['doctor_email'])->send(new AppointmentCancelled($appointmentData));

        // Send to Patient
        $appointmentData['recipient_name'] = $appointmentData['patient_name'];
        $appointmentData['recipient_role'] = 'patient';
        Mail::to($appointmentData['patient_email'])->send(new AppointmentCancelled($appointmentData));

        return 'Appointment notifications sent successfully!';
    }

    public function render()
    {
        $user = auth()->user();

        if (auth()->user() && auth()->user()->role == 1) {

            $doctor = Doctor::where('user_id', $user->id)->first();

            return view('livewire.all-appointments', [
                'all_appointments' => Appointment::search($this->search)
                    ->with('patient', 'doctor')
                    ->where('doctor_id', $doctor->id)
                    ->paginate($this->perPage)
            ]);
        }
        if (auth()->user() && auth()->user()->role == 0) {

            return view('livewire.all-appointments', [
                'all_appointments' => Appointment::search($this->search)
                    ->with('patient', 'doctor')
                    ->where('patient_id', $user->id)
                    ->paginate($this->perPage)
            ]);
        }
        return view('livewire.all-appointments', [
            'all_appointments' => Appointment::search($this->search)
                ->with('patient', 'doctor')
                ->paginate($this->perPage)
        ]);
    }
}
