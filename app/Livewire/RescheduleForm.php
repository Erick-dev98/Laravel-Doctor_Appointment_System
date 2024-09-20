<?php

namespace App\Livewire;

use App\Mail\AppointmentCreated;
use App\Models\Appointment;
use App\Models\DoctorSchedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class RescheduleForm extends Component
{
    public $doctor_details;
    public $selectedDate;
    public $appointment_details;
    public $availableDates = [];
    public $timeSlots = [];

    public function mount($appointment_id)
    {
        $this->appointment_details = Appointment::find($appointment_id);
        $this->doctor_details = $this->appointment_details->doctor;
        // Fetch the available dates when the booking page is loading
        $this->fetchAvailableDates($this->doctor_details);
    }

    public function bookAppointment($slot)
    {
        $carbonDate = Carbon::parse($this->selectedDate)->format('Y-m-d');
        $newAppointment = new Appointment();
        $newAppointment->update([
            
        ]);
        $newAppointment->patient_id = auth()->user()->id;
        $newAppointment->doctor_id = $this->doctor_details->id;
        $newAppointment->appointment_date = $carbonDate;
        $newAppointment->appointment_time = $slot;
        $newAppointment->save();


        // Retrieve all admins' emails where the role is 2
        $adminEmails = User::where('role', 2)->pluck('email')->toArray();
        $appointmentEmailData = [
            'date' => $this->selectedDate,
            'time' => Carbon::parse($slot)->format('H:i A'),
            'location' => '123 Medical Street, Health City',
            'patient_name' => auth()->user()->name,
            'patient_email' => auth()->user()->email,
            'doctor_name' => $this->doctor_details->doctorUser->name,
            'doctor_email' => $this->doctor_details->doctorUser->email,
            'admin_email' => $adminEmails,
            // 'appointment_type' => $this->appointment_type == 0 ? 'on-site' : 'live consultation',
            'doctor_specialization' => $this->doctor_details->speciality->speciality_name,
        ];
        // dd($appointmentEmailData);
        $this->sendAppointmentNotification($appointmentEmailData);

        session()->flash('message', 'appointment with Dr.' . $this->doctor_details->doctorUser->name . ' on ' . $this->selectedDate . $slot . ' was created!');

        return $this->redirect('/my/appointments', navigate: true);
    }

    public function fetchAvailableDates($doctor)
    {
        // Get all the time schedules from the database
        $schedules = DoctorSchedule::where('doctor_id', $doctor->id)
            ->get();

        $availability = [];
        foreach ($schedules as $schedule) {
            $dayOfWeek = $schedule->available_day; //0 - sunday, 1 - Monday
            $fromTime = Carbon::createFromFormat('H:i:s', $schedule->from);
            $toTime = Carbon::createFromFormat('H:i:s', $schedule->to);
            $availability[$dayOfWeek] = [
                'from' => $fromTime,
                'to' => $toTime,
            ];
        }

        $dates = [];
        $today = Carbon::today();
        for ($i = 0; $i < 365; $i++) { // Looping for 1 year
            $date = $today->copy()->addDays($i);
            $dayOfWeek = $date->dayOfWeek;

            if (isset($availability[$dayOfWeek])) {
                $dates[] = $date->format('Y-m-d');
            }
        }

        $this->availableDates = $dates;
    }

    public function selectDate($date)
    {
        $this->selectedDate = $date;
        $this->fetchTimeSlots($date, $this->doctor_details);
    }

    public function fetchTimeSlots($date, $doctor)
    {
        $dayOfWeek = Carbon::parse($date)->dayOfWeek; //0 , 1... 5
        $carbonDate = Carbon::parse($date)->format('Y-m-d');
        $schedule = DoctorSchedule::where('doctor_id', $doctor->id)
            ->where('available_day', $dayOfWeek)
            ->first();

        if ($schedule) {
            $fromTime = Carbon::createFromFormat('H:i:s', $schedule->from);
            $toTime = Carbon::createFromFormat('H:i:s', $schedule->to);

            $slots = [];
            while ($fromTime->lessThan($toTime)) {

                $timeSlot = $fromTime->format('H:i:s');
                $appointmentExists = Appointment::where('doctor_id',  $doctor->id)
                    ->where('appointment_date', $carbonDate)
                    ->where('appointment_time', $timeSlot)
                    ->exists();
                if (!$appointmentExists) {
                    $slots[] = $timeSlot;
                }

                $fromTime->addHour();
            }

            $this->timeSlots = $slots;
            // dd($this->timeSlots);

        } else {
            $this->timeSlots = [];
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
                Mail::to($adminEmail)->send(new AppointmentCreated($appointmentData));
            }
        } else {
            // If it's a single email, send as usual
            Mail::to($appointmentData['admin_email'])->send(new AppointmentCreated($appointmentData));
        }

        // Send to Doctor
        $appointmentData['recipient_name'] = $appointmentData['doctor_name'];
        $appointmentData['recipient_role'] = 'doctor';
        Mail::to($appointmentData['doctor_email'])->send(new AppointmentCreated($appointmentData));

        // Send to Patient
        $appointmentData['recipient_name'] = $appointmentData['patient_name'];
        $appointmentData['recipient_role'] = 'patient';
        Mail::to($appointmentData['patient_email'])->send(new AppointmentCreated($appointmentData));

        return 'Appointment notifications sent successfully!';
    }

    public function render()
    {
        return view('livewire.reschedule-form');
    }
}
